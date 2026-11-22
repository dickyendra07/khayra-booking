# 14 — Isolasi hostname portal dan URL pasien kanonis (PR #9)

## Ringkas
Satu aplikasi Laravel melayani tiga hostname. Sebelum PR ini, **semua path terbuka di semua hostname** (mis. form login admin dapat dibuka dan di-POST dari `patient.khayraphysio.com`, dan `Host` apa pun diterima). PR ini menambah lapis pertahanan terpusat. **Ini bukan autentikasi/otorisasi**: sesi login (`RequireAreaLogin`), RBAC (`PortalAccess`), dan guard inline di route tetap berjalan penuh.

| Hostname | Portal | Area path |
|---|---|---|
| `app.khayraphysio.com` | Admin + Owner | `/admin/*`, `/owner/*` |
| `physio.khayraphysio.com` | Therapist | `/therapist/*` |
| `patient.khayraphysio.com` | Pasien + booking publik | `/patient/*`, `/booking` (GET/POST) |
| semua hostname resmi | — | `/` (redirect ke login portalnya sendiri) |
| hostname apa pun | health check | `/up` (tanpa data) |

Aturan (`EnforcePortalHost`, middleware **global**, berjalan sebelum sesi dimulai):
1. Hostname tidak dikenal → **404** (semua method, tanpa membuat sesi).
2. Path milik portal lain → **404** (GET, POST, PUT, DELETE, …).
3. Path di luar pemetaan → **404** (fail-closed; route baru tidak otomatis terbuka di semua hostname).
4. Hostname dibaca dari `Host`; **`X-Forwarded-Host` tidak pernah dipercaya** (lihat `bootstrap/app.php`).
5. Hanya aktif saat `APP_ENV=production` (atau `PORTAL_HOST_ENFORCE=true`). Lokal/testing tidak terpengaruh.

## Audit route (hasil)
165 route terdaftar: `admin` 129, `owner` 14, `therapist` 12, `patient` 6, `booking` 2, `/` 1, `/up` 1. Tidak ada route di luar pemetaan. Dicek khusus:
- **Redirect**: semua `redirect()` memakai path relatif dalam portal yang sama (tidak ada redirect lintas portal). Redirect login tetap di hostname yang sama.
- **Logout**: `POST /admin|therapist|patient/logout` hanya di hostname masing-masing.
- **Lampiran medis**: `/admin/medical-record-files/{id}` hanya di app, `/therapist/medical-record-files/{id}` hanya di physio. Tampilan admin tidak menautkan lampiran therapist.
- **Booking publik**: `GET/POST /booking` hanya di hostname pasien. Dashboard pasien menautkan `/booking` (hostname sama).
- **Endpoint bersama**: hanya `/` dan `/up`.
- **Tautan lintas portal yang sah dan diubah**: sidebar admin ("Form Booking Publik", "Patient Portal", "Therapist Portal") dan tombol "Open Public Booking" di daftar booking admin kini memakai URL absolut ke hostname tujuan (`PortalHosts::url()`); di lokal/testing tetap relatif. Dua tautan "Admin View" di halaman booking publik **dihapus** (tautan publik ke area admin; akan 404 di hostname pasien).
- Halaman `welcome` hanya tampil di hostname non-resmi (lokal), tidak terjangkau di production.

## Temuan tambahan yang ditutup: bypass gerbang role Owner lewat percent-encoding
Ditemukan saat audit dan **terbukti di baseline `f3f8fb5`**: sesi **Admin** dapat membuka halaman Owner (200) dengan `/%6Fwner/dashboard` atau `/owner%2Fdashboard`. Penyebab: `PortalAccess` dan `RequireAreaLogin` mencocokkan `$request->path()` (belum di-decode), sedangkan router mencocokkan path setelah `rawurldecode`. Perbaikan: keduanya (dan `EnforcePortalHost`) memakai `decodedPath()` huruf kecil. Test regresi: `PortalHostIsolationTest` ("BYPASS DITUTUP"). Pada baseline, test gerbang Owner gagal; setelah perbaikan lulus.

## Konfigurasi baru (`config/khayra_hosts.php`, `config/trustedproxy.php`)
Semua `env()` hanya di file config (kompatibel `config:cache`, dibuktikan test yang menjalankan `config:cache` sungguhan).

| Env | Default | Keterangan |
|---|---|---|
| `PORTAL_HOST_ENFORCE` | kosong | kosong = otomatis (hidup hanya di production); `true`/`false` memaksa |
| `PORTAL_HOST_ADMIN` | `app.khayraphysio.com` | |
| `PORTAL_HOST_THERAPIST` | `physio.khayraphysio.com` | |
| `PORTAL_HOST_PATIENT` | `patient.khayraphysio.com` | |
| `PATIENT_PORTAL_URL` | `https://patient.khayraphysio.com` | URL kanonis tautan ke pasien; tidak pernah dari Host permintaan |
| `TRUSTED_PROXIES` | kosong | IP/CIDR spesifik; wildcard dibuang. Lihat `docs/16` |

**Production tidak perlu menambah env apa pun**: default sudah sesuai tiga hostname produksi.

## URL pasien kanonis
`InvoiceWhatsappMessage` memakai `PortalHosts::patientUrl('/patient')` (dari config), bukan `url('/patient')`. Sebelumnya, dari `app.khayraphysio.com` tautan yang dikirim ke pasien menjadi `https://app.khayraphysio.com/patient`, dan Host palsu dapat memasukkan hostname penyerang ke pesan. Audit `url()/route()/asset()` lain: hanya `therapist-medical-record.blade.php` (`url('/therapist/medical-record-files/..')`, ditampilkan ke therapist yang sedang login di hostname-nya sendiri, bukan komunikasi ke pasien; tidak diubah). Tidak ada URL absolut lain ke pasien.

## Preflight
`php artisan khayra:preflight` kini memeriksa: tiga hostname terisi dan berbeda; isolasi hostname aktif di production (FAIL bila dimatikan); `PATIENT_PORTAL_URL` https dan sama dengan hostname pasien (FAIL bila tidak, di production); `TRUSTED_PROXIES` kosong di production = **WARN** (topologi proxy belum diverifikasi; lihat `docs/16`). WARN itu disengaja dan tidak mengubah exit code.

## Urutan deploy yang disarankan (jangan dijalankan sebelum disetujui)
Default **otomatis hidup di production**, jadi lakukan bertahap:
1. Pastikan aplikasi menerima `Host` asli (bukan `127.0.0.1:8080`): lihat `docs/16` bagian verifikasi. Bila Host yang diterima salah, penegakan akan 404-kan semuanya.
2. Deploy kode. Tambahkan di `.env` server `PORTAL_HOST_ENFORCE=false`, lalu `php artisan config:clear && php artisan config:cache && php artisan route:clear`. Perilaku sama seperti sebelumnya, kecuali perbaikan percent-encoding dan URL kanonis.
3. Jalankan `php artisan khayra:preflight --release-gate`.
4. Ubah ke `PORTAL_HOST_ENFORCE=true` (atau hapus barisnya), `config:cache`, lalu uji browser: tiap hostname membuka portalnya; portal lain 404; `curl -H 'Host: evil.example' https://<ip>/admin/login` → 404.
5. Pasang Nginx defense-in-depth (`docs/15`) setelah langkah 4 stabil.

**Rollback**: `PORTAL_HOST_ENFORCE=false` + `config:cache` (tanpa deploy ulang). Perbaikan percent-encoding tidak perlu di-rollback.

## Residual risk
- Hostname ditentukan header `Host`; klien yang bisa menjangkau backend langsung (port 8080 / IP) dapat mengirim `Host` resmi. Itu sebabnya sesi + RBAC tetap wajib dan port 8080 harus terikat ke loopback (`docs/15`, `docs/16`).
- Cookie sesi host-only (`SESSION_DOMAIN` kosong) memisahkan sesi antar hostname; **jangan** mengisi `SESSION_DOMAIN=.khayraphysio.com`. Tidak bisa diverifikasi dari repo (perlu cek `.env` server).
- Satu `APP_KEY` dan satu tabel sesi dipakai bersama ketiga portal; isolasi bergantung pada cookie host-only dan flag sesi per area.
