<?php

/*
|--------------------------------------------------------------------------
| Uji konkurensi NYATA (MySQL/MariaDB, banyak proses PHP paralel)
|--------------------------------------------------------------------------
| Membuktikan penutupan dua RELEASE BLOCKER:
|   1. Closing race: snapshot tutup buku selalu sama dengan keadaan final periode (tanpa drift), apa pun urutan
|      pembayaran/refund/checkout/invoice manual/transaksi manual/void/registrasi yang berjalan bersamaan.
|   2. Invoice pengganti ganda: satu invoice void hanya punya satu pengganti aktif, kredit berpindah sekali,
|      stok dan omzet tidak ganda.
|
| Skrip membuat DATABASE SEMENTARA (khayra_race_<acak>) di server yang ditunjuk DB_HOST/DB_PORT/DB_USERNAME/DB_PASSWORD,
| menjalankan migration di sana, lalu menghapusnya. TIDAK menyentuh database lain.
|
| Pemakaian:  php tests/Concurrency/run.php            (exit 0 = semua lulus)
| Variabel :  DB_HOST (127.0.0.1) DB_PORT (3306) DB_USERNAME (root) DB_PASSWORD ()   RACE_ITERATIONS (6)
*/

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$iterations = (int) (getenv('RACE_ITERATIONS') ?: 6);
$database = 'khayra_race_'.bin2hex(random_bytes(4));

$pdo = new PDO("mysql:host=$host;port=$port", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

$env = [
    'APP_ENV' => 'testing', 'DB_CONNECTION' => 'mysql', 'DB_HOST' => $host, 'DB_PORT' => $port, 'DB_DATABASE' => $database,
    'DB_USERNAME' => $user, 'DB_PASSWORD' => $pass, 'SESSION_DRIVER' => 'array', 'CACHE_STORE' => 'array', 'QUEUE_CONNECTION' => 'sync',
    'NEW_PATIENT_ADMIN_FEE_ENABLED' => 'true', 'APP_MAINTENANCE_DRIVER' => 'file', 'BCRYPT_ROUNDS' => '4',
    'ADMIN_EMAIL' => 'race@uji.test', 'ADMIN_PASSWORD' => 'kata-sandi-uji-lokal', 'ADMIN_PASSWORD_HASH' => '',
];

foreach ($env as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

$failures = [];
$checks = 0;

$assert = function (bool $condition, string $label, string $detail = '') use (&$failures, &$checks) {
    $checks++;
    echo ($condition ? '  PASS ' : '  FAIL ').$label.($detail !== '' ? '  ['.$detail.']' : '')."\n";

    if (! $condition) {
        $failures[] = $label.($detail !== '' ? " [$detail]" : '');
    }
};

$cleanup = function () use ($pdo, $database) {
    $pdo->exec("DROP DATABASE IF EXISTS `$database`");
};
register_shutdown_function($cleanup);

// ---------------------------------------------------------------- util proses paralel
$runArtisan = function (string $command) use ($php, $root, $env) {
    $process = proc_open([$php, 'artisan', ...explode(' ', $command)], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root, array_merge(getenv(), $env));
    $output = stream_get_contents($pipes[1]).stream_get_contents($pipes[2]);
    $code = proc_close($process);

    return [$code, $output];
};

/**
 * @param  array<int, array{0: int, 1: string, 2: array}>  $jobs  [offset ms, aksi, params]
 * @return array<int, array> hasil JSON tiap job (urut sama)
 */
$parallel = function (array $jobs) use ($php, $root, $env) {
    $start = microtime(true);
    $running = [];
    $results = [];
    $pending = $jobs;
    $index = array_keys($jobs);

    while ($pending || $running) {
        foreach ($pending as $i => [$offset, $action, $params]) {
            if ((microtime(true) - $start) * 1000 >= $offset) {
                $process = proc_open([$php, $root.'/tests/Concurrency/worker.php', $action, json_encode($params)], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root, array_merge(getenv(), $env));
                $running[$i] = [$process, $pipes];
                unset($pending[$i]);
            }
        }

        foreach ($running as $i => [$process, $pipes]) {
            $status = proc_get_status($process);

            if (! $status['running']) {
                $out = stream_get_contents($pipes[1]).stream_get_contents($pipes[2]);
                proc_close($process);
                $lines = array_values(array_filter(explode("\n", trim($out))));
                $results[$i] = json_decode(end($lines) ?: '{}', true) ?: ['ok' => false, 'error' => 'keluaran tidak valid: '.substr($out, 0, 200)];
                unset($running[$i]);
            }
        }

        usleep(10000);
    }

    ksort($results);

    return array_values($results);
};

// ---------------------------------------------------------------- migrasi + bootstrap in-process
[$code, $out] = $runArtisan('migrate --force');

if ($code !== 0) {
    fwrite(STDERR, "migrate gagal:\n$out\n");
    exit(2);
}

require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\BillingItem;
use App\Models\BillingPayment;
use App\Models\FinanceMonthlyClosing;
use App\Models\FinanceTransaction;
use App\Models\InventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\Patient;
use App\Models\PendingRegistrationFee;
use App\Services\FinanceReport;
use App\Services\MonthlyClosing;
use App\Services\PaymentLedger;
use App\Support\ClinicTime;
use Carbon\Carbon;

$patient = Patient::create(['full_name' => 'Pasien Balapan', 'whatsapp' => '0811000111']);
$month = ClinicTime::monthKey();
$today = ClinicTime::today();
$prevMonth = Carbon::parse($month.'-01')->subMonth()->format('Y-m');
$seq = 0;

$ledgerInvoice = function (int $amount, string $date, int $paid = 0) use ($patient, &$seq) {
    $billing = Billing::create([
        'patient_id' => $patient->id, 'invoice_number' => 'INV-RACE-'.(++$seq), 'invoice_date' => $date, 'subtotal_amount' => $amount, 'amount' => $amount,
        'paid_amount' => 0, 'remaining_amount' => $amount, 'payment_status' => 'unpaid', 'payment_model' => 'ledger',
    ]);

    if ($paid > 0) {
        (new PaymentLedger)->recordPayment($billing, $paid, 'cash');
    }

    return $billing->fresh();
};

/** Selisih snapshot vs keadaan final periode (harus kosong). */
$drift = function (string $m) {
    $closing = FinanceMonthlyClosing::active()->where('month', $m)->first();

    if (! $closing) {
        return ['tidak ada closing aktif'];
    }

    $live = FinanceReport::forMonth($m, false)['summary'];
    $snap = $closing->snapshot['summary'];
    $diffs = [];

    foreach (['invoice_revenue' => 'invoice_revenue', 'billing_income' => 'billing_income', 'manual_income' => 'manual_income', 'expense' => 'total_expense', 'total_income' => 'total_income', 'net_cashflow' => 'net_cashflow'] as $liveKey => $snapKey) {
        if (abs((float) $live[$liveKey] - (float) $snap[$snapKey]) > 0.01) {
            $diffs[] = "$snapKey snapshot {$snap[$snapKey]} vs live {$live[$liveKey]}";
        }
    }

    if ((int) $live['transaction_count'] !== (int) $closing->transaction_count) {
        $diffs[] = "transaction_count snapshot {$closing->transaction_count} vs live {$live['transaction_count']}";
    }

    return $diffs;
};

$badErrors = function (array $results) {
    return array_values(array_filter(array_map(function ($r) {
        $text = json_encode($r, JSON_UNESCAPED_UNICODE);

        return preg_match('/Deadlock|Lock wait timeout|SQLSTATE/i', $text) ? $text : null;
    }, $results)));
};

$checkoutParams = fn (array $o = []) => array_merge([
    'patient_id' => $patient->id, 'invoice_date' => $today, 'paid_amount' => 20000, 'payment_method' => 'cash', 'checkout_token' => bin2hex(random_bytes(8)),
    'item_type' => ['service'], 'description' => ['Jasa balapan'], 'quantity' => [1], 'unit_price' => [50000], 'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
], $o);

// =============================================================================================
echo "\n== A. Pembanding: algoritma tutup buku LAMA kehilangan pembayaran yang commit di antara hitung dan tulis (bug terbukti)\n";
$invoice = $ledgerInvoice(500000, $today);
$res = $parallel([
    [0, 'legacy', ['month' => $month, 'gap_ms' => 1800]],
    [900, 'pay', ['billing' => $invoice->id, 'amount' => 123000, 'key' => 'legacy-pay']],
]);
$legacyDrift = $drift($month);
$assert($res[1]['ok'] ?? false, 'A1 pembayaran commit saat closing LAMA sedang menghitung');
$assert($legacyDrift !== [], 'A2 closing LAMA menghasilkan DRIFT (bug nyata)', implode('; ', $legacyDrift));
FinanceMonthlyClosing::query()->where('month', $month)->delete();     // bersihkan closing eksperimen lama

// =============================================================================================
echo "\n== B. Closing BARU vs 7 jenis penulis paralel ($iterations iterasi, urutan acak)\n";
mt_srand((int) (getenv('RACE_SEED') ?: 20261012));
$allDrift = [];
$bad = [];
$committedAfterClose = 0;
$closedRejections = 0;
$writersCommitted = 0;

for ($iteration = 1; $iteration <= $iterations; $iteration++) {
    $payInvoiceA = $ledgerInvoice(300000, $today);
    $payInvoiceB = $ledgerInvoice(300000, $today);
    $refundInvoice = $ledgerInvoice(200000, $today, 200000);
    $voidInvoice = $ledgerInvoice(150000, $today, 150000);

    // invoice yang akan direfund: void dulu dengan kredit tertahan
    $refundInvoice->update(['payment_status' => 'void', 'voided_at' => now(), 'void_reason' => 'uji', 'paid_amount' => 0, 'remaining_amount' => 0]);

    $at = fn (int $min, int $max) => mt_rand($min, $max);
    $jobs = [
        [$at(150, 500), 'close', ['month' => $month, 'hold_ms' => $at(200, 600), 'gap_ms' => $at(800, 1200)]],
        [$at(0, 1500), 'pay', ['billing' => $payInvoiceA->id, 'amount' => 11000, 'key' => "p-a-$iteration"]],
        [$at(0, 1500), 'pay', ['billing' => $payInvoiceB->id, 'amount' => 12000, 'key' => "p-b-$iteration"]],
        [$at(0, 1500), 'refund', ['billing' => $refundInvoice->id, 'amount' => 70000, 'key' => "r-$iteration"]],
        [$at(0, 1500), 'http', ['method' => 'POST', 'uri' => '/admin/cashier/checkout', 'params' => $checkoutParams()]],
        [$at(0, 1500), 'http', ['method' => 'POST', 'uri' => '/admin/billings', 'params' => [
            'patient_id' => $patient->id, 'invoice_date' => $today, 'amount' => 80000, 'paid_amount' => 30000, 'payment_status' => 'partial', 'payment_method' => 'cash', 'checkout_token' => bin2hex(random_bytes(8)),
        ]]],
        [$at(0, 1500), 'http', ['method' => 'POST', 'uri' => '/admin/owner-finance/transactions', 'params' => [
            'transaction_date' => $today, 'type' => 'expense', 'title' => 'Biaya uji '.$iteration, 'amount' => 45000,
        ]]],
        [$at(0, 1500), 'http', ['method' => 'POST', 'uri' => "/admin/billings/{$voidInvoice->id}/void", 'params' => [
            'void_reason' => 'Void uji konkurensi', 'money_disposition' => 'refund', 'refund_method' => 'cash',
        ]]],
        [$at(0, 1500), 'register', ['token' => "reg-$iteration-".bin2hex(random_bytes(3))]],
    ];

    $results = $parallel($jobs);
    $closeResult = $results[0];
    $bad = array_merge($bad, $badErrors($results));

    foreach (array_slice($results, 1) as $r) {
        $text = json_encode($r, JSON_UNESCAPED_UNICODE);

        if (str_contains($text, 'tutup buku') || str_contains($text, 'TERTUNDA')) {
            $closedRejections++;
        } elseif (($r['ok'] ?? false) && (($r['status'] ?? 302) < 400)) {
            $writersCommitted++;
        }
    }

    if (! ($closeResult['ok'] ?? false)) {
        $bad[] = 'close gagal: '.json_encode($closeResult, JSON_UNESCAPED_UNICODE);
    } else {
        $closedAt = $closeResult['closed_at'];
        // Tidak boleh ada invoice/pembayaran/transaksi periode ini yang dibuat SETELAH closing commit.
        $committedAfterClose += Billing::whereBetween('invoice_date', ["$month-01", "$month-31"])->where('created_at', '>', $closedAt)->count();
        $committedAfterClose += BillingPayment::whereBetween('paid_on', ["$month-01", "$month-31"])->where('created_at', '>', $closedAt)->count();
        $committedAfterClose += FinanceTransaction::whereBetween('transaction_date', ["$month-01", "$month-31"])->where('created_at', '>', $closedAt)->count();
    }

    $iterationDrift = $drift($month);
    $allDrift = array_merge($allDrift, array_map(fn ($d) => "iterasi $iteration: $d", $iterationDrift));

    MonthlyClosing::reopen($month, 'Buka kembali untuk iterasi uji berikutnya', 'race@uji.test');
}

$assert($allDrift === [], 'B1 snapshot closing = keadaan final periode pada SEMUA iterasi (tanpa drift)', implode('; ', array_slice($allDrift, 0, 3)));
$assert($committedAfterClose === 0, 'B2 tidak ada transaksi periode itu yang commit SETELAH closing', (string) $committedAfterClose);
$assert($bad === [], 'B3 tidak ada deadlock / lock wait timeout / error SQL', implode(' | ', array_slice($bad, 0, 2)));
$assert($writersCommitted > 0 && $closedRejections > 0, 'B4 uji menghasilkan kedua urutan (penulis sebelum closing dan ditolak sesudah closing)', "commit=$writersCommitted ditolak=$closedRejections");

// =============================================================================================
echo "\n== C. Pembayaran invoice BULAN LALU pada bulan berjalan (terbuka) saat bulan lalu ditutup bersamaan\n";
$lastMonthInvoice = $ledgerInvoice(400000, "$prevMonth-15");
$before = FinanceReport::forMonth($prevMonth, false)['summary'];
$res = $parallel([
    [0, 'close', ['month' => $prevMonth, 'hold_ms' => 400, 'gap_ms' => 900]],
    [700, 'pay', ['billing' => $lastMonthInvoice->id, 'amount' => 150000, 'key' => 'lm-1']],
    [800, 'http', ['method' => 'POST', 'uri' => "/admin/billings/{$lastMonthInvoice->id}/void", 'params' => ['void_reason' => 'Void invoice bulan tertutup', 'money_disposition' => 'refund', 'refund_method' => 'cash']]],
]);
$assert($res[0]['ok'] ?? false, 'C1 closing bulan lalu berhasil');
$assert(($res[1]['ok'] ?? false) && ($res[1]['created'] ?? false), 'C2 pembayaran invoice bulan lalu pada bulan berjalan yang terbuka DIIZINKAN', json_encode($res[1], JSON_UNESCAPED_UNICODE));
$assert(str_contains(json_encode($res[2], JSON_UNESCAPED_UNICODE), 'tutup buku'), 'C3 void invoice bulan yang sudah tutup ditolak');
$payment = BillingPayment::where('billing_id', $lastMonthInvoice->id)->first();
$assert($payment && $payment->paid_on->format('Y-m') === $month, 'C4 kas tercatat pada bulan berjalan, bukan bulan lalu', optional($payment)->paid_on?->toDateString() ?? '-');
$assert($drift($prevMonth) === [], 'C5 snapshot bulan lalu konsisten dan tidak berubah oleh pembayaran bulan berjalan', implode('; ', $drift($prevMonth)));
$assert((float) FinanceReport::forMonth($prevMonth, false)['summary']['billing_income'] === (float) $before['billing_income'], 'C6 arus kas bulan lalu tidak bergeser');

// =============================================================================================
echo "\n== D. Registrasi pasien (biaya admin) vs closing: invoice atau kewajiban tertunda, tidak pernah invoice di periode tertutup\n";
$tokens = array_map(fn ($i) => "regd-$i-".bin2hex(random_bytes(3)), range(1, 6));
$jobs = [[0, 'close', ['month' => $month, 'hold_ms' => 300, 'gap_ms' => 900]]];

foreach ($tokens as $i => $token) {
    $jobs[] = [200 + $i * 280, 'register', ['token' => $token]];
}

$results = $parallel($jobs);
$closedAt = $results[0]['closed_at'] ?? null;
$assert($closedAt !== null, 'D1 closing berhasil');
$feeInvoices = Billing::where('checkout_token', 'like', 'registration-fee:%')->where('created_at', '>', $closedAt ?? '2000-01-01')->count();
$assert($feeInvoices === 0, 'D2 tidak ada invoice biaya registrasi terbit setelah closing', (string) $feeInvoices);
$pendingCount = PendingRegistrationFee::whereIn('patient_id', Patient::whereIn('registration_token', $tokens)->pluck('id'))->count();
$feeCount = Billing::whereIn('checkout_token', Patient::whereIn('registration_token', $tokens)->pluck('id')->map(fn ($id) => "registration-fee:$id"))->count();
$assert($pendingCount + $feeCount === count($tokens), 'D3 setiap registrasi punya TEPAT satu kewajiban (invoice atau pending)', "pending=$pendingCount invoice=$feeCount");
$assert($drift($month) === [], 'D4 snapshot konsisten', implode('; ', $drift($month)));
MonthlyClosing::reopen($month, 'Buka kembali untuk uji berikutnya', 'race@uji.test');

// =============================================================================================
echo "\n== E. Invoice pengganti: dua/enam checkout paralel mengganti invoice void yang sama\n";
$item = InventoryItem::create(['sku' => 'RACE-1', 'name' => 'Produk Balapan', 'stock' => 10, 'selling_price' => 50000, 'status' => 'active']);
$sourceParams = $checkoutParams(['paid_amount' => 0, 'item_type' => ['service', 'inventory'], 'description' => ['Jasa balapan', 'Produk Balapan'], 'quantity' => [1, 1],
    'unit_price' => [450000, 50000], 'inventory_item_id' => ['', (string) $item->id], 'clinic_service_id' => ['', ''], 'item_category' => ['other', 'product']]);
$setup = $parallel([[0, 'http', ['method' => 'POST', 'uri' => '/admin/cashier/checkout', 'params' => $sourceParams]]]);
$source = Billing::where('checkout_token', $sourceParams['checkout_token'])->first();
$assert($source !== null && (float) $source->amount === 500000.0, 'E0 invoice sumber dibuat (Rp500.000 termasuk 1 produk)', json_encode($setup[0], JSON_UNESCAPED_UNICODE));
(new PaymentLedger)->recordPayment($source, 500000, 'cash');
$voidRes = $parallel([[0, 'http', ['method' => 'POST', 'uri' => "/admin/billings/{$source->id}/void", 'params' => ['void_reason' => 'Void untuk koreksi', 'money_disposition' => 'credit']]]]);
$source->refresh();
$assert($source->payment_status === 'void' && PaymentLedger::heldOnVoid($source) === 500000.0, 'E1 invoice sumber void dengan kredit tertahan Rp500.000', json_encode($voidRes[0], JSON_UNESCAPED_UNICODE));
$stockAfterVoid = (int) InventoryItem::find($item->id)->stock;
$assert($stockAfterVoid === 10, 'E2 stok kembali 10 setelah void');

$replaceJobs = [];

for ($i = 0; $i < 6; $i++) {
    $replaceJobs[] = [$i * 25, 'http', ['method' => 'POST', 'uri' => '/admin/cashier/checkout', 'params' => array_merge($sourceParams, ['replaces_billing_id' => $source->id, 'checkout_token' => 'repl-'.$i.'-'.bin2hex(random_bytes(4))])]];
}

$replaceResults = $parallel($replaceJobs);
$bad = $badErrors($replaceResults);
$active = Billing::where('replaces_billing_id', $source->id)->where('payment_status', '!=', 'void')->get();
$successes = count(array_filter($replaceResults, fn ($r) => ($r['ok'] ?? false) && str_contains((string) ($r['location'] ?? ''), '/admin/billings/') && ($r['success'] ?? null)));
$rejected = count(array_filter($replaceResults, fn ($r) => in_array('Invoice ini sudah pernah digantikan oleh invoice lain.', $r['errors'] ?? [], true)));
$assert($active->count() === 1, 'E3 HANYA satu invoice pengganti aktif', 'aktif='.$active->count().' sukses='.$successes.' ditolak='.$rejected);
$assert($successes === 1 && $rejected === 5, 'E4 1 checkout berhasil, 5 ditolak dengan pesan "sudah pernah digantikan"', "sukses=$successes ditolak=$rejected ".json_encode(array_map(fn ($r) => $r['errors'] ?? $r, $replaceResults), JSON_UNESCAPED_UNICODE));
$assert($bad === [], 'E5 tidak ada deadlock / error SQL pada replacement paralel', implode(' | ', array_slice($bad, 0, 2)));
$assert(BillingPayment::where('billing_id', $source->id)->where('type', 'credit_out')->count() === 1 && (float) BillingPayment::where('billing_id', $source->id)->where('type', 'credit_out')->sum('amount') === 500000.0, 'E6 kredit dipindahkan SATU kali (credit_out 1 baris = Rp500.000)');
$assert((float) BillingPayment::where('type', 'credit_in')->sum('amount') === 500000.0, 'E7 credit_in total Rp500.000 (tidak ganda)');
$assert(PaymentLedger::heldOnVoid($source->fresh()) === 0.0, 'E8 saldo kredit invoice sumber nol, tidak negatif');
$assert((int) InventoryItem::find($item->id)->stock === 9, 'E9 stok berkurang SEKALI untuk pengganti (10 -> 9)', 'stok='.InventoryItem::find($item->id)->stock);
$assert(InventoryStockMovement::where('inventory_item_id', $item->id)->where('type', 'out')->whereNotNull('billing_id')->count() === 2, 'E10 mutasi keluar: 1 invoice sumber + 1 pengganti (tidak ada mutasi ganda)', (string) InventoryStockMovement::where('inventory_item_id', $item->id)->where('type', 'out')->count());
$assert(Billing::where('replaces_billing_id', $source->id)->count() === 1, 'E11 tidak ada invoice setengah jadi / yatim: tepat satu baris invoice bertaut ke sumber');
$assert(BillingItem::whereNotIn('billing_id', Billing::pluck('id'))->count() === 0, 'E12 tidak ada item tanpa invoice');
$assert(BillingAuditLog::where('billing_id', $source->id)->where('action', 'replaced_by')->count() === 1, 'E13 audit replaced_by tepat satu');
$replacement = $active->first();
$assert($replacement && $replacement->payment_status === 'paid' && (float) $replacement->paid_amount === 500000.0, 'E14 pengganti lunas dari kredit non-kas (tidak ada kas baru)');
$assert(BillingPayment::where('type', 'payment')->where('billing_id', $replacement?->id)->count() === 0, 'E15 tidak ada baris pembayaran kas pada pengganti');

echo "\n== F. Replacement vs refund bersamaan atas kredit tertahan yang sama (urutan kunci)\n";
$source2 = $ledgerInvoice(300000, $today, 300000);
$parallel([[0, 'http', ['method' => 'POST', 'uri' => "/admin/billings/{$source2->id}/void", 'params' => ['void_reason' => 'Void uji kredit', 'money_disposition' => 'credit']]]]);
$jobs = [];

foreach ([0, 1, 2] as $i) {
    $jobs[] = [$i * 30, 'http', ['method' => 'POST', 'uri' => '/admin/cashier/checkout', 'params' => $checkoutParams(['paid_amount' => 0, 'unit_price' => [300000], 'replaces_billing_id' => $source2->id, 'checkout_token' => 'f-'.$i.'-'.bin2hex(random_bytes(3))])]];
}

foreach ([0, 1] as $i) {
    $jobs[] = [$i * 30 + 10, 'refund', ['billing' => $source2->id, 'amount' => 300000, 'key' => 'f-ref-'.$i]];
}

$results = $parallel($jobs);
$held = PaymentLedger::heldOnVoid($source2->fresh());
$moved = (float) BillingPayment::where('billing_id', $source2->id)->where('type', 'credit_out')->sum('amount');
$refunded = (float) BillingPayment::where('billing_id', $source2->id)->where('type', 'refund')->sum('amount');
$assert($badErrors($results) === [], 'F1 tidak ada deadlock antara replacement (kunci sumber) dan refund/pemindahan kredit');
$assert($held >= 0 && $moved + $refunded <= 300000.01, 'F2 kredit tidak digunakan lebih dari sekali: dipindah + refund <= Rp300.000', "dipindah=$moved refund=$refunded sisa=$held");
$assert(Billing::where('replaces_billing_id', $source2->id)->where('payment_status', '!=', 'void')->count() <= 1, 'F3 paling banyak satu pengganti aktif');

echo "\n== G. Koreksi sah tetap berjalan: pengganti di-void lalu digantikan lagi; audit integrity bersih\n";
$active = Billing::where('replaces_billing_id', $source->id)->where('payment_status', '!=', 'void')->first();
$res = $parallel([[0, 'http', ['method' => 'POST', 'uri' => "/admin/billings/{$active->id}/void", 'params' => ['void_reason' => 'Void pengganti uji', 'money_disposition' => 'credit']]]]);
$active->refresh();
$assert($active->payment_status === 'void' && PaymentLedger::heldOnVoid($active) === 500000.0, 'G1 pengganti di-void: kredit tertahan kembali padanya (aturan koreksi existing)', json_encode($res[0], JSON_UNESCAPED_UNICODE));
$res = $parallel([[0, 'http', ['method' => 'POST', 'uri' => '/admin/cashier/checkout', 'params' => array_merge($sourceParams, ['replaces_billing_id' => $active->id, 'checkout_token' => 'g-'.bin2hex(random_bytes(4))])]]]);
$second = Billing::where('replaces_billing_id', $active->id)->where('payment_status', '!=', 'void')->first();
$assert($second !== null && $second->payment_status === 'paid', 'G2 pengganti dari pengganti berhasil (checkout sah)', json_encode($res[0], JSON_UNESCAPED_UNICODE));
$assert((int) InventoryItem::find($item->id)->stock === 9, 'G3 stok konsisten setelah koreksi berantai (9)', 'stok='.InventoryItem::find($item->id)->stock);

[$code, $audit] = $runArtisan('billing:audit-integrity --strict');
$assert($code === 0 && str_contains($audit, 'Tidak ada temuan'), 'G4 billing:audit-integrity --strict bersih setelah seluruh uji paralel', $code === 0 ? '' : implode(' / ', preg_grep('/temuan|^\s+- /', explode("\n", $audit))));

// ---------------------------------------------------------------- ringkasan
echo "\n".($failures ? 'GAGAL: '.count($failures)." dari $checks pemeriksaan\n".implode("\n", array_map(fn ($f) => ' - '.$f, $failures))."\n" : "SEMUA LULUS: $checks pemeriksaan\n");
exit($failures ? 1 : 0);
