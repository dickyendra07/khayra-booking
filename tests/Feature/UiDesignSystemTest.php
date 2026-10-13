<?php

use App\Models\Billing;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Therapist;
use App\Models\Visit;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * Sprint UI: design system tombol & konsistensi tampilan (hanya lapisan presentasi).
 * Tes ini memastikan (1) setiap halaman utama tetap dapat dirender dan memuat stylesheet bersama,
 * (2) tidak ada tombol aksi baru yang lolos dari design system, (3) label PIC fisioterapis tampil.
 */
beforeEach(function () {
    $this->patient = Patient::create(['full_name' => 'Pasien UI', 'whatsapp' => '081200000001', 'birth_date' => '1990-01-01']);
    $this->therapist = Therapist::create(['full_name' => 'Fisio PIC Uji', 'email' => 'pic@uji.test', 'password' => Hash::make('x-uji-pass'), 'status' => 'active']);
    $this->booking = Booking::create([
        'therapist_id' => $this->therapist->id, 'room_name' => 'VIP 1', 'patient_id' => $this->patient->id, 'full_name' => 'Pasien UI',
        'whatsapp' => '081200000001', 'service' => 'Konsultasi', 'booking_date' => ClinicTime::today(), 'booking_time' => '09:00', 'status' => 'confirmed',
    ]);
    $this->visit = Visit::create([
        'patient_id' => $this->patient->id, 'booking_id' => $this->booking->id, 'therapist_id' => $this->therapist->id, 'therapist' => 'Fisio PIC Uji',
        'room_name' => 'VIP 1', 'visit_date' => ClinicTime::today(), 'status' => 'scheduled',
    ]);
    $this->billing = Billing::create([
        'patient_id' => $this->patient->id, 'visit_id' => $this->visit->id, 'invoice_number' => 'INV-UI-0001', 'invoice_date' => ClinicTime::today(),
        'amount' => 100000, 'subtotal_amount' => 100000, 'remaining_amount' => 100000, 'paid_amount' => 0, 'payment_status' => 'unpaid', 'payment_model' => 'full',
    ]);
});

function uiAdminPages($test): array
{
    return [
        '/admin/dashboard', '/admin/owner-dashboard', '/admin/owner-finance', '/admin/reports', '/admin/bookings', '/admin/bookings/calendar',
        '/admin/bookings/create', "/admin/bookings/{$test->booking->id}", "/admin/bookings/{$test->booking->id}/edit", '/admin/patients', '/admin/patients/create',
        "/admin/patients/{$test->patient->id}", "/admin/patients/{$test->patient->id}/edit", "/admin/patients/{$test->patient->id}/informed-consent",
        '/admin/visits', '/admin/visits/create', "/admin/visits/{$test->visit->id}/edit", '/admin/therapists', '/admin/therapists/create',
        "/admin/therapists/{$test->therapist->id}/edit", '/admin/therapist-availabilities', '/admin/staff-leaves', '/admin/billings', '/admin/billings/create',
        "/admin/billings/{$test->billing->id}", '/admin/cashier', '/admin/promos', '/admin/promos/create', '/admin/services', '/admin/rooms',
        '/admin/registration-fees', '/admin/package-treatments', '/admin/consent-archive', '/admin/documents', '/admin/exercise-library',
        '/admin/referral-letters', '/admin/inventory', '/admin/inventory/create', '/admin/inventory/stock-movements',
        '/admin/inventory/stock-opname', '/admin/inventory/monthly-summary', '/admin/inventory/import', '/admin/satu-sehat-readiness',
        '/admin/reports/revenue', '/admin/reports/monthly-clinic', '/admin/reports/inventory', '/admin/reports/therapist-performance',
        '/admin/rest-letter/create',
    ];
}

it('semua halaman admin utama tetap dirender dan memuat stylesheet design system bersama', function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);

    $problems = [];

    foreach (uiAdminPages($this) as $url) {
        $response = $this->get($url);

        if ($response->status() !== 200 || ! str_contains($response->getContent(), 'css/khayra-ui.css')) {
            $problems[] = $url.' => HTTP '.$response->status();
        }
    }

    expect($problems)->toBe([]);
});

it('halaman therapist, pasien, booking publik, dan login memuat stylesheet design system', function () {
    $this->get('/booking')->assertOk()->assertSee('css/khayra-ui.css', false);
    $this->get('/admin/login')->assertOk()->assertSee('css/khayra-ui.css', false);
    $this->get('/therapist/login')->assertOk()->assertSee('css/khayra-ui.css', false);
    $this->get('/patient')->assertOk()->assertSee('css/khayra-ui.css', false);

    $this->flushSession()->withSession(['therapist_logged_in' => true, 'therapist_id' => $this->therapist->id, 'therapist_name' => 'Fisio PIC Uji']);
    $this->get('/therapist/dashboard')->assertOk()->assertSee('css/khayra-ui.css', false)->assertSee('kx-btn', false);

    $this->flushSession()->withSession(['patient_logged_in' => true, 'patient_id' => $this->patient->id]);
    $this->get('/patient/dashboard')->assertOk()->assertSee('css/khayra-ui.css', false);
});

it('stylesheet design system berisi token dan semua varian tombol standar', function () {
    $css = File::get(public_path('css/khayra-ui.css'));

    foreach (['--kx-btn-h-sm', '--kx-btn-h-md', '--kx-btn-h-lg', '--kx-btn-radius-md', '--kx-btn-focus', '--kx-btn-disabled-opacity',
        '.kx-btn--primary', '.kx-btn--secondary', '.kx-btn--outline', '.kx-btn--danger', '.kx-btn--icon', '.kx-btn.is-loading', '.kx-btn:disabled'] as $needle) {
        expect($css)->toContain($needle);
    }
});

it('lint: setiap tombol aksi di Blade memakai kelas kx-btn (kecuali tab dan dokumen cetak consent)', function () {
    // Pengecualian disengaja: tab/tombol pemilih bukan tombol aksi; cetak consent dibekukan oleh golden file v1.
    $allowedClasses = ['mr-tab-button', 'anatomy-side-tab'];
    $allowedFiles = ['admin-informed-consent-print.blade.php'];
    $offenders = [];

    foreach (File::allFiles(resource_path('views')) as $file) {
        if (in_array($file->getFilename(), $allowedFiles, true)) {
            continue;
        }

        preg_match_all('/<(button|input)\b((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>/s', $file->getContents(), $matches, PREG_SET_ORDER);

        foreach ($matches as [$tag, $name, $attrs]) {
            if ($name === 'input' && ! preg_match('/type=["\'](submit|button)/', $attrs)) {
                continue;
            }
            if (str_contains($attrs, 'kx-btn')) {
                continue;
            }
            preg_match('/class="([^"]*)"/', $attrs, $cls);
            if (array_intersect(preg_split('/\s+/', $cls[1] ?? ''), $allowedClasses)) {
                continue;
            }
            $offenders[] = $file->getFilename().' => '.substr($tag, 0, 80);
        }
    }

    expect($offenders)->toBe([]);
});

it('lint: tidak ada lagi definisi visual tombol legacy (warna/padding/radius) di <style> halaman', function () {
    $legacy = ['btn-primary', 'btn-soft', 'submit-btn', 'mini-btn', 'ghost-link', 'primary-link', 'action-link'];
    $offenders = [];

    foreach (File::allFiles(resource_path('views')) as $file) {
        if ($file->getFilename() === 'admin-informed-consent-print.blade.php') {
            continue;
        }
        preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $file->getContents(), $styles);
        foreach ($styles[1] as $css) {
            foreach ($legacy as $class) {
                if (preg_match('/(^|[\s,}])\.'.preg_quote($class, '/').'\s*\{[^}]*(background|border-radius|padding)\s*:/m', $css)) {
                    $offenders[] = $file->getFilename().' => .'.$class;
                }
            }
        }
    }

    // Pengecualian: admin-inventory memakai .action-link sebagai teks kartu (bukan tombol).
    $offenders = array_values(array_filter($offenders, fn ($o) => ! str_contains($o, 'admin-inventory.blade.php => .action-link')));

    expect($offenders)->toBe([]);
});

it('PIC fisioterapis tampil di daftar booking, detail booking, daftar visit, dan jadwal terapis', function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);

    $this->get('/admin/bookings')->assertOk()->assertSee('PIC Fisioterapis')->assertSee('Fisio PIC Uji');
    $this->get("/admin/bookings/{$this->booking->id}")->assertOk()->assertSee('PIC (Fisioterapis)')->assertSee('Fisio PIC Uji');
    $this->get('/admin/visits')->assertOk()->assertSee('PIC Fisioterapis')->assertSee('Fisio PIC Uji');
    $this->get('/admin/bookings/calendar')->assertOk()->assertSee('PIC · Fisioterapis', false);

    $this->booking->update(['therapist_id' => null]);
    $this->get('/admin/bookings')->assertOk()->assertSee('Belum ditentukan');
});

it('master ruangan: satuan tarif standar Per sesi; satuan lama tetap tampil bila sudah tersimpan', function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);

    $legacy = Room::findByName('VIP 2');
    $legacy->update(['is_billable' => true, 'rate' => 80000, 'rate_unit' => 'per_hour']);

    $html = $this->get('/admin/rooms')->assertOk()->assertSee('dihitung per sesi')->assertSee('tidak terkena diskon')->getContent();

    expect($html)->toContain('>Per sesi<')
        ->and($html)->toContain('Per jam (satuan lama)');

    // Backend tidak diubah: form tetap mengirim field yang sama dan menolak billable tanpa satuan.
    $room = Room::findByName('VIP 1');
    $this->post("/admin/rooms/{$room->id}/update", ['status' => 'active', 'is_billable' => 1, 'rate' => 50000])->assertSessionHasErrors('rate');
    $this->post("/admin/rooms/{$room->id}/update", ['status' => 'active', 'is_billable' => 1, 'rate' => 50000, 'rate_unit' => 'per_session'])->assertRedirect('/admin/rooms');
    expect($room->fresh()->rate_unit)->toBe('per_session');
});

it('form consent: petunjuk wali/perwakilan dan identitas fisioterapis tampil, field tidak berubah', function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);

    $this->get("/admin/patients/{$this->patient->id}/informed-consent")->assertOk()
        ->assertSee('name="representative_name"', false)->assertSee('name="relationship_to_patient"', false)
        ->assertSee('name="physiotherapy_name"', false)->assertSee('Isi hanya bila persetujuan diberikan oleh wali')
        ->assertSee('Identitas fisioterapis pelaksana tetap dicatat');
});

it('PIC mengikuti therapist_id masing-masing booking (bukan terapis lain) dan jadwal existing tidak berubah', function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);

    $other = Therapist::create(['full_name' => 'Fisio Lain Uji', 'email' => 'lain@uji.test', 'password' => Hash::make('x-uji-pass'), 'status' => 'active']);
    $second = Booking::create([
        'therapist_id' => $other->id, 'room_name' => 'VIP 2', 'patient_id' => $this->patient->id, 'full_name' => 'Pasien Kedua',
        'whatsapp' => '081200000002', 'service' => 'Konsultasi', 'booking_date' => ClinicTime::today(), 'booking_time' => '10:00', 'status' => 'pending',
    ]);

    $this->get("/admin/bookings/{$this->booking->id}")->assertOk()->assertSee('Fisio PIC Uji')->assertDontSee('Fisio Lain Uji');
    $this->get("/admin/bookings/{$second->id}")->assertOk()->assertSee('Fisio Lain Uji')->assertDontSee('Fisio PIC Uji');

    \App\Models\TherapistAvailability::create(['therapist_id' => $other->id, 'day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '15:00', 'slot_duration_minutes' => 60, 'capacity' => 1, 'status' => 'active']);
    $before = \App\Models\TherapistAvailability::count();
    $this->get('/admin/therapist-availabilities')->assertOk()->assertSee('PIC Fisioterapis')->assertSee('Fisio Lain Uji')->assertSee('Jam kerja: 09:00 - 15:00');
    expect(\App\Models\TherapistAvailability::count())->toBe($before)
        ->and($this->booking->fresh()->therapist_id)->toBe($this->therapist->id)
        ->and($second->fresh()->therapist_id)->toBe($other->id);
});

it('master ruangan: tidak ada tarif/satuan yang dikarang; seed tetap non-billable dan form tidak memilih satuan sendiri', function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);

    expect(Room::where('is_billable', true)->count())->toBe(0)
        ->and(Room::whereNotNull('rate')->count())->toBe(0)
        ->and(Room::whereNotNull('rate_unit')->count())->toBe(0);

    $html = $this->get('/admin/rooms')->assertOk()->getContent();
    expect($html)->not->toContain('Rp 150.000')
        ->and($html)->not->toContain('selected>Per sesi')
        ->and($html)->toContain('data-kx-fill-unit');

    // Menyimpan ruangan non-billable tanpa mengubah apa pun tidak menciptakan tarif/satuan.
    $room = Room::findByName('VIP 1');
    $this->post("/admin/rooms/{$room->id}/update", ['status' => 'active', 'is_billable' => 0, 'notes' => 'cek'])->assertRedirect('/admin/rooms');
    expect($room->fresh()->rate)->toBeNull()->and($room->fresh()->rate_unit)->toBeNull()->and($room->fresh()->is_billable)->toBeFalse();
});
