# Rancangan Revisi Klien: Req 1, 3, 6, 7 — Khayra Physio

Deliverable Workstream C. Berisi audit kondisi existing, rancangan implementasi, dan pertanyaan bisnis.
**Tidak ada kode yang mengaktifkan penagihan otomatis, tidak ada tarif/satuan/ruangan berbayar yang diasumsikan,
dan template informed consent lama tidak diubah.** Semua rancangan bersifat additive dan nonaktif secara default.

Prinsip bersama:
- Fitur baru dimatikan sampai klinik menyetujui aturannya (flag di `config/clinic.php`).
- Data lama tidak diubah. Perubahan skema hanya menambah tabel/kolom nullable.
- Perhitungan keuangan tetap lewat satu sumber (`BillingCalculator` + `BillingIntegrity` + `BillingMoney`).
- Biaya ruangan dan biaya admin sudah memiliki kategori sendiri (`room_charge`, `admin_fee`) dan **tidak pernah didiskon**
  (dijaga test), sehingga pemisahan dari harga treatment sudah tersedia di sisi tagihan.

---

## REQ 1: PIC / Kolom Shift

**Catatan klien:** "PIC (kolom shift)". Tidak jelas siapa PIC dan di mana kolomnya.

### Kondisi existing
- Tidak ada konsep shift atau PIC di kode.
- Jadwal ada di `therapist_availabilities` (hari, jam mulai/selesai, durasi slot, kapasitas, masa berlaku, `notes` teks bebas; contoh teks di form: "shift pagi").
- Booking dan visit memiliki `therapist_id`. Cuti terapis ada di `therapist_leave_requests`.
- Admin hanya satu akun bersama (`admin_email` di sesi), sehingga "admin PIC" tidak dapat diturunkan dari login.

### Tafsiran dan rancangan
| Tafsiran | Rancangan minimal | Perubahan data |
|---|---|---|
| A. PIC = **terapis penanggung jawab slot/shift** | Tampilkan kolom "PIC" di daftar jadwal dan kalender dari relasi yang sudah ada (`therapist_id`). Tanpa perubahan data. | Tidak ada |
| B. PIC = **petugas admin/front-desk per shift** | Tabel `shift_assignments` (tanggal, shift [pagi/siang/…], nama petugas teks, catatan). Tampil di kalender harian dan dashboard. Nama petugas berupa teks (bukan akun baru). | 1 tabel baru |
| C. PIC = **supervisor/penanggung jawab klinik per hari** | Sama dengan B tetapi satu PIC per tanggal. | 1 tabel baru |

Rekomendasi engineering: setelah klien memilih, implementasi A bisa langsung (tanpa migration); B/C membutuhkan definisi shift.
Tidak membangun sistem manajemen shift penuh.

### Pertanyaan ke klinik
1. PIC itu siapa: terapis, petugas admin, atau supervisor?
2. "Kolom shift" ada di layar mana: jadwal terapis, kalender booking, atau laporan harian?
3. Apa saja shift-nya dan jamnya (mis. pagi 08–14, sore 14–20)? PIC per shift atau per hari?
4. Siapa yang mengisi PIC (admin) dan apakah perlu histori perubahan?

---

## REQ 3: Booking Ruangan dengan Biaya Tambahan

**Aturan terkonfirmasi:** biaya ruangan adalah **biaya tambahan penggunaan/sewa ruangan**, bukan perubahan harga treatment,
dan harus menjadi **billing line tersendiri**. Contoh angka di brief hanya ilustrasi.

### Kondisi existing
- Ruangan hanya teks `room_name` di `bookings` dan `visits`. Daftar ruangan di-hardcode di 4 route
  (VIP 1, VIP 2, R.1.1, R.2.1–R.2.5, R. GYM, R. Studio Pilates). Tidak ada master ruangan, tarif, atau status.
- Tidak ada pengecekan bentrok ruangan (dua booking di ruangan dan jam yang sama tidak ditolak).
- Tagihan: kategori `room_charge` sudah ada dan tidak didiskon; saat ini baris ruangan hanya bisa ditambah manual di kasir.
- Master layanan sudah memuat add-on `+ VVIP` (Rp70.000) dan tombol kasir "Booking Fee Non-Paket" (Rp50.000): bisa jadi klinik sudah
  menagih sesuatu yang mirip. Perlu dikonfirmasi agar tidak ada penagihan ganda.

### Rancangan (nonaktif sampai aturan disetujui)
1. **Master ruangan** `rooms`: `name` (unik), `is_billable` (default **false**), `rate` (nullable), `rate_unit` (nullable),
   `status`. Seed dari 10 nama yang sekarang, semua `is_billable = false` dan tanpa tarif ⇒ perilaku tidak berubah.
   Route yang menulis `room_name` tetap kompatibel (teks tetap tersimpan).
2. **Tagihan**: baris kasir `room_charge` dibuat dari kunjungan *hanya bila ruangan berstatus berbayar*. Tahap awal berupa **saran**
   (tombol "Tambahkan biaya ruangan") dan **bukan otomatis**; penagihan otomatis dihidupkan oleh flag setelah disetujui.
3. **Pencegahan penagihan ganda** (saat edit booking, reschedule, update visit): satu baris `room_charge` aktif per visit.
   Kunci: kolom `billing_items.reference_type/reference_id` (visit) dan pengecekan di dalam transaction dengan lock pada visit.
   Void invoice melepas kunci. Mengubah ruangan setelah invoice terbit hanya menandai peringatan, tidak menagih ulang diam-diam.
4. **Diskon**: tidak pernah (sudah dijaga test); tidak mengubah harga treatment.
5. **Laporan**: biaya ruangan terpisah per kategori pada rincian invoice dan laporan (kategori sudah tersimpan di `billing_items`).
6. **Bentrok ruangan**: opsional dan terpisah (peringatan, bukan penolakan) setelah ada master ruangan.

### Pertanyaan ke klinik
1. Ruangan mana yang berbayar, dan apakah semua pasien dikenai atau hanya jenis tertentu?
2. Satuan: per sesi, per jam, per hari, atau per tipe ruangan? Berapa tarifnya?
3. Apakah tarif sama untuk semua ruangan berbayar?
4. Apakah `+ VVIP` (Rp70.000) dan Booking Fee Rp50.000 adalah biaya ruangan/booking yang sama dengan yang dimaksud, atau terpisah?
5. Kapan biaya dikenakan: saat booking, saat hadir, atau saat visit selesai? Bagaimana bila booking dibatalkan/reschedule?
6. Apakah biaya ruangan boleh dibayar terpisah dari treatment, dan apakah termasuk paket (3x/6x/12x)?

---

## REQ 6: Biaya Administrasi Pasien Baru (Rp35.000)

**Catatan klien:** pasien baru dikenai admin awal kedatangan Rp35.000. Aturan kelayakan belum dikonfirmasi.

### Kondisi existing
- Tidak ada deteksi "kunjungan pertama". Pasien dibuat lewat 3 jalur: form admin, tombol "Create Patient" dari booking
  (data kosong), dan penautan otomatis booking publik ke pasien yang cocok (WhatsApp/nama).
- Kategori `admin_fee` ada (tidak pernah didiskon). Admin sudah bisa menambah baris manual berkategori itu di kasir.
- Tidak ada penanda bahwa biaya sudah pernah dikenakan ke pasien.

### Rancangan (nonaktif sampai aturan disetujui)
- Kebijakan `NewPatientAdminFee` di `config/clinic.php`: `enabled=false`, `amount=35000`, `scope` (menunggu keputusan: `once_per_patient`, `per_registration`, dll.).
- Saat checkout dan fitur aktif: kasir menampilkan **saran** baris "Biaya Administrasi Awal" bila pasien memenuhi aturan; tidak otomatis dikenakan tanpa tindakan kasir pada tahap awal.
- **Pencegahan duplikat**: pemeriksaan di dalam transaction dengan lock pada baris pasien terhadap invoice non-void yang sudah memuat baris `admin_fee` untuk pasien itu; invoice void melepas kelayakan kembali. Satu baris per invoice.
- Penanganan kasus: reschedule (tidak memicu biaya, biaya terjadi di invoice), booking batal (tidak ada invoice, tidak ada biaya),
  pasien lama (tidak ada riwayat invoice/visit sebelum fitur aktif ⇒ perlu keputusan apakah dianggap "baru"),
  edit invoice (sudah dikunci oleh P1; koreksi lewat void + terbit ulang).
- Auditabilitas: baris tersendiri berkategori `admin_fee`, tercatat di `billing_audit_logs` (`created`), terlihat di laporan per kategori.

### Pertanyaan ke klinik
1. Sekali seumur hidup per pasien, per pendaftaran (mis. pasien kembali setelah sekian bulan), atau per episode perawatan?
2. Siapa yang dianggap "pasien baru": belum pernah punya invoice, belum pernah hadir, atau baru terdaftar? Bagaimana pasien lama yang belum pernah ditagih biaya ini?
3. Biaya dikenakan di invoice pertama atau saat pertama hadir (kunjungan selesai)? Jika booking pertama dibatalkan, apakah biaya tetap?
4. Apakah biaya ini ikut dalam paket program, dan boleh dibebaskan (waiver) oleh admin?
5. Nilai tetap Rp35.000 untuk semua kondisi?

---

## REQ 7: Informed Consent Tanpa TTD Fisioterapis

**Catatan klien:** informed consent tidak perlu tanda tangan fisioterapis.

### Kondisi existing
- Tidak ada tanda tangan tersimpan (tidak ada gambar/berkas). Tanda tangan hanya **kotak cetak**: "Tanda Tangan Patient / Perwakilan" dan
  "Tanda Tangan Physiotherapy" (menampilkan `physiotherapy_name`, default "Khayra Physio").
- `physiotherapy_name` wajib diisi di form dan tampil di cetak, arsip consent, dan detail pasien.
- Dokumen cetak dirender langsung dari database setiap kali dibuka (bukan PDF tersimpan). Konsekuensi: **mengubah template cetak akan mengubah tampilan semua consent lama**.
- Jejak yang sudah ada: `created_at`, `consent_date`, `status` (signed/pending), pasien/perwakilan, kontak darurat, visit terkait. Tidak ada identitas staf pencatat.

### Rancangan (tidak diaktifkan)
1. `informed_consents.template_version` (integer, default 1). Seluruh consent lama otomatis versi 1 dan **tetap tercetak persis seperti sekarang**.
2. Versi 2 (hanya consent baru, setelah disetujui): kotak "Tanda Tangan Physiotherapy" dihapus dari cetak; kotak pasien/wali **dipertahankan**;
   `physiotherapy_name` tidak lagi wajib tetapi tetap tersimpan sebagai nama pelaksana untuk jejak.
3. Tambah `recorded_by` (email admin pencatat) dan tampilkan waktu pembuatan pada cetak v2 agar jejak staf dan waktu tetap ada.
4. Aktivasi lewat flag + tanggal efektif; tidak ada migrasi data lama.
5. Tes: consent v1 dirender identik (snapshot), v2 tanpa kotak fisio, validasi, dan arsip menampilkan keduanya.

### Perlu sebelum aktivasi (keputusan kepatuhan, bukan teknis)
1. Apakah legal/klinik menyetujui template consent baru tanpa TTD fisioterapis (persetujuan tertulis atas teks template)?
2. Apakah tanda tangan pasien/wali tetap wajib? Apakah perlu saksi?
3. Apa pengganti identifikasi fisioterapis: nama saja, nama + nomor STR, atau cap klinik?
4. Apakah consent lama yang sedang berstatus `pending` mengikuti template baru atau lama?

---

## Ringkasan pertanyaan bisnis (satu halaman)

| Req | Pertanyaan inti |
|---|---|
| 1 | PIC = siapa (terapis/admin/supervisor), di layar mana, per shift atau per hari? |
| 3 | Ruangan berbayar mana, satuan (sesi/jam), tarif, kapan dikenakan, hubungan dengan `+ VVIP` dan Booking Fee? |
| 6 | Sekali per pasien atau per pendaftaran? Definisi "pasien baru"? Kapan dikenakan dan bagaimana bila batal? |
| 7 | Persetujuan tertulis template tanpa TTD fisio; pengganti identifikasi; TTD pasien tetap? |

## Urutan implementasi setelah jawaban diterima
1. Req 1 (opsi A tanpa migration, atau B/C bila dipilih).
2. Req 3: master ruangan (nonaktif) → saran biaya di kasir → pencegahan ganda → baru penagihan otomatis bila disetujui.
3. Req 6: kebijakan + saran + pencegahan duplikat.
4. Req 7: `template_version` + v2 setelah persetujuan kepatuhan.
