# Kesiapan Staging — Khayra Physio (PR #2, commit `7f1c5cf` + perbaikan preflight)

Dokumen ini hanya rencana dan prosedur. **Belum ada deployment, belum ada akses ke production, belum ada dump production, dan belum ada migration di lingkungan mana pun selain database uji lokal.**
Strategi data staging awal: **DATA SINTETIS** (bagian L). Tidak ada data pasien production yang boleh masuk staging.

Status tombol-tombol penting saat staging pertama naik:
- `NEW_PATIENT_ADMIN_FEE_ENABLED` **tidak diisi / `false`** (biaya admin Rp35.000 NONAKTIF).
- Ruangan non-billable, consent template v1, PIC/shift belum ada.

---

## A. Persyaratan infrastruktur
| Komponen | Kebutuhan |
|---|---|
| Server aplikasi | Linux, PHP-FPM atau setara, docroot `public/`, `try_files` ke `index.php`; health check `/up` |
| HTTPS | Wajib untuk staging yang dapat diakses umum (cookie `secure`); akses dibatasi (IP allowlist atau basic auth) dan tidak diindeks |
| Database | MySQL 8.x, engine InnoDB, charset `utf8mb4`, **server atau skema terpisah dari production** |
| Cache dan sesi | Driver `database` (tabel `cache`, `sessions` ada di migration). **Jangan `array`/`null`** (throttling login tidak efektif) |
| Queue, scheduler, mail | **Tidak dipakai** (tidak ada dispatch/schedule/mail). Tidak perlu worker maupun cron |
| Storage | `storage/` dan `bootstrap/cache` writable; `php artisan storage:link` untuk lampiran rekam medis |
| Build frontend | **Tidak diperlukan** (tidak ada `@vite`/`public/build`; Node tidak dibutuhkan) |
| Cache konfigurasi | `config:cache` boleh (isi `.env` dulu). **`route:cache` tidak dapat dipakai** (rute berupa closure) |
| Reverse proxy | Tidak ada konfigurasi *trusted proxy*. Bila staging di belakang proxy/load balancer TLS, verifikasi skema URL dan cookie secure sebelum QA (butuh keputusan; lihat J) |
| Deployment automation | Belum ada artefak (Dockerfile/workflow). Metode deployment staging perlu ditetapkan oleh Kara Digital |

## B. Kompatibilitas PHP dan database
| Item | Versi / status |
|---|---|
| PHP | `^8.3` (composer); diuji di 8.4.20. Ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `ctype`, `fileinfo`, `tokenizer`, `xml`, `curl` |
| Composer | 2.x; instal staging: `composer install --no-dev --prefer-dist --optimize-autoloader` |
| MySQL | **Terverifikasi di 8.4.11** (suite penuh, harness paralel, preflight). Samakan versi dengan production |
| MariaDB | **Belum terverifikasi**. Bila dipakai, jalankan seluruh gerbang (F/G) di MariaDB sebelum dianggap kompatibel |
| SQLite | Hanya untuk development/test. `khayra:preflight --release-gate` sengaja GAGAL pada SQLite (tidak ada kunci baris) |

## C. Variabel environment
Nilai rahasia tidak ditulis di dokumen atau repo. Isi langsung di server staging.

| Variabel | Wajib | Nilai staging |
|---|---|---|
| `APP_ENV` | ya | `staging` (bukan `local`) |
| `APP_DEBUG` | ya | `false` |
| `APP_KEY` | ya | dibuat baru (`php artisan key:generate --show`), **berbeda dari production** |
| `APP_URL` | ya | `https://<host-staging>` (dipakai tautan portal di pesan WhatsApp) |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | ya | `mysql` + kredensial **khusus staging** |
| `SESSION_DRIVER`, `CACHE_STORE` | ya | `database` |
| `SESSION_SECURE_COOKIE` | ya (HTTPS) | `true` |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD_HASH` | ya | email staging + hash bcrypt, **berbeda dari production** (tanpa ini admin selalu terkunci) |
| `CLINIC_TIMEZONE` | ya | `Asia/Jakarta` (jangan ubah `app.timezone` = UTC) |
| `NEW_PATIENT_ADMIN_FEE_ENABLED` | **tidak diisi / `false`** | Naik pertama: MATI. Dinyalakan hanya di fase 2 dengan persetujuan |
| `LOG_LEVEL` | disarankan | `info` atau `warning` |
| `QUEUE_CONNECTION`, `MAIL_*`, `AWS_*`, `REDIS_*` | tidak | tidak dipakai |

Urutan: isi `.env` → `php artisan config:cache` (bila dipakai) → `php artisan khayra:preflight`. Perubahan `.env` setelah `config:cache` tidak berlaku sampai `config:cache` ulang.

## D. Isolasi database dan kredensial
1. Server/skema MySQL staging **terpisah**; user DB staging hanya punya hak pada skema staging (tanpa hak `CREATE DATABASE`).
2. Tidak ada rute jaringan dari staging ke database production (dan sebaliknya); firewall menolak.
3. Kredensial staging (DB, `APP_KEY`, akun admin) **tidak pernah dipakai ulang** dari production dan tidak disimpan di repo.
4. Pemeriksaan sebelum migrate: `DB_HOST`/`DB_DATABASE` di server staging menunjuk host staging; bandingkan secara manual dengan catatan host production (tanpa menyambung ke production).
5. Tidak ada integrasi eksternal aktif (mail `log`, tidak ada pengiriman WhatsApp otomatis; WhatsApp hanya tombol manual).

## E. Strategi backup
- **Staging awal (data sintetis):** backup tidak berisi data pasien. Tetap lakukan latihan: sebelum `migrate`, `mysqldump --single-transaction --routines --triggers --default-character-set=utf8mb4 <db_staging> | gzip > staging_pra_migrate_<tanggal>.sql.gz`; catat nama dan ukuran; uji pemulihan ke skema sementara.
- **Production (nanti, hanya oleh orang berwenang, dengan persetujuan tertulis):** dump penuh sebelum migrate; simpan terenkripsi di luar server aplikasi; tidak masuk repo/chat/laptop; retensi 30 hari; akses tercatat. Data kesehatan adalah data pribadi spesifik.
- **Dump production tidak boleh masuk staging tanpa anonimisasi** (nomor WhatsApp, NIK, nama, alamat) dan persetujuan eksplisit. Strategi saat ini: tidak dipakai.
- File lampiran rekam medis tidak ikut dump DB; jangan menyalin file production ke staging.

## F. Verifikasi migration
Seluruhnya di staging; tidak ada langkah di production.

1. `php artisan khayra:preflight` (database belum dimigrasi → **WARN** untuk skema, bukan FAIL; kredensial/cache/sesi harus PASS).
2. `php artisan migrate --pretend` dan tinjau DDL. Filter cepat (harus kosong): `... | grep -iE "drop|update |delete|truncate"`. Seluruh migration additive (15 pada rencana rilis, termasuk `finance_period_locks`).
3. Backup staging (bagian E), catat checksum baseline: `SELECT COUNT(*), SUM(amount), SUM(paid_amount), SUM(discount_amount) FROM billings;` `SELECT COUNT(*), SUM(line_total) FROM billing_items;` `SELECT COUNT(*) FROM patients;`
4. `php artisan migrate`; cocokkan checksum baseline (harus identik).
5. **Gerbang rilis:** `php artisan khayra:preflight --release-gate` harus exit 0 (0 FAIL). Gerbang ini memeriksa:
   - migration sudah dijalankan penuh dan driver MySQL/MariaDB;
   - **UNIQUE INDEX `billings.invoice_number` — SYARAT WAJIB**: bila tidak terpasang pada database yang sudah dimigrasi = FAIL (dengan jumlah duplikat bila ada);
   - tabel `pending_registration_fees` (unique `patient_id`) dan `finance_period_locks` (primary key `month`);
   - kolom + unique `billing_payments.idempotency_key` dan `patients.registration_token`;
   - serta constraint lain yang menjaga keuangan: `invoice_sequences.period`, `billings.checkout_token`, `rooms.name`, index referensi `billing_items`.
   Preflight **hanya membaca**: tidak memasang index, tidak memperbaiki skema/data, tidak mencetak kredensial atau data pasien.
6. Bila gagal pada unique index karena duplikat: berhenti; keputusan penyelesaian oleh manusia (tidak ada renumber otomatis); lalu `php artisan billing:ensure-invoice-unique-index` (perlu persetujuan) dan ulangi langkah 5.
7. Perbedaan keadaan: *belum dimigrasi* = WARN (FAIL pada `--release-gate`); *sudah dimigrasi tetapi tidak lengkap* = FAIL pada kedua mode.

## G. Pemeriksaan integritas finansial
1. `php artisan billing:audit-integrity --strict` → "Tidak ada temuan", exit 0 (bagian 8 membandingkan closing dengan sumber kas yang sama; bagian 13 memeriksa kewajiban administrasi).
2. **Uji konkurensi nyata:** `php tests/Concurrency/run.php` (39 pemeriksaan) terhadap **MySQL sekali pakai dengan versi yang sama**, dijalankan dari mesin dev/CI. Skrip membuat dan menghapus database sementara sendiri dan butuh hak `CREATE DATABASE`, sehingga **jangan memakai user/skema staging**.
3. Alur QA keuangan manual (bagian I) lalu ulangi `billing:audit-integrity --strict`.
4. Invoice legacy tidak berubah: bandingkan checksum baseline sebelum/sesudah.

## H. Pemeriksaan keamanan
- [ ] `khayra:preflight` 0 FAIL (kredensial admin, cache throttle, tabel sessions, `APP_DEBUG` mati, tidak ada `env()` di `routes`/`app`).
- [ ] Tanpa login, seluruh `/admin/*`, `/therapist/*`, `/patient/*` mengarah ke login (suite `RouteProtectionTest`).
- [ ] Salah login 6 kali terblokir (akun uji); logout menutup sesi; sesi therapist/pasien tidak membuka area admin.
- [ ] HTTPS aktif, cookie `secure` dan `http_only`, `APP_URL` benar.
- [ ] Staging tidak terindeks dan akses dibatasi; tidak ada kredensial production di server staging.
- [ ] Log tidak memuat data pasien atau kredensial.
- [ ] `.env` tidak ada di repo; dump/backup tidak ada di folder web.

## I. QA manual (data sintetis)
Gunakan akun/pasien uji. Biaya admin **mati** pada fase 1; butir bertanda (F2) membutuhkan fase 2.

1. **Login:** admin (benar/salah/blokir), fisioterapis, pasien (WhatsApp + tanggal lahir); logout.
2. **Registrasi pasien baru:** fase 1 — pasien tersimpan, **tidak ada invoice**; submit ganda = satu pasien. (F2) tagihan Rp35.000 unpaid, kategori admin_fee tanpa diskon; Create Patient dari booking = satu tagihan; pasien lama tidak ditagih.
3. **Checkout:** Program dengan promo (hanya Program didiskon), add-on, kembalian bukan pendapatan; invoice tampil di admin, cetak, portal pasien.
4. **Cicilan:** dua pembayaran terpisah; kirim ulang form tidak mencatat ganda.
5. **Refund:** void dengan refund; void dengan kredit tertahan.
6. **Void dan invoice pengganti:** Terbitkan Ulang memakai kredit non-kas; percobaan pengganti kedua ditolak; stok kembali lalu berkurang sekali.
7. **Monthly closing:** tutup buku → transaksi periode itu ditolak; buka kembali → tutup lagi; bayar invoice bulan lalu di bulan berjalan yang terbuka diizinkan; Owner Finance menampilkan Omzet per Kategori.
8. **Biaya admin tertunda (F2):** tutup bulan berjalan lalu daftarkan pasien → kewajiban pending (pasien terdaftar, tanpa invoice); penerbitan ditolak selama tutup; setelah dibuka kembali, invoice terbit bertanggal hari itu; penerbitan ganda tidak menggandakan; lencana sidebar benar.
9. **Invoice WhatsApp:** layar konfirmasi menampilkan nomor tujuan; pesan minimal tanpa rincian layanan; tautan portal memakai host staging.
10. **Lain:** `/booking` publik; dropdown agama/pendidikan (desktop dan mobile); cetak consent v1 tidak berubah; preview consent v2 ditandai PREVIEW.

## J. Checklist persetujuan rilis
Tiap baris butuh tanda tangan/persetujuan tertulis dari pihak yang disebut.
- [ ] Target hosting staging dan metode deployment ditetapkan (Kara Digital).
- [ ] Rencana dataset sintetis dan seeder disetujui (bagian L).
- [ ] Deploy staging dari SHA PR #2 (bukan `main`); preflight tanpa `--release-gate` hijau sebelum migrate.
- [ ] `migrate --pretend` ditinjau; `migrate` di staging disetujui; checksum identik.
- [ ] `khayra:preflight --release-gate` exit 0; unique index `invoice_number` terpasang.
- [ ] `billing:audit-integrity --strict` bersih; harness konkurensi lulus di MySQL sekali pakai.
- [ ] QA manual bagian I fase 1 lulus; biaya admin terbukti mati.
- [ ] (Fase 2, persetujuan terpisah) menyalakan `NEW_PATIENT_ADMIN_FEE_ENABLED=true` **di staging saja**; QA butir F2 lulus.
- [ ] Review dan merge PR #2 (keputusan manusia); hasil merge identik dengan SHA yang diuji (`git diff <sha> <merge>` kosong). Bila `main` bergerak, ulangi uji staging.
- [ ] Production (di luar cakupan dokumen ini): hanya setelah semua di atas, backup penuh, dan persetujuan tertulis; fee tetap mati saat naik pertama.

## K. Strategi rollback
> **Lampiran medis (PR #3):** rollback tidak boleh membuka kembali akses tanpa login ke lampiran; lihat `docs/12-runbook-lampiran-medis-legacy.md` §7.

- Skema additive: **rollback kode tidak membutuhkan rollback database**; kode lama mengabaikan tabel/kolom baru. Jangan `migrate:rollback` kecuali kerusakan struktur, dan sebelumnya backup tabel baru (`billing_payments`, `billing_audit_logs`, `invoice_sequences`, `rooms`, `pending_registration_fees`, `finance_period_locks`).
- Rollback kode staging: deploy ulang commit sebelumnya; `config:clear`/`config:cache`.
- Konsekuensi memakai kode lama setelah kode baru berjalan: invoice ledger tetap konsisten per invoice, tetapi laporan lama tidak melihat refund/kredit; invoice ber-`registration_token` dan kewajiban tertunda diabaikan; unique index menolak nomor invoice kembar (error, bukan data ganda). Jangan aktifkan biaya admin lagi sebelum kembali ke kode baru.
- Pulihkan data staging dari backup bila perlu (sintetis; tidak sensitif).

---

## L. Strategi data uji sintetis (rancangan; belum diimplementasikan)

**Prinsip:** data fiktif seluruhnya, dibuat lewat alur aplikasi/service (bukan SQL mentah) agar invarian terjaga, deterministik, dan dapat diulang. Penanda jelas: nama diawali `[SINTETIS]`, nomor WhatsApp `0800000xxxx`, NIK `0000…`. **Tidak ada data production yang diambil atau disalin.**

| Skenario | Dataset | Cara pembuatan | Hasil yang diharapkan |
|---|---|---|---|
| Pasien existing | 3 pasien tanpa `registration_token` (satu dengan teks agama/pendidikan legacy di luar dropdown) | `Patient::create` langsung (meniru data lama) | Tidak ada tagihan administrasi; form edit tetap menampilkan nilai legacy |
| Pasien baru | 3 pasien lewat form registrasi (1 lewat Create Patient dari booking) | HTTP/`PatientRegistration::register` | Fase 1: tanpa invoice. Fase 2: 1 invoice admin_fee Rp35.000 unpaid per pasien |
| Invoice legacy | 3 invoice `payment_model=legacy` (paid/unpaid/partial; satu berdiskon) bulan lalu | `Billing::create` | Laporan memakai rumus lama; tidak dihitung ulang; audit bersih |
| Invoice ledger | 4 invoice (program+promo, add-on, produk inventory, ruangan billable uji) | Checkout HTTP | Hanya Program didiskon; stok produk berkurang |
| Cicilan | 1 invoice 2 cicilan + 1 pembayaran berlebih (kembalian) | `/payments` dengan token | `partial` lalu `paid`; kembalian bukan pendapatan |
| Refund | 1 invoice dibayar lalu void + refund sebagian | Void + `/refunds` | Saldo tertahan berkurang sekali |
| Void | 1 invoice void tanpa uang | Void | Stok kembali |
| Invoice pengganti | 1 rantai: invoice → void (kredit) → pengganti → void → pengganti | Terbitkan Ulang | Kredit pindah sekali; 1 pengganti aktif; stok konsisten |
| Monthly closing | Bulan lalu ditutup; 1 invoice bulan lalu dibayar di bulan berjalan | `MonthlyClosing::close` + pembayaran | Snapshot bulan lalu tidak bergeser; kas masuk bulan berjalan |
| Registration fee | (Fase 2) pasien baru berbiaya | lihat "Pasien baru" | admin_fee tanpa diskon |
| Pending registration fee | (Fase 2) 1 pasien didaftarkan saat bulan berjalan ditutup | Tutup bulan berjalan → registrasi → buka kembali → terbitkan | Pending lalu terbit bertanggal hari itu, satu invoice |

**Kebutuhan implementasi (butuh persetujuan sebelum dikerjakan):**
1. Satu seeder/command, mis. `khayra:seed-synthetic`, **menolak berjalan bila `APP_ENV=production`** atau host database tidak diizinkan; idempoten (token tetap, aman diulang); fase 2 hanya berjalan bila fee diaktifkan.
2. Sekitar 200–300 baris kode + test (memastikan seeder menolak production dan hasilnya lolos `billing:audit-integrity --strict`).
3. Skenario bulan lalu memakai parameter `invoice_date` pada checkout dan `MonthlyClosing`, tanpa memanipulasi jam sistem.
4. Skenario pending membutuhkan penutupan bulan berjalan sementara di staging (lalu dibuka kembali dengan alasan tercatat).
5. Seeder tidak menyentuh data selain yang ia buat (semua baris bertanda `[SINTETIS]`), sehingga dapat dihapus per penanda.
