# Penutupan Release Blockers — Closing Race dan Invoice Pengganti Ganda

Status: **kedua release blocker CLOSED** (belum di-commit; menunggu review). Tidak ada fitur baru dan tidak ada perubahan data historis.

## 1. Closing race

**Penyebab.** Tutup buku menghitung laporan lalu menulis snapshot sebagai dua langkah terpisah, sedangkan pembayaran, refund, invoice,
void, dan transaksi manual berjalan di transaction lain yang hanya memeriksa "periode tertutup" tanpa kunci bersama. Transaksi yang commit
di antara perhitungan dan penulisan snapshot masuk ke bulan itu tetapi tidak ke snapshot. Pemeriksaan tertutup di luar transaction juga bisa
basi (REPEATABLE READ): penulis dapat menulis ke periode yang baru saja ditutup.
Bukti bug lama: algoritma lama dijalankan di harness (`legacy`) dan menghasilkan snapshot Rp0 padahal data final Rp123.000.

**Solusi.** `App\Services\FinancePeriodLock` + `App\Services\MonthlyClosing` + tabel `finance_period_locks` (satu baris per bulan Y-m).
- **Tutup buku/buka kembali** (`MonthlyClosing`): satu transaction; langkah pertama kunci EKSKLUSIF bulan itu; status "sudah tutup" dibaca ulang; laporan dihitung dan snapshot ditulis di transaction yang sama. Tidak ada transaksi periode itu yang dapat commit di antaranya.
- **Penulis** mengambil kunci BERSAMA (`FOR SHARE`) sebagai langkah pertama di dalam transaction-nya, lalu membaca ulang status tutup buku dengan locking read (`isClosed`). Sesama penulis tidak saling menunggu; tutup buku menunggu penulis yang sedang berjalan, dan penulis baru menunggu tutup buku selesai lalu melihat periode tertutup (ditolak, tidak ada invoice mundur).
- **Periode yang dikunci per jenis transaksi** (omzet = bulan `invoice_date`, kas = bulan `paid_on` = hari bisnis):

| Transaksi | Periode terkunci |
|---|---|
| Checkout / invoice pengganti | bulan invoice_date + bulan hari ini (pembayaran awal, pemindahan kredit) |
| Invoice manual | bulan invoice_date + bulan hari ini |
| Pembayaran/cicilan | bulan hari ini saja (membayar invoice bulan lalu hanya memengaruhi kas bulan berjalan) |
| Refund | bulan hari ini |
| Void | bulan invoice_date (omzet) + bulan hari ini (refund) |
| Edit invoice / ubah status | bulan invoice lama dan baru |
| Transaksi finance manual (tambah/hapus) | bulan transaksi |
| Registrasi pasien (biaya admin) dan penerbitan kewajiban tertunda | bulan hari ini; keputusan "invoice atau pending" diambil setelah kunci |
| Tutup buku, buka kembali | bulan yang bersangkutan (eksklusif) |

- **Urutan kunci global (anti lock inversion):** (1) kunci periode, bulan terurut menaik; (2) invoice sumber koreksi / baris kewajiban; (3) sumber biaya berreferensi (visit/pasien); (4) counter nomor invoice; (5) invoice, stok, pembayaran. `PaymentLedger` mengambil kunci periode sebelum kunci invoice; `transferCredit` tetap mengunci dua invoice terurut id.
- Baris kunci dibuat di luar transaction (`prepare`) dengan baca biasa dulu agar tidak memblokir; insert di dalam transaction hanya sebagai cadangan.
- Closing historis tidak diubah; tutup ulang tetap wajib lewat buka kembali. `billing:audit-integrity` bagian 8 kini membandingkan closing dengan sumber yang sama (kas per tanggal pembayaran) sehingga tidak lagi salah menandai pembayaran invoice bulan lalu pada bulan berjalan.

## 2. Invoice pengganti ganda

**Penyebab.** Checkout memeriksa "invoice void ini sudah punya pengganti?" di luar transaction. Dua checkout paralel dengan token berbeda sama-sama lolos dan sama-sama membuat pengganti.
Bukti bug lama (mutasi, pemeriksaan di dalam transaction dicabut): 2 pengganti aktif, stok berkurang dua kali, 3 mutasi stok, `replaced_by` ganda, kredit terpakai Rp600.000 dari Rp300.000 (saldo -300.000) dan audit integrity menandainya.

**Solusi.** Di dalam transaction checkout: kunci periode → `Billing::lockForUpdate()` invoice sumber → validasi ulang (harus void, pasien sama, belum punya pengganti non-void) → baru membuat invoice, stok, dan memindahkan kredit. Checkout kedua berserialisasi di kunci invoice sumber dan ditolak (`ReplacementConflictException`, pesan "sudah pernah digantikan"). Seluruhnya satu transaction: kegagalan (mis. stok kurang) membatalkan semuanya. Aturan non-kas, audit (`created`, `replaced_by`), dan aturan koreksi berantai tidak berubah. Tidak ada constraint database baru (aturan "satu pengganti non-void" tidak dapat dinyatakan sebagai unique index tanpa melarang koreksi berantai yang sah).

## 3. Pembuktian

- **Deterministik** (`tests/Feature/Integrity/ReleaseBlockersTest.php`, 14 test aktif, SQLite dan MySQL): keadaan "closing/pengganti rival terjadi tepat setelah pemeriksaan awal" disimulasikan lewat `DB::listen`; test gagal bila pemeriksaan di dalam transaction dicabut (mutation check).
- **Paralel nyata** (`tests/Concurrency/run.php`, worker multi-proses, database MySQL sementara): 39 pemeriksaan, dijalankan otomatis oleh `ReleaseBlockersTest` bila test suite memakai MySQL. Ringkasan skenario: A pembanding algoritma lama (drift terbukti); B closing vs 9 penulis paralel (pembayaran ×2, refund, checkout, invoice manual, transaksi manual, void, registrasi) × N iterasi acak: tanpa drift, tanpa transaksi periode itu yang commit setelah closing, tanpa deadlock, kedua urutan terjadi; C bayar invoice bulan lalu di bulan terbuka saat bulan lalu ditutup; D registrasi vs closing (selalu tepat satu kewajiban: invoice atau pending); E enam checkout paralel mengganti invoice void yang sama; F replacement vs refund atas kredit yang sama; G koreksi berantai dan audit integrity.
- **Mutation check:** menonaktifkan kunci periode pada penulis → drift terdeteksi (B1, C3, C5, D4 gagal); mencabut validasi pengganti di dalam transaction → E3, E4, E9–E11, E13, F2, G3, G4 gagal.

## 4. Cara menjalankan di staging/lokal

```
php tests/Concurrency/run.php                      # perlu MySQL; membuat database sementara khayra_race_xxxx lalu menghapusnya
RACE_ITERATIONS=12 RACE_SEED=777 php tests/Concurrency/run.php
```
Variabel: `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` (server MySQL; default 127.0.0.1/3306/root). Tidak menyentuh database lain.

## 5. Keterbatasan

- Hook uji `MonthlyClosing::$afterLockHook/$afterComputeHook` hanya dipakai harness (null di aplikasi).
- Kunci periode bersifat per bulan: transaksi penulis pada bulan yang sama berjalan paralel (kunci bersama); tutup buku bulan itu menunggu penulis yang sedang berjalan (milidetik) dan penulis baru menunggu tutup buku (beberapa ratus ms).
- `innodb_lock_wait_timeout` (default 50 dtk) berlaku bila tutup buku sangat lama; closing berjalan beberapa ratus ms pada data klinik.
- Jalur pembaruan di luar yang tercantum di tabel (mis. impor data massal di luar aplikasi) harus mengikuti urutan kunci yang sama atau dijalankan saat periode tidak sedang ditutup.
