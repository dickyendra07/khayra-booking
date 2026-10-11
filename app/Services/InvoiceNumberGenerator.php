<?php

namespace App\Services;

use App\Models\Billing;
use App\Support\ClinicTime;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Nomor invoice berurutan per bulan: INV-YYYYMM-0001.
 *
 * Aman untuk checkout bersamaan di MySQL/MariaDB (InnoDB): counter dinaikkan dengan
 * UPDATE atomik yang langsung mengambil row lock eksklusif, lalu dibaca dalam transaction
 * yang sama. Pola INSERT IGNORE + SELECT ... FOR UPDATE sengaja dihindari karena memicu
 * deadlock saat banyak proses berjalan serentak (terbukti pada uji 40 proses paralel).
 *
 * Panggil di dalam transaction pembuatan invoice (idealnya `DB::transaction($callback, 3)`
 * agar deadlock langka pada awal bulan di-retry) sehingga kegagalan checkout ikut
 * membatalkan kenaikan counter. Nomor lama tidak pernah diubah dan nomor yang sudah dipakai
 * (mis. data lama) dilewati.
 */
class InvoiceNumberGenerator
{
    /**
     * Pastikan baris counter bulan ini ada. Panggil SEBELUM membuka transaction pembuatan invoice
     * (autocommit): INSERT di luar transaction tidak menahan lock sehingga tidak deadlock ketika
     * banyak kasir membuat invoice pertama bulan itu secara bersamaan.
     */
    public function prepare(?CarbonInterface $at = null): void
    {
        DB::table('invoice_sequences')->insertOrIgnore([
            'period' => ($at ?? ClinicTime::now())->format('Ym'),
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function next(?CarbonInterface $at = null): string
    {
        $period = ($at ?? ClinicTime::now())->format('Ym');

        return DB::transaction(function () use ($period) {
            do {
                $number = $this->increment($period);
                $invoiceNumber = sprintf('INV-%s-%04d', $period, $number);
            } while (Billing::where('invoice_number', $invoiceNumber)->exists());

            return $invoiceNumber;
        });
    }

    private function increment(string $period): int
    {
        $affected = $this->bump($period);

        if ($affected === 0) {
            // Baris periode belum ada (invoice pertama bulan ini).
            DB::table('invoice_sequences')->insertOrIgnore([
                'period' => $period,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->bump($period);
        }

        // Baris sudah dikunci eksklusif oleh UPDATE di transaction ini, sehingga nilai ini milik kita.
        return (int) DB::table('invoice_sequences')->where('period', $period)->value('last_number');
    }

    private function bump(string $period): int
    {
        return DB::table('invoice_sequences')->where('period', $period)->update([
            'last_number' => DB::raw('last_number + 1'),
            'updated_at' => now(),
        ]);
    }
}
