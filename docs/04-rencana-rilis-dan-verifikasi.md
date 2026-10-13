# Rencana Rilis, Backup, Rollback, dan Verifikasi — Khayra Physio

Dokumen ini melengkapi `01-staging-readiness-dan-rollback.md`. **Belum ada deployment dan belum ada akses ke production.**
Semua langkah di production menunggu persetujuan eksplisit Kara Digital.

## 1. Strategi rilis — DIPERBARUI setelah PR #1 di-merge ke `main` (13 Okt 2026)

> **Strategi lama (Rilis 1 hotfix keamanan terpisah di atas `main`, lalu Rilis 2) TIDAK BERLAKU LAGI untuk `main`.**
> PR #1 sudah di-merge: `origin/main` (`0803214`) kini memuat hotfix keamanan (`aff9a45`) *bersama* integritas finansial P1 dan seluruh
> fitur. `docs/hotfix/security-hotfix-on-main.patch` **tidak dapat lagi diterapkan** ke `main` (`git apply --check` gagal: berkas
> sudah ada); patch itu hanya valid terhadap basis lama `5eae3f8` (main sebelum PR #1).
> Catatan: branch lokal `main` di mesin ini masih tertinggal di `5eae3f8`; selalu pakai `origin/main`.

**Fakta yang menentukan opsi:** `origin/main` (`0803214`) memuat dua release blocker yang sudah diperbaiki di branch
`feat/khayra-client-revisions-final` (closing race dan invoice pengganti ganda). **Jangan merilis `0803214` ke production.**
Yang boleh dirilis hanya commit akhir branch ini (atau hasil merge-nya) setelah lulus staging.

### Opsi A (direkomendasikan): satu rilis bertahap dari commit akhir

Syarat: production belum menjalankan kode PR #1 (belum ada deployment ke production yang tercatat) atau akan dimigrasi sekaligus.

1. Review dan merge branch ini ke `main` lewat PR (keputusan manusia; tidak dilakukan otomatis). Rilis dari hash merge tersebut.
2. **Staging dulu**: restore dump production ke staging, `migrate --pretend`, `migrate`, `billing:audit-integrity --strict`, bandingkan checksum
   baseline (bagian 4), jalankan `php tests/Concurrency/run.php` terhadap MySQL staging, uji manual bagian 7.
3. **Prasyarat konfigurasi production sebelum kode naik** (karena keamanan P0 ikut naik di rilis yang sama): `ADMIN_EMAIL` dan
   `ADMIN_PASSWORD_HASH` terisi, cache driver bukan `array`/`null`, tabel `cache` dan `sessions` ada. Tanpa ini admin terkunci.
4. **Fitur uang baru tetap MATI saat naik:** `NEW_PATIENT_ADMIN_FEE_ENABLED` tidak diisi / `false` (default mati). Ruangan tetap non-billable, consent tetap v1.
5. Setelah stabil beberapa hari kerja dan staf dilatih memakai menu *Biaya Admin Tertunda*, aktifkan biaya admin di environment production
   (`NEW_PATIENT_ADMIN_FEE_ENABLED=true`, lalu `config:cache` bila dipakai) sebagai langkah terpisah dengan persetujuan tertulis.
6. Migration (15 additive) dijalankan satu kali pada jendela sepi setelah backup penuh; rollback kode tidak membutuhkan rollback database.

### Opsi B: bila production SUDAH menjalankan kode lama dan celah keamanan harus ditutup lebih dulu

Syarat: ada kebutuhan mendesak menutup celah login/rute admin tanpa menunggu fitur lain. Dilakukan dari **basis yang benar-benar berjalan di production**, bukan dari `main`.

1. Pastikan commit yang berjalan di production (baca-saja, oleh orang berwenang: `git rev-parse HEAD` di server atau catatan deployment).
2. Bila commit itu = `5eae3f8` (main sebelum PR #1): buat branch hotfix dari commit tersebut, `git apply --check docs/hotfix/security-hotfix-on-main.patch`
   (diverifikasi bersih terhadap `5eae3f8`), terapkan, jalankan `php artisan test --filter=Security`, `php artisan khayra:preflight`, staging, lalu production.
   Hotfix tanpa migration; prasyarat konfigurasi kredensial sama seperti Opsi A butir 3.
3. Bila basisnya berbeda: cherry-pick `aff9a45` ke branch hotfix dari basis itu dan selesaikan konflik secara manual, ulangi test keamanan.
4. Rilis penuh (Opsi A) menyusul setelah hotfix stabil. Hotfix **tidak** boleh dibuat dari `main` dan **tidak** boleh melewati staging.

### Opsi yang TIDAK aman
- Merilis `origin/main` (`0803214`) apa adanya: memuat dua release blocker (closing race, invoice pengganti ganda).
- Menerapkan patch hotfix ke `main`: sudah tidak valid dan akan konflik/dobel.
- Mengaktifkan biaya admin bersamaan dengan rilis pertama kode ini ke production.
- Menjalankan `tests/Concurrency/run.php` atau suite pada database production (skrip hanya untuk server uji/staging; ia membuat database sementara sendiri).

Pada bagian-bagian di bawah, istilah **Rilis 1** = hotfix keamanan Opsi B (bila dipakai) dan **Rilis 2** = rilis penuh (Opsi A). Bila Opsi A dipilih,
lewati langkah khusus Rilis 1.

## 2. Status fitur saat rilis (apa yang AKTIF dan yang MASIH MATI)

| Fitur | Status saat rilis |
|---|---|
| Buku pembayaran (kembalian, cicilan, refund, kredit) | **Aktif untuk invoice BARU.** Invoice lama tetap `legacy`, tidak dihitung ulang |
| Laporan: omzet (tanggal invoice) vs arus kas (tanggal pembayaran) | Aktif; closing lama tidak berubah |
| Tanggal bisnis WIB (`ClinicTime`) | Aktif; penyimpanan tetap UTC |
| Anti klik-ganda checkout, nomor invoice berurutan, penguncian invoice, audit | Aktif |
| Diskon hanya Program; Specialist tidak didiskon | Aktif |
| Master ruangan | Aktif; **semua ruangan non-billable**, tidak ada tarif; tidak ada penagihan otomatis |
| Biaya admin pasien baru Rp35.000 | **MATI secara default**; aktif hanya bila `NEW_PATIENT_ADMIN_FEE_ENABLED=true` diisi eksplisit di environment (aturan final: sekali saat registrasi pertama; tagihan unpaid otomatis; periode tutup → kewajiban tertunda di menu Biaya Admin Tertunda; pasien lama tidak ditagih) |
| Informed consent tanpa TTD fisioterapis | **MATI** (`clinic.consent.active_template_version = 1`) |
| PIC / shift (Req 1) | Belum ada implementasi (menunggu definisi klien) |

## 3. Rencana migration

Urutan dijalankan otomatis oleh `php artisan migrate`. Semua additive, idempotent (`hasTable`/`hasColumn`), tidak mengubah/menghapus data.

| # | Migration | Tindakan | Data lama |
|---|---|---|---|
| 1 | `2026_10_11_100000` billing_items: kategori/diskon | tambah 4 kolom nullable/default | NULL/0 (tidak dihitung ulang) |
| 2 | `2026_10_12_100000` billing_audit_logs | tabel baru | n/a |
| 3 | `2026_10_12_100100` invoice_sequences | tabel baru | n/a |
| 4 | `2026_10_12_100200` billings.replaces_billing_id | kolom nullable + index | NULL |
| 5 | `2026_10_12_100300` finance_monthly_closings.reopened_* | 3 kolom nullable | NULL = tetap tertutup |
| 6 | `2026_10_12_100400` unique index invoice_number | **bersyarat**: hanya bila tidak ada duplikat | tidak diubah |
| 7 | `2026_10_13_100000` billing_payments | tabel baru | n/a |
| 8 | `2026_10_13_100100` billings.payment_model, checkout_token | kolom: `payment_model` default `legacy`, `checkout_token` nullable unik | semua invoice lama = `legacy` |
| 9 | `2026_10_13_100200` rooms | tabel baru + 10 ruangan bawaan (non-billable) | n/a |
| 10 | `2026_10_13_100300` billing_items.reference_*, rate_unit | 3 kolom nullable + index | NULL |
| 11 | `2026_10_13_100400` informed_consents.template_version, recorded_by | `template_version` default 1, `recorded_by` nullable | semua consent lama = versi 1 |
| 12 | `2026_10_14_100000` billing_payments.idempotency_key | 1 kolom nullable + unique index (2 DDL) | NULL; baris lama tidak diubah |
| 13 | `2026_10_14_110000` patients.registration_token | 1 kolom nullable + unique index (2 DDL) | NULL untuk semua pasien lama; tidak ada tagihan retroaktif |
| 14 | `2026_10_14_120000` pending_registration_fees | tabel baru (unique patient_id) | n/a; tidak ada baris untuk data lama |
| 15 | `2026_10_15_100000` finance_period_locks | tabel baru (primary key month), baris dibuat on-demand | n/a; tidak memuat data keuangan |

Dua migration historis (`2026_03_23_224421_*`) diberi guard agar database kosong MySQL berhasil dibangun. Database yang sudah menjalankannya
tidak terpengaruh dan skemanya identik (diverifikasi).

Langkah:
1. `php artisan migrate --pretend` di staging dan production, tinjau DDL (sekitar 39 pernyataan; tanpa `DROP`/`UPDATE`/`DELETE` terhadap data).
2. Jalankan migrate di luar jam sibuk. Tabel besar tidak ada (ALTER hanya menambah kolom pada billings/billing_items/informed_consents).
3. Setelah migrate: `php artisan billing:audit-integrity` dan baca bagian "Unique index invoice_number". Jika belum terpasang karena duplikat,
   putuskan penyelesaian duplikat lalu `php artisan billing:ensure-invoice-unique-index`.
4. `php artisan config:cache` bila production memakai cache config (rute memakai closure sehingga `route:cache` tidak dipakai).

Validasi yang sudah dilakukan (lokal, MySQL 8.4 uji): migrate dari nol, rollback 6 langkah lalu migrate ulang, upgrade dari data lama dengan
nomor duplikat (total identik, index tertahan), seluruh suite 227 test.

## 4. Rencana backup database (sebelum Rilis 2; untuk Rilis 1 cukup backup rutin)

1. **Backup penuh** sebelum migrate: `mysqldump --single-transaction --routines --triggers --default-character-set=utf8mb4 <db> | gzip > khayra_pra_rilis2_<tanggal>.sql.gz`.
   Simpan di luar server aplikasi; catat nama berkas dan ukuran.
2. **Checksum baseline** (dicatat sebelum dan sesudah migrate, harus identik):
   `SELECT COUNT(*), SUM(amount), SUM(paid_amount), SUM(discount_amount) FROM billings;`
   `SELECT COUNT(*), SUM(line_total) FROM billing_items;`
   `SELECT COUNT(*), SUM(total_income) FROM finance_monthly_closings;`
   `SELECT COUNT(*) FROM patients; SELECT COUNT(*) FROM informed_consents;`
3. **Uji pemulihan:** pulihkan dump ke database staging dan jalankan migrate di sana lebih dulu (ini sekaligus latihan Rilis 2).
4. Simpan backup minimal 30 hari. Backup tidak boleh dibawa ke repo atau chat.

## 5. Rencana rollback

Prinsip: skema additive, jadi **rollback kode tidak membutuhkan rollback database**.

### Rilis 1 (hotfix keamanan), tanpa database
Kembalikan kode ke rilis sebelumnya; `php artisan config:clear` (dan `config:cache` bila dipakai). Bila penyebabnya admin terkunci, lebih cepat isi
`ADMIN_EMAIL` dan password di environment lalu `config:cache`.

### Rilis 2
1. Kembalikan kode ke Rilis 1 / rilis sebelumnya. Tabel/kolom baru diabaikan kode lama. Data tidak hilang.
2. **Jangan** `migrate:rollback` kecuali kerusakan struktur. `migrate:rollback --step=15` akan menghapus `billing_payments`, `billing_audit_logs`,
   `invoice_sequences`, `rooms`, dan kolom baru. **Backup tabel-tabel itu lebih dulu** karena berisi histori yang tidak ada di tempat lain.
3. Akibat memakai kode lama setelah Rilis 2 berjalan:
   - Invoice baru (`ledger`) tetap punya `paid_amount`, `remaining_amount`, dan status yang sinkron (net), jadi angka per invoice tetap tampil.
   - Kode lama menghitung finance berdasarkan tanggal invoice dan `paid_amount`; **refund dan pemindahan kredit tidak terlihat** di laporan lama dan
     pembayaran dihitung di bulan invoice. Tidak ada data hilang; angka kembali benar setelah kembali ke Rilis 2.
   - Nomor invoice lama berformat `INV-Ymd-His` dapat kembali dibuat oleh kode lama; unique index akan menolak nomor kembar (error, bukan data ganda).
     Bila perlu: `App\Support\InvoiceNumberIndex::drop()`.
   - Consent baru (jika sempat versi 2) akan tercetak dengan template lama oleh kode lama (kolom versi diabaikan); jangan aktifkan versi 2 sebelum stabil.
4. Closing yang sempat dibuka kembali: kolom `reopened_*` diabaikan oleh kode lama (tampak tertutup). Periksa manual.
5. Setelah rollback: `php artisan khayra:preflight`, bandingkan checksum baseline, catat penyebab sebelum mencoba lagi.

## 6. Checklist deployment

**Rilis 1 (hotfix keamanan)**
1. Persetujuan tertulis. Backup database rutin dan salinan `.env`.
2. `ADMIN_EMAIL` dan `ADMIN_PASSWORD_HASH` (atau `ADMIN_PASSWORD`) terisi di environment target; cache driver bukan `array`/`null`; tabel `cache` dan `sessions` ada.
3. `git apply --check docs/hotfix/security-hotfix-on-main.patch` lalu terapkan pada branch hotfix dari `main`; jalankan `php artisan test --filter=Security`.
4. `php artisan khayra:preflight` (0 FAIL).
5. Deploy; `php artisan config:cache` (bila dipakai).
6. Verifikasi pasca-deploy bagian 7A.

**Rilis 2**
1. Rilis 1 stabil; persetujuan tertulis; keputusan bisnis pada bagian 9 dicatat.
2. Backup penuh (bagian 4) dan catat checksum baseline.
3. Staging: pulihkan dump production, `migrate --pretend`, migrate, bandingkan checksum, `billing:audit-integrity`, uji alur penuh, uji paralel kecil.
4. Atur env: `CLINIC_TIMEZONE=Asia/Jakarta` (default sudah demikian). **Jangan ubah `APP_TIMEZONE`/`app.timezone` (tetap UTC).**
5. Production: mode pemeliharaan singkat, `migrate`, `config:cache`, `up`.
6. Verifikasi pasca-deploy bagian 7B.

## 7. Verifikasi pasca-deploy

### 7A. Setelah Rilis 1 (keamanan)
- Login admin dengan kredensial benar berhasil; salah 6 kali terblokir (gunakan akun uji, bukan akun produksi).
- `/admin/reports/therapist-performance` tanpa login mengarah ke login.
- Login therapist dan pasien normal; logout menutup sesi.
- Halaman publik `/booking` terbuka.
- Log aplikasi tidak menampilkan error baru.

### 7B. Setelah Rilis 2
- `php artisan khayra:preflight` : 0 FAIL.
- Checksum baseline identik (jumlah baris, `SUM(amount)`, `SUM(paid_amount)`, `SUM(discount_amount)`).
- `php artisan billing:audit-integrity`: bagian 10-12 (buku pembayaran) bersih; catat bagian lama (temuan data lama bukan kegagalan rilis).
- Checkout uji (pasien uji): uang diserahkan > total → kembalian benar, pendapatan = nilai bersih.
- Cicilan dua kali (tunai lalu QR) → dua baris, status partial lalu paid.
- Void invoice uji dengan refund → baris refund; arus kas bulan itu bersih; stok kembali.
- Terbitkan Ulang dengan kredit → invoice pengganti lunas dari kredit; tidak ada kas baru.
- Klik ganda tombol Simpan Checkout → satu invoice.
- Owner Finance bulan berjalan: kartu Arus Kas, Omzet, Refund tampil; bulan lama (closing lama) tidak berubah dan tidak menampilkan peringatan selisih baru.
- Tanggal invoice default dan jam di riwayat memakai WIB; nomor invoice berformat `INV-YYYYMM-NNNN`.
- WhatsApp: layar konfirmasi menampilkan nomor tujuan dan pesan minimal.
- Dropdown agama/pendidikan (desktop dan mobile); portal pasien membuka invoice miliknya sendiri.
- Master Ruangan: semua non-billable; tidak ada baris biaya ruangan otomatis pada invoice baru.

## 8. Checklist pemeriksaan production READ-ONLY (butuh persetujuan; hanya membaca, tidak menampilkan secret)

Dijalankan oleh orang yang berwenang, pada replika/baca saja bila memungkinkan:
1. `php artisan khayra:preflight`: kredensial admin terkonfigurasi (ya/tidak), `config:cache` aktif/tidak, driver cache dan sesi, versi MySQL/MariaDB, engine InnoDB, zona waktu.
2. `php artisan migrate:status` (pending apa yang ada).
3. Duplikat nomor invoice: `SELECT invoice_number, COUNT(*) c FROM billings WHERE invoice_number IS NOT NULL AND invoice_number<>'' GROUP BY 1 HAVING c>1;`
4. `SELECT DISTINCT category FROM clinic_services;` (kategori dan penulisannya; Specialist/Add-on).
5. `php artisan billing:audit-integrity` (hanya membaca): paid vs amount, uang kembalian pada data lama, dampak zona waktu, drift closing.
6. `SELECT COUNT(*) FROM billings WHERE payment_status='paid' AND (paid_amount IS NULL OR paid_amount<=0);` (data lama yang memakai fallback).
7. Akses rute `/admin/reports/therapist-performance` tanpa login dari luar (hanya satu permintaan GET, tanpa kredensial) untuk memastikan status 302.
8. Koleksi collation/engine tabel finansial dan versi MySQL (untuk menyamakan staging).

Tidak boleh: login ke production, mengubah env/konfigurasi, menjalankan migrate, atau membuka data pasien.

## 9. Keputusan yang menggerbang rilis

| Keputusan | Mengapa penting | Rekomendasi |
|---|---|---|
| Uang kembalian bukan pendapatan; invoice baru memakai buku pembayaran | sudah diimplementasikan (disetujui) | selesai |
| Kebijakan refund (siapa/alasan) | refund kini ada di UI | semua admin, alasan wajib (sudah demikian) |
| Zona waktu operasional WIB | sudah diimplementasikan | konfirmasi |
| Aturan biaya admin | sudah final (sekali per pasien saat registrasi pertama, tanpa retroaktif) | diterapkan; lihat `docs/07-aturan-final-biaya-administrasi.md` |
| Tarif/satuan ruangan | semua non-billable sampai disetujui | tetap non-billable |
| Persetujuan template consent baru | versi 2 mati | tetap versi 1 |
| Specialist eligible diskon? | saat ini tidak | tetap tidak sampai ada keputusan |
| Definisi PIC/shift | belum diimplementasikan | tunggu klien |
