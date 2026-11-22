<?php

namespace App\Console\Commands;

use App\Support\InvoiceNumberIndex;
use Illuminate\Console\Command;

/**
 * Memasang unique index invoice_number setelah audit duplikat bersih.
 * Tidak mengubah/menghapus data; menolak bila masih ada duplikat.
 */
class EnsureInvoiceUniqueIndex extends Command
{
    protected $signature = 'billing:ensure-invoice-unique-index';

    protected $description = 'Pasang unique index billings.invoice_number bila tidak ada nomor duplikat (aman, tanpa ubah data).';

    public function handle(): int
    {
        $result = InvoiceNumberIndex::ensure();

        if ($result === 'blocked_by_duplicates') {
            $this->error('Index TIDAK dipasang: masih ada nomor invoice duplikat. Jalankan `php artisan billing:audit-integrity` untuk daftar duplikat.');

            return self::FAILURE;
        }

        $this->info($result === 'exists' ? 'Unique index sudah terpasang.' : 'Unique index berhasil dipasang.');

        return self::SUCCESS;
    }
}
