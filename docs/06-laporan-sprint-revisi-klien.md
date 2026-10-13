# Laporan Sprint Revisi Klien — Khayra Physio

Kara Digital · 12 Oktober 2026 · Branch `feat/khayra-client-revisions-final` (dari `origin/main` = `0803214`, PR #1 sudah merged).
**Belum ada commit, push, merge, deploy, maupun migration production.** Menunggu review.

Fitur dari PR sebelumnya (P0 keamanan, P1 integritas finansial, BillingCalculator, PaymentLedger, FinanceReport, ClinicTime,
master ruangan, audit billing, WhatsApp invoice, versioning consent) **tidak dibangun ulang**; sprint ini hanya melengkapi celahnya.

---

## Status tujuh revisi klien

| # | Revisi | Status | Dasar |
|---|---|---|---|
| 1 | PIC / kolom shift | **BLOCKED BY CLIENT DECISION** | Definisi PIC belum ada. Tidak ada kode; hanya audit + rekomendasi (`docs/05-rekomendasi-pic-shift.md`) |
| 2 | Dropdown agama & pendidikan | **COMPLETE** | Tes otomatis lulus; QA browser desktop + mobile; data legacy teks bebas tetap tampil dan bisa disimpan |
| 3 | Booking ruangan berbayar | **IMPLEMENTED BUT DISABLED** | Semua ruangan non-billable, tanpa tarif/satuan (menunggu persetujuan). Tidak ada penagihan otomatis |
| 4 | Invoice via WhatsApp | **COMPLETE** | Tes otomatis lulus; pesan minimal (tanpa layanan/ruangan/biaya admin/diskon/tautan invoice); QA browser |
| 5 | Diskon Program vs Add-on | **COMPLETE** | Matriks diskon lewat checkout nyata; QA browser (Rp635.000 − diskon 10% program = Rp585.000) |
| 6 | Biaya administrasi pasien baru Rp35.000 | **IMPLEMENTED BUT DISABLED BY DEFAULT** (aturan final dikonfirmasi 12 Okt; aktif hanya via `NEW_PATIENT_ADMIN_FEE_ENABLED=true`) | Tagihan unpaid otomatis saat registrasi pertama (2 jalur pembuatan pasien diaudit), kewajiban tertunda bila periode tutup, aktivasi eksplisit lewat env; pasien lama tidak ditagih; idempotensi + unique index; tes SQLite/MySQL, uji paralel, QA browser. Detail: `docs/07-aturan-final-biaya-administrasi.md` |
| 7 | Informed consent tanpa TTD fisioterapis | **IMPLEMENTED BUT DISABLED** | Template v2 + preview siap; aktif hanya setelah persetujuan tertulis klinik |

---

## A. Revisi klien yang selesai
Req 2, 4, 5 (lihat tabel). Ditambah penguatan keselamatan pembayaran (Step 7, bagian C).

## B. Menunggu persetujuan / keputusan klien
- Req 1: definisi PIC (rekomendasi dan 5 pertanyaan di `docs/05`).
- Req 3: tarif resmi dan satuan (sesi/jam/hari) per ruangan; kapan dikenakan; hubungan dengan `+ VVIP` Rp70.000 dan Booking Fee Rp50.000.
- Req 7: persetujuan tertulis template v2 + keputusan pengganti identifikasi fisioterapis (nama saja / nomor STR / cap).

## C. Implementasi baru sprint ini

**Req 3 (ruangan berbayar)**
- Laporan **Omzet per Kategori** (Owner Finance + cetak): program, add-on, specialist, biaya ruangan, biaya admin, dst., setelah diskon; total = omzet; invoice lama dikumpulkan sebagai "tanpa kategori" (tidak dihitung ulang). Void tidak dihitung. Arus kas dan closing tidak berubah.
- Satuan tarif ("Tarif per sesi/jam") tampil di baris invoice (admin, cetak, portal pasien).
- Peringatan saat ruangan visit diubah setelah biayanya ditagih (tidak menagih ulang, invoice tidak berubah; koreksi lewat Void + Terbitkan Ulang). Daftar visit kini menampilkan pesan flash.
- Banner status tarif di Master Ruangan (belum ada ruangan berbayar / N berbayar aktif; invoice lama memakai snapshot).
- Sudah ada dari PR sebelumnya dan **diaudit + diuji ulang**: master ruangan, billable/non-billable, tarif+satuan sebagai konfigurasi, saran di kasir, snapshot tarif, satu baris per visit (lock + cek di transaction), tidak didiskon.

**Req 6 (biaya admin)** — direvisi setelah aturan bisnis final (lihat `docs/07-aturan-final-biaya-administrasi.md`). Mekanisme "saran manual di kasir + eligibility_basis" yang dibuat lebih awal sprint ini **diganti** dengan penagihan otomatis saat registrasi (`PatientRegistration`).

**Req 7 (consent)**
- Preview `/admin/informed-consents/template-preview/{1|2}` (admin saja, data contoh, tidak menyimpan, ditandai PREVIEW + watermark, ada tombol di Arsip Consent). Template v2: tanpa kotak TTD fisioterapis; TTD pasien/wali, identitas pelaksana, "Dicatat oleh" + waktu dipertahankan.
- **Golden file** `tests/Fixtures/consent-v1-print.html`: cetak v1 identik byte-per-byte dengan sebelum sprint (terbukti juga saat v2 diaktifkan). `active_template_version` tetap 1.

**Step 7 (keselamatan pembayaran)**
- Migration `billing_payments.idempotency_key` + **unique index database**.
- `PaymentLedger::recordPayment/refund` idempoten: pengiriman ulang dengan token sama mengembalikan baris yang sudah ada (pesan "sudah tercatat sebelumnya"), dicek di dalam transaction setelah invoice dikunci, plus fallback saat unique constraint tertabrak. Token yang sama untuk invoice/jenis lain ditolak.
- Rute pembayaran dan refund mewajibkan token; formulir membuat UUID baru tiap tampil, tombol dikunci setelah submit (dibuka lagi saat halaman dipulihkan dari tombol Back). Billing manual kini memakai `checkout_token` seperti checkout kasir. Void diulang tetap menghasilkan satu refund. Error validasi kini tampil di halaman detail invoice.
- Diuji konkurensi nyata di MySQL: 8 proses paralel dengan token sama → 1 baris (1 CREATED + 7 REPLAY); 8 proses dengan token berbeda dan melebihi sisa tagihan → total tepat Rp500.000, 4 ditolak "sudah lunas", tidak ada kelebihan bayar.
- `PaymentLedger` tetap sebagai satu-satunya penulis; tidak ada mekanisme pembayaran baru.

**QA mobile:** memperbaiki overflow horizontal 18px pada form Void di halaman detail invoice (cacat lama).

## D. File yang berubah
- Kode: `app/Services/NewPatientAdminFee.php` (baru), `PaymentLedger.php`, `FinanceReport.php`, `ReferencedChargeGuard.php`, `app/Models/BillingPayment.php`, `config/clinic.php`, `routes/web.php`.
- View: `admin-billing-detail`, `admin-billing-create`, `admin-cashier-create`, `admin-consent-archive`, `admin-informed-consent-print`, `admin-owner-finance`, `admin-owner-finance-print`, `admin-rooms`, `admin-visits`, `partials/billing-item-category`.
- Migration: `2026_10_14_100000_add_idempotency_key_to_billing_payments_table.php` (baru).
- Tes: `ClientRevisionsTest` (+16), `Integrity/PaymentIdempotencyTest` (baru, 14), `Integrity/ReleaseBlockersTest` (baru, 2 todo), `tests/Fixtures/consent-v1-print.html` (baru), `tests/Pest.php` (helper `idem()`), 3 file tes Integrity disesuaikan (token pada request pembayaran/refund).
- Dokumen: `docs/05-rekomendasi-pic-shift.md`, `docs/06-…` (ini), `docs/04-…` (tabel migration).

## E. Migration
Tiga migration baru, additive: `billing_payments.idempotency_key`, `patients.registration_token` (kolom nullable + unique index) dan tabel `pending_registration_fees` (unique `patient_id`). `migrate --pretend` hanya 2 DDL (`add column`, `add unique`), tanpa UPDATE/DELETE. Rollback lalu migrate ulang di MySQL: checksum `billings` dan `billing_payments` identik; `billing:audit-integrity` bersih. Total sekarang 14 migration additive pada Rilis 2 (`docs/04` diperbarui).

## F. Hasil automated tests
| Lingkungan | Hasil |
|---|---|
| SQLite (`:memory:`) | **286 lulus**, 2 todo (release blockers), 1925 assertion (baseline sebelum sprint: 228 lulus) |
| MySQL 8.4.11 (database uji khusus, sudah dihapus) | **286 lulus**, 2 todo |
| Mutation check | Menonaktifkan pemeriksaan replay → test gagal; unique index sendiri masih menahan duplikat (defense in depth) |
| Konkurensi MySQL (8 proses paralel) | Lihat bagian C |
| Golden file consent v1 | Identik byte-per-byte |
Catatan: Pint hanya dijalankan pada file tes baru; `routes/web.php` memang tidak ber-format Pint sejak awal.

> Catatan: butir QA di bagian ini yang menyangkut saran/eligibility biaya administrasi dilakukan sebelum aturan final dan sudah digantikan; QA aturan final ada di `docs/07-aturan-final-biaya-administrasi.md` §7.

## G. Hasil browser QA (server lokal, DB SQLite sementara, fitur nonaktif dinyalakan hanya di proses QA)
- Desktop: login admin; Master Ruangan (banner, tarif); kasir dengan pasien baru → indikator "memenuhi syarat", kedua tombol saran menambah baris, tombol hilang setelah dipakai; checkout Program + ruangan + admin dengan promo 10% = Rp585.000, kembalian Rp15.000; invoice menampilkan "Biaya Ruangan · Tarif per sesi · Tidak termasuk diskon"; Owner Finance "Omzet per Kategori" (450.000 + 35.000 + 100.000 + 200.000 lama = 785.000); preview consent v2; pembayaran cicilan lalu kirim ulang token sama → "sudah tercatat sebelumnya", 1 baris.
- Mobile 375px: kasir (pasien legacy → indikator "punya riwayat", tanpa tombol), Master Ruangan, Owner Finance, detail invoice (setelah perbaikan), cetak invoice, preview consent, form pasien (dropdown agama/pendidikan; nilai teks bebas legacy tetap terpilih), daftar visit, layar WhatsApp: **tanpa overflow horizontal**.
- Tidak diuji: pembukaan WhatsApp nyata (wa.me), cetak ke printer, browser selain Chromium bawaan.
- Data uji dan kredensial QA hanya di folder sementara di luar repo.

## H. Risiko yang masih tersisa

### Release blockers — CLOSED (lihat `docs/08-penutupan-release-blockers.md`)
1. **Closing race — CLOSED.** Tutup buku dan semua penulis finansial memakai kunci periode (`FinancePeriodLock`) di dalam transaction; laporan dihitung setelah kunci eksklusif. Dibuktikan dengan uji paralel nyata di MySQL.
2. **Invoice pengganti ganda — CLOSED.** Invoice sumber dikunci dan validasi diulang di dalam transaction. Dibuktikan dengan uji paralel nyata di MySQL.

### Lainnya
- Token pembayaran/refund kini wajib. Halaman invoice yang sudah terbuka sebelum deploy akan menolak submit ("formulir kedaluwarsa"); muat ulang. Dua tab terbuka menghasilkan dua token berbeda (dua niat pembayaran sungguhan), jadi tetap dibatasi oleh sisa tagihan.
- Perubahan tarif ruangan tidak tercatat di jejak audit (hanya invoice yang menyimpan snapshot). Pertimbangkan log perubahan tarif saat tarif disetujui.
- Dua pendaftaran terpisah (token berbeda) untuk orang yang sama menghasilkan dua pasien dan dua tagihan administrasi; tidak ada deteksi duplikat orang. Koreksi lewat Void.
- Pesan flash `warning` hanya ditampilkan di daftar visit (satu-satunya tujuan redirect yang dipakai).
- Logo `khayra-logo.png` tidak termuat di lingkungan QA lokal (aset tidak ada di worktree; tidak berkaitan dengan perubahan ini).

## I. Kesiapan staging
**Siap diuji di staging** sebagai Rilis 2, dengan fitur baru tetap mati (ruangan non-billable, admin fee nonaktif, consent v1). Prasyarat sebelum production, selain Rilis 1 (hotfix keamanan) yang stabil dan langkah backup di `docs/04`: jalankan `migrate --pretend` pada salinan data production di staging, dan bandingkan checksum baseline.

## J. Keputusan klien yang diperlukan
1. **PIC**: siapa, di layar mana, per shift atau per hari (5 pertanyaan di `docs/05`).
2. **Ruangan berbayar**: ruangan mana; tarif; satuan; kapan dikenakan (booking/hadir/selesai); perlakuan saat batal/reschedule; apakah sama dengan `+ VVIP`/Booking Fee; ikut paket?
3. **Biaya admin Rp35.000**: SUDAH FINAL (tidak ada keputusan tersisa). Pertanyaan operasional kecil: apakah pasien yang didaftarkan ganda secara tidak sengaja (dua baris pasien untuk orang yang sama) boleh diwaive lewat Void.
4. **Consent v2**: persetujuan tertulis klinik/legal atas teks (gunakan halaman preview), TTD pasien/wali tetap wajib atau perlu saksi, pengganti identifikasi fisioterapis, nasib consent `pending`.
5. **Internal (Kara Digital)**: jadwal perbaikan dua release blocker dan persetujuan merge/push setelah review ini.
