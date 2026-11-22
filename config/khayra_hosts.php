<?php

/*
|--------------------------------------------------------------------------
| Hostname portal & URL kanonis
|--------------------------------------------------------------------------
|
| Satu aplikasi Laravel melayani tiga hostname. Nilai env() hanya dibaca di
| file config ini supaya tetap berfungsi setelah `php artisan config:cache`.
|
| Penegakan hostname (middleware EnforcePortalHost) HANYA lapis tambahan di atas
| sesi login + RBAC yang sudah ada; bukan pengganti autentikasi/otorisasi.
|
*/

return [

    /*
    | true  = tolak hostname tidak dikenal dan akses lintas portal (404).
    | false = nonaktif.
    | null  = otomatis: aktif hanya bila APP_ENV=production (lokal/testing tidak terpengaruh).
    */
    'enforce' => env('PORTAL_HOST_ENFORCE') === null || env('PORTAL_HOST_ENFORCE') === ''
        ? null
        : filter_var(env('PORTAL_HOST_ENFORCE'), FILTER_VALIDATE_BOOLEAN),

    /*
    | Hostname resmi tiap portal (tanpa skema/port, huruf kecil).
    */
    'hosts' => [
        'admin' => strtolower(trim((string) env('PORTAL_HOST_ADMIN', 'app.khayraphysio.com'))),
        'therapist' => strtolower(trim((string) env('PORTAL_HOST_THERAPIST', 'physio.khayraphysio.com'))),
        'patient' => strtolower(trim((string) env('PORTAL_HOST_PATIENT', 'patient.khayraphysio.com'))),
    ],

    /*
    | Segmen path pertama -> portal pemilik. Path di luar daftar ini (dan di luar
    | `any_host_paths`) ditolak saat penegakan aktif (fail-closed): route baru tidak
    | otomatis terbuka di semua hostname.
    */
    'areas' => [
        'admin' => 'admin',
        'owner' => 'admin',
        'therapist' => 'therapist',
        'patient' => 'patient',
        'booking' => 'patient',
    ],

    /*
    | Path yang boleh diakses di hostname apa pun (health check; tidak memuat data).
    */
    'any_host_paths' => ['up'],

    /*
    | URL kanonis untuk tautan yang dikirim ke pasien (WhatsApp, dsb.). Tidak pernah
    | diturunkan dari header Host/X-Forwarded-Host permintaan.
    */
    'urls' => [
        'patient' => rtrim(trim((string) env('PATIENT_PORTAL_URL', 'https://patient.khayraphysio.com')), '/'),
    ],

];
