<?php

/*
|--------------------------------------------------------------------------
| Trusted proxy (dibaca oleh middleware TrustProxies bawaan framework)
|--------------------------------------------------------------------------
|
| Default: tidak ada proxy yang dipercaya (perilaku lama tidak berubah).
| Isi TRUSTED_PROXIES hanya dengan IP/CIDR spesifik reverse proxy lokal, mis.
| "127.0.0.1" (lihat docs/16). Wildcard "*"/"**" SENGAJA dibuang di sini.
| Header X-Forwarded-Host tidak pernah dipercaya (lihat bootstrap/app.php).
|
*/

$proxies = array_values(array_filter(
    array_map('trim', explode(',', (string) env('TRUSTED_PROXIES', ''))),
    fn (string $entry) => $entry !== '' && ! str_contains($entry, '*'),
));

return [
    'proxies' => $proxies === [] ? null : $proxies,
];
