<?php

use App\Models\Booking;
use App\Models\Therapist;
use App\Models\TherapistAvailability;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function bookingPayload(string $date, array $overrides = []): array
{
    return array_merge([
        'full_name' => 'Calon Pasien', 'whatsapp' => '081277770001', 'service' => 'Konsultasi',
        'booking_date' => $date, 'booking_time' => '09:00',
    ], $overrides);
}

beforeEach(function () {
    $therapist = Therapist::create(['full_name' => 'Fisio Uji', 'email' => 'f@uji.test', 'password' => Hash::make('x-uji-pass'), 'status' => 'active']);

    // Jadwal aktif tiap hari supaya kapasitas slot 09:00 tersedia pada tanggal uji mana pun.
    foreach (range(1, 7) as $day) {
        TherapistAvailability::create(['therapist_id' => $therapist->id, 'day_of_week' => $day, 'start_time' => '09:00', 'end_time' => '12:00', 'slot_duration_minutes' => 60, 'capacity' => 50, 'status' => 'active']);
    }
});

it('menolak booking publik untuk tanggal yang sudah lewat (zona waktu klinik)', function () {
    $yesterday = ClinicTime::now()->subDay()->toDateString();

    $this->post('/booking', bookingPayload($yesterday))->assertSessionHasErrors('booking_date');

    expect(Booking::count())->toBe(0);
});

it('tetap menerima booking publik untuk hari ini dan tanggal mendatang', function () {
    $this->post('/booking', bookingPayload(ClinicTime::today()))->assertSessionHasNoErrors();
    $this->post('/booking', bookingPayload(ClinicTime::now()->addDays(3)->toDateString(), ['whatsapp' => '081277770002']))->assertSessionHasNoErrors();

    expect(Booking::count())->toBe(2);
});

it('membatasi POST booking publik per IP (429 setelah 10 permintaan per menit)', function () {
    $date = ClinicTime::now()->addDay()->toDateString();

    foreach (range(1, 10) as $i) {
        $this->post('/booking', bookingPayload($date, ['whatsapp' => '08127777'.str_pad((string) $i, 4, '0', STR_PAD_LEFT)]))->assertStatus(302);
    }

    $this->post('/booking', bookingPayload($date, ['whatsapp' => '081277779999']))->assertStatus(429);
});
