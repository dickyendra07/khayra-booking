<?php

use App\Models\Patient;
use App\Services\PatientDuplicateFinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function dupPatient(array $attrs = []): Patient
{
    return Patient::create(array_merge(['full_name' => 'Sinta Uji', 'whatsapp' => '081200000100'], $attrs));
}

it('NIK yang sama (format berbeda) = keyakinan tinggi', function () {
    $a = dupPatient(['nik' => '3273-0101-0101-0001', 'whatsapp' => '081200000101']);
    $b = dupPatient(['full_name' => 'Nama Lain', 'nik' => '3273010101010001', 'whatsapp' => '081200000102']);

    $pairs = PatientDuplicateFinder::scan();

    expect($pairs)->toHaveCount(1)
        ->and($pairs[0])->toMatchArray(['a' => $a->id, 'b' => $b->id, 'level' => 'high']);
});

it('nama + tanggal lahir sama (huruf/spasi/tanda baca berbeda) = keyakinan menengah', function () {
    dupPatient(['full_name' => 'Budi  Santoso', 'birth_date' => '1990-05-05', 'whatsapp' => '081200000111']);
    dupPatient(['full_name' => 'budi santoso.', 'birth_date' => '1990-05-05', 'whatsapp' => '081200000112']);

    expect(PatientDuplicateFinder::scan()->pluck('level')->all())->toBe(['medium']);
});

it('WhatsApp sama dipakai bersama oleh anggota keluarga BUKAN duplikat (false positive dihindari)', function () {
    dupPatient(['full_name' => 'Ibu Rina', 'birth_date' => '1980-01-01', 'whatsapp' => '081200000120']);
    dupPatient(['full_name' => 'Andi Anak Rina', 'birth_date' => '2012-02-02', 'whatsapp' => '+6281200000120']);

    expect(PatientDuplicateFinder::scan())->toBeEmpty();
});

it('WhatsApp sama dengan nama sangat mirip ditandai keyakinan rendah', function () {
    dupPatient(['full_name' => 'Rina Wijaya', 'whatsapp' => '081200000130']);
    dupPatient(['full_name' => 'Rina Wijaya S', 'whatsapp' => '6281200000130']);

    expect(PatientDuplicateFinder::scan()->pluck('level')->all())->toBe(['low']);
});

it('NIK kosong atau terlalu pendek tidak pernah dianggap sama', function () {
    dupPatient(['nik' => '', 'whatsapp' => '081200000141', 'full_name' => 'A Satu']);
    dupPatient(['nik' => '', 'whatsapp' => '081200000142', 'full_name' => 'B Dua']);
    dupPatient(['nik' => '123', 'whatsapp' => '081200000143', 'full_name' => 'C Tiga']);
    dupPatient(['nik' => '123', 'whatsapp' => '081200000144', 'full_name' => 'D Empat']);

    expect(PatientDuplicateFinder::scan())->toBeEmpty();
});

it('forAttributes menilai data pendaftaran baru tanpa menyimpan apa pun', function () {
    $existing = dupPatient(['full_name' => 'Dewi Lestari', 'birth_date' => '1995-09-09', 'whatsapp' => '081200000150']);

    $candidates = PatientDuplicateFinder::forAttributes(['full_name' => 'dewi lestari', 'birth_date' => '1995-09-09', 'whatsapp' => '081299999999']);

    expect($candidates)->toHaveCount(1)
        ->and($candidates[0]['patient']->id)->toBe($existing->id)
        ->and($candidates[0]['level'])->toBe('medium')
        ->and(Patient::count())->toBe(1);
});

it('laporan artisan hanya membaca dan tidak mencetak nama, NIK, atau nomor kontak', function () {
    dupPatient(['full_name' => 'Rahasia Nama', 'nik' => '3273010101019999', 'whatsapp' => '081200000160']);
    dupPatient(['full_name' => 'Rahasia Nama', 'nik' => '3273010101019999', 'whatsapp' => '081200000161']);
    $checksum = DB::table('patients')->orderBy('id')->get()->toJson();

    Artisan::call('khayra:patients-duplicates');
    $output = Artisan::output();

    expect($output)->toContain('high')->not->toContain('Rahasia')->not->toContain('3273010101019999')->not->toContain('0812000001')
        ->and(DB::table('patients')->orderBy('id')->get()->toJson())->toBe($checksum);

    Artisan::call('khayra:patients-duplicates', ['--json' => true]);
    $json = Artisan::output();

    expect($json)->not->toContain('Rahasia')->not->toContain('3273010101019999');
});

it('tanpa kandidat melaporkan bersih', function () {
    dupPatient(['full_name' => 'Satu', 'whatsapp' => '081200000171']);
    dupPatient(['full_name' => 'Dua', 'whatsapp' => '081200000172']);

    $this->artisan('khayra:patients-duplicates')->expectsOutputToContain('Tidak ada kandidat')->assertSuccessful();
});
