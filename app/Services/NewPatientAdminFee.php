<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\PendingRegistrationFee;

/**
 * Biaya administrasi pasien baru (aturan bisnis FINAL).
 *
 * Rp35.000 dikenakan SATU KALI saat pasien pertama kali didaftarkan ke sistem. Tagihannya dibuat otomatis
 * oleh PatientRegistration pada registrasi pertama yang berhasil; tidak ada jalur lain yang membuatnya:
 *  - pasien lama (terdaftar sebelum aturan ini) tidak ditagih retroaktif,
 *  - kunjungan berikutnya, booking, reschedule, dan pembatalan tidak membuat tagihan,
 *  - invoice dibuat dengan kategori admin_fee, tanpa diskon, dan dibayar lewat PaymentLedger yang sama.
 *
 * Bila periode tutup buku saat registrasi, kewajiban dicatat di pending_registration_fees (lihat PatientRegistration) dan
 * diterbitkan kemudian oleh PendingRegistrationFees; tidak pernah hilang.
 *
 * "Pernah ditagih" dicatat oleh baris invoice dengan checkout_token unik `registration-fee:{id pasien}`
 * (unique index database). Token itu tetap ada walau invoice di-void, sehingga biaya tidak otomatis tertagih
 * lagi; koreksi dilakukan lewat Void + Terbitkan Ulang yang sudah ada.
 */
class NewPatientAdminFee
{
    public const TOKEN_PREFIX = 'registration-fee:';

    public const CHARGED = 'charged';

    public const VOIDED = 'voided';

    public const PENDING = 'pending';

    public const NOT_APPLICABLE = 'not_applicable';

    public static function enabled(): bool
    {
        return (bool) config('clinic.new_patient_admin_fee.enabled');
    }

    public static function amount(): float
    {
        return (float) config('clinic.new_patient_admin_fee.amount');
    }

    public static function tokenFor(int $patientId): string
    {
        return self::TOKEN_PREFIX.$patientId;
    }

    /** Invoice biaya administrasi registrasi pasien ini (termasuk yang sudah void), atau null untuk pasien lama. */
    public static function registrationInvoice(int $patientId): ?Billing
    {
        return Billing::where('checkout_token', self::tokenFor($patientId))->first();
    }

    /**
     * Status biaya administrasi untuk banyak pasien sekaligus (satu query).
     *
     * @param  iterable<int>  $patientIds
     * @return array<int, array{state: string, label: string, billing_id: ?int, invoice_number: ?string, payment_status: ?string}>
     */
    public static function states(iterable $patientIds): array
    {
        $ids = collect($patientIds)->map(fn ($id) => (int) $id)->unique()->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $invoices = Billing::whereIn('checkout_token', $ids->map(fn ($id) => self::tokenFor($id))->all())->get()->keyBy('checkout_token');
        $pendingIds = array_flip(PendingRegistrationFee::whereIn('patient_id', $ids)->where('status', PendingRegistrationFee::PENDING)->pluck('patient_id')->all());
        $states = [];

        foreach ($ids as $id) {
            $invoice = $invoices[self::tokenFor($id)] ?? null;
            $state = $invoice
                ? ($invoice->payment_status === 'void' ? self::VOIDED : self::CHARGED)
                : (isset($pendingIds[$id]) ? self::PENDING : self::NOT_APPLICABLE);

            $states[$id] = [
                'state' => $state,
                'label' => self::label($state, $invoice),
                'billing_id' => $invoice?->id,
                'invoice_number' => $invoice?->invoice_number,
                'payment_status' => $invoice?->payment_status,
            ];
        }

        return $states;
    }

    /** @return array{state: string, label: string, billing_id: ?int, invoice_number: ?string, payment_status: ?string} */
    public static function stateFor(int $patientId): array
    {
        return self::states([$patientId])[$patientId];
    }

    public static function label(string $state, ?Billing $invoice = null): string
    {
        $number = $invoice ? ($invoice->invoice_number ?: '#'.$invoice->id) : '';

        return match ($state) {
            self::CHARGED => 'Biaya administrasi registrasi sudah ditagihkan pada invoice '.$number.' ('.self::statusLabel($invoice?->payment_status).'). Tidak ditagih lagi.',
            self::PENDING => 'Biaya administrasi registrasi TERTUNDA (periode tutup buku saat registrasi). Terbitkan dari menu Biaya Admin Tertunda; tidak hilang dan tidak akan ganda.',
            self::VOIDED => 'Invoice biaya administrasi registrasi '.$number.' sudah di-void. Tidak ditagih otomatis lagi; gunakan Terbitkan Ulang bila perlu koreksi.',
            default => 'Pasien lama: biaya administrasi pasien baru tidak dikenakan (tidak ditagih retroaktif).',
        };
    }

    private static function statusLabel(?string $status): string
    {
        return match ($status) {
            'paid' => 'lunas',
            'partial' => 'dibayar sebagian',
            'unpaid' => 'belum dibayar',
            default => (string) $status,
        };
    }
}
