# Rancangan Pembayaran, Refund, dan Analisis Zona Waktu — Khayra Physio

Deliverable Workstream B. **Ini rancangan dan analisis dampak. Belum ada tabel `billing_payments`, belum ada perubahan
semantik keuangan, dan zona waktu aplikasi belum diubah.** Semua perubahan menunggu persetujuan.

> **Status implementasi (Finalization Sprint, setelah persetujuan Kara Digital):** rancangan bagian 2-4 dan 7 sudah **diimplementasikan secara lokal** dan
> diuji (227 test lulus di SQLite dan MySQL 8.4; konkurensi 20-80 proses): tabel `billing_payments` (`PaymentLedger`), pemisahan omzet vs arus kas
> (`FinanceReport`), refund dan pemindahan kredit non-kas, serta opsi A zona waktu (`ClinicTime`, penyimpanan tetap UTC). Invoice lama bertanda `legacy`
> dan **tidak dihitung ulang**. Temuan F1-F5 di bawah berlaku untuk invoice lama (dijaga sebagai test karakterisasi) dan **teratasi untuk invoice baru**.
> Backfill invoice lama (fase 5) **tidak dilakukan**. Rincian rilis ada di `04-rencana-rilis-dan-verifikasi.md`.

## 1. Kondisi saat ini (terbukti dengan test karakterisasi)

Satu invoice hanya memiliki satu kolom `paid_amount` (+ `change_amount`, `remaining_amount`, `payment_status`). Tidak ada tabel pembayaran.
Test `tests/Feature/Integrity/FinancialCharacterizationTest.php` mendokumentasikan perilaku berikut:

| # | Temuan | Bukti | Dampak |
|---|---|---|---|
| F1 | **Uang kembalian dihitung sebagai pendapatan.** Bayar Rp600.000 untuk invoice Rp550.000 menyimpan `paid_amount` 600.000 dan `change_amount` 50.000; laporan finance mengambil `paid_amount` sehingga pemasukan = 600.000. | test lulus | Pemasukan terlalu tinggi sebesar total kembalian. Bisa signifikan pada pembayaran tunai. |
| F2 | **Void menghapus uang diterima dari laporan finance tanpa catatan refund.** Setelah void, `paid_amount` dinolkan dan invoice dikeluarkan dari pemasukan; tidak ada pengeluaran/refund tercatat. Jumlah yang pernah dibayar hanya ada di `billing_audit_logs`. | test lulus | Bila uang dikembalikan: benar secara kas tetapi tak tercatat sebagai refund. Bila tidak dikembalikan (void-lalu-terbit-ulang): kas hilang dari laporan sampai invoice pengganti dibuat. |
| F3 | **Pembayaran dicatat di bulan invoice, bukan bulan uang diterima.** Invoice bulan lalu yang dilunasi hari ini masuk ke pemasukan bulan lalu; bulan ini 0. | test lulus | Laporan bulanan tidak mencerminkan arus kas; bila bulan lalu sudah tutup buku, pelunasan diblokir sampai buku dibuka (lihat F6). |
| F4 | **Dashboard dan Owner Finance berbeda** untuk invoice lama berstatus `paid` dengan `paid_amount` 0: finance memakai fallback `amount` (300.000), dashboard menjumlah `paid_amount` mentah (0). | test lulus | Dua angka "terbayar bulan ini" yang berbeda di dua halaman. |
| F5 | Pembayaran sebagian: pemasukan = `paid_amount`, sisa = `remaining_amount`; tidak ada histori cicilan (tanggal, metode, pelaku). Satu-satunya cara menambah pembayaran adalah menandai lunas. | test lulus | Cicilan kedua dan seterusnya tidak dapat dicatat bertahap. |
| F6 | Pelunasan invoice di bulan yang sudah tutup buku diblokir (P1). Tanpa mekanisme pembayaran bertanggal, satu-satunya jalan adalah membuka buku. | tes P1 | Perlu kebijakan (bagian 6). |
| F7 | Rumus "uang terbayar" sebelumnya disalin di 13 closure; kini satu sumber `App\Services\BillingMoney` (perilaku identik, dibuktikan 180 kombinasi). Dashboard (`sum('paid_amount')`) dan halaman detail pasien masih menjumlah mentah (F4). | test lulus | Konsistensi parsial. |

Yang **sudah** konsisten: seluruh halaman finance, closing, dan laporan memakai `BillingMoney`; total checkout, diskon, dan item satu sumber (`BillingCalculator`).

## 2. Rancangan data

### 2.1 Pemetaan konsep → data

| Konsep | Sekarang | Rancangan |
|---|---|---|
| Invoice amount | `billings.amount` | tetap |
| Tendered amount (uang diserahkan) | tercampur di `paid_amount` | `billing_payments.tendered_amount` |
| Change amount | `billings.change_amount` | `billing_payments.change_amount` |
| Net payment received | tidak ada (`paid_amount` termasuk kembalian) | `billing_payments.amount` = tendered − change |
| Refund amount | tidak ada | baris `type = refund` (nilai positif, mengurangi total terbayar) |
| Outstanding balance | `remaining_amount` (bisa basi) | **diturunkan**: `amount − (Σ payment − Σ refund)`, disalin ke `remaining_amount` untuk kompatibilitas |
| Payment timestamp | tidak ada | `paid_at` (UTC, timestamp) + `paid_on` (tanggal bisnis WIB, date) |

### 2.2 Tabel `billing_payments` (USULAN, belum dibuat)

```
id
billing_id            FK ke billings, index
type                  payment | refund                 -- append-only, koreksi dengan baris baru
method                cash|qr|debit|credit|bank_*|insurance
tendered_amount       decimal(12,2)                    -- uang yang diserahkan (payment) / dikembalikan (refund)
change_amount         decimal(12,2) default 0
amount                decimal(12,2)                    -- net: tendered - change  (> 0)
paid_at               timestamp                        -- UTC
paid_on               date                             -- tanggal bisnis WIB, dasar laporan kas dan periode closing
reference             string null                      -- no. referensi QR/transfer
reason                text null                        -- wajib untuk refund
recorded_by           string                           -- email admin (bukan akun baru)
reverses_payment_id   FK null                          -- refund menunjuk pembayaran asal
created_at
```
Batasan: `amount > 0`; `amount = tendered − change`; refund total pada satu invoice tidak boleh melebihi total payment.
Baris tidak diubah/dihapus (model append-only seperti `BillingAuditLog`).

Kolom tambahan di `billings` (nullable, default `legacy`): `payment_model` (`legacy` | `ledger`) agar invoice lama tidak
pernah dihitung ulang (bagian 4).

### 2.3 Invarian yang dijaga
- `paid_total = Σ payment.amount − Σ refund.amount`; `outstanding = amount − paid_total` (≥ 0).
- Status diturunkan: 0 → `unpaid`; 0 < paid < amount → `partial`; paid ≥ amount → `paid`.
- `billings.paid_amount` tetap diisi (= `paid_total`) selama masa transisi agar view/laporan lama tidak rusak.
- Pemasukan kas bulan M = Σ payment.amount dengan `paid_on` di bulan M − Σ refund.amount dengan `paid_on` di bulan M.

## 3. Alur void dan refund

| Situasi | Rancangan |
|---|---|
| Void invoice **belum dibayar** | Void biasa (seperti sekarang). Tidak ada refund. |
| Void invoice **sudah dibayar sebagian/lunas**, uang dikembalikan | Void + otomatis membuat baris `refund` sebesar `paid_total` (metode dan alasan wajib). Pemasukan bulan refund berkurang (bukan bulan invoice). |
| Void invoice dibayar lalu **diterbitkan ulang** (koreksi) | Void + pembayaran dipindahkan sebagai **kredit** ke invoice pengganti: refund pada invoice lama dan payment pada invoice baru dengan `reference` saling menunjuk, tanpa pergerakan kas nyata. Selisih (lebih/kurang) menjadi tagihan atau refund baru. |
| Refund sebagian (mis. pasien batal 1 sesi) | Baris `refund` dengan alasan; invoice tidak di-void; status turun otomatis ke `partial` bila perlu. |
| Bulan pembayaran asal sudah tutup buku | Refund/pembayaran dicatat pada **bulan berjalan** (`paid_on`), sehingga bulan tertutup tidak berubah dan tidak perlu membuka buku. Ini menyelesaikan F6. |

Dengan model ini, F2 hilang: uang yang diterima tidak pernah menghilang, selalu ada pasangan payment/refund bertanggal.

## 4. Rencana migrasi (bertahap, tanpa menghitung ulang laporan lama)

| Fase | Isi | Reversible | Risiko |
|---|---|---|---|
| 0 | Audit read-only: `billing:audit-integrity` (sudah ada) di staging dan production (dengan persetujuan): jumlah invoice kelebihan bayar, paid=0, dan dampak zona waktu. Putuskan kebijakan. | n/a | Nol |
| 1 | Tambah tabel `billing_payments` + kolom `billings.payment_model` (default `legacy`). Tanpa perubahan perilaku. | Ya (additive) | Rendah |
| 2 | **Dual-write untuk invoice baru**: checkout/pelunasan menulis `billing_payments` (net, tendered, change) dan tetap mengisi `paid_amount` = net. Invoice baru bertanda `ledger`. Job rekonsiliasi harian (read-only) membandingkan. | Ya (matikan flag) | Menengah |
| 3 | Laporan baru (finance, closing, dashboard) menjumlahkan: invoice `legacy` → rumus `BillingMoney` yang sekarang; invoice `ledger` → `billing_payments`. Satu layanan `BillingMoney` tetap satu sumber. | Ya | Menengah |
| 4 | UI: pelunasan/cicilan bertahap, refund, dan riwayat pembayaran di detail invoice. | Ya | Menengah |
| 5 (opsional) | Backfill invoice lama ke `billing_payments`. **Hanya dengan persetujuan eksplisit** dan setelah closing historis dibekukan; tendered/kembalian invoice lama tidak dapat dipulihkan dari data (`paid_amount` tercampur). | Sulit | Tinggi |

Prinsip: invoice historis dan closing yang sudah ada **tidak diubah dan tidak dihitung ulang**; angka lama tetap dapat direproduksi
karena rumus `legacy` dipertahankan dan dijaga test karakterisasi.

## 5. Analisis dampak

| Modul | Dampak | Pengaman |
|---|---|---|
| Kasir / checkout | Input uang diserahkan; sistem menghitung kembalian dan menyimpan net | test karakterisasi diperbarui eksplisit hanya untuk invoice `ledger` |
| Detail/cetak/invoice pasien | Menampilkan riwayat pembayaran; "Paid" = net | tampilan `legacy` tidak berubah |
| Owner finance + closing | Pemasukan per `paid_on` untuk invoice `ledger`; snapshot closing lama tidak berubah | closing aktif tidak disentuh; drift tetap terdeteksi |
| Dashboard/laporan | Memakai `BillingMoney` (satu sumber) | menghilangkan F4 |
| Void/koreksi | Void-lalu-terbit-ulang membawa kredit pembayaran | audit log tetap |
| WhatsApp | Sisa tagihan dari `outstanding` | tidak ada |
| Audit | Pembayaran/refund ikut `billing_audit_logs` | append-only |

## 6. Keputusan yang diperlukan (dengan rekomendasi)

1. **Uang kembalian**: setujui bahwa pemasukan = net (tendered − kembalian) untuk invoice baru? *Rekomendasi: ya.* Angka historis tidak diubah.
2. **Pembuatan tabel `billing_payments`** (bagian 2.2) dan fase 1–4? *Rekomendasi: ya, bertahap, setelah staging.*
3. **Dasar laporan**: kas (tanggal uang diterima) atau akrual (tanggal invoice)? *Rekomendasi: finance/closing berbasis kas (`paid_on`), laporan omzet berbasis tanggal invoice; tampilkan keduanya berlabel jelas.*
4. **Kebijakan refund**: siapa yang boleh, alasan wajib, batas jumlah? *Rekomendasi: semua admin dengan alasan wajib dan tercatat; tanpa role baru.*
5. **Pembayaran untuk invoice bulan tertutup**: dicatat di bulan berjalan (rekomendasi, butuh #2) atau tetap membuka buku?
6. Backfill invoice lama (fase 5): *rekomendasi: tidak dilakukan.*

---

## 7. Analisis zona waktu (UTC vs WIB)

### 7.1 Fakta
- `config/app.php` memakai `'timezone' => 'UTC'` (hard-code, bukan dari env). Klinik beroperasi di WIB (UTC+7).
- Laravel menyimpan kolom `timestamp` (`created_at`, `voided_at`, `closed_at`, …) sebagai string waktu **dalam zona waktu aplikasi tanpa penanda zona**.
  Jadi semua timestamp lama di database berisi waktu UTC.
- Kolom `date` (`invoice_date`, `booking_date`, `visit_date`, …) adalah tanggal polos tanpa zona.
- Jumlah penggunaan di kode: `now()` 52 kali di `routes/web.php` dan 26 di view; `toDateString` 48; `whereBetween` 29; `startOfMonth` 14; `whereDate` 26; `Carbon::parse` 27.

### 7.2 Titik yang terdampak (antara 00:00–07:00 WIB, "hari ini" masih kemarin)

| Area | Penggunaan | Dampak |
|---|---|---|
| Tanggal invoice default | form kasir dan billing manual memakai tanggal UTC | Invoice dini hari bertanggal kemarin. **Di akhir bulan, bertanggal bulan lalu.** |
| Periode closing dan penguncian | bulan ditentukan dari `invoice_date` | Invoice 1 Nov 03:00 WIB bertanggal 31 Okt: terhitung bulan Oktober; **diblokir bila Oktober sudah tutup buku**. |
| Nomor invoice | periode `YYYYMM` dari `now()` UTC | Nomor memakai bulan UTC. |
| Dashboard/booking | `todayBookings`, kalender, jadwal terapis, "hari ini" | Hitungan harian bergeser dini hari. |
| Tampilan waktu | audit log, void, closing (`format('Y-m-d H:i')`) | Staf melihat waktu UTC (mis. 17:14) padahal di klinik 00:14 hari berikutnya (terlihat di uji tampilan). |
| Laporan bulanan default | `now()->format('Y-m')` | Bulan default bergeser pada hari pertama. |
| Pesan WhatsApp | tanggal invoice | tanggal mengikuti `invoice_date` |

Perintah `php artisan billing:audit-integrity` (bagian 9) menghitung berapa invoice lama yang bertanggal hari UTC padahal dibuat pada hari
berikutnya menurut WIB, dan berapa yang melintasi batas **bulan**. Itu ukuran dampak nyata pada data production dan harus dijalankan (read-only, dengan persetujuan).

### 7.3 Mengapa **tidak** langsung mengubah `app.timezone`
Mengganti ke `Asia/Jakarta` membuat Laravel membaca string timestamp lama (yang sebenarnya UTC) sebagai WIB: seluruh `created_at`/`voided_at`/`closed_at`
historis bergeser 7 jam menjadi salah, dan perbandingan rentang `whereBetween(created_at)` ikut berubah. Karena itu perubahan ini membutuhkan
konversi data atau pendekatan lain, bukan sekadar mengganti satu baris config.

### 7.4 Opsi

| Opsi | Cara | Kelebihan | Kekurangan |
|---|---|---|---|
| **A (direkomendasikan)** | Biarkan penyimpanan UTC. Tambahkan helper tunggal `ClinicTime` (`today()`, `now()`, `monthKey()`, `display($timestamp)`) yang memakai `Asia/Jakarta`, dan pakai untuk **tanggal bisnis** (default tanggal invoice, periode closing, nomor invoice, "hari ini", tampilan waktu). | Tidak ada data historis yang bergeser; perubahan bertahap per titik | Perlu menyentuh ±50–100 titik; harus disiplin memakai helper |
| B | Ganti `app.timezone` + migrasi konversi seluruh kolom timestamp (+7 jam) | Satu pengaturan | Berisiko tinggi, harus mengubah data historis (ditolak oleh prinsip "jangan ubah data lama") |
| C | Biarkan UTC | Tanpa risiko perubahan | Masalah dini hari/akhir bulan tetap ada |

Invoice yang sudah ada **tidak diubah tanggalnya** pada opsi mana pun. Setelah opsi A, invoice baru memakai tanggal WIB; closing lama tidak berubah.

### 7.5 Keputusan
7. Pilih opsi zona waktu (*rekomendasi: A*), dan tetapkan aturan: **tanggal bisnis klinik = WIB**.
8. Izin menjalankan audit read-only di production untuk mengukur dampak (jumlah invoice terdampak dan yang melintasi batas bulan).

## 8. Rencana implementasi setelah persetujuan (urutan)

1. Helper `ClinicTime` + pengujian titik tanggal bisnis (opsi A) — tidak mengubah data.
2. `billing_payments` fase 1–2 (staging dulu) dengan flag fitur.
3. Fase 3–4 (laporan dan UI), lalu verifikasi rekonsiliasi.
4. Tinjau ulang closing bulan berikutnya dengan metode baru (hanya periode baru).
