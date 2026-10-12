# Aturan Final Biaya Administrasi Pasien Baru — Implementasi

Keputusan bisnis (12 Okt 2026): **Rp35.000 dikenakan SATU KALI saat pasien pertama kali didaftarkan ke sistem.**
Dokumen ini menggantikan bagian Req 6 pada `docs/03` dan pendekatan "saran manual + eligibility_basis" yang sempat dibuat di sprint yang sama
(sudah dihapus dari kode). **Belum ada commit/push/deploy.**

## 1. Audit jalur pembuatan pasien

| Jalur | Ada? | Perlakuan |
|---|---|---|
| Form admin `POST /admin/patients` | Ya | Lewat `PatientRegistration::register` + token formulir |
| Create Patient dari booking `POST /admin/bookings/{id}/create-patient` | Ya | Lewat `PatientRegistration::register`, token `booking:{id}`, booking dikunci dan ditautkan dalam transaction yang sama |
| Booking publik `POST /booking` | Tidak membuat pasien | Hanya menautkan ke pasien yang cocok (WhatsApp/nama); bila tidak cocok, `patient_id` kosong. Tidak ada tagihan |
| Booking admin, visit, kasir, edit pasien, update consent | Tidak membuat pasien | Tidak menyentuh biaya administrasi |
| Self-registration / portal pasien | **Tidak ada** (portal hanya login) | — |
| Seeder, perintah artisan, `Patient::create`/`insert` di kode aplikasi | Tidak ada | Dijaga test pemindai (lihat §4) |

## 2. Aturan → implementasi

| # | Aturan | Implementasi |
|---|---|---|
| 1 | Registrasi pertama → tagihan Rp35.000 | `PatientRegistration` membuat pasien **dan** invoice dalam satu transaction; nominal dari `clinic.new_patient_admin_fee.amount` (35000) |
| 2 | Pasien lama tidak ditagih retroaktif | Tidak ada migrasi/backfill. Pasien lama tidak punya token → status `not_applicable`; kasir menolak baris administrasi untuk mereka |
| 3 | Kunjungan berikutnya tidak ditagih | Tagihan hanya dibuat di jalur registrasi; kunjungan/booking/invoice biasa tidak menyentuhnya; penambahan manual ditolak selama invoice administrasi aktif |
| 4 | Reschedule/batal tidak membuat tagihan | Tidak ada kode penagihan di jalur booking; diuji (update waktu, cancel, no_show) |
| 5 | Kategori `admin_fee` terpisah | Baris item tersendiri: `item_category=admin_fee`, `reference_type=patient_admin_fee` |
| 6 | Tanpa diskon | `admin_fee` tidak ada di `discountable_categories`; invoice registrasi tanpa promo; baris administrasi tetap Rp0 diskon walau ditagihkan bersama Program berpromo (jalur koreksi) |
| 7 | Pembayaran lewat PaymentLedger | Invoice `payment_model=ledger`; dibayar lewat `/admin/billings/{id}/payments` (cicilan, kembalian, refund) tanpa mekanisme baru |
| 8 | Belum bayar → tetap unpaid | Status awal `unpaid`, `remaining_amount=35000`, tidak ada baris pembayaran |
| 9 | Idempotensi + perlindungan database | Lihat §3 |
| 10 | Koreksi/void tetap beraudit | Void + Terbitkan Ulang (mekanisme existing); audit `created` (source `patient_registration`) dan `voided`; refund/kredit wajib diputuskan bila sudah dibayar |

## 3. Perlindungan duplikasi (berlapis)

1. **Token registrasi** `patients.registration_token` (**unique index**, migration `2026_10_14_110000`). Form admin membuat UUID baru tiap tampil dan mewajibkannya (formulir lama tanpa token ditolak: "muat ulang"); jalur booking memakai `booking:{id}`. Token yang sama mengembalikan pasien yang sudah ada tanpa membuat apa pun.
2. **Satu transaction**: pasien + invoice + item + audit gagal/berhasil bersama (diuji: kegagalan invoice → pasien tidak tersimpan).
3. **Token tagihan** `billings.checkout_token = registration-fee:{id pasien}` (**unique index** yang sudah ada). Token ini tetap ada walau invoice di-void, sehingga biaya tidak otomatis tertagih lagi dan status pasien tetap tercatat.
4. **Fallback race**: bila dua request lolos bersamaan sampai insert, unique index menolak yang kedua dan service mengembalikan pasien pemenang.
5. Tombol Simpan dikunci setelah submit; dibuka lagi saat halaman dipulihkan dari tombol Back (token tetap menjaga server).

Mutation check: dengan pemeriksaan token di aplikasi dimatikan, seluruh test tetap lulus karena unique index database menahan duplikat.

## 4. Pagar untuk jalur masa depan

`PatientRegistrationFeeTest` memindai `routes/` dan `app/`: hanya **dua** rute yang menyimpan `new Patient`, keduanya wajib memanggil `PatientRegistration::register`, dan tidak boleh ada `Patient::create/firstOrCreate/updateOrCreate/insert` atau `DB::table('patients')->insert` di kode aplikasi. Jalur pendaftaran baru yang melewati service akan menggagalkan test.

## 5. Kasir dan koreksi

- Saran manual "+ Biaya administrasi pasien baru" **dihapus**. Kasir hanya menampilkan indikator: *sudah ditagihkan pada invoice X (status)* / *invoice di-void* / *pasien lama: tidak dikenakan*.
- Koreksi: Void invoice administrasi → status `voided` (tidak ditagih otomatis lagi) → **Terbitkan Ulang** membawa baris administrasi (referensi dipertahankan) → invoice pengganti tertaut ke yang di-void. Baris administrasi di kasir hanya sah bila pasien punya tagihan registrasi (token) dan tidak ada invoice non-void lain yang memuatnya.

## 6. Perilaku tepi yang perlu diketahui

- **Periode tutup buku**: kewajiban **tidak hilang**; lihat §8 (kewajiban tertunda).
- **Orang yang sama didaftarkan dua kali** (dua pendaftaran terpisah dengan token berbeda) = dua pasien = dua tagihan. Tidak ada deteksi duplikat orang; koreksi lewat Void.
- **Saklar**: lihat §9 (aktivasi eksplisit lewat environment, default mati).
- Registrasi tidak langsung menerima pembayaran; kasir membayar lewat halaman invoice (alur existing).

## 7. Pengujian

- `tests/Feature/Integrity/PatientRegistrationFeeTest.php`: 23 test (registrasi admin, dari booking, klik ulang, token ganda/tanpa token, unique index, atomisitas, pasien lama, kunjungan berikutnya, reschedule/batal, booking publik, pembayaran/cicilan/kembalian, laporan kategori, void/terbit ulang/refund, saklar, nominal konfigurasi, periode tutup, data lama tidak berubah, indikator kasir, pagar jalur pembuatan).
- Seluruh suite: SQLite **286 lulus**, MySQL 8.4 **286 lulus**, 2 todo (release blockers, lihat `docs/06`).
- Uji paralel MySQL: 8 proses dengan token sama → 1 pasien + 1 invoice (7 REPLAY); 12 proses dengan token berbeda → 12 pasien, 12 tagihan, nomor invoice unik semua.
- Migration (3 migration sprint ini):  rollback + migrate ulang pada data berisi → checksum pasien/invoice identik; `billing:audit-integrity` bersih.
- Browser (desktop + mobile 375px): registrasi lewat form dengan submit ganda → satu pasien; flash menampilkan nomor tagihan; Create Patient dari booking; indikator kasir (pasien baru/lama); pembayaran Rp50.000 → lunas, kembalian Rp15.000; tanpa overflow horizontal.

## 8. Kewajiban tertunda (periode tutup buku saat registrasi) — penutup P0

**Masalah:** registrasi saat periode berjalan sudah tutup buku sebelumnya tetap berhasil tetapi tidak membuat invoice, sehingga kewajiban Rp35.000 bisa hilang.

**Solusi:** tabel persisten `pending_registration_fees` (migration `2026_10_14_120000`):
- Satu baris per pasien (**unique `patient_id`**), dibuat dalam **transaction yang sama** dengan pasien (gagal bersama). Kolom: nominal (snapshot), status `pending|issued`, `closed_month`, alasan, pembuat, invoice terkait, waktu dan petugas penerbitan. Baris tidak pernah dihapus.
- Audit: `registration_fee_pending` (saat dicatat, `finance_month` terisi) dan `registration_fee_issued` (di invoice) pada `billing_audit_logs`.
- **Tidak ada invoice mundur dan closing tidak disentuh**: invoice hanya bertanggal hari penerbitan dan hanya terbit bila periode hari itu terbuka (`BillingIntegrity::isPeriodClosed`). Setelah bulan berganti, atau periode dibuka kembali lewat prosedur existing, admin menerbitkan. Selama masih tutup: ditolak dengan pesan jelas dan kewajiban tetap pending.
- **Tanpa duplikasi**: penerbitan mengunci baris pending (`lockForUpdate`); penerbitan kedua melihat `issued` dan mengembalikan invoice yang sama. Lapis terakhir: unique `checkout_token = registration-fee:{pasien}` pada invoice. Registrasi ulang dengan token sama tidak membuat pasien/kewajiban baru.
- **Tanpa pembayaran otomatis**: invoice terbit `unpaid`; dibayar lewat alur PaymentLedger yang ada.
- **Registrasi tetap bisa dilakukan** saat periode tutup (kebutuhan operasional), dengan peringatan jelas di layar.
- **Visibilitas admin**: menu **Biaya Admin Tertunda** (`/admin/registration-fees`) dengan lencana jumlah di sidebar; daftar "Menunggu Diterbitkan" (tombol Terbitkan Invoice, nonaktif selama periode tutup) dan riwayat "Sudah Diterbitkan"; kasir menampilkan status "TERTUNDA" dan menolak penambahan manual. `billing:audit-integrity` bagian 13 melaporkan jumlah/total tertunda dan mendeteksi data tidak konsisten (pending padahal invoice ada; issued tanpa invoice).
- Pasien lama tidak pernah punya kewajiban tertunda (tidak ada backfill).

**Bukti:** 15 test baru (registrasi tertutup dari form dan dari booking, token ganda, unique DB, atomisitas, ditolak saat tutup, terbit setelah bulan berganti dan setelah dibuka kembali dengan tanggal hari ini, penerbitan ganda, pembayaran lewat ledger, pasien lama, saklar, daftar/sidebar, audit command). Uji paralel MySQL: registrasi periode-tutup 8 proses token sama → 1 pasien + 1 kewajiban; 6 token berbeda → 6; penerbitan #1 oleh 8 proses bersamaan → 1 invoice (7 REPLAY); 12 penerbitan (2 proses per kewajiban) → 6 invoice, 6 REPLAY; semua nomor invoice dan token unik, 0 pembayaran.

## 9. Keselamatan konfigurasi

`config/clinic.php`: `'enabled' => (bool) env('NEW_PATIENT_ADMIN_FEE_ENABLED', false)`. **Default MATI**; aktif hanya bila `NEW_PATIENT_ADMIN_FEE_ENABLED=true` diisi eksplisit di environment lingkungan itu. `.env.example` memuat `NEW_PATIENT_ADMIN_FEE_ENABLED=false` beserta penjelasan; `phpunit.xml` menyalakannya khusus untuk tes. `php artisan khayra:preflight` menampilkan status (AKTIF/NONAKTIF). Nominal tetap Rp35.000 dan aturan satu kali saat registrasi pertama tidak berubah. Rekomendasi: nyalakan di staging dahulu; production hanya setelah keputusan sadar. Perhatikan `config:cache`: ubah env sebelum `config:cache`.
