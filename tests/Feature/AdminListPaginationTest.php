<?php

use App\Models\Billing;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Visit;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
 * Karakterisasi angka ringkasan halaman Booking (dikunci pada kode LAMA sebelum optimasi query) dan
 * perilaku paginasi daftar admin (booking, visit, billing).
 */
function seedBookingsForSummary(): array
{
    $selected = ClinicTime::now()->startOfMonth()->addDays(14);   // pertengahan bulan berjalan
    $date = fn (int $offset) => $selected->copy()->addDays($offset)->toDateString();
    $rows = [
        [$date(0), 'pending'], [$date(0), 'pending'], [$date(0), 'confirmed'], [$date(0), 'completed'],
        [$date(1), 'confirmed'], [$date(-1), 'completed'], [$date(-1), 'cancelled'],
        [$date(10), 'pending'], [$date(-12), 'arrived'], [$date(-12), 'in_treatment'], [$date(-12), 'no_show'],
        [$selected->copy()->subMonths(2)->toDateString(), 'completed'],   // di luar grid bulan
        [$selected->copy()->addMonths(2)->toDateString(), 'pending'],      // di luar grid bulan
    ];

    foreach ($rows as $i => [$d, $status]) {
        Booking::create(['full_name' => "Pasien $i", 'whatsapp' => '08120000'.str_pad((string) $i, 4, '0', STR_PAD_LEFT), 'service' => 'Konsultasi', 'booking_date' => $d, 'booking_time' => '09:00', 'status' => $status]);
    }

    return [$selected->toDateString(), $rows];
}

it('ringkasan halaman booking: hitung status, minggu, dan bulan tetap benar', function () {
    [$selected, $rows] = seedBookingsForSummary();
    $this->withSession(adminSession());

    $response = $this->get('/admin/bookings?date='.$selected)->assertOk();
    $data = $response->viewData('statusCounts');

    expect($data)->toBe([
        'pending' => 4, 'confirmed' => 2, 'arrived' => 1, 'in_treatment' => 1, 'completed' => 3, 'cancelled' => 1, 'no_show' => 1,
    ]);

    // Hitung ulang hari-per-hari secara independen dari data seed.
    $byDate = collect($rows)->groupBy(0);
    $expectDay = fn (string $d) => [
        'count' => $byDate->get($d, collect())->count(),
        'pending' => $byDate->get($d, collect())->where(1, 'pending')->count(),
        'confirmed' => $byDate->get($d, collect())->where(1, 'confirmed')->count(),
        'completed' => $byDate->get($d, collect())->where(1, 'completed')->count(),
    ];

    foreach ($response->viewData('weekDays') as $day) {
        expect(collect($day)->only(['count', 'pending', 'confirmed', 'completed'])->all())->toBe($expectDay($day['date']));
    }

    $monthDays = $response->viewData('monthDays');
    expect($monthDays)->not->toBeEmpty();

    foreach ($monthDays as $day) {
        expect(collect($day)->only(['count', 'pending', 'confirmed', 'completed'])->all())->toBe($expectDay($day['date']));
    }

    $inMonth = $monthDays->where('is_current_month', true)->sum('count');
    expect($response->viewData('monthlyTotal'))->toBe($inMonth)
        ->and($response->viewData('weeklyTotal'))->toBe($response->viewData('weekDays')->sum('count'))
        ->and($response->viewData('selectedDateBookings'))->toHaveCount(4);
});

it('daftar booking dipaginasi 50 per halaman, tanpa baris dobel/hilang, dan filter terbawa ke tautan halaman', function () {
    $now = now();
    $rows = [];
    for ($i = 1; $i <= 120; $i++) {
        // created_at SAMA untuk semua baris: urutan harus tetap deterministik lewat id.
        $rows[] = ['full_name' => "Pasien $i", 'whatsapp' => '0811'.$i, 'service' => 'Konsultasi', 'booking_date' => ClinicTime::today(), 'booking_time' => '09:00', 'status' => $i % 2 ? 'pending' : 'confirmed', 'created_at' => $now, 'updated_at' => $now];
    }
    Booking::insert($rows);
    $this->withSession(adminSession());

    $seen = [];
    foreach ([1, 2, 3] as $page) {
        $paginator = $this->get("/admin/bookings?page={$page}")->assertOk()->viewData('bookings');
        expect($paginator->total())->toBe(120)->and($paginator->perPage())->toBe(50);
        $seen = array_merge($seen, $paginator->pluck('id')->all());
    }

    expect($seen)->toHaveCount(120)->and(array_unique($seen))->toHaveCount(120);

    $html = $this->get('/admin/bookings?status=pending&page=1')->assertOk()->getContent();
    expect($html)->toContain('Menampilkan 1–50 dari 60 data')->and($html)->toContain('status=pending');
});

it('daftar visit dan billing dipaginasi dan menampilkan navigasi halaman', function () {
    $patient = Patient::create(['full_name' => 'Pasien Daftar', 'whatsapp' => '081255550000']);
    $now = now();
    $visits = [];
    $bills = [];
    for ($i = 1; $i <= 55; $i++) {
        $visits[] = ['patient_id' => $patient->id, 'visit_date' => ClinicTime::today(), 'status' => 'scheduled', 'created_at' => $now, 'updated_at' => $now];
        $bills[] = ['patient_id' => $patient->id, 'invoice_number' => "INV-PG-$i", 'invoice_date' => ClinicTime::today(), 'amount' => 100000, 'subtotal_amount' => 100000, 'paid_amount' => 0, 'remaining_amount' => 100000, 'payment_status' => 'unpaid', 'created_at' => $now, 'updated_at' => $now];
    }
    Visit::insert($visits);
    Billing::insert($bills);
    $this->withSession(adminSession());

    $v = $this->get('/admin/visits')->assertOk();
    expect($v->viewData('visits')->total())->toBe(55)->and($v->viewData('visits')->count())->toBe(50);
    $v->assertSee('Menampilkan 1–50 dari 55 data');
    expect($this->get('/admin/visits?page=2')->viewData('visits')->count())->toBe(5);

    $b = $this->get('/admin/billings')->assertOk();
    expect($b->viewData('billings')->total())->toBe(55)->and($b->viewData('billings')->count())->toBe(50);
    $b->assertSee('Menampilkan 1–50 dari 55 data');

    // Angka ringkasan tetap dihitung dari SELURUH data, bukan hanya halaman ini.
    expect($b->viewData('totalTransactions'))->toBe(55)->and($b->viewData('unpaidCount'))->toBe(55);
});

it('halaman booking tidak lagi melakukan satu query per hari kalender (jumlah query kecil dan tetap)', function () {
    seedBookingsForSummary();
    $this->withSession(adminSession());

    \Illuminate\Support\Facades\DB::flushQueryLog();
    \Illuminate\Support\Facades\DB::enableQueryLog();
    $this->get('/admin/bookings')->assertOk();
    $queries = count(\Illuminate\Support\Facades\DB::getQueryLog());

    // Sebelumnya ±60 query (7 hari + ±35 hari grid kalender masing-masing satu query).
    expect($queries)->toBeLessThan(25);
});
