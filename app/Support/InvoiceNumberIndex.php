<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Memasang / mengecek unique index billings.invoice_number secara aman.
 */
class InvoiceNumberIndex
{
    public const INDEX = 'billings_invoice_number_unique';

    public static function exists(): bool
    {
        return Schema::hasIndex('billings', self::INDEX);
    }

    /** Nomor invoice yang dipakai lebih dari satu invoice (NULL / kosong diabaikan). */
    public static function duplicates(): array
    {
        return DB::table('billings')
            ->select('invoice_number', DB::raw('COUNT(*) as total'))
            ->whereNotNull('invoice_number')
            ->where('invoice_number', '!=', '')
            ->groupBy('invoice_number')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->map(fn ($row) => ['invoice_number' => $row->invoice_number, 'total' => (int) $row->total])
            ->all();
    }

    /**
     * @return string 'exists' | 'created' | 'blocked_by_duplicates'
     */
    public static function ensure(): string
    {
        if (self::exists()) {
            return 'exists';
        }

        $duplicates = self::duplicates();

        if ($duplicates !== []) {
            Log::warning('Unique index billings.invoice_number TIDAK dipasang: ditemukan nomor invoice duplikat.', [
                'jumlah_nomor_duplikat' => count($duplicates),
            ]);

            return 'blocked_by_duplicates';
        }

        Schema::table('billings', function ($table) {
            $table->unique('invoice_number', self::INDEX);
        });

        return 'created';
    }

    public static function drop(): void
    {
        if (self::exists()) {
            Schema::table('billings', function ($table) {
                $table->dropUnique(self::INDEX);
            });
        }
    }
}
