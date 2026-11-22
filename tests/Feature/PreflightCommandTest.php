<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

it('preflight gagal bila kredensial admin belum dikonfigurasi dan tidak menampilkan secret', function () {
    config(['khayra_auth.admin.email' => null, 'khayra_auth.admin.password' => null, 'khayra_auth.admin.password_hash' => null]);

    $this->artisan('khayra:preflight')
        ->expectsOutputToContain('Kredensial admin')
        ->assertFailed();
});

it('preflight lulus dengan konfigurasi benar dan tidak pernah mencetak nilai kredensial', function () {
    config([
        'khayra_auth.admin.email' => 'admin@uji.test', 'khayra_auth.admin.password' => 'RAHASIA-UJI-123', 'khayra_auth.admin.password_hash' => null,
        'cache.default' => 'file',
    ]);

    $exit = Artisan::call('khayra:preflight', ['--json' => true]);
    $output = Artisan::output();
    $rows = collect(json_decode($output, true));

    expect($exit)->toBe(0)
        ->and($output)->not->toContain('RAHASIA-UJI-123')->not->toContain('admin@uji.test')
        ->and($rows->firstWhere('check', 'Kredensial admin')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'Cache untuk throttling login')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'Pemanggilan fungsi env di routes/app')['status'])->toBe('PASS');
});

it('preflight menandai cache array sebagai tidak efektif untuk throttling', function () {
    config([
        'khayra_auth.admin.email' => 'admin@uji.test', 'khayra_auth.admin.password' => 'x-uji', 'cache.default' => 'array',
    ]);

    $this->artisan('khayra:preflight')->expectsOutputToContain('TIDAK efektif')->assertFailed();
});

it('preflight memeriksa zona waktu penyimpanan UTC + zona operasional klinik dan memberi peringatan format password teks biasa', function () {
    config(['khayra_auth.admin.email' => 'admin@uji.test', 'khayra_auth.admin.password' => 'x-uji', 'cache.default' => 'file']);

    Artisan::call('khayra:preflight', ['--json' => true]);
    $rows = collect(json_decode(Artisan::output(), true));

    expect($rows->firstWhere('check', 'Zona waktu penyimpanan')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'Zona waktu operasional klinik')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'Format password admin')['status'])->toBe('WARN');
});

// ======================= GERBANG SKEMA RILIS =======================

use App\Models\Patient;
use App\Support\SchemaGate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function pfConfig(): void
{
    config(['khayra_auth.admin.email' => 'admin@uji.test', 'khayra_auth.admin.password_hash' => password_hash('x-uji', PASSWORD_BCRYPT, ['cost' => 4]), 'khayra_auth.admin.password' => null, 'cache.default' => 'file']);
}

function pfRows(array $options = []): array
{
    $exit = Artisan::call('khayra:preflight', array_merge(['--json' => true], $options));

    return [$exit, collect(json_decode(Artisan::output(), true))];
}

function pfRow($rows, string $fragment): array
{
    return $rows->first(fn ($row) => str_contains($row['check'], $fragment)) ?? [];
}

it('GATE: database termigrasi lengkap — seluruh kelompok skema PASS dan gerbang lulus', function () {
    pfConfig();
    [, $rows] = pfRows();

    $schemaRows = $rows->filter(fn ($row) => str_starts_with($row['check'], 'Skema: '));

    expect($schemaRows)->toHaveCount(count(SchemaGate::requirements()))
        ->and($schemaRows->pluck('status')->unique()->all())->toBe(['PASS'])
        ->and(pfRow($rows, 'Gerbang skema rilis')['status'])->toBe('PASS')
        ->and(pfRow($rows, 'UNIQUE INDEX billings.invoice_number')['status'])->toBe('PASS')
        ->and(pfRow($rows, 'pending_registration_fees')['status'])->toBe('PASS')
        ->and(pfRow($rows, 'finance_period_locks')['status'])->toBe('PASS')
        ->and(pfRow($rows, 'billing_payments.idempotency_key')['status'])->toBe('PASS')
        ->and(pfRow($rows, 'patients.registration_token')['status'])->toBe('PASS');
});

it('GATE: setiap migration yang dirujuk gerbang ada di repo dan kolom/index-nya benar-benar dibuat oleh migration itu', function () {
    foreach (SchemaGate::requirements() as $requirement) {
        expect(file_exists(database_path('migrations/'.$requirement['migration'].'.php')))->toBeTrue($requirement['migration']);
        expect(SchemaGate::missing($requirement))->toBe([], $requirement['label']);
        expect(SchemaGate::ranMigrations())->toContain($requirement['migration']);
    }
});

it('GATE: unique index invoice_number hilang pada database yang sudah dimigrasi = FAIL dan exit gagal (tanpa memperbaiki apa pun)', function () {
    pfConfig();
    Schema::table('billings', fn ($table) => $table->dropUnique('billings_invoice_number_unique'));
    $indexesBefore = Schema::getIndexes('billings');

    [$exit, $rows] = pfRows();
    $row = pfRow($rows, 'UNIQUE INDEX billings.invoice_number');

    expect($exit)->toBe(1)
        ->and($row['status'])->toBe('FAIL')->and($row['detail'])->toContain('TIDAK LENGKAP')->toContain('unique index billings(invoice_number)')
        ->and(pfRow($rows, 'Gerbang skema rilis')['status'])->toBe('FAIL')
        ->and(Schema::getIndexes('billings'))->toBe($indexesBefore);                 // preflight tidak memasang index
})->skip(fn () => config('database.default') !== 'sqlite', 'Mengubah skema: DDL MySQL commit implisit dan tidak ter-rollback oleh RefreshDatabase; jalur ini diverifikasi manual di MySQL (lihat docs/09).');

it('GATE: duplikat nomor invoice dilaporkan sebagai jumlah (bukan nomornya) dan tidak diperbaiki otomatis', function () {
    pfConfig();
    Schema::table('billings', fn ($table) => $table->dropUnique('billings_invoice_number_unique'));
    $patient = Patient::create(['full_name' => 'Pasien Rahasia Preflight', 'whatsapp' => '081299998888']);

    foreach ([1, 2, 3] as $i) {
        DB::table('billings')->insert(['patient_id' => $patient->id, 'invoice_number' => 'INV-DUP-RAHASIA', 'invoice_date' => '2026-10-01', 'amount' => 1000 * $i, 'created_at' => now(), 'updated_at' => now()]);
    }

    Artisan::call('khayra:preflight', ['--json' => true]);
    $output = Artisan::output();
    $row = collect(json_decode($output, true))->first(fn ($r) => str_contains($r['check'], 'UNIQUE INDEX billings.invoice_number'));

    expect($row['status'])->toBe('FAIL')->and($row['detail'])->toContain('1 nomor invoice duplikat')->toContain('tidak ada renumber otomatis')
        ->and($output)->not->toContain('INV-DUP-RAHASIA')->not->toContain('Pasien Rahasia Preflight')->not->toContain('081299998888')
        ->and(DB::table('billings')->where('invoice_number', 'INV-DUP-RAHASIA')->count())->toBe(3)
        ->and(Schema::hasIndex('billings', ['invoice_number'], 'unique'))->toBeFalse();
})->skip(fn () => config('database.default') !== 'sqlite', 'Mengubah skema: DDL MySQL commit implisit dan tidak ter-rollback oleh RefreshDatabase; jalur ini diverifikasi manual di MySQL (lihat docs/09).');

it('GATE: tabel baru hilang pada database termigrasi = FAIL (pending_registration_fees dan finance_period_locks)', function () {
    pfConfig();
    Schema::drop('pending_registration_fees');
    Schema::drop('finance_period_locks');

    [$exit, $rows] = pfRows();

    expect($exit)->toBe(1)
        ->and(pfRow($rows, 'pending_registration_fees')['status'])->toBe('FAIL')->and(pfRow($rows, 'pending_registration_fees')['detail'])->toContain('tabel pending_registration_fees')
        ->and(pfRow($rows, 'finance_period_locks')['status'])->toBe('FAIL')
        ->and(pfRow($rows, 'Gerbang skema rilis')['detail'])->toContain('2 persyaratan');
})->skip(fn () => config('database.default') !== 'sqlite', 'Mengubah skema: DDL MySQL commit implisit dan tidak ter-rollback oleh RefreshDatabase; jalur ini diverifikasi manual di MySQL (lihat docs/09).');

it('GATE: kolom hilang (idempotency_key, registration_token) = FAIL', function () {
    pfConfig();
    Schema::table('billing_payments', function ($table) {
        $table->dropUnique('billing_payments_idempotency_key_unique');
        $table->dropColumn('idempotency_key');
    });
    Schema::table('patients', function ($table) {
        $table->dropUnique('patients_registration_token_unique');
        $table->dropColumn('registration_token');
    });

    [$exit, $rows] = pfRows();

    expect($exit)->toBe(1)
        ->and(pfRow($rows, 'billing_payments.idempotency_key')['detail'])->toContain('kolom billing_payments.idempotency_key')
        ->and(pfRow($rows, 'patients.registration_token')['detail'])->toContain('kolom patients.registration_token')
        ->and(pfRow($rows, 'billing_payments.idempotency_key')['status'])->toBe('FAIL');
})->skip(fn () => config('database.default') !== 'sqlite', 'Mengubah skema: DDL MySQL commit implisit dan tidak ter-rollback oleh RefreshDatabase; jalur ini diverifikasi manual di MySQL (lihat docs/09).');

it('GATE: constraint diperiksa, bukan hanya kolom — kolom ada tetapi unique index hilang = FAIL', function () {
    pfConfig();
    Schema::table('patients', fn ($table) => $table->dropUnique('patients_registration_token_unique'));
    Schema::table('pending_registration_fees', fn ($table) => $table->dropUnique('pending_registration_fees_patient_id_unique'));
    Schema::table('billing_payments', fn ($table) => $table->dropUnique('billing_payments_idempotency_key_unique'));

    [$exit, $rows] = pfRows();

    expect($exit)->toBe(1)
        ->and(Schema::hasColumn('patients', 'registration_token'))->toBeTrue()
        ->and(pfRow($rows, 'patients.registration_token')['detail'])->toContain('unique index patients(registration_token)')
        ->and(pfRow($rows, 'pending_registration_fees')['detail'])->toContain('unique index pending_registration_fees(patient_id)')
        ->and(pfRow($rows, 'billing_payments.idempotency_key')['detail'])->toContain('unique index billing_payments(idempotency_key)');
})->skip(fn () => config('database.default') !== 'sqlite', 'Mengubah skema: DDL MySQL commit implisit dan tidak ter-rollback oleh RefreshDatabase; jalur ini diverifikasi manual di MySQL (lihat docs/09).');

it('GATE: dibedakan — belum dimigrasi = WARN (mode biasa) dan FAIL (--release-gate); sudah dimigrasi tetapi hilang = FAIL di kedua mode', function () {
    pfConfig();

    // Keadaan "belum dimigrasi": elemen tidak ada DAN migration pembuatnya belum tercatat.
    Schema::drop('finance_period_locks');
    DB::table('migrations')->where('migration', 'like', '%create_finance_period_locks_table')->delete();

    [$exit, $rows] = pfRows();
    $row = pfRow($rows, 'finance_period_locks');

    expect($row['status'])->toBe('WARN')->and($row['detail'])->toContain('BELUM DIMIGRASI')
        ->and(pfRow($rows, 'Gerbang skema rilis')['status'])->toBe('WARN')
        ->and(pfRow($rows, 'Migration')['status'])->toBe('WARN')
        ->and($exit)->toBe(0);

    [$exitRelease, $releaseRows] = pfRows(['--release-gate' => true]);

    expect(pfRow($releaseRows, 'finance_period_locks')['status'])->toBe('FAIL')->and(pfRow($releaseRows, 'Gerbang skema rilis')['status'])->toBe('FAIL')
        ->and($exitRelease)->toBe(1);

    // Keadaan "tidak lengkap": migration tercatat dijalankan tetapi tabel hilang.
    DB::table('migrations')->insert(['migration' => '2026_10_15_100000_create_finance_period_locks_table', 'batch' => 99]);
    [$exitIncomplete, $incompleteRows] = pfRows();

    expect(pfRow($incompleteRows, 'finance_period_locks')['status'])->toBe('FAIL')->and(pfRow($incompleteRows, 'finance_period_locks')['detail'])->toContain('TIDAK LENGKAP')
        ->and($exitIncomplete)->toBe(1);
})->skip(fn () => config('database.default') !== 'sqlite', 'Mengubah skema: DDL MySQL commit implisit dan tidak ter-rollback oleh RefreshDatabase; jalur ini diverifikasi manual di MySQL (lihat docs/09).');

it('GATE: database tanpa tabel migrations sama sekali dianggap belum dimigrasi (bukan tidak lengkap)', function () {
    pfConfig();
    $ran = SchemaGate::ranMigrations();

    expect($ran)->not->toBe([]);

    Schema::drop('migrations');

    expect(SchemaGate::ranMigrations())->toBe([])
        ->and(collect(SchemaGate::evaluate())->where('state', SchemaGate::INCOMPLETE))->toBeEmpty();
})->skip(fn () => config('database.default') !== 'sqlite', 'Mengubah skema: DDL MySQL commit implisit dan tidak ter-rollback oleh RefreshDatabase; jalur ini diverifikasi manual di MySQL (lihat docs/09).');

it('GATE: --release-gate pada SQLite gagal di Row locking; mode biasa hanya WARN', function () {
    pfConfig();

    [$exit, $rows] = pfRows();
    expect(pfRow($rows, 'Row locking')['status'])->toBe('WARN')->and($exit)->toBe(0);

    [$exitRelease, $releaseRows] = pfRows(['--release-gate' => true]);
    expect(pfRow($releaseRows, 'Row locking')['status'])->toBe('FAIL')->and($exitRelease)->toBe(1);
})->skip(fn () => config('database.default') !== 'sqlite', 'Mengubah skema: DDL MySQL commit implisit dan tidak ter-rollback oleh RefreshDatabase; jalur ini diverifikasi manual di MySQL (lihat docs/09).');

it('GATE: keluaran tidak membocorkan kredensial, nilai env, atau data pasien; hanya membaca (data dan skema tidak berubah)', function () {
    pfConfig();
    config(['khayra_auth.admin.password' => 'SANDI-RAHASIA-PREFLIGHT']);
    $patient = Patient::create(['full_name' => 'Nama Pasien Sangat Rahasia', 'whatsapp' => '081277776666', 'nik' => '3273000000000001']);
    DB::table('billings')->insert(['patient_id' => $patient->id, 'invoice_number' => 'INV-RAHASIA-1', 'invoice_date' => '2026-10-01', 'amount' => 123456, 'created_at' => now(), 'updated_at' => now()]);

    $before = [DB::table('billings')->get()->toArray(), DB::table('patients')->get()->toArray(), Schema::getTables()];

    foreach ([[], ['--release-gate' => true]] as $options) {
        Artisan::call('khayra:preflight', array_merge(['--json' => true], $options));
        $output = Artisan::output();

        expect($output)->not->toContain('SANDI-RAHASIA-PREFLIGHT')->not->toContain('admin@uji.test')->not->toContain('Nama Pasien Sangat Rahasia')
            ->not->toContain('081277776666')->not->toContain('3273000000000001')->not->toContain('INV-RAHASIA-1')->not->toContain((string) config('app.key'));
    }

    expect([DB::table('billings')->get()->toArray(), DB::table('patients')->get()->toArray(), Schema::getTables()])->toEqual($before);
});

it('GATE: dalam keadaan lulus penuh di MySQL, --release-gate mengembalikan exit 0 (dilewati bila bukan MySQL)', function () {
    pfConfig();

    [$exit, $rows] = pfRows(['--release-gate' => true]);

    expect($rows->where('status', 'FAIL'))->toBeEmpty()->and($exit)->toBe(0);
})->skip(fn () => config('database.default') !== 'mysql', 'Butuh database uji MySQL.');
