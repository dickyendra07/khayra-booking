<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Visit;
use DomainException;

/**
 * Baris tagihan berreferensi dengan pencegahan penagihan ganda:
 *  - visit_room        : biaya ruangan untuk satu visit (Req 3)
 *  - patient_admin_fee : biaya administrasi pasien baru (Req 6)
 *
 * Kedua fitur NONAKTIF secara default: biaya ruangan hanya untuk ruangan billable dengan tarif
 * dan satuan lengkap; biaya admin hanya bila config('clinic.new_patient_admin_fee.enabled') true.
 * Aturan kelayakan lengkap menunggu keputusan klinik; kelas ini hanya menyediakan struktur dan
 * pencegah duplikasi, bukan penagihan otomatis.
 */
class ReferencedChargeGuard
{
    public const VISIT_ROOM = 'visit_room';

    public const PATIENT_ADMIN_FEE = 'patient_admin_fee';

    /**
     * Validasi sebuah baris berreferensi untuk checkout.
     *
     * @return array{category: string, reference_prices: array<int, float>, rate_unit: ?string, reference_type: string, reference_id: int}
     *
     * @throws DomainException
     */
    public static function resolve(string $type, int $referenceId, int $patientId): array
    {
        if ($type === self::VISIT_ROOM) {
            $visit = Visit::find($referenceId);

            if (! $visit || (int) $visit->patient_id !== $patientId) {
                throw new DomainException('Visit untuk biaya ruangan tidak ditemukan atau bukan milik pasien ini.');
            }

            $room = Room::findByName($visit->room_name);

            if (! $room || ! $room->is_chargeable) {
                throw new DomainException('Ruangan "'.($visit->room_name ?: '-').'" belum berstatus berbayar dengan tarif dan satuan lengkap. Biaya ruangan tidak dapat ditagihkan.');
            }

            return [
                'category' => 'room_charge',
                'reference_prices' => [(float) $room->rate],
                'rate_unit' => $room->rate_unit,
                'reference_type' => self::VISIT_ROOM,
                'reference_id' => $visit->id,
            ];
        }

        if ($type === self::PATIENT_ADMIN_FEE) {
            if (! config('clinic.new_patient_admin_fee.enabled')) {
                throw new DomainException('Biaya administrasi pasien baru belum diaktifkan (menunggu persetujuan aturan oleh klinik).');
            }

            if ($referenceId !== $patientId || ! Patient::find($patientId)) {
                throw new DomainException('Biaya administrasi harus untuk pasien pada invoice ini.');
            }

            return [
                'category' => 'admin_fee',
                'reference_prices' => [(float) config('clinic.new_patient_admin_fee.amount')],
                'rate_unit' => null,
                'reference_type' => self::PATIENT_ADMIN_FEE,
                'reference_id' => $patientId,
            ];
        }

        throw new DomainException('Jenis referensi tagihan tidak dikenal.');
    }

    /**
     * Pastikan sumber yang sama belum ditagihkan pada invoice non-void lain. Panggil di dalam
     * transaction pembuatan invoice; baris sumber dikunci agar dua kasir tidak menagih bersamaan.
     *
     * @param  array<int, array{reference_type: ?string, reference_id: ?int}>  $rows
     *
     * @throws DomainException
     */
    public static function assertNotAlreadyCharged(array $rows): void
    {
        $seen = [];

        foreach ($rows as $row) {
            if (empty($row['reference_type'])) {
                continue;
            }

            $key = $row['reference_type'].':'.$row['reference_id'];

            if (isset($seen[$key])) {
                throw new DomainException('Biaya yang sama tidak boleh muncul dua kali pada satu invoice.');
            }

            $seen[$key] = true;

            // Kunci baris sumber supaya pengecekan + penyimpanan bersifat atomik.
            if ($row['reference_type'] === self::VISIT_ROOM) {
                Visit::lockForUpdate()->find($row['reference_id']);
            } else {
                Patient::lockForUpdate()->find($row['reference_id']);
            }

            $existing = self::existingCharge($row['reference_type'], (int) $row['reference_id']);

            if ($existing) {
                throw new DomainException('Biaya ini sudah ditagihkan pada invoice '.($existing->invoice_number ?: '#'.$existing->id).'. Tidak ditagihkan ganda.');
            }
        }
    }

    /** Invoice non-void yang sudah memuat biaya dengan referensi ini (atau null). */
    public static function existingCharge(string $type, int $referenceId): ?Billing
    {
        $item = BillingItem::where('reference_type', $type)->where('reference_id', $referenceId)
            ->whereHas('billing', fn ($q) => $q->where('payment_status', '!=', 'void'))
            ->with('billing')->first();

        return $item?->billing;
    }

    /** @return array<int, int> id visit yang biaya ruangannya sudah ditagihkan (non-void). */
    public static function billedVisitIds(): array
    {
        return self::chargedIds(self::VISIT_ROOM);
    }

    /** @return array<int, int> id pasien yang biaya adminnya sudah ditagihkan (non-void). */
    public static function adminFeeChargedPatientIds(): array
    {
        return self::chargedIds(self::PATIENT_ADMIN_FEE);
    }

    private static function chargedIds(string $type): array
    {
        return BillingItem::where('reference_type', $type)
            ->whereHas('billing', fn ($q) => $q->where('payment_status', '!=', 'void'))
            ->pluck('reference_id')->map(fn ($id) => (int) $id)->unique()->values()->all();
    }
}
