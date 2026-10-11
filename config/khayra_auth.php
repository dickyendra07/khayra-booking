<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi autentikasi area admin / terapis / pasien
|--------------------------------------------------------------------------
|
| Nilai env() hanya dibaca di file config ini supaya tetap berfungsi
| setelah `php artisan config:cache`. Tidak ada nilai default untuk kredensial:
| jika ADMIN_EMAIL dan salah satu dari ADMIN_PASSWORD_HASH / ADMIN_PASSWORD
| tidak diisi, login admin selalu ditolak.
|
*/

return [

    'admin' => [
        'email' => env('ADMIN_EMAIL'),

        // Disarankan: hash bcrypt (php artisan tinker -> Hash::make('...')).
        'password_hash' => env('ADMIN_PASSWORD_HASH'),

        // Masih didukung demi kompatibilitas dengan konfigurasi lama.
        'password' => env('ADMIN_PASSWORD'),
    ],

    /*
    | Kebijakan pembatasan percobaan login (gagal) per area.
    |
    | pair     : kombinasi identitas + IP
    | ip       : seluruh percobaan dari satu IP (lintas identitas)
    | identity : seluruh percobaan ke satu identitas (lintas IP); null = nonaktif
    | decay    : detik sebelum hitungan di-reset
    */
    'throttle' => [
        'admin' => ['pair' => 5, 'ip' => 20, 'identity' => null, 'decay' => 900],
        'therapist' => ['pair' => 5, 'ip' => 20, 'identity' => 20, 'decay' => 900],
        'patient' => ['pair' => 5, 'ip' => 20, 'identity' => 20, 'decay' => 900],
    ],

];
