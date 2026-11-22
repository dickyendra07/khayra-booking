# 13 — Pemisahan akses Admin dan Owner

## Ringkas
- Dua role memakai **satu form login** (`/admin/login`); role ditentukan dari kredensial.
  - **Admin** (`ADMIN_EMAIL` + `ADMIN_PASSWORD_HASH`/`ADMIN_PASSWORD`): staf operasional. Mendarat di `/admin/bookings`.
  - **Owner** (`OWNER_EMAIL` + `OWNER_PASSWORD_HASH`/`OWNER_PASSWORD`): pemilik. Mendarat di `/owner/dashboard`. Akun owner **opsional**; kosong = login owner nonaktif.
- 4 menu **hanya Owner** (admin mendapat 403, termasuk lewat URL langsung):

| Menu | URL baru | URL lama (dialihkan) |
|---|---|---|
| Dashboard ERM | `/owner/erm-dashboard` | `/admin/dashboard` |
| Owner Dashboard | `/owner/dashboard` | `/admin/owner-dashboard` |
| Owner Finance / Cashflow (termasuk tutup/buka buku, transaksi manual, cetak) | `/owner/finance/*` | `/admin/owner-finance/*` |
| Reporting Center (semua laporan) | `/owner/reports/*` | `/admin/reports/*` |

- Owner **tetap bisa membuka halaman admin** (dashboard owner punya tautan drill-down ke booking, billing, kunjungan, cuti).
- Route baru di bawah `/owner/*` otomatis hanya untuk owner (middleware `PortalAccess`), tanpa perlu guard manual.

## Keamanan (memakai mekanisme yang sudah ada)
- Login dipagari `RequireAreaLogin` (area `owner` memakai sesi login yang sama); rate-limit login berbagi kebijakan `admin`.
- Sidik jari kredensial dihitung **per akun**; sesi diperiksa terhadap akun sesuai role. Merotasi kredensial owner hanya membatalkan sesi owner, dan sebaliknya.
- Sesi tanpa `portal_role` dianggap admin (tidak pernah owner). Role yang dipalsukan ditolak karena sidik jari tidak cocok.
- Jika kredensial owner = kredensial admin, hasil login tetap **admin** (hak paling rendah).
- Buka-kembali buku meminta password ulang untuk **akun yang sedang login** (owner).

## Langkah deploy
1. Isi `OWNER_EMAIL` dan `OWNER_PASSWORD_HASH` (bcrypt, sama seperti `ADMIN_PASSWORD_HASH`) di `.env` server; jalankan `php artisan config:clear` bila config pernah di-cache.
2. Sesi yang sedang login tetap dianggap admin sampai logout; owner harus login ulang dengan akun owner agar melihat 4 menu.
3. Beri tahu staf admin: Dashboard ERM, Owner Dashboard, Owner Finance, dan Reporting Center sekarang khusus owner.
