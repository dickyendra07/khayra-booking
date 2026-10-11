<?php

namespace App\Console\Commands;

use App\Models\Billing;
use App\Models\FinanceMonthlyClosing;
use App\Services\BillingMoney;
use App\Support\InvoiceNumberIndex;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Audit integritas data billing. HANYA MEMBACA: tidak mengubah data apa pun.
 * Aman dijalankan di staging / production (dengan persetujuan) untuk melihat kondisi data lama.
 */
class BillingAuditIntegrity extends Command
{
    protected $signature = 'billing:audit-integrity {--strict : Exit code 1 bila ada temuan}';

    protected $description = 'Audit integritas billing (read-only): nomor duplikat, paid vs amount, item vs total, drift closing.';

    public function handle(): int
    {
        $findings = 0;

        $findings += $this->section('1. Nomor invoice duplikat', collect(InvoiceNumberIndex::duplicates())
            ->map(fn ($row) => "{$row['invoice_number']} (dipakai {$row['total']}x)")->all());

        $this->line('   Unique index billings.invoice_number: '.(InvoiceNumberIndex::exists() ? 'TERPASANG' : 'BELUM terpasang'));
        $this->newLine();

        $findings += $this->section('2. Status paid dengan paid_amount 0 (data lama; laporan memakai amount sebagai fallback)',
            Billing::where('payment_status', 'paid')->where(fn ($q) => $q->whereNull('paid_amount')->orWhere('paid_amount', '<=', 0))
                ->pluck('id')->map(fn ($id) => "billing #{$id}")->all());

        $findings += $this->section('3. paid_amount berbeda dari amount pada invoice paid (selisih > Rp1)',
            Billing::where('payment_status', 'paid')->where('paid_amount', '>', 0)
                ->whereRaw('ABS(paid_amount - amount) > 1')->get()
                ->map(fn ($b) => "billing #{$b->id}: amount {$b->amount}, paid {$b->paid_amount}")->all());

        $overpaid = Billing::where('payment_status', '!=', 'void')->whereRaw('paid_amount > amount + 1')->get();
        $this->line('   Catatan: '.$overpaid->count().' invoice dengan paid_amount > amount (total kelebihan Rp '
            .number_format((float) $overpaid->sum(fn ($b) => (float) $b->paid_amount - (float) $b->amount), 0, ',', '.')
            .'). Laporan finance memakai paid_amount, sehingga uang kembalian bisa tercatat sebagai pemasukan.');
        $this->newLine();

        $findings += $this->section('4. Partial dengan paid_amount tidak valid atau sisa tidak konsisten',
            Billing::where('payment_status', 'partial')->get()
                ->filter(fn ($b) => (float) $b->paid_amount <= 0 || (float) $b->paid_amount >= (float) $b->amount
                    || abs((float) $b->remaining_amount - ((float) $b->amount - (float) $b->paid_amount)) > 1)
                ->map(fn ($b) => "billing #{$b->id}: amount {$b->amount}, paid {$b->paid_amount}, remaining {$b->remaining_amount}")->all());

        $findings += $this->section('5. Invoice itemised: total item - diskon berbeda dari amount (selisih > Rp1)',
            Billing::whereHas('items')->with('items')->where('payment_status', '!=', 'void')->get()
                ->filter(fn ($b) => abs((float) $b->items->sum('line_total') - (float) $b->discount_amount - (float) $b->amount) > 1)
                ->map(fn ($b) => "billing #{$b->id}: items ".(float) $b->items->sum('line_total').", diskon {$b->discount_amount}, amount {$b->amount}")->all());

        $findings += $this->section('6. Item tanpa invoice (yatim)',
            DB::table('billing_items')->leftJoin('billings', 'billings.id', '=', 'billing_items.billing_id')
                ->whereNull('billings.id')->pluck('billing_items.id')->map(fn ($id) => "item #{$id}")->all());

        $findings += $this->section('7. Invoice void yang masih memiliki paid_amount',
            Billing::where('payment_status', 'void')->where('paid_amount', '>', 0)->pluck('id')->map(fn ($id) => "billing #{$id}")->all());

        $drift = [];

        foreach (FinanceMonthlyClosing::active()->get() as $closing) {
            $live = Billing::whereBetween('invoice_date', [$closing->period_start->toDateString(), $closing->period_end->toDateString()])
                ->where('payment_status', '!=', 'void')->get()
                ->sum(fn ($b) => BillingMoney::paid($b));

            if (abs($live - (float) $closing->billing_income) >= 1) {
                $drift[] = "bulan {$closing->month}: snapshot ".(float) $closing->billing_income.', data billing saat ini '.$live;
            }
        }

        $findings += $this->section('8. Closing aktif yang berbeda dari data billing saat ini (pemasukan invoice)', $drift);

        // Dampak zona waktu: aplikasi memakai UTC, klinik WIB (UTC+7). Invoice yang dibuat antara
        // 00:00-07:00 WIB bertanggal hari sebelumnya. Hanya informasi; tidak dihitung sebagai temuan.
        $shifted = Billing::whereNotNull('created_at')->get()->filter(function ($b) {
            $utcDate = $b->created_at->copy()->setTimezone('UTC')->toDateString();
            $wibDate = $b->created_at->copy()->setTimezone('Asia/Jakarta')->toDateString();

            return $utcDate !== $wibDate && $b->invoice_date && $b->invoice_date->toDateString() === $utcDate;
        });

        $this->line('<options=bold>9. Dampak zona waktu UTC vs WIB (informasi)</>');
        $this->line('   Invoice bertanggal hari UTC padahal dibuat pada hari berikutnya menurut WIB: '.$shifted->count()
            .' dari '.Billing::count());
        $this->line('   Di antaranya melintasi batas BULAN (berisiko pada closing): '
            .$shifted->filter(fn ($b) => $b->created_at->copy()->setTimezone('Asia/Jakarta')->format('Y-m') !== $b->invoice_date->format('Y-m'))->count());
        $this->newLine();

        $this->info($findings === 0 ? 'Tidak ada temuan.' : "Total kelompok temuan: {$findings}");

        return $findings > 0 && $this->option('strict') ? self::FAILURE : self::SUCCESS;
    }

    /** @return int 1 bila ada temuan pada bagian ini */
    private function section(string $title, array $rows): int
    {
        $this->line("<options=bold>{$title}</>");

        if ($rows === []) {
            $this->line('   OK');
            $this->newLine();

            return 0;
        }

        $this->warn('   '.count($rows).' temuan');

        foreach (array_slice($rows, 0, 10) as $row) {
            $this->line('   - '.$row);
        }

        if (count($rows) > 10) {
            $this->line('   ... dan '.(count($rows) - 10).' lainnya');
        }

        $this->newLine();

        return 1;
    }
}
