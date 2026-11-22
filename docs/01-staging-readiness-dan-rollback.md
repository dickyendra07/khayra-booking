# Kesiapan Staging, Rilis, dan Rollback — Khayra Physio

Dokumen ini adalah deliverable Workstream A. Tidak ada perubahan pada production. Semua pemeriksaan di
bawah dilakukan di lingkungan lokal (SQLite dan MySQL 8.4 uji) dan harus diulang di staging sebelum production.

## 1. Strategi rilis: hotfix keamanan terpisah dari fitur

> **DIGANTIKAN (13 Okt 2026):** PR #1 sudah di-merge ke `main`, sehingga hotfix tidak lagi dapat dirilis terpisah dari `main`. Lihat `docs/04-rencana-rilis-dan-verifikasi.md` bagian 1 untuk opsi rilis yang berlaku. Isi di bawah dipertahankan sebagai riwayat.

| Rilis | Isi | Migration | Risiko |
|---|---|---|---|
| **Rilis 1: Hotfix keamanan** | Commit `aff9a45` (middleware `RequireAreaLogin`, login hardening, throttle). Tersedia sebagai patch yang sudah diselesaikan terhadap `main`: `docs/hotfix/security-hotfix-on-main.patch` | **Tidak ada** | Rendah, tetapi admin bisa terkunci bila env kredensial belum diisi |
| **Rilis 2: Fitur + integritas** | `2680dc5` (Req 2, 4, 5) dan `630aa61` (P1) | 5 migration additive + 1 bersyarat | Menengah (perubahan alur kerja staf) |

Bukti hotfix bisa berdiri sendiri: patch diterapkan ke worktree sementara berbasis `main`, satu konflik (blok `use` di
`routes/web.php`) diselesaikan, lalu **39 test keamanan lulus**. Worktree sementara sudah dihapus.
Cara memakai patch: `git apply --check docs/hotfix/security-hotfix-on-main.patch` lalu `git apply` pada branch hotfix dari `main`.

Urutan yang disarankan: Rilis 1 (staging, lalu production) → tunggu stabil → Rilis 2 (staging, lalu production).

## 2. Kebutuhan konfigurasi autentikasi (wajib sebelum Rilis 1)

Tanpa nilai default. Bila tidak diisi, **login admin selalu ditolak**.

| Variabel | Keterangan |
|---|---|
| `ADMIN_EMAIL` | Wajib. |
| `ADMIN_PASSWORD_HASH` | Disarankan (bcrypt). |
| `ADMIN_PASSWORD` | Masih didukung agar kompatibel dengan setup lama. Jangan isi bersamaan dengan hash. |

Cara membuat hash tanpa menampilkan password di chat atau log: jalankan di terminal server/lokal
`php artisan tinker` lalu `Hash::make('...')`, salin hasilnya langsung ke `.env`.

**Pastikan sebelum deploy:** di server production, cek (tanpa menampilkan nilainya) bahwa `ADMIN_EMAIL` dan salah satu password
terisi. Jika production selama ini mengandalkan nilai default lama, setelah hotfix admin terkunci.
Perintah cek yang aman: `php artisan khayra:preflight` (tidak mencetak secret).

## 3. Kompatibilitas `config:cache`

- Hasil: tidak ada lagi pemanggilan `env()` di `routes/` dan `app/` (dijaga oleh test `RouteProtectionTest` dan
  pemeriksaan preflight). Kredensial dibaca dari `config/khayra_auth.php`.
- Diuji lokal: `config:cache` dengan nilai uji, kredensial terbaca dari cache config, kredensial default lama ditolak.
- Konsekuensi operasional: setiap mengubah `.env`, jalankan `php artisan config:cache` ulang.
- Catatan: rute memakai closure sehingga `route:cache` **tidak bisa** dipakai (sudah demikian sebelumnya).

## 4. Cache untuk throttling login

Throttle memakai `RateLimiter` Laravel di atas cache default.

| Driver cache | Efektif? |
|---|---|
| `database` (default `.env.example`) | Ya, asalkan tabel `cache` ada (migration bawaan `0001_01_01_000001`) |
| `file`, `redis`, `memcached` | Ya |
| `array`, `null` | **Tidak**: hitungan hilang di setiap request |

Divalidasi di MySQL 8.4 uji: driver `database` menulis/membaca benar. `php artisan khayra:preflight` melaporkan FAIL bila
cache `array`/`null` atau tabel cache belum ada. Saat server memakai banyak instance, cache harus bersama (database/redis), bukan `file` lokal.

## 5. Audit kesiapan migration MySQL/MariaDB

Seluruh migration dijalankan di **MySQL 8.4.11** (database baru, InnoDB, `utf8mb4_unicode_ci`). Temuan:

| # | Temuan | Status |
|---|---|---|
| 1 | **Migration lama gagal di database kosong MySQL**: `add_therapist_id_to_visits_table` dan `create_therapists_table` memiliki timestamp sama; urutan alfabet membuat FK ke tabel `therapists` dibuat sebelum tabelnya ada (SQLite tidak menangkap ini). Production tidak terdampak (migration itu sudah tercatat selesai), tetapi **staging/CI yang dibangun dari nol akan gagal**. | **Diperbaiki** (kolom dibuat setelah tabel ada; database existing tidak terpengaruh) |
| 2 | **Generator nomor invoice versi awal deadlock di MySQL** (38 dari 40 proses paralel), tidak terlihat di SQLite. | **Diperbaiki** (UPDATE atomik + retry + `prepare()`). Lihat bagian 6 |
| 3 | Semua tabel InnoDB; transaction dan row lock berfungsi | OK |
| 4 | `finance_monthly_closings.snapshot` dan `billing_audit_logs.before/after` bertipe JSON | OK (MySQL 5.7+/MariaDB 10.2+; di MariaDB menjadi LONGTEXT dengan validasi JSON) |
| 5 | Unique index `billings.invoice_number` (varchar 255, utf8mb4 = 1020 byte) di bawah batas 3072 byte | OK |
| 6 | Collation `utf8mb4_unicode_ci` bersifat case-insensitive: `INV-1` dan `inv-1` dianggap duplikat oleh MySQL (SQLite tidak) | Perlu dicek saat audit duplikat production |
| 7 | `down()` migration yang menghapus kolom ber-index gagal di SQLite | Diperbaiki; rollback 6 migration lalu migrate ulang diuji di SQLite dan MySQL |
| 8 | Upgrade simulasi data lama di MySQL (4 invoice, 1 duplikat nomor, 1 NULL): migrate berhasil, total identik (4 baris, Rp1.000.000, Rp300.000 terbayar), unique index **tidak dipasang** karena duplikat, audit melaporkan duplikatnya | OK |
| 9 | `ALTER TABLE` pada `billing_items` dan `billings`: menambah kolom nullable/default, tabel kecil | Jalankan di luar jam sibuk; MySQL 8.0.29+ mengerjakan sebagian secara instan |

Hasil uji **seluruh suite test (163 saat itu) di MySQL: lulus semua**.

## 6. Uji konkurensi nyata di MySQL (hasil)

40 hingga 80 proses PHP paralel, masing-masing mengambil nomor invoice dan menyimpannya dalam satu transaction:

| Skenario | Hasil |
|---|---|
| Metode lama `INV-Ymd-His`, 40 proses | **40 invoice, hanya 2 nomor unik** (38 kembar), diterima database |
| Generator awal (INSERT IGNORE + FOR UPDATE), 40 proses | 38 deadlock |
| Generator final, baris counter sudah ada, 40 dan 80 proses | 40/40 dan 80/80 unik, berurutan tanpa celah |
| Generator final, baris counter belum ada (invoice pertama bulan itu), `prepare()` aktif | 40/40 dan 3x ulang 60/60 unik |
| Stok inventory (`lockForUpdate`), stok 10, 30 proses | tepat 10 terjual, 20 ditolak, stok akhir 0 |

Keterbatasan SQLite: `lockForUpdate` tidak berlaku sehingga uji konkurensi hanya valid di MySQL/MariaDB. Uji ini harus diulang di staging
dengan versi MySQL/MariaDB yang sama dengan production.

## 7. Checklist validasi staging (urut)

Prasyarat: dump production hanya-baca dipulihkan ke database staging yang sama jenis/versinya. Jangan arahkan staging ke database production.

1. `php artisan khayra:preflight`: semua PASS (WARN zona waktu diharapkan; FAIL harus nol).
2. Isi `ADMIN_EMAIL` + `ADMIN_PASSWORD_HASH` di env staging; `php artisan config:cache`; ulangi preflight.
3. **Rilis 1 (hotfix)**: terapkan patch; uji manual: login admin benar/salah, salah 6 kali terblokir, logout menutup sesi, URL
   `/admin/reports/therapist-performance` tanpa login mengarah ke login, login pasien dan therapist normal.
4. **Rilis 2**: `php artisan migrate --pretend`, tinjau SQL; catat baseline: jumlah baris, `SUM(amount)`, `SUM(paid_amount)`,
   `SUM(discount_amount)` untuk `billings`, jumlah `billing_items`, dan `finance_monthly_closings`.
5. `php artisan migrate`; ulangi baseline: **harus identik**.
6. `php artisan billing:audit-integrity`: catat seluruh temuan (duplikat nomor, paid vs amount, dampak zona waktu, drift closing).
   Selesaikan duplikat nomor secara manual dengan keputusan bisnis, lalu `php artisan billing:ensure-invoice-unique-index`.
7. Uji beban kecil: 20 checkout paralel lewat HTTP; pastikan tidak ada nomor kembar dan stok benar.
8. Jalankan Pest dengan `DB_CONNECTION=mysql` pada skema uji terpisah (bukan database staging berisi data).
9. Uji manual alur penuh: checkout, void, Terbitkan Ulang, tutup buku, buka kembali, WhatsApp (nomor tujuan, pratinjau), cetak.
10. Uji tampilan mobile: dropdown pasien, billing, riwayat audit, kasir.

## 8. Rencana regression testing

- **Otomatis (175 test saat ini):** keamanan rute dan login (39), integritas billing/closing/nomor/diskon/koreksi (±60),
  Req 2/4/5, navigasi sidebar, portal pasien, `BillingMoney`, preflight, karakterisasi keuangan. Dijalankan di SQLite (cepat) dan MySQL (otoritatif).
- **Manual (staging):** langkah 3, 9, 10 di atas, ditambah login pasien/therapist nyata dan satu siklus tutup buku bulan uji.

## 9. Prosedur rollback

Prinsip: perubahan database bersifat **additive**, sehingga rollback kode tidak membutuhkan rollback database.
Rollback database (`migrate:rollback`) hanya bila benar-benar diperlukan karena akan menghapus tabel audit.

### Sebelum deploy (wajib)
1. Backup penuh database (dump) dan catat nama berkas; simpan di luar server aplikasi.
2. Catat commit/rilis yang sedang berjalan dan salinan `.env`.
3. Aktifkan mode pemeliharaan singkat bila memungkinkan (`php artisan down`).

### Rollback Rilis 1 (hotfix keamanan), tanpa database
1. Kembalikan kode ke rilis sebelumnya (`git checkout <commit-lama>` atau deploy ulang artefak lama).
2. `php artisan config:clear && php artisan config:cache`, lalu `php artisan up`.
3. Verifikasi: login admin berhasil (dengan kredensial lama), halaman utama terbuka.
4. Catatan: kembali ke kode lama berarti rute `/admin/reports/therapist-performance` terbuka lagi dan login tanpa throttle. Bila rollback karena admin terkunci, lebih cepat memperbaiki env (`ADMIN_EMAIL`, password) lalu `config:cache` daripada rollback.

### Rollback Rilis 2 (fitur + integritas)
1. Kembalikan kode ke Rilis 1 / rilis sebelumnya (kolom/tabel baru diabaikan oleh kode lama).
2. **Jangan** `migrate:rollback` kecuali ada kerusakan struktur. Bila terpaksa: `migrate:rollback --step=6` menghapus `billing_audit_logs`,
   `invoice_sequences`, kolom baru, dan unique index. **Backup tabel audit lebih dulu.**
3. Konsekuensi memakai kode lama setelah Rilis 2: invoice baru bernomor `INV-YYYYMM-NNNN` tetap valid; kode lama membuat
   nomor `INV-Ymd-His` (risiko kembar kembali, dan unique index akan **menolak** nomor kembar dengan error alih-alih menyimpannya).
   Bila perlu kembali ke kode lama untuk waktu lama, lepas index: `php artisan tinker` → `App\Support\InvoiceNumberIndex::drop()`.
4. Invoice yang dikoreksi (void + terbit ulang) tetap konsisten: void adalah status biasa, pengganti adalah invoice biasa.
5. Closing yang sempat dibuka kembali: kolom `reopened_*` diabaikan oleh kode lama (closing tampak tertutup kembali). Periksa manual.

### Setelah rollback
- Jalankan `php artisan khayra:preflight` dan baseline angka (jumlah/total invoice) dibandingkan dengan sebelum deploy.
- Catat penyebab rollback, jangan deploy ulang tanpa perbaikan dan uji di staging.

## 10. Kriteria go / no-go production

- Preflight: 0 FAIL di staging dan di production (read-only, dengan persetujuan).
- Env admin terisi dan login admin terverifikasi di staging dengan konfigurasi yang sama (termasuk `config:cache`).
- Baseline angka sebelum/sesudah migrate identik di staging.
- Audit duplikat nomor invoice dan keputusan penyelesaiannya tercatat.
- Backup database terverifikasi dapat dipulihkan.
- Persetujuan tertulis dari Kara Digital.
