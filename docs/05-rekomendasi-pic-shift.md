# Rekomendasi PIC / Shift (Req 1) — Khayra Physio

> **Pembaruan 26 Okt 2026:** bagian "PIC = terapis penangan booking/jadwal" (tafsiran A/D) sudah ditampilkan di UI sejak commit `7c70079` (tanpa skema baru). Yang tetap BLOCKED adalah "kolom shift"/petugas per shift. Lihat `docs/11-finalisasi-produksi-dan-kesiapan-rilis.md`.


**Status: BLOCKED BY CLIENT DECISION. Tidak ada kode, migration, atau perilaku yang diimplementasikan.**
Catatan klien hanya berbunyi "PIC (kolom shift)". Definisi PIC dan lokasi kolomnya belum final, sehingga setiap
implementasi saat ini adalah asumsi. Dokumen ini berisi hasil audit, titik integrasi paling sederhana, rancangan UI minimal,
dan keputusan yang dibutuhkan agar implementasi bisa langsung dikerjakan setelah klien menjawab.

Batasan yang dipegang: bukan sistem HR, bukan payroll, bukan penjadwalan shift kompleks (rotasi, tukar shift, absensi, lembur).

---

## 1. Audit kondisi existing

| Area | Temuan | File |
|---|---|---|
| Staf | Satu-satunya tabel staf adalah `therapists` (akun terapis). Admin/front-desk **bukan** akun individual: hanya satu login admin bersama (`admin_email` di sesi). | `app/Models/Therapist.php`, `config/khayra_auth.php` |
| Jadwal kerja terapis | `therapist_availabilities`: terapis, hari (1–7), `start_time`, `end_time`, `slot_duration_minutes`, `capacity`, masa berlaku (`valid_from/valid_until`), `status`, `notes` teks bebas. Contoh isian di form: "shift pagi". | `app/Models/TherapistAvailability.php`, `routes/web.php` (`/admin/therapist-availabilities`) |
| Booking dan visit | Memiliki `therapist_id` (terapis yang menangani) dan `room_name`. Tidak ada konsep shift/PIC. | `bookings`, `visits` |
| Cuti | `therapist_leave_requests` (pending/approved/rejected) per terapis. | `app/Models/TherapistLeaveRequest.php` |
| Kalender harian | Slot 08:00–20:00 per 30 menit, kolom per terapis aktif. Tidak ada baris/kolom PIC. | `/admin/bookings/calendar`, `resources/views/admin-booking-calendar.blade.php` |
| Shift | Tidak ada tabel, kolom, atau enum shift. "Shift" hanya muncul sebagai teks contoh pada `notes` jadwal. | — |

Kesimpulan audit: jam kerja per terapis sudah ada (cukup untuk menurunkan "shift" bila shift = rentang jam), tetapi **tidak ada data
tentang "penanggung jawab" per hari/shift**, dan tidak ada tempat menyimpan petugas admin.

## 2. Tafsiran yang mungkin dan konsekuensinya

| # | PIC berarti | Data yang dibutuhkan | Perubahan skema | Perkiraan |
|---|---|---|---|---|
| A | **Terapis penanggung jawab slot/shift** (terapis yang bertugas pada jadwal itu) | Sudah ada: `therapist_availabilities.therapist_id` + jam | **Tidak ada** | Kecil. Hanya kolom tampilan "PIC" di daftar jadwal dan kalender |
| B | **Petugas admin / front-desk per shift** | Nama petugas per tanggal + shift | 1 tabel kecil | Kecil–sedang |
| C | **Penanggung jawab klinik per hari** (satu orang per tanggal) | Terapis atau nama per tanggal | 1 tabel kecil (sama dengan B, tanpa shift) | Kecil |
| D | **PIC sebuah booking/visit** (siapa yang menangani pasien itu) | Sudah ada: `therapist_id` pada booking/visit | **Tidak ada** | Hampir nol: pada dasarnya kolom terapis yang sudah ada diberi label PIC |

## 3. Titik integrasi paling sederhana (rekomendasi teknis)

1. **Gunakan data yang sudah ada dahulu.** Untuk tafsiran A dan D tidak perlu migration: tambahkan kolom/label "PIC" yang mengambil
   `therapist.full_name` dari `therapist_availabilities` (halaman jadwal) dan dari `bookings.therapist_id` (daftar dan kalender).
2. **Bila PIC harus dicatat per hari/shift (B/C): satu tabel saja**, mis. `shift_assignments`
   (`date`, `shift_label` nullable, `therapist_id` nullable → `therapists`, `staff_name` nullable teks, `notes`, timestamps).
   Mengacu ke `therapists` bila PIC adalah terapis; `staff_name` teks bila petugas admin (karena admin tidak punya akun individual).
   `shift_label` berupa teks bebas atau dua nilai tetap (mis. "Pagi"/"Sore") — **tanpa** master shift, rotasi, atau template mingguan.
3. **Jangan** menambah akun admin individual demi PIC. Itu pekerjaan otentikasi tersendiri dan di luar scope.

## 4. Rancangan UI minimal (belum dibuat)

- **Kalender booking harian**: satu baris di atas grid: `PIC hari ini: <nama> (Pagi) · <nama> (Sore)` dengan tautan "Ubah" yang membuka
  formulir inline kecil (pilih terapis atau ketik nama, pilih shift, simpan). Hanya admin yang login.
- **Halaman jadwal terapis**: kolom "PIC" (tafsiran A) atau badge pada baris yang menjadi PIC.
- **Dashboard admin** (opsional): kartu kecil "PIC hari ini".
- Tanpa laporan, tanpa notifikasi, tanpa histori selain `created_at/updated_at` (tambahkan audit hanya bila klien meminta).

## 5. Keputusan yang dibutuhkan dari klien

1. **PIC itu siapa?** Terapis / petugas admin front-desk / supervisor klinik / terapis yang menangani pasien tertentu.
2. **"Kolom shift" ada di layar mana?** Kalender booking, jadwal terapis, laporan harian, atau dokumen/spreadsheet lain (mohon contoh/foto).
3. **Apa saja shift-nya dan jamnya?** (mis. Pagi 08–14, Sore 14–20). PIC dihitung **per shift** atau **per hari**?
4. **Siapa yang mengisi PIC dan kapan?** Admin; perlu diisi sebelum hari H atau cukup di hari itu; perlu histori perubahan?
5. **Apakah PIC memengaruhi aturan lain?** (mis. hanya PIC yang boleh void invoice, atau PIC tercetak di invoice/laporan). Bila ya, itu fitur
   terpisah dan harus didefinisikan sendiri.

## 6. Yang akan dikerjakan setelah keputusan masuk

| Jawaban klien | Pekerjaan |
|---|---|
| PIC = terapis pada jadwal (A) / terapis penangan booking (D) | Tambah kolom tampilan + tes. Tanpa migration. |
| PIC = petugas per shift/hari (B/C) | Migration `shift_assignments` (additive) + formulir inline di kalender + tes. |
| Ada aturan hak akses berbasis PIC | Dipisah menjadi tiket baru; butuh akun admin individual. |

Sebelum jawaban 1–3 diterima, **tidak ada kode PIC/shift di branch ini**.
