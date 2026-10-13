<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\PendingRegistrationFee;
use App\Support\ClinicTime;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Penerbitan kewajiban biaya administrasi yang tertunda (lihat PatientRegistration).
 *
 * - Invoice SELALU bertanggal hari ini dan hanya terbit bila periode hari ini terbuka: tidak ada invoice mundur ke
 *   periode tertutup dan closing historis tidak disentuh.
 * - Idempoten dan aman bersamaan: baris pending dikunci (lockForUpdate); penerbitan kedua melihat status issued dan
 *   mengembalikan invoice yang sama. Token invoice unik (`registration-fee:{pasien}`) menjadi lapis terakhir.
 * - Tidak ada pembayaran otomatis: invoice terbit unpaid.
 */
class PendingRegistrationFees
{
    public static function pendingCount(): int
    {
        return PendingRegistrationFee::where('status', PendingRegistrationFee::PENDING)->count();
    }

    /**
     * @return array{billing: Billing, created: bool}
     *
     * @throws DomainException bila periode hari ini tertutup atau data tidak konsisten
     */
    public static function issue(int $pendingId): array
    {
        // Baris counter nomor invoice dibuat di luar transaction (lihat InvoiceNumberGenerator).
        (new InvoiceNumberGenerator)->prepare();
        FinancePeriodLock::prepare([ClinicTime::today()]);

        return DB::transaction(function () use ($pendingId) {
            // Urutan kunci: periode (bersama) -> baris kewajiban -> invoice. Status tutup buku dibaca ulang setelah kunci.
            FinancePeriodLock::shared([ClinicTime::today()]);

            $pending = PendingRegistrationFee::lockForUpdate()->findOrFail($pendingId);

            if ($pending->status === PendingRegistrationFee::ISSUED) {
                return ['billing' => Billing::findOrFail($pending->billing_id), 'created' => false];
            }

            if (FinancePeriodLock::isClosed(ClinicTime::today())) {
                throw new DomainException(
                    'Periode '.BillingIntegrity::monthOf(ClinicTime::today()).' masih tutup buku. Invoice tidak diterbitkan dengan tanggal mundur. '
                    .'Terbitkan setelah periode baru dimulai, atau buka kembali periode lewat prosedur Buka Kembali Buku di Owner Finance.'
                );
            }

            if ($existing = NewPatientAdminFee::registrationInvoice($pending->patient_id)) {
                // Tidak seharusnya terjadi; jangan menerbitkan ganda, cukup tautkan.
                $billing = $existing;
            } else {
                $billing = PatientRegistration::issueInvoice($pending->patient()->firstOrFail());
            }

            $pending->update([
                'status' => PendingRegistrationFee::ISSUED,
                'billing_id' => $billing->id,
                'issued_at' => now(),
                'issued_by' => session('admin_email') ?: 'admin',
            ]);

            BillingAudit::record('registration_fee_issued', $billing, null, [
                'patient_id' => $pending->patient_id, 'pending_fee_id' => $pending->id, 'invoice_date' => ClinicTime::today(), 'closed_month' => $pending->closed_month,
            ], 'Kewajiban administrasi tertunda diterbitkan (tanggal hari penerbitan)');

            return ['billing' => $billing, 'created' => true];
        }, 3);
    }
}
