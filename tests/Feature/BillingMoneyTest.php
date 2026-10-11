<?php

use App\Models\Billing;
use App\Services\BillingMoney;

/** Rumus lama (salinan persis dari closure sebelum dipusatkan) sebagai acuan kesetaraan. */
function legacyPaid($billing)
{
    if (($billing->payment_status ?? null) === 'void') {
        return 0;
    }

    $paid = (float) ($billing->paid_amount ?? 0);
    $amount = (float) ($billing->amount ?? 0);

    if (($billing->payment_status ?? null) === 'paid' && $paid <= 0) {
        return $amount;
    }

    return $paid;
}

function legacyOutstanding($billing)
{
    if (($billing->payment_status ?? null) === 'void') {
        return 0;
    }

    $total = (float) ($billing->grand_total ?? $billing->total_amount ?? $billing->amount ?? 0);
    $paid = (float) ($billing->paid_amount ?? 0);
    $status = $billing->payment_status ?: 'unpaid';

    if ($status === 'paid') {
        return 0;
    }

    return max($total - $paid, 0);
}

it('BillingMoney sama persis dengan rumus lama untuk semua kombinasi status dan nominal', function () {
    $statuses = ['paid', 'unpaid', 'partial', 'void', null, ''];
    $amounts = [0, 1, 100000, 465000, 550000.5];
    $paids = [null, 0, 1, 50000, 465000, 600000];

    $checked = 0;

    foreach ($statuses as $status) {
        foreach ($amounts as $amount) {
            foreach ($paids as $paid) {
                $billing = new Billing(['payment_status' => $status, 'amount' => $amount, 'paid_amount' => $paid]);

                expect(BillingMoney::paid($billing))->toEqual(legacyPaid($billing))
                    ->and(BillingMoney::outstanding($billing))->toEqual(legacyOutstanding($billing));

                $checked++;
            }
        }
    }

    expect($checked)->toBe(180);
});

it('rute tidak lagi memiliki salinan rumus uang terbayar sendiri', function () {
    $routes = file_get_contents(base_path('routes/web.php'));

    expect(substr_count($routes, '$moneyPaid = function'))->toBe(0)
        ->and(substr_count($routes, '$moneyOutstanding = function'))->toBe(0)
        ->and(substr_count($routes, 'BillingMoney::paid'))->toBeGreaterThanOrEqual(8)
        ->and(substr_count($routes, 'BillingMoney::outstanding'))->toBeGreaterThanOrEqual(5);
});
