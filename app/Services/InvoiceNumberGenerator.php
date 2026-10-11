<?php

namespace App\Services;

use App\Models\Billing;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Nomor invoice berurutan per bulan: INV-YYYYMM-0001.
 *
 * Aman untuk checkout bersamaan: counter bulanan dikunci dengan
 * SELECT ... FOR UPDATE (MySQL/MariaDB InnoDB) di dalam transaction.
 * Panggil di dalam transaction pembuatan invoice agar kegagalan checkout
 * ikut membatalkan kenaikan counter. Nomor lama tidak pernah diubah dan
 * nomor yang sudah dipakai (mis. data lama) dilewati.
 */
class InvoiceNumberGenerator
{
    public function next(?CarbonInterface $at = null): string
    {
        $period = ($at ?? now())->format('Ym');

        return DB::transaction(function () use ($period) {
            DB::table('invoice_sequences')->insertOrIgnore([
                'period' => $period,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sequence = DB::table('invoice_sequences')->where('period', $period)->lockForUpdate()->first();
            $number = (int) $sequence->last_number;

            do {
                $number++;
                $invoiceNumber = sprintf('INV-%s-%04d', $period, $number);
            } while (Billing::where('invoice_number', $invoiceNumber)->exists());

            DB::table('invoice_sequences')->where('period', $period)->update([
                'last_number' => $number,
                'updated_at' => now(),
            ]);

            return $invoiceNumber;
        });
    }
}
