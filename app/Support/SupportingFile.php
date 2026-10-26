<?php

namespace App\Support;

use App\Models\MedicalRecordSupportingData;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Penyaji lampiran data pendukung rekam medis (hasil lab, X-ray, dsb.) lewat rute yang wajib login.
 *
 * Lampiran lama tersimpan di disk `public` (terbuka lewat /storage/... tanpa login); lampiran baru di disk `local`
 * (privat). Penyaji mencari di kedua disk sehingga tautan lama tetap berfungsi setelah file dipindahkan oleh operator.
 */
class SupportingFile
{
    public const DIRECTORY = 'medical-record-supporting-data';

    /** Disk tempat file tersimpan (privat lebih dulu), atau null bila tidak ditemukan / path tidak sah. */
    public static function diskFor(?string $path): ?string
    {
        if (blank($path) || ! str_starts_with($path, self::DIRECTORY.'/') || str_contains($path, '..') || str_contains($path, "\0")) {
            return null;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }

    public static function response(MedicalRecordSupportingData $item): Response
    {
        $disk = self::diskFor($item->file_path);

        abort_if($disk === null, 404);

        $name = preg_replace('/[^A-Za-z0-9._ -]+/', '_', (string) ($item->file_name ?: basename($item->file_path)));

        return Storage::disk($disk)->response($item->file_path, $name, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; sandbox",
        ]);
    }
}
