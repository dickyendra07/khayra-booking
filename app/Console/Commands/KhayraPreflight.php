<?php

namespace App\Console\Commands;

use App\Support\AdminCredentials;
use App\Support\InvoiceNumberIndex;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Pemeriksaan kesiapan lingkungan (staging / production). HANYA MEMBACA.
 * Tidak menampilkan nilai secret; hanya status ada/tidak.
 */
class KhayraPreflight extends Command
{
    protected $signature = 'khayra:preflight {--json : Keluaran JSON}';

    protected $description = 'Periksa kesiapan lingkungan: kredensial admin, cache throttle, sesi, database, migration, zona waktu (read-only).';

    /** @var array<int, array{status: string, check: string, detail: string}> */
    private array $results = [];

    public function handle(): int
    {
        $this->checkApplication();
        $this->checkAuthentication();
        $this->checkCacheForThrottling();
        $this->checkSession();
        $this->checkDatabase();
        $this->checkTimezone();
        $this->checkAdminFee();

        if ($this->option('json')) {
            $this->line(json_encode($this->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            foreach ($this->results as $row) {
                $label = ['PASS' => '<info>PASS</info>', 'WARN' => '<comment>WARN</comment>', 'FAIL' => '<error>FAIL</error>'][$row['status']];
                $this->line(sprintf('%s  %s — %s', $label, $row['check'], $row['detail']));
            }
        }

        $fails = collect($this->results)->where('status', 'FAIL')->count();
        $warns = collect($this->results)->where('status', 'WARN')->count();

        if (! $this->option('json')) {
            $this->newLine();
            $this->line("Ringkasan: {$fails} FAIL, {$warns} WARN.");
        }

        return $fails > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function add(string $status, string $check, string $detail): void
    {
        $this->results[] = ['status' => $status, 'check' => $check, 'detail' => $detail];
    }

    private function checkApplication(): void
    {
        $env = config('app.env');
        $this->add('PASS', 'APP_ENV', (string) $env);

        $this->add(filled(config('app.key')) ? 'PASS' : 'FAIL', 'APP_KEY', filled(config('app.key')) ? 'terisi' : 'KOSONG (sesi/enkripsi tidak aman)');

        if ($env === 'production' && config('app.debug')) {
            $this->add('FAIL', 'APP_DEBUG', 'aktif di production (membocorkan detail error)');
        } else {
            $this->add('PASS', 'APP_DEBUG', config('app.debug') ? 'aktif (bukan production)' : 'nonaktif');
        }

        $this->add('PASS', 'config:cache', app()->configurationIsCached() ? 'aktif (nilai env dibaca dari cache config)' : 'tidak aktif');
    }

    private function checkAuthentication(): void
    {
        if (! AdminCredentials::isConfigured()) {
            $this->add('FAIL', 'Kredensial admin', 'ADMIN_EMAIL / ADMIN_PASSWORD(_HASH) belum terbaca dari config. Login admin akan SELALU ditolak. '
                .'Jika config:cache aktif, jalankan ulang config:cache setelah mengisi .env.');

            return;
        }

        $this->add('PASS', 'Kredensial admin', 'terkonfigurasi (nilai tidak ditampilkan)');

        if (blank(config('khayra_auth.admin.password_hash'))) {
            $this->add('WARN', 'Format password admin', 'memakai ADMIN_PASSWORD (teks biasa); disarankan ADMIN_PASSWORD_HASH (bcrypt)');
        } else {
            $this->add('PASS', 'Format password admin', 'ADMIN_PASSWORD_HASH (bcrypt)');
        }

        if (mb_strtolower((string) config('khayra_auth.admin.email')) === 'admin@khayraphysio.com') {
            $this->add('WARN', 'Email admin', 'masih memakai email default lama; disarankan diganti');
        }

        // Pastikan fungsi pembaca env tidak dipanggil di luar config (rusak saat config:cache).
        $offenders = [];

        foreach ([base_path('routes'), app_path()] as $dir) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file) {
                if ($file->isFile() && $file->getExtension() === 'php' && preg_match('/(?<![A-Za-z_>:])env\(/', file_get_contents($file->getPathname()))) {
                    $offenders[] = str_replace(base_path().'/', '', $file->getPathname());
                }
            }
        }

        $this->add($offenders === [] ? 'PASS' : 'FAIL', 'Pemanggilan fungsi env di routes/app', $offenders === [] ? 'tidak ada (aman untuk config:cache)' : implode(', ', $offenders));
    }

    private function checkCacheForThrottling(): void
    {
        $store = config('cache.default');

        if (in_array($store, ['array', 'null'], true)) {
            $this->add('FAIL', 'Cache untuk throttling login', "driver '{$store}' tidak menyimpan antar request; pembatasan login TIDAK efektif");

            return;
        }

        try {
            $key = 'khayra-preflight:'.bin2hex(random_bytes(4));
            Cache::put($key, 'ok', 30);
            $works = Cache::get($key) === 'ok';
            Cache::forget($key);

            $this->add($works ? 'PASS' : 'FAIL', 'Cache untuk throttling login', $works ? "driver '{$store}': tulis/baca berhasil" : "driver '{$store}': nilai tidak terbaca kembali");
        } catch (Throwable $e) {
            $this->add('FAIL', 'Cache untuk throttling login', "driver '{$store}' error: ".class_basename($e).' (tabel cache sudah dimigrasi?)');
        }
    }

    private function checkSession(): void
    {
        $driver = config('session.driver');
        $this->add('PASS', 'Session driver', (string) $driver);

        if ($driver === 'database') {
            try {
                $exists = Schema::hasTable(config('session.table', 'sessions'));
                $this->add($exists ? 'PASS' : 'FAIL', 'Tabel sessions', $exists ? 'ada' : 'TIDAK ADA');
            } catch (Throwable $e) {
                $this->add('FAIL', 'Tabel sessions', 'tidak dapat diperiksa (database tidak terhubung)');
            }
        }

        if (config('app.env') === 'production') {
            $this->add(config('session.secure') ? 'PASS' : 'WARN', 'SESSION_SECURE_COOKIE', config('session.secure') ? 'aktif' : 'nonaktif; aktifkan bila situs HTTPS');
            $this->add(config('session.http_only') ? 'PASS' : 'WARN', 'Session http_only', config('session.http_only') ? 'aktif' : 'nonaktif');
        }
    }

    private function checkDatabase(): void
    {
        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();
            $version = $driver === 'sqlite' ? $connection->selectOne('select sqlite_version() as v')->v : $connection->selectOne('select version() as v')->v;
            $this->add('PASS', 'Database', "{$driver} {$version}");
        } catch (Throwable $e) {
            $this->add('FAIL', 'Database', 'tidak dapat terhubung: '.class_basename($e));

            return;
        }

        $pending = $this->pendingMigrations();
        $this->add($pending === 0 ? 'PASS' : 'WARN', 'Migration', $pending === 0 ? 'semua sudah dijalankan' : "{$pending} migration belum dijalankan (jalankan migrate --pretend dulu)");

        foreach (['billing_audit_logs', 'invoice_sequences', 'billing_payments', 'rooms'] as $table) {
            $this->add(Schema::hasTable($table) ? 'PASS' : 'WARN', "Tabel {$table}", Schema::hasTable($table) ? 'ada' : 'belum ada (migration P1 belum dijalankan)');
        }

        if (Schema::hasTable('billings') && Schema::hasColumn('billings', 'payment_model')) {
            $legacy = DB::table('billings')->where('payment_model', 'legacy')->count();
            $ledger = DB::table('billings')->where('payment_model', 'ledger')->count();
            $this->add('PASS', 'Model pembayaran invoice', "legacy: {$legacy} (tidak dihitung ulang), ledger: {$ledger}");
        }

        if (Schema::hasTable('billings')) {
            if (InvoiceNumberIndex::exists()) {
                $this->add('PASS', 'Unique index invoice_number', 'terpasang');
            } else {
                $duplicates = count(InvoiceNumberIndex::duplicates());
                $this->add('WARN', 'Unique index invoice_number', $duplicates > 0
                    ? "belum terpasang; {$duplicates} nomor duplikat harus diselesaikan dulu (billing:audit-integrity)"
                    : 'belum terpasang; jalankan billing:ensure-invoice-unique-index');
            }
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            $database = DB::connection()->getDatabaseName();
            $nonInnoDb = DB::select('select table_name as t, engine as e from information_schema.tables where table_schema = ? and engine <> ? and table_type = ?', [$database, 'InnoDB', 'BASE TABLE']);
            $this->add($nonInnoDb === [] ? 'PASS' : 'FAIL', 'Engine tabel', $nonInnoDb === [] ? 'semua InnoDB (transaction & row lock berfungsi)' : 'bukan InnoDB: '.implode(', ', array_map(fn ($r) => $r->t.'='.$r->e, $nonInnoDb)));
        } else {
            $this->add('WARN', 'Row locking', 'driver '.DB::connection()->getDriverName().' tidak mendukung lockForUpdate seperti MySQL; uji konkurensi hanya valid di MySQL/MariaDB');
        }
    }

    private function checkAdminFee(): void
    {
        // Biaya administrasi pasien baru hanya menyala bila diaktifkan eksplisit lewat environment (default mati).
        $enabled = (bool) config('clinic.new_patient_admin_fee.enabled');
        $this->add('PASS', 'Biaya administrasi pasien baru', $enabled
            ? 'AKTIF (NEW_PATIENT_ADMIN_FEE_ENABLED=true): registrasi pasien baru menerbitkan tagihan Rp'.number_format((float) config('clinic.new_patient_admin_fee.amount'), 0, ',', '.')
            : 'NONAKTIF (default; set NEW_PATIENT_ADMIN_FEE_ENABLED=true hanya setelah keputusan sadar untuk lingkungan ini)');
    }

    private function checkTimezone(): void
    {
        $tz = config('app.timezone');
        $clinicTz = config('clinic.timezone');

        // Penyimpanan tetap UTC (tidak menggeser timestamp historis); tanggal bisnis memakai zona waktu klinik.
        $this->add($tz === 'UTC' ? 'PASS' : 'WARN', 'Zona waktu penyimpanan', "app.timezone = {$tz}".($tz === 'UTC' ? ' (benar: timestamp database tetap UTC)' : ' (tidak UTC: timestamp lama akan terbaca bergeser)'));
        $this->add($clinicTz ? 'PASS' : 'FAIL', 'Zona waktu operasional klinik', 'clinic.timezone = '.($clinicTz ?: 'KOSONG').' (dipakai untuk tanggal invoice, pembayaran, closing, nomor invoice)');
    }

    private function pendingMigrations(): int
    {
        try {
            $repository = app('migration.repository');
            $files = app('migrator')->getMigrationFiles(app('migrator')->paths() + [database_path('migrations')]);
            $ran = $repository->repositoryExists() ? $repository->getRan() : [];

            return count(array_diff(array_keys($files), $ran));
        } catch (Throwable) {
            return -1;
        }
    }
}
