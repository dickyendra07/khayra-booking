<?php

/*
|--------------------------------------------------------------------------
| Portal Accounts (Admin & Owner)
|--------------------------------------------------------------------------
|
| Dibaca lewat config() supaya tetap jalan saat `php artisan config:cache`
| (env() langsung di route akan bernilai null jika config di-cache).
|
| - admin : staf operasional (booking, pasien, kasir, inventory, dokumen).
| - owner : pemilik klinik. Satu-satunya yang boleh membuka Dashboard ERM,
|           Owner Dashboard, Owner Finance / Cashflow dan Reporting Center.
|
| Akun owner dinonaktifkan jika OWNER_EMAIL / OWNER_PASSWORD belum diisi.
|
*/

return [
    'admin_email' => env('ADMIN_EMAIL', 'admin@khayraphysio.com'),
    'admin_password' => env('ADMIN_PASSWORD', 'password'),

    'owner_email' => env('OWNER_EMAIL'),
    'owner_password' => env('OWNER_PASSWORD'),
];
