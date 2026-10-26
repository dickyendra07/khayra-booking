<?php

/*
| Worker uji konkurensi (dipanggil oleh tests/Concurrency/run.php, satu proses PHP per "pengguna paralel").
| Memakai kernel HTTP aplikasi di dalam proses (rute asli, middleware asli) dengan sesi admin palsu, atau memanggil service.
| Hanya untuk database UJI sementara; jangan diarahkan ke database lain.
|
| Pemakaian: php worker.php <aksi> '<json params>'
|   http    {"method":"POST","uri":"/admin/...","params":{...}}   -> menulis satu baris JSON hasil
|   close   {"month":"2026-10","hold_ms":1500,"gap_ms":800}    -> MonthlyClosing::close dengan jeda setelah kunci dan jeda setelah hitung
|   legacy  {"month":"2026-10","gap_ms":1500}                   -> algoritma tutup buku LAMA (hitung dulu, tulis kemudian) sebagai pembanding bug
|   pay     {"billing":1,"amount":1000,"key":"k"}               -> PaymentLedger::recordPayment
|   refund  {"billing":1,"amount":1000,"key":"k"}               -> PaymentLedger::refund
|   register{"token":"t"}                                        -> PatientRegistration::register
|   issue   {"pending":1}                                        -> PendingRegistrationFees::issue
*/

use App\Models\Billing;
use App\Models\FinanceMonthlyClosing;
use App\Models\Patient;
use App\Services\FinanceReport;
use App\Services\MonthlyClosing;
use App\Services\PatientRegistration;
use App\Services\PaymentLedger;
use App\Services\PendingRegistrationFees;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;

$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

[, $action, $json] = $argv + [null, null, '{}'];
$p = json_decode($json, true) ?: [];

$out = static function (array $data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE)."\n";
};

try {
    switch ($action) {
        case 'http':
            $session = $app['session']->driver();
            $session->put('admin_logged_in', true);
            $session->put('admin_email', 'race@uji.test');
            $session->put('admin_credential_fp', \App\Support\AdminCredentials::fingerprint());
            $request = Request::create($p['uri'], $p['method'] ?? 'POST', $p['params'] ?? []);
            $response = $app->make(\Illuminate\Contracts\Http\Kernel::class)->handle($request);
            $errors = $session->get('errors');
            $flat = [];
            $walk = function ($node) use (&$walk, &$flat) {
                if ($node instanceof MessageBag) {
                    $node = $node->all();
                }

                if ($node instanceof ViewErrorBag) {
                    $node = $node->getBag('default')->all();
                }

                if (is_array($node)) {
                    array_walk($node, fn ($child) => $walk($child));
                } elseif (is_string($node)) {
                    $flat[] = $node;
                }
            };
            $walk($errors);
            $out([
                'ok' => true,
                'status' => $response->getStatusCode(),
                'location' => $response->headers->get('Location'),
                'errors' => $flat,
                'success' => $session->get('success'),
                'error' => $session->get('error'),
            ]);
            break;

        case 'close':
            if (($p['hold_ms'] ?? 0) > 0) {
                MonthlyClosing::$afterLockHook = static fn () => usleep($p['hold_ms'] * 1000);
            }
            if (($p['gap_ms'] ?? 0) > 0) {
                MonthlyClosing::$afterComputeHook = static fn () => usleep($p['gap_ms'] * 1000);
            }
            $closing = MonthlyClosing::close($p['month'], 'race', 'race@uji.test');
            $out(['ok' => true, 'closed_at' => $closing->closed_at->toDateTimeString(), 'income' => (float) $closing->billing_income]);
            break;

        case 'legacy':
            // Salinan algoritma LAMA (sebelum perbaikan): laporan dihitung di luar kunci, snapshot ditulis kemudian.
            $month = $p['month'];
            $report = FinanceReport::forMonth($month, false);
            usleep(($p['gap_ms'] ?? 0) * 1000);
            $closing = FinanceMonthlyClosing::updateOrCreate(['month' => $month], [
                'reopened_at' => null, 'period_start' => $month.'-01', 'period_end' => $month.'-28', 'billing_income' => $report['summary']['billing_income'],
                'manual_income' => $report['summary']['manual_income'], 'total_income' => $report['summary']['total_income'], 'total_expense' => $report['summary']['expense'],
                'net_cashflow' => $report['summary']['net_cashflow'], 'transaction_count' => $report['rows']->count(), 'closed_by' => 'legacy', 'closed_at' => now(),
                'snapshot' => ['basis' => 'cash_v2', 'summary' => [
                    'invoice_revenue' => $report['summary']['invoice_revenue'], 'billing_income' => $report['summary']['billing_income'], 'manual_income' => $report['summary']['manual_income'],
                    'total_income' => $report['summary']['total_income'], 'total_expense' => $report['summary']['expense'], 'net_cashflow' => $report['summary']['net_cashflow'],
                ]],
            ]);
            $out(['ok' => true, 'income' => (float) $closing->billing_income]);
            break;

        case 'pay':
            $payment = (new PaymentLedger)->recordPayment(Billing::findOrFail($p['billing']), (float) $p['amount'], 'cash', null, $p['key'] ?? null);
            $out(['ok' => true, 'created' => $payment->wasRecentlyCreated, 'id' => $payment->id]);
            break;

        case 'refund':
            $payment = (new PaymentLedger)->refund(Billing::findOrFail($p['billing']), (float) $p['amount'], 'cash', 'Refund uji konkurensi', $p['key'] ?? null);
            $out(['ok' => true, 'created' => $payment->wasRecentlyCreated, 'id' => $payment->id]);
            break;

        case 'register':
            $r = PatientRegistration::register(new Patient(['full_name' => 'Pasien '.$p['token'], 'whatsapp' => '0811']), $p['token']);
            $out(['ok' => true, 'created' => $r['created'], 'patient' => $r['patient']->id, 'fee' => $r['fee']?->id, 'pending' => $r['pending']?->id]);
            break;

        case 'issue':
            $r = PendingRegistrationFees::issue((int) $p['pending']);
            $out(['ok' => true, 'created' => $r['created'], 'billing' => $r['billing']->id]);
            break;

        default:
            $out(['ok' => false, 'error' => 'aksi tidak dikenal: '.$action]);
    }
} catch (Throwable $e) {
    $out(['ok' => false, 'exception' => get_class($e), 'error' => substr($e->getMessage(), 0, 300)]);
}
