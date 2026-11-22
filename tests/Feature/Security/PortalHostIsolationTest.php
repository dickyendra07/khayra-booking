<?php

use App\Models\Billing;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\TherapistAvailability;
use App\Services\InvoiceWhatsappMessage;
use App\Support\ClinicTime;
use App\Support\PortalHosts;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

const H_ADMIN = 'app.khayraphysio.com';
const H_THERAPIST = 'physio.khayraphysio.com';
const H_PATIENT = 'patient.khayraphysio.com';

function on(string $host, string $path = '/'): string
{
    return "https://{$host}{$path}";
}

beforeEach(function () {
    // Lingkungan testing tidak menegakkan hostname secara default; di sini kita meniru production.
    config(['khayra_hosts.enforce' => true]);
});

// --------------------------------------------------------------------------------------------- hostname valid

it('hostname resmi membuka portalnya sendiri', function () {
    $this->get(on(H_ADMIN, '/admin/login'))->assertOk();
    $this->get(on(H_THERAPIST, '/therapist/login'))->assertOk();
    $this->get(on(H_PATIENT, '/patient'))->assertOk();
    $this->get(on(H_PATIENT, '/booking'))->assertOk();
});

it('route "/" mengarahkan tiap hostname resmi ke login portalnya dan memakai hostname yang sama', function () {
    $this->get(on(H_ADMIN))->assertRedirect(on(H_ADMIN, '/admin/login'));
    $this->get(on(H_THERAPIST))->assertRedirect(on(H_THERAPIST, '/therapist/login'));
    $this->get(on(H_PATIENT))->assertRedirect(on(H_PATIENT, '/patient'));
});

it('hostname dibandingkan tanpa peka huruf besar/kecil dan mengabaikan port', function () {
    $this->get('https://APP.KhayraPhysio.com/admin/login')->assertOk();
    $this->get('https://app.khayraphysio.com:8443/admin/login')->assertOk();
});

it('health check /up terbuka di hostname resmi maupun hostname lain (tidak memuat data)', function () {
    $this->get(on(H_ADMIN, '/up'))->assertOk();
    $this->get(on('evil.example', '/up'))->assertOk();
});

// --------------------------------------------------------------------------------------------- hostname tidak dikenal

it('menolak hostname tidak dikenal untuk semua area (GET)', function (string $host) {
    foreach (['/', '/admin/login', '/admin/bookings', '/owner/dashboard', '/therapist/login', '/patient', '/booking', '/tidak-ada'] as $path) {
        $this->get(on($host, $path))->assertNotFound();
    }
})->with([
    'domain asing' => 'evil.example',
    'sufiks mengecoh' => 'app.khayraphysio.com.evil.example',
    'prefiks mengecoh' => 'evil-app.khayraphysio.com',
    'subdomain lain' => 'www.khayraphysio.com',
    'alamat IP' => '203.0.113.10',
    'localhost' => 'localhost',
]);

it('menolak hostname tidak dikenal untuk POST (login, logout, booking) sebelum sesi/validasi berjalan', function () {
    foreach (['/admin/login', '/admin/logout', '/therapist/login', '/patient/login', '/booking'] as $path) {
        $this->post(on('evil.example', $path), ['email' => 'a@b.test', 'password' => 'x'])->assertNotFound();
    }
});

it('header Host yang tidak valid ditolak (400/404), bukan diproses', function (string $host) {
    // Request::create menimpa HTTP_HOST dari URL, jadi header Host dipasang manual lalu dijalankan lewat kernel.
    $request = Request::create('https://localhost/admin/login');
    $request->headers->set('Host', $host);
    $request->server->set('HTTP_HOST', $host);

    $response = app(Kernel::class)->handle($request);

    expect($response->getStatusCode())->toBeIn([400, 404]);
})->with([
    'userinfo' => 'app.khayraphysio.com@evil.example',
    'spasi' => 'app.khayraphysio.com evil.example',
    'garis miring' => 'app.khayraphysio.com/evil',
    'tidak dikenal' => 'evil.example',
]);

it('X-Forwarded-Host tidak pernah dipercaya, juga bila proxy dipercaya', function () {
    config(['trustedproxy.proxies' => ['127.0.0.1']]);
    $server = ['REMOTE_ADDR' => '127.0.0.1'];

    // Hostname asli tidak dikenal; X-Forwarded-Host berpura-pura jadi portal admin.
    $this->call('GET', on('evil.example', '/admin/login'), [], [], [], $server + ['HTTP_X_FORWARDED_HOST' => H_ADMIN])->assertNotFound();
    expect(request()->getHost())->toBe('evil.example');

    // Hostname asli pasien; X-Forwarded-Host tidak membuka portal admin.
    $this->call('GET', on(H_PATIENT, '/admin/login'), [], [], [], $server + ['HTTP_X_FORWARDED_HOST' => H_ADMIN])->assertNotFound();
    expect(request()->getHost())->toBe(H_PATIENT);

    // Sebaliknya: X-Forwarded-Host asing tidak memblokir hostname resmi (tidak dipakai sama sekali).
    $this->call('GET', on(H_ADMIN, '/admin/login'), [], [], [], $server + ['HTTP_X_FORWARDED_HOST' => 'evil.example'])->assertOk();
    expect(request()->getHost())->toBe(H_ADMIN);
});

// --------------------------------------------------------------------------------------------- lintas portal

dataset('cross_portal', [
    'admin diminta di hostname therapist' => [H_THERAPIST, '/admin/login'],
    'admin diminta di hostname pasien' => [H_PATIENT, '/admin/login'],
    'booking admin di hostname pasien' => [H_PATIENT, '/admin/bookings'],
    'owner di hostname therapist' => [H_THERAPIST, '/owner/dashboard'],
    'owner di hostname pasien' => [H_PATIENT, '/owner/finance'],
    'therapist di hostname admin' => [H_ADMIN, '/therapist/login'],
    'therapist di hostname pasien' => [H_PATIENT, '/therapist/dashboard'],
    'pasien di hostname admin' => [H_ADMIN, '/patient'],
    'pasien di hostname therapist' => [H_THERAPIST, '/patient/dashboard'],
    'booking publik di hostname admin' => [H_ADMIN, '/booking'],
    'booking publik di hostname therapist' => [H_THERAPIST, '/booking'],
    'lampiran admin di hostname therapist' => [H_THERAPIST, '/admin/medical-record-files/1'],
    'lampiran therapist di hostname admin' => [H_ADMIN, '/therapist/medical-record-files/1'],
    'lampiran therapist di hostname pasien' => [H_PATIENT, '/therapist/medical-record-files/1'],
]);

it('menolak akses lintas portal (GET)', function (string $host, string $path) {
    $this->get(on($host, $path))->assertNotFound();
})->with('cross_portal');

it('menolak akses lintas portal (POST dan method lain)', function (string $host, string $path) {
    $this->post(on($host, $path), ['email' => 'a@b.test', 'password' => 'x'])->assertNotFound();
    $this->put(on($host, $path))->assertNotFound();
    $this->delete(on($host, $path))->assertNotFound();
})->with('cross_portal');

it('penolakan lintas portal terjadi walau sesi sudah login (hostname bukan pengganti sesi, tetapi lapis tambahan)', function () {
    $this->withSession(adminSession());
    $this->get(on(H_PATIENT, '/admin/bookings'))->assertNotFound();
    $this->get(on(H_THERAPIST, '/admin/bookings'))->assertNotFound();

    $this->flushSession()->withSession(['therapist_logged_in' => true, 'therapist_id' => 1]);
    $this->get(on(H_ADMIN, '/therapist/dashboard'))->assertNotFound();

    $this->flushSession()->withSession(['patient_logged_in' => true, 'patient_id' => 1]);
    $this->get(on(H_ADMIN, '/patient/dashboard'))->assertNotFound();
});

it('path di-decode seperti router: encoding persen tidak melewati penolakan hostname', function () {
    $this->get(on(H_PATIENT, '/%61dmin/login'))->assertNotFound();
    $this->get(on(H_PATIENT, '/%6Fwner/dashboard'))->assertNotFound();
    $this->get(on(H_PATIENT, '/owner%2Fdashboard'))->assertNotFound();
    $this->get(on(H_ADMIN, '/%70atient'))->assertNotFound();
    $this->get(on(H_ADMIN, '/%74herapist/login'))->assertNotFound();
});

it('path di luar pemetaan area ditolak (fail-closed) di hostname resmi', function () {
    $this->get(on(H_ADMIN, '/storage/medical-record-supporting-data/x.pdf'))->assertNotFound();
    $this->get(on(H_PATIENT, '/rute-baru-yang-lupa-dipetakan'))->assertNotFound();
});

it('SEMUA route terdaftar berada di area terpetakan atau path any-host (audit agar route baru tidak terlupa)', function () {
    $areas = array_keys(config('khayra_hosts.areas'));
    $allowed = array_merge($areas, config('khayra_hosts.any_host_paths'), ['']);
    $unmapped = [];

    foreach (Route::getRoutes() as $route) {
        $segment = explode('/', trim($route->uri(), '/'))[0];

        if (! in_array($segment, $allowed, true)) {
            $unmapped[] = $route->methods()[0].' '.$route->uri();
        }
    }

    expect($unmapped)->toBe([]);

    foreach (config('khayra_hosts.areas') as $segment => $portal) {
        expect(PortalHosts::host($portal))->not->toBeNull("area {$segment} menunjuk portal {$portal} tanpa hostname");
    }
});

it('nama hostname tiga portal terisi dan berbeda', function () {
    $hosts = array_map(fn ($portal) => PortalHosts::host($portal), ['admin', 'therapist', 'patient']);

    expect($hosts)->toBe([H_ADMIN, H_THERAPIST, H_PATIENT])->and(array_unique($hosts))->toHaveCount(3);
});

// --------------------------------------------------------------------------------------------- penegakan otomatis

it('penegakan mati di lokal/testing dan otomatis hidup di production (config enforce = null)', function () {
    config(['khayra_hosts.enforce' => null]);

    expect(app()->environment('production'))->toBeFalse()->and(PortalHosts::enforcing())->toBeFalse();
    $this->get('https://localhost/admin/login')->assertOk();
    $this->get('https://localhost/')->assertOk();   // halaman welcome pengembangan

    $this->app['env'] = 'production';

    expect(PortalHosts::enforcing())->toBeTrue();
    $this->get('https://localhost/admin/login')->assertNotFound();
});

it('PORTAL_HOST_ENFORCE=false dapat mematikan penegakan secara eksplisit', function () {
    config(['khayra_hosts.enforce' => false]);
    $this->app['env'] = 'production';

    expect(PortalHosts::enforcing())->toBeFalse();
    $this->get('https://localhost/admin/login')->assertOk();
});

// --------------------------------------------------------------------------------------------- redirect autentikasi

it('redirect login tetap berada di hostname portal yang sama', function () {
    $this->get(on(H_ADMIN, '/admin/bookings'))->assertRedirect(on(H_ADMIN, '/admin/login'));
    $this->get(on(H_ADMIN, '/owner/dashboard'))->assertRedirect(on(H_ADMIN, '/admin/login'));
    $this->get(on(H_THERAPIST, '/therapist/dashboard'))->assertRedirect(on(H_THERAPIST, '/therapist/login'));
    $this->get(on(H_PATIENT, '/patient/dashboard'))->assertRedirect(on(H_PATIENT, '/patient'));
});

it('logout tiap portal hanya di hostname-nya dan kembali ke login hostname yang sama', function () {
    $this->post(on(H_ADMIN, '/admin/logout'))->assertRedirect(on(H_ADMIN, '/admin/login'));
    $this->post(on(H_THERAPIST, '/therapist/logout'))->assertRedirect(on(H_THERAPIST, '/therapist/login'));
    $this->post(on(H_PATIENT, '/patient/logout'))->assertRedirect(on(H_PATIENT, '/patient'));

    $this->post(on(H_PATIENT, '/admin/logout'))->assertNotFound();
    $this->post(on(H_ADMIN, '/patient/logout'))->assertNotFound();
});

it('RBAC dan guard sesi tetap berjalan di hostname yang benar (hostname bukan autentikasi)', function () {
    // Sesi pasien/therapist tidak membuka area admin walau hostname admin benar.
    $this->withSession(['patient_logged_in' => true, 'patient_id' => 1]);
    $this->get(on(H_ADMIN, '/admin/bookings'))->assertRedirect(on(H_ADMIN, '/admin/login'));

    $this->flushSession()->withSession(['therapist_logged_in' => true, 'therapist_id' => 1]);
    $this->get(on(H_ADMIN, '/admin/bookings'))->assertRedirect(on(H_ADMIN, '/admin/login'));
    $this->get(on(H_ADMIN, '/owner/dashboard'))->assertRedirect(on(H_ADMIN, '/admin/login'));

    // Sesi admin tidak membuka area therapist/pasien di hostname masing-masing.
    $this->flushSession()->withSession(adminSession());
    $this->get(on(H_THERAPIST, '/therapist/dashboard'))->assertRedirect(on(H_THERAPIST, '/therapist/login'));
    $this->get(on(H_PATIENT, '/patient/dashboard'))->assertRedirect(on(H_PATIENT, '/patient'));
});

// --------------------------------------------------------------------------------------------- Admin vs Owner

it('Admin ditolak 403 di /owner/* pada hostname admin; Owner diizinkan; tanpa login diarahkan ke login', function () {
    $this->withSession(adminSession());
    $this->get(on(H_ADMIN, '/owner/dashboard'))->assertForbidden();
    $this->get(on(H_ADMIN, '/owner/finance'))->assertForbidden();
    $this->get(on(H_ADMIN, '/admin/bookings'))->assertOk();

    $this->flushSession()->withSession(ownerSession());
    $this->get(on(H_ADMIN, '/owner/dashboard'))->assertOk();
    $this->get(on(H_ADMIN, '/admin/bookings'))->assertOk();
});

it('BYPASS DITUTUP: encoding persen pada path tidak melewati gerbang role Owner', function () {
    $this->withSession(adminSession());

    foreach (['/%6Fwner/dashboard', '/owner%2Fdashboard', '/%6fwner/finance', '/owner/%64ashboard', '//owner/dashboard'] as $path) {
        expect($this->get(on(H_ADMIN, $path))->getStatusCode())->toBeIn([403, 404], "admin membuka {$path}");
    }

    // Juga tanpa penegakan hostname (lokal/testing): gerbang role berdiri sendiri.
    config(['khayra_hosts.enforce' => false]);
    $this->get('/%6Fwner/dashboard')->assertForbidden();
    $this->get('/owner%2Fdashboard')->assertForbidden();
});

it('BYPASS DITUTUP: encoding persen pada path tidak melewati guard login terpusat', function () {
    config(['khayra_hosts.enforce' => false]);

    foreach (['/%61dmin/bookings', '/%61dmin/billings', '/%74herapist/dashboard', '/%70atient/dashboard', '/%6Fwner/dashboard'] as $path) {
        $response = $this->get($path);

        expect($response->getStatusCode())->toBe(302, $path);
        expect($response->headers->get('Location'))->toMatch('#/(admin/login|therapist/login|patient)$#');
    }
});

// --------------------------------------------------------------------------------------------- booking publik

it('booking publik hanya di hostname pasien: GET dan POST', function () {
    $therapist = Therapist::create(['full_name' => 'Fisio Uji', 'email' => 'f@uji.test', 'password' => Hash::make('x-uji-pass'), 'status' => 'active']);
    foreach (range(1, 7) as $day) {
        TherapistAvailability::create(['therapist_id' => $therapist->id, 'day_of_week' => $day, 'start_time' => '09:00', 'end_time' => '12:00', 'slot_duration_minutes' => 60, 'capacity' => 50, 'status' => 'active']);
    }

    $payload = [
        'full_name' => 'Calon Pasien', 'whatsapp' => '081288880001', 'service' => 'Konsultasi',
        'booking_date' => ClinicTime::today(), 'booking_time' => '09:00',
    ];

    $this->post(on(H_ADMIN, '/booking'), $payload)->assertNotFound();
    $this->post(on(H_THERAPIST, '/booking'), $payload)->assertNotFound();
    $this->post(on('evil.example', '/booking'), $payload)->assertNotFound();
    expect(Booking::count())->toBe(0);

    $this->post(on(H_PATIENT, '/booking'), $payload)->assertRedirect();
    expect(Booking::count())->toBe(1);
});

it('halaman booking publik tidak lagi menautkan ke area admin', function () {
    $html = $this->get(on(H_PATIENT, '/booking'))->assertOk()->getContent();

    expect($html)->not->toContain('/admin');
});

it('tautan lintas portal di sidebar admin memakai URL absolut ke hostname tujuan saat penegakan aktif', function () {
    $this->withSession(adminSession());
    $html = $this->get(on(H_ADMIN, '/admin/bookings'))->assertOk()->getContent();

    expect($html)
        ->toContain('href="https://patient.khayraphysio.com/booking"')
        ->toContain('href="https://patient.khayraphysio.com/patient"')
        ->toContain('href="https://physio.khayraphysio.com/therapist/login"')
        ->not->toContain('href="/booking"')
        ->not->toContain('href="/therapist/login"');
});

it('tautan lintas portal tetap relatif di lokal/testing (penegakan mati)', function () {
    config(['khayra_hosts.enforce' => false]);
    $this->withSession(adminSession());
    $html = $this->get('/admin/bookings')->assertOk()->getContent();

    expect($html)->toContain('href="/booking"')->and(PortalHosts::url('patient', '/booking'))->toBe('/booking');
});

// --------------------------------------------------------------------------------------------- URL pasien kanonis

function makeInvoiceFor(string $whatsapp = '0812-3456-7890'): Billing
{
    $patient = Patient::create(['full_name' => 'Siti Aminah', 'whatsapp' => $whatsapp]);

    return Billing::create([
        'patient_id' => $patient->id, 'invoice_number' => 'INV-HOST-1', 'invoice_date' => '2026-10-11',
        'amount' => 250000, 'paid_amount' => 0, 'remaining_amount' => 250000, 'payment_status' => 'unpaid',
    ]);
}

it('pesan WhatsApp invoice memakai URL portal pasien kanonis dari konfigurasi', function () {
    $message = app(InvoiceWhatsappMessage::class)->build(makeInvoiceFor());

    expect($message)->toContain('https://patient.khayraphysio.com/patient')->not->toContain('localhost');
});

it('URL kanonis tidak dipengaruhi Host atau X-Forwarded-Host permintaan', function () {
    $billing = makeInvoiceFor();

    foreach ([H_ADMIN, 'evil.example', 'localhost'] as $host) {
        $request = Request::create("https://{$host}/admin/billings/1/whatsapp", 'GET', [], [], [], ['HTTP_X_FORWARDED_HOST' => 'attacker.example', 'HTTP_X_FORWARDED_PROTO' => 'http']);
        app()->instance('request', $request);

        $message = app(InvoiceWhatsappMessage::class)->build($billing);

        expect($message)
            ->toContain('https://patient.khayraphysio.com/patient')
            ->not->toContain('evil.example')
            ->not->toContain('attacker.example')
            ->not->toContain('app.khayraphysio.com');
    }
});

it('halaman WhatsApp invoice di admin (hostname admin atau Host palsu) hanya memuat URL kanonis', function () {
    $billing = makeInvoiceFor();
    $this->withSession(adminSession());

    // Hostname admin sah.
    $html = $this->get(on(H_ADMIN, "/admin/billings/{$billing->id}/whatsapp"))->assertOk()->getContent();
    expect($html)->toContain('https://patient.khayraphysio.com/patient')->not->toContain(H_ADMIN.'/patient');

    // Tanpa penegakan (lokal) dengan Host palsu + X-Forwarded-Host: URL tetap kanonis.
    config(['khayra_hosts.enforce' => false]);
    $html = $this->call('GET', "https://evil.example/admin/billings/{$billing->id}/whatsapp", [], [], [], ['HTTP_X_FORWARDED_HOST' => 'attacker.example'])->assertOk()->getContent();
    expect($html)->toContain('https://patient.khayraphysio.com/patient')->not->toContain('evil.example/patient')->not->toContain('attacker.example');
});

it('URL portal pasien dapat diganti lewat konfigurasi dan tidak punya garis miring ganda', function () {
    config(['khayra_hosts.urls.patient' => 'https://pasien.contoh.test']);

    expect(PortalHosts::patientUrl('/patient'))->toBe('https://pasien.contoh.test/patient')
        ->and(PortalHosts::patientUrl('patient'))->toBe('https://pasien.contoh.test/patient')
        ->and(PortalHosts::patientUrl())->toBe('https://pasien.contoh.test');
});

it('pesan WhatsApp ke pasien tidak memakai url()/route()/asset()/request() (berbasis Host permintaan)', function () {
    $source = file_get_contents(base_path('app/Services/InvoiceWhatsappMessage.php'));

    expect($source)->not->toMatch('/(?<![A-Za-z_>:$])(url|route|asset|secure_url|secure_asset)\s*\(/')
        ->and($source)->not->toContain('request()')
        ->and($source)->toContain('PortalHosts::patientUrl(');
});

// --------------------------------------------------------------------------------------------- sesi & cookie

it('cookie sesi host-only (tanpa atribut Domain), HttpOnly, SameSite, dan Secure di HTTPS', function () {
    config(['khayra_auth.admin.email' => 'a@t.test', 'khayra_auth.admin.password' => 'pw-uji-lokal-123', 'khayra_auth.admin.password_hash' => null]);

    expect(config('session.domain'))->toBeNull()->and(config('session.http_only'))->toBeTrue()->and(config('session.same_site'))->toBe('lax');

    $response = $this->post(on(H_ADMIN, '/admin/login'), ['email' => 'a@t.test', 'password' => 'pw-uji-lokal-123']);
    $cookie = collect($response->headers->getCookies())->first(fn ($c) => $c->getName() === config('session.cookie'));

    expect($cookie)->not->toBeNull()
        ->and($cookie->getDomain())->toBeNull()
        ->and($cookie->isHttpOnly())->toBeTrue()
        ->and($cookie->isSecure())->toBeTrue()
        ->and($cookie->getSameSite())->toBe('lax');
});

it('login admin dari hostname lain ditolak sebelum kredensial diperiksa dan tidak membuat sesi', function () {
    config(['khayra_auth.admin.email' => 'a@t.test', 'khayra_auth.admin.password' => 'pw-uji-lokal-123', 'khayra_auth.admin.password_hash' => null]);

    foreach ([H_PATIENT, H_THERAPIST, 'evil.example'] as $host) {
        $response = $this->post(on($host, '/admin/login'), ['email' => 'a@t.test', 'password' => 'pw-uji-lokal-123']);

        $response->assertNotFound();
        expect(session('admin_logged_in'))->toBeNull()
            ->and(collect($response->headers->getCookies())->contains(fn ($c) => $c->getName() === config('session.cookie')))->toBeFalse();
    }
});

// --------------------------------------------------------------------------------------------- config cache & proxy

it('config trustedproxy membuang wildcard dan hanya meneruskan IP/CIDR spesifik', function () {
    $load = function (?string $value) {
        $previous = getenv('TRUSTED_PROXIES');
        $value === null ? putenv('TRUSTED_PROXIES') : putenv("TRUSTED_PROXIES={$value}");
        $_ENV['TRUSTED_PROXIES'] = $_SERVER['TRUSTED_PROXIES'] = $value;

        try {
            return (include base_path('config/trustedproxy.php'))['proxies'];
        } finally {
            $previous === false ? putenv('TRUSTED_PROXIES') : putenv("TRUSTED_PROXIES={$previous}");
            unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);
        }
    };

    expect($load(null))->toBeNull()
        ->and($load(''))->toBeNull()
        ->and($load('*'))->toBeNull()
        ->and($load('**'))->toBeNull()
        ->and($load('127.0.0.1'))->toBe(['127.0.0.1'])
        ->and($load(' 127.0.0.1 , 10.0.0.0/8 , * '))->toBe(['127.0.0.1', '10.0.0.0/8']);
});

it('tanpa TRUSTED_PROXIES tidak ada proxy yang dipercaya (IP klien tidak bisa dipalsukan lewat X-Forwarded-For)', function () {
    $this->get(on(H_ADMIN, '/admin/login'), ['X-Forwarded-For' => '198.51.100.7']);

    expect(request()->ip())->toBe('127.0.0.1');
});

it('proxy tepercaya spesifik: X-Forwarded-For dipakai, tetapi bukan dari alamat lain', function () {
    config(['trustedproxy.proxies' => ['127.0.0.1']]);
    $this->call('GET', '/admin/login', [], [], [], ['HTTP_HOST' => H_ADMIN, 'HTTPS' => 'on', 'REMOTE_ADDR' => '127.0.0.1', 'HTTP_X_FORWARDED_FOR' => '198.51.100.7']);
    expect(request()->ip())->toBe('198.51.100.7');

    $this->call('GET', '/admin/login', [], [], [], ['HTTP_HOST' => H_ADMIN, 'HTTPS' => 'on', 'REMOTE_ADDR' => '203.0.113.9', 'HTTP_X_FORWARDED_FOR' => '198.51.100.7']);
    expect(request()->ip())->toBe('203.0.113.9');
});

it('KOMPATIBEL config:cache: nilai env hanya dibaca saat cache dibuat, lalu middleware memakai nilai cache', function () {
    $dir = sys_get_temp_dir().'/khq-cfgcache-'.bin2hex(random_bytes(4));
    mkdir($dir);
    $cacheFile = "{$dir}/config.php";
    $script = "{$dir}/probe.php";

    file_put_contents($script, '<?php require getenv("KHQ_BASE")."/vendor/autoload.php"; $app = require getenv("KHQ_BASE")."/bootstrap/app.php"; $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap(); echo json_encode(["cached" => $app->configurationIsCached(), "admin" => config("khayra_hosts.hosts.admin"), "enforce" => config("khayra_hosts.enforce"), "patient" => config("khayra_hosts.urls.patient"), "proxies" => config("trustedproxy.proxies"), "enforcing" => App\\Support\\PortalHosts::enforcing()]);');

    $base = array_merge(getenv(), [
        'KHQ_BASE' => base_path(), 'APP_CONFIG_CACHE' => $cacheFile, 'APP_ENV' => 'production', 'APP_KEY' => (string) config('app.key'),
        'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:', 'APP_DEBUG' => 'false',
    ]);

    $run = function (array $command, array $env) {
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, base_path(), $env);
        $out = stream_get_contents($pipes[1]).stream_get_contents($pipes[2]);
        $code = proc_close($process);

        return [$code, $out];
    };

    try {
        // 1) cache dibuat dengan nilai env tertentu.
        [$code, $out] = $run([PHP_BINARY, 'artisan', 'config:cache'], $base + ['PORTAL_HOST_ADMIN' => 'cache-admin.example', 'PATIENT_PORTAL_URL' => 'https://pasien-cache.example/', 'TRUSTED_PROXIES' => '10.9.8.7,*', 'PORTAL_HOST_ENFORCE' => '']);
        expect($code)->toBe(0, $out);
        expect(file_exists($cacheFile))->toBeTrue();

        // 2) saat berjalan, env berbeda tidak lagi berpengaruh; nilai berasal dari cache.
        [$code, $out] = $run([PHP_BINARY, $script], array_merge($base, ['PORTAL_HOST_ADMIN' => 'berubah.example', 'PATIENT_PORTAL_URL' => 'https://berubah.example', 'TRUSTED_PROXIES' => '1.1.1.1', 'PORTAL_HOST_ENFORCE' => 'false']));
        $state = json_decode(substr($out, (int) strrpos($out, '{')), true);

        expect($code)->toBe(0, $out)
            ->and($state['cached'])->toBeTrue()
            ->and($state['admin'])->toBe('cache-admin.example')
            ->and($state['patient'])->toBe('https://pasien-cache.example')
            ->and($state['proxies'])->toBe(['10.9.8.7'])
            ->and($state['enforce'])->toBeNull()
            ->and($state['enforcing'])->toBeTrue();      // APP_ENV=production + enforce null
    } finally {
        @unlink($cacheFile);
        @unlink($script);
        @rmdir($dir);
    }
});

it('tidak ada pemanggilan env() di middleware, support, atau view baru (aman config:cache)', function () {
    foreach (['app/Http/Middleware/EnforcePortalHost.php', 'app/Support/PortalHosts.php', 'bootstrap/app.php'] as $file) {
        expect(file_get_contents(base_path($file)))->not->toMatch('/\benv\s*\(/', $file);
    }
});
