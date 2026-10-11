<?php

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->withSession(['admin_logged_in' => true]));

function patientPayload(array $overrides = []): array
{
    return array_merge(['full_name' => 'Budi', 'whatsapp' => '0811111111'], $overrides);
}

it('renders religion and education as dropdowns on the create form', function () {
    $this->get('/admin/patients/create')->assertOk()
        ->assertSee('<select name="religion">', false)
        ->assertSee('<select name="education">', false)
        ->assertSee('Konghucu')
        ->assertSee('Sarjana (S1/D4)');
});

it('creates a patient with valid dropdown values', function () {
    $this->post('/admin/patients', patientPayload(['religion' => 'Hindu', 'education' => 'SMA/SMK']))
        ->assertRedirect('/admin/patients');

    expect(Patient::first())->religion->toBe('Hindu')->education->toBe('SMA/SMK');
});

it('keeps both fields optional', function () {
    $this->post('/admin/patients', patientPayload())->assertRedirect('/admin/patients');

    expect(Patient::first())->religion->toBeNull()->education->toBeNull();
});

it('rejects arbitrary free text for a new patient', function () {
    $this->post('/admin/patients', patientPayload(['religion' => 'abc', 'education' => 'xyz']))
        ->assertSessionHasErrors(['religion', 'education']);

    expect(Patient::count())->toBe(0);
});

it('keeps a legacy free-text value visible and saveable on edit', function () {
    $patient = Patient::create(['full_name' => 'Lama', 'whatsapp' => '08', 'religion' => 'islam', 'education' => 'S1 Teknik']);

    $this->get("/admin/patients/{$patient->id}/edit")->assertOk()
        ->assertSee('islam (data lama)')
        ->assertSee('S1 Teknik (data lama)');

    $this->post("/admin/patients/{$patient->id}/update", patientPayload(['religion' => 'islam', 'education' => 'S1 Teknik', 'full_name' => 'Lama Baru']))
        ->assertRedirect("/admin/patients/{$patient->id}");

    expect($patient->fresh())->full_name->toBe('Lama Baru')->religion->toBe('islam')->education->toBe('S1 Teknik');
});

it('lets a legacy patient move to a standard value but not to another free-text value', function () {
    $patient = Patient::create(['full_name' => 'Lama', 'whatsapp' => '08', 'religion' => 'islam']);

    $this->post("/admin/patients/{$patient->id}/update", patientPayload(['religion' => 'Katolik']))
        ->assertRedirect();
    expect($patient->fresh()->religion)->toBe('Katolik');

    $this->post("/admin/patients/{$patient->id}/update", patientPayload(['religion' => 'sesuatu lain']))
        ->assertSessionHasErrors('religion');
});

it('allows clearing a stored value', function () {
    $patient = Patient::create(['full_name' => 'Lama', 'whatsapp' => '08', 'religion' => 'Islam']);

    $this->post("/admin/patients/{$patient->id}/update", patientPayload(['religion' => '']))->assertRedirect();

    expect($patient->fresh()->religion)->toBeNull();
});
