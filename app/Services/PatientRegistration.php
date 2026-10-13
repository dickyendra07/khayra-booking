<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Patient;
use App\Models\PendingRegistrationFee;
use App\Support\ClinicTime;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Satu-satunya pintu pendaftaran pasien BARU. Setiap jalur yang membuat baris pasien (form admin, Create Patient
 * dari booking, dan jalur lain di masa depan) wajib lewat register() agar biaya administrasi pasien baru
 * (aturan: sekali, saat registrasi pertama) tidak terlewat dan tidak pernah ganda. Dijaga test yang memindai kode.
 *
 * Idempotensi berlapis:
 *  1. token registrasi unik (patients.registration_token, unique index): token yang sama mengembalikan pasien
 *     yang sudah ada tanpa membuat pasien/tagihan baru;
 *  2. pasien dan tagihan administrasinya dibuat dalam SATU transaction (gagal bersama);
 *  3. tagihan memakai billings.checkout_token unik `registration-fee:{id pasien}` (unique index).
 * Pasien yang sudah ada tidak pernah disentuh: tidak ada penagihan retroaktif.
 *
 * Kewajiban tidak boleh hilang: bila periode berjalan sudah tutup buku, invoice TIDAK diterbitkan (tidak ada invoice
 * bertanggal mundur ke periode tertutup) dan sebagai gantinya dicatat kewajiban tertunda (pending_registration_fees, satu baris
 * per pasien, unique) dalam transaction yang sama dengan pasien. Pasien tetap dapat didaftarkan; kewajiban diterbitkan
 * kemudian lewat PendingRegistrationFees::issue.
 */
class PatientRegistration
{
    /**
     * @param  callable(Patient): void|null  $within  dijalankan di dalam transaction yang sama (mis. menautkan booking)
     * @return array{patient: Patient, created: bool, fee: ?Billing, pending: ?PendingRegistrationFee}
     */
    public static function register(Patient $patient, string $token, ?callable $within = null): array
    {
        if ($existing = Patient::where('registration_token', $token)->first()) {
            return self::result($existing, false);
        }

        $chargeFee = NewPatientAdminFee::enabled();

        if ($chargeFee) {
            // Baris counter nomor invoice dan baris kunci periode dibuat di luar transaction (lihat InvoiceNumberGenerator).
            (new InvoiceNumberGenerator)->prepare();
            FinancePeriodLock::prepare([ClinicTime::today()]);
        }

        try {
            [$fee, $pending] = DB::transaction(function () use ($patient, $token, $within, $chargeFee) {
                if ($chargeFee) {
                    // Kunci periode (bersama) SEBAGAI LANGKAH PERTAMA, lalu tentukan terbuka/tertutup dengan locking read: keputusan
                    // "terbitkan invoice atau catat kewajiban tertunda" tidak bisa basi terhadap tutup buku yang sedang berjalan.
                    FinancePeriodLock::shared([ClinicTime::today()]);
                    $closedMonth = FinancePeriodLock::isClosed(ClinicTime::today()) ? BillingIntegrity::monthOf(ClinicTime::today()) : null;
                }

                $patient->registration_token = $token;
                $patient->save();

                if ($within) {
                    $within($patient);
                }

                if (! $chargeFee) {
                    return [null, null];
                }

                return $closedMonth ? [null, self::recordPending($patient, $closedMonth)] : [self::issueInvoice($patient), null];
            }, 3);
        } catch (UniqueConstraintViolationException $exception) {
            // Dua pengiriman bersamaan dengan token yang sama: pemenangnya sudah menyimpan pasien dan kewajibannya.
            if ($existing = Patient::where('registration_token', $token)->first()) {
                return self::result($existing, false);
            }

            throw $exception;
        }

        return ['patient' => $patient, 'created' => true, 'fee' => $fee, 'pending' => $pending];
    }

    /** Kalimat tambahan pada pesan sukses registrasi: nomor tagihan administrasi atau status kewajiban tertunda. */
    public static function feeMessage(array $registration): string
    {
        if ($registration['fee']) {
            return ' Tagihan administrasi pasien baru '.$registration['fee']->invoice_number.' (Rp'
                .number_format((float) $registration['fee']->amount, 0, ',', '.').') dibuat dan dapat dibayar lewat halaman invoice.';
        }

        if ($registration['pending']) {
            return ' PERHATIAN: periode '.$registration['pending']->closed_month.' sudah tutup buku sehingga tagihan administrasi Rp'
                .number_format((float) $registration['pending']->amount, 0, ',', '.').' dicatat sebagai kewajiban TERTUNDA (tidak hilang, tidak ganda). '
                .'Terbitkan dari menu Biaya Admin Tertunda (/admin/registration-fees) setelah periode baru dimulai atau dibuka kembali.';
        }

        return '';
    }

    private static function result(Patient $patient, bool $created): array
    {
        return [
            'patient' => $patient,
            'created' => $created,
            'fee' => NewPatientAdminFee::registrationInvoice($patient->id),
            'pending' => PendingRegistrationFee::where('patient_id', $patient->id)->where('status', PendingRegistrationFee::PENDING)->first(),
        ];
    }

    /** Kewajiban tertunda (periode tutup buku). Unique per pasien; diaudit. Dipanggil di dalam transaction registrasi. */
    private static function recordPending(Patient $patient, string $closedMonth): PendingRegistrationFee
    {
        $pending = PendingRegistrationFee::create([
            'patient_id' => $patient->id,
            'amount' => NewPatientAdminFee::amount(),
            'status' => PendingRegistrationFee::PENDING,
            'closed_month' => $closedMonth,
            'reason' => 'Periode '.$closedMonth.' sudah tutup buku saat registrasi pasien baru',
            'created_by' => session('admin_email') ?: 'admin',
        ]);

        BillingAudit::record('registration_fee_pending', null, null, [
            'patient_id' => $patient->id, 'pending_fee_id' => $pending->id, 'amount' => (float) $pending->amount, 'closed_month' => $closedMonth,
        ], $pending->reason, $closedMonth);

        return $pending;
    }

    /**
     * Invoice administrasi (belum dibayar) untuk pasien. Bertanggal HARI INI (tidak pernah mundur). Dipanggil di dalam
     * transaction oleh registrasi atau penerbitan kewajiban tertunda; token unik menjaga satu invoice per pasien.
     */
    public static function issueInvoice(Patient $patient): Billing
    {
        $calculation = (new BillingCalculator)->calculate([[
            'category' => 'admin_fee', 'quantity' => 1, 'unit_price' => NewPatientAdminFee::amount(),
        ]]);
        $line = $calculation['lines'][0];

        $billing = Billing::create([
            'patient_id' => $patient->id,
            'visit_id' => null,
            'invoice_number' => (new InvoiceNumberGenerator)->next(),
            'invoice_date' => ClinicTime::today(),
            'subtotal_amount' => $calculation['subtotal'],
            'discount_type' => 'none',
            'discount_value' => 0,
            'discount_amount' => 0,
            'amount' => $calculation['grand_total'],
            'paid_amount' => 0,
            'change_amount' => 0,
            'remaining_amount' => $calculation['grand_total'],
            'payment_status' => 'unpaid',
            'payment_model' => 'ledger',
            'checkout_token' => NewPatientAdminFee::tokenFor($patient->id),
            'notes' => 'Biaya administrasi pasien baru (registrasi pertama).',
        ]);

        $billing->items()->create([
            'item_type' => 'service',
            'item_category' => 'admin_fee',
            'description' => 'Biaya Administrasi Pasien Baru',
            'quantity' => 1,
            'unit_price' => $line['unit_price'],
            'line_total' => $line['line_total'],
            'is_discount_eligible' => false,
            'discount_amount' => 0,
            'reference_type' => ReferencedChargeGuard::PATIENT_ADMIN_FEE,
            'reference_id' => $patient->id,
        ]);

        BillingAudit::record('created', $billing->fresh(), null, array_merge(BillingAudit::snapshot($billing->fresh()), [
            'source' => 'patient_registration',
            'items' => [['description' => 'Biaya Administrasi Pasien Baru', 'category' => 'admin_fee', 'quantity' => 1, 'unit_price' => $line['unit_price'], 'discount_eligible' => false, 'discount_amount' => 0]],
        ]), 'Tagihan otomatis registrasi pasien baru');

        return $billing->fresh();
    }
}
