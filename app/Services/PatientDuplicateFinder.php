<?php

namespace App\Services;

use App\Models\Patient;
use App\Support\LoginThrottle;
use Illuminate\Support\Collection;

/**
 * Deteksi KANDIDAT pasien ganda. HANYA MEMBACA: tidak menggabungkan, mengubah, atau menghapus data apa pun.
 * Hasilnya hanya petunjuk untuk tinjauan manual oleh staf (kemungkinan salah deteksi harus dianggap wajar).
 *
 * Tingkat keyakinan:
 *  - high   : NIK sama (setelah dinormalisasi ke angka, minimal 10 digit).
 *  - medium : nama sama (dinormalisasi) dan tanggal lahir sama.
 *  - low    : nomor WhatsApp sama. Nomor sering dipakai bersama (orang tua-anak, suami-istri), jadi TIDAK cukup
 *             untuk menyimpulkan duplikat; hanya ditandai bila nama juga mirip (>= 80%).
 */
class PatientDuplicateFinder
{
    public const HIGH = 'high';

    public const MEDIUM = 'medium';

    public const LOW = 'low';

    /** Kandidat untuk data pendaftaran baru (belum disimpan) terhadap pasien yang ada. */
    public static function forAttributes(array $attributes, ?int $exceptPatientId = null): Collection
    {
        $probe = new Patient(array_intersect_key($attributes, array_flip(['full_name', 'birth_date', 'whatsapp', 'nik'])));
        $probe->id = -1;

        return self::candidatesFor($probe, Patient::query()->when($exceptPatientId, fn ($q) => $q->whereKeyNot($exceptPatientId))->get());
    }

    /**
     * Seluruh pasangan kandidat di antara pasien yang ada.
     *
     * @return Collection<int, array{a: int, b: int, level: string, reasons: array<int, string>}>
     */
    public static function scan(): Collection
    {
        $patients = Patient::query()->orderBy('id')->get();
        $pairs = [];

        foreach ($patients as $patient) {
            foreach (self::candidatesFor($patient, $patients->where('id', '>', $patient->id)) as $candidate) {
                $pairs[] = ['a' => $patient->id, 'b' => $candidate['patient']->id, 'level' => $candidate['level'], 'reasons' => $candidate['reasons']];
            }
        }

        return collect($pairs);
    }

    /**
     * @param  Collection<int, Patient>  $others
     * @return Collection<int, array{patient: Patient, level: string, reasons: array<int, string>}>
     */
    private static function candidatesFor(Patient $patient, Collection $others): Collection
    {
        $nik = self::nik($patient->nik);
        $name = self::name($patient->full_name);
        $birth = $patient->birth_date?->toDateString();
        $phone = LoginThrottle::normalizePhone((string) $patient->whatsapp);

        $found = collect();

        foreach ($others as $other) {
            if ($other->id === $patient->id) {
                continue;
            }

            $reasons = [];
            $level = null;

            if ($nik !== '' && $nik === self::nik($other->nik)) {
                $reasons[] = 'NIK sama';
                $level = self::HIGH;
            }

            if ($name !== '' && $birth !== null && $name === self::name($other->full_name) && $birth === $other->birth_date?->toDateString()) {
                $reasons[] = 'Nama dan tanggal lahir sama';
                $level ??= self::MEDIUM;
            }

            if ($phone !== '' && $phone === LoginThrottle::normalizePhone((string) $other->whatsapp)) {
                similar_text($name, self::name($other->full_name), $percent);

                if ($percent >= 80) {
                    $reasons[] = 'WhatsApp sama dan nama mirip';
                    $level ??= self::LOW;
                }
            }

            if ($level !== null) {
                $found->push(['patient' => $other, 'level' => $level, 'reasons' => $reasons]);
            }
        }

        return $found->sortBy(fn ($c) => [self::HIGH => 0, self::MEDIUM => 1, self::LOW => 2][$c['level']])->values();
    }

    private static function nik(?string $nik): string
    {
        $digits = preg_replace('/\D+/', '', (string) $nik);

        return strlen($digits) >= 10 ? $digits : '';
    }

    private static function name(?string $name): string
    {
        $name = mb_strtolower(trim((string) $name));
        $name = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $name);

        return trim(preg_replace('/\s+/', ' ', $name));
    }
}
