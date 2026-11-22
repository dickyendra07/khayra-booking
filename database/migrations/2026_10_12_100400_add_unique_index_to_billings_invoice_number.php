<?php

use App\Support\InvoiceNumberIndex;
use Illuminate\Database\Migrations\Migration;

/**
 * Unique index pada billings.invoice_number, HANYA jika data existing tidak
 * memiliki duplikat. Jika ada duplikat, index tidak dipasang (tidak ada data
 * yang diubah) dan peringatan dicatat; selesaikan duplikat lalu jalankan
 * `php artisan billing:ensure-invoice-unique-index`. Nomor lama tidak diubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        InvoiceNumberIndex::ensure();
    }

    public function down(): void
    {
        InvoiceNumberIndex::drop();
    }
};
