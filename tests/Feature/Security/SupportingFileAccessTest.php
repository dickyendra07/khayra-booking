<?php

use App\Models\MedicalRecord;
use App\Models\MedicalRecordSupportingData;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Visit;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/** Lampiran data pendukung rekam medis adalah data kesehatan: tidak boleh terbuka tanpa login. Semua data sintetis. */
beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');

    $this->patient = Patient::create(['full_name' => 'Pasien Lampiran Uji', 'whatsapp' => '081200000009']);
    $this->therapist = Therapist::create(['full_name' => 'Terapis Lampiran', 'email' => 'lampiran@uji.test', 'password' => Hash::make('x-uji-lokal'), 'status' => 'active']);
    $this->visit = Visit::create(['patient_id' => $this->patient->id, 'therapist_id' => $this->therapist->id, 'visit_date' => ClinicTime::today(), 'status' => 'in_progress']);
    $this->record = MedicalRecord::create(['visit_id' => $this->visit->id, 'patient_id' => $this->patient->id, 'subjective' => '', 'objective' => '', 'assessment' => '', 'plan' => '']);
});

function attachment(string $path, string $disk, string $name = 'hasil-lab.pdf'): MedicalRecordSupportingData
{
    Storage::disk($disk)->put($path, '%PDF-1.4 sintetis');

    return MedicalRecordSupportingData::create([
        'medical_record_id' => MedicalRecord::firstOrFail()->id, 'data_type' => 'Lab', 'file_path' => $path, 'file_name' => $name,
    ]);
}

it('lampiran tidak dapat dibuka tanpa login (admin maupun terapis)', function () {
    $item = attachment('medical-record-supporting-data/a.pdf', 'local');

    $this->get("/admin/medical-record-files/{$item->id}")->assertRedirect('/admin/login');
    $this->get("/therapist/medical-record-files/{$item->id}")->assertRedirect('/therapist/login');
});

it('sesi pasien tidak dapat membuka lampiran', function () {
    $item = attachment('medical-record-supporting-data/a.pdf', 'local');

    $this->withSession(['patient_logged_in' => true, 'patient_id' => $this->patient->id]);

    $this->get("/admin/medical-record-files/{$item->id}")->assertRedirect('/admin/login');
    $this->get("/therapist/medical-record-files/{$item->id}")->assertRedirect('/therapist/login');
});

it('admin dan terapis yang login dapat membuka lampiran dengan header yang aman', function () {
    $item = attachment('medical-record-supporting-data/a.pdf', 'local');

    $this->withSession(adminSession());
    $response = $this->get("/admin/medical-record-files/{$item->id}")->assertOk();

    expect($response->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($response->headers->get('Cache-Control'))->toContain('no-store')
        ->and($response->headers->get('Content-Security-Policy'))->toContain('sandbox');

    $this->flushSession()->withSession(['therapist_logged_in' => true, 'therapist_id' => $this->therapist->id]);
    $this->get("/therapist/medical-record-files/{$item->id}")->assertOk();
});

it('lampiran lama di disk publik tetap dapat dibuka lewat rute terautentikasi', function () {
    $item = attachment('medical-record-supporting-data/lama.pdf', 'public');

    $this->withSession(adminSession())->get("/admin/medical-record-files/{$item->id}")->assertOk();
});

it('path tidak sah, traversal, atau file hilang menghasilkan 404 (tidak membaca file lain)', function () {
    Storage::disk('local')->put('rahasia.txt', 'bukan lampiran');
    $this->withSession(adminSession());

    foreach (['../rahasia.txt', 'medical-record-supporting-data/../rahasia.txt', 'rahasia.txt', '', 'medical-record-supporting-data/tidak-ada.pdf'] as $path) {
        $item = MedicalRecordSupportingData::create(['medical_record_id' => $this->record->id, 'data_type' => 'X', 'file_path' => $path, 'file_name' => 'x.pdf']);

        $this->get("/admin/medical-record-files/{$item->id}")->assertNotFound();
    }

    $this->get('/admin/medical-record-files/999999')->assertNotFound();
    $this->get('/admin/medical-record-files/abc')->assertNotFound();
});

it('unggahan baru disimpan di disk privat, bukan di /storage publik', function () {
    $this->withSession(['therapist_logged_in' => true, 'therapist_id' => $this->therapist->id, 'therapist_name' => 'T']);

    $this->post("/therapist/visits/{$this->visit->id}/medical-record", [
        'subjective' => 'S', 'objective' => 'O', 'assessment' => 'A', 'plan' => 'P',
        'supporting_data_type' => ['Lab'], 'supporting_data_date' => [ClinicTime::today()], 'supporting_data_interpretation' => ['normal'],
        'supporting_data_file' => [UploadedFile::fake()->create('hasil.pdf', 20, 'application/pdf')],
    ])->assertSessionHasNoErrors();

    $item = MedicalRecordSupportingData::whereNotNull('file_path')->firstOrFail();

    expect(Storage::disk('local')->exists($item->file_path))->toBeTrue()
        ->and(Storage::disk('public')->exists($item->file_path))->toBeFalse();

    $this->get("/therapist/visits/{$this->visit->id}/medical-record")->assertOk()
        ->assertSee("/therapist/medical-record-files/{$item->id}", false)
        ->assertDontSee('/storage/medical-record-supporting-data', false);

    $this->get("/therapist/medical-record-files/{$item->id}")->assertOk();
});

it('gambar tampil inline sedangkan PDF/dokumen selalu diunduh (attachment) dengan nama aman', function () {
    Storage::disk('local')->put('medical-record-supporting-data/foto.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
    $image = MedicalRecordSupportingData::create(['medical_record_id' => $this->record->id, 'data_type' => 'X-ray', 'file_path' => 'medical-record-supporting-data/foto.png', 'file_name' => "foto\r\nSet-Cookie: x=1.png"]);
    $pdf = attachment('medical-record-supporting-data/b.pdf', 'local');

    $this->withSession(adminSession());

    $imageResponse = $this->get("/admin/medical-record-files/{$image->id}")->assertOk();
    expect($imageResponse->headers->get('Content-Disposition'))->toStartWith('inline')
        ->and($imageResponse->headers->get('Content-Disposition'))->not->toContain("\n")
        ->and($imageResponse->headers->get('Content-Disposition'))->not->toContain("\r")
        ->and($imageResponse->headers->get('Content-Disposition'))->toContain('filename="foto_');

    expect($this->get("/admin/medical-record-files/{$pdf->id}")->assertOk()->headers->get('Content-Disposition'))->toStartWith('attachment');
});

it('tidak ada rute /storage bawaan Laravel untuk disk privat (tanpa jalur akses publik tambahan)', function () {
    Storage::disk('local')->put('medical-record-supporting-data/privat.pdf', 'rahasia');

    expect(config('filesystems.disks.local.serve'))->toBeFalse();

    foreach (['/storage/medical-record-supporting-data/privat.pdf', '/storage/medical-record-supporting-data/privat.pdf?signature=palsu'] as $url) {
        $this->get($url)->assertNotFound();
    }

    $this->put('/storage/medical-record-supporting-data/baru.pdf', [])->assertNotFound();
});

it('lampiran milik rekam medis lain tidak dapat disisipkan lewat supporting_data_id manipulasi', function () {
    $otherPatient = Patient::create(['full_name' => 'Pasien Lain', 'whatsapp' => '081200000777']);
    $otherVisit = Visit::create(['patient_id' => $otherPatient->id, 'therapist_id' => $this->therapist->id, 'visit_date' => ClinicTime::today(), 'status' => 'in_progress']);
    $otherRecord = MedicalRecord::create(['visit_id' => $otherVisit->id, 'patient_id' => $otherPatient->id, 'subjective' => '', 'objective' => '', 'assessment' => '', 'plan' => '']);
    Storage::disk('local')->put('medical-record-supporting-data/milik-lain.pdf', 'rahasia');
    $foreign = MedicalRecordSupportingData::create(['medical_record_id' => $otherRecord->id, 'data_type' => 'Lab', 'file_path' => 'medical-record-supporting-data/milik-lain.pdf', 'file_name' => 'lain.pdf']);

    $this->withSession(['therapist_logged_in' => true, 'therapist_id' => $this->therapist->id, 'therapist_name' => 'T']);

    $this->post("/therapist/visits/{$this->visit->id}/medical-record", [
        'subjective' => 'S', 'objective' => 'O', 'assessment' => 'A', 'plan' => 'P',
        'supporting_data_id' => [$foreign->id], 'supporting_data_type' => ['Lab'], 'supporting_data_date' => [ClinicTime::today()], 'supporting_data_interpretation' => ['x'],
    ])->assertSessionHasNoErrors();

    expect(MedicalRecordSupportingData::where('medical_record_id', $this->record->id)->whereNotNull('file_path')->count())->toBe(0);
});
