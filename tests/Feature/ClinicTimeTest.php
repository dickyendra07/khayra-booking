<?php

use App\Support\ClinicTime;
use Illuminate\Support\Carbon;

afterEach(fn () => Carbon::setTestNow());

it('tanggal bisnis klinik memakai WIB, bukan UTC (dini hari WIB masih hari sebelumnya di UTC)', function () {
    // 20:30 UTC tanggal 31 Okt = 03:30 WIB tanggal 1 Nov.
    Carbon::setTestNow('2026-10-31 20:30:00');

    expect(now()->toDateString())->toBe('2026-10-31')      // UTC (perilaku lama)
        ->and(ClinicTime::today())->toBe('2026-11-01')
        ->and(ClinicTime::monthKey())->toBe('2026-11');
});

it('siang hari WIB tetap tanggal yang sama dengan UTC', function () {
    Carbon::setTestNow('2026-10-11 05:00:00');   // 12:00 WIB

    expect(ClinicTime::today())->toBe('2026-10-11')->and(ClinicTime::monthKey())->toBe('2026-10');
});

it('menampilkan timestamp UTC dari database sebagai waktu klinik tanpa mengubah nilai asli', function () {
    $utc = Carbon::parse('2026-10-11 17:14:00', 'UTC');

    expect(ClinicTime::display($utc))->toBe('2026-10-12 00:14')
        ->and(ClinicTime::display('2026-10-11 17:14:00'))->toBe('2026-10-12 00:14')
        ->and(ClinicTime::display(null))->toBe('-')
        ->and($utc->format('Y-m-d H:i'))->toBe('2026-10-11 17:14');   // nilai asli tidak bergeser
});

it('rentang UTC untuk tanggal bisnis klinik', function () {
    [$start, $end] = ClinicTime::utcRange('2026-11-01', '2026-11-30');

    expect($start)->toBe('2026-10-31 17:00:00')->and($end)->toBe('2026-11-30 16:59:59');
});

it('zona waktu aplikasi (penyimpanan) tetap UTC dan tidak diubah', function () {
    expect(config('app.timezone'))->toBe('UTC')->and(config('clinic.timezone'))->toBe('Asia/Jakarta');
});
