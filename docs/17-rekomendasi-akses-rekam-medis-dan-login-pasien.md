# 17 — Rekomendasi teknis: akses rekam medis therapist dan autentikasi pasien

> **Dokumen rekomendasi. Tidak ada logika bisnis yang diubah di PR #9.** Seluruh usulan di bawah memerlukan keputusan klinik dan, bila disetujui, migration baru (butuh persetujuan terpisah). Estimasi adalah kasar (hari-orang engineer berpengalaman di kode ini, termasuk test dan dokumentasi, di luar QA klinik).

## Bagian 1 — Therapist dapat mengakses rekam medis lintas therapist

### Kondisi saat ini (terbukti di Quality Gate dan kode)
Semua rute therapist hanya memeriksa `session('therapist_logged_in')`; `therapist_id` dipakai hanya untuk menandai penulis:

| Rute | Perilaku |
|---|---|
| `GET therapist/visits/{id}/report` dan `/print` | membuka laporan **visit mana pun** (probe: therapist T2 membuka visit T1 → 200) |
| `GET therapist/visits/{id}/medical-record` | membuka form rekam medis visit mana pun |
| `POST therapist/visits/{id}/medical-record` | **menulis/menimpa** rekam medis visit mana pun (`updated_by_therapist_id` hanya mencatat) |
| `GET therapist/medical-record-files/{id}` | membuka lampiran mana pun lewat ID berurutan |
| `GET therapist/dashboard` | jadwal seluruh klinik (nama pasien) selain jadwal sendiri |

`$therapistId` sudah dibaca di beberapa handler tetapi tidak dipakai untuk membatasi. Jadwal klinik terbuka bagi semua therapist tampaknya disengaja (koordinasi ruangan/slot); akses ke **isi klinis** tidak ada dasar kebutuhan serupa.

### Usulan model otorisasi
Prinsip: *minimum necessary* + *hubungan pelayanan* (treatment relationship), bukan "semua therapist aktif".

**Hubungan pelayanan yang sah (salah satu, dievaluasi berurutan):**
1. **Assignment langsung**: `visits.therapist_id` atau `bookings.therapist_id` = therapist ini untuk visit tersebut (akses penuh baca/tulis pada visit itu).
2. **Care team pasien**: therapist adalah therapist pada ≥1 visit/booking pasien itu dalam jendela waktu konfigurasi (mis. 12 bulan terakhir) → **baca** seluruh riwayat pasien (continuity), **tulis** hanya pada visit sendiri.
3. **Grant eksplisit** oleh admin/owner (cover cuti, handover, rujukan internal): tabel `patient_access_grants` (patient_id, therapist_id, scope `read|write`, mulai, selesai, alasan, pemberi). Kedaluwarsa otomatis.
4. **Emergency access** (lihat Bagian 2).

Tanpa salah satu → 403 (dan tercatat sebagai percobaan ditolak di audit log).

**Matriks per jenis data** (usulan, perlu persetujuan klinik):

| Data | Assignment | Care team | Grant | Lainnya |
|---|---|---|---|---|
| Jadwal/slot (nama + jam) | ya | ya | ya | tampil terbatas: inisial/nama depan + jam, tanpa keluhan |
| Rekam medis & laporan visit (baca) | ya | ya | ya | 403 |
| Lampiran (hasil lab, foto) | ya | ya | ya | 403 |
| Tulis/ubah rekam medis | hanya visit sendiri | tidak | `write` | 403 |
| Cetak/unduh laporan | ya | ya | ya | 403 + dicatat |

**Implementasi teknis (tanpa mengubah data lama):**
- Satu titik keputusan: `App\Services\ClinicalAccess::decide(Therapist, Patient|Visit|Attachment): Decision{allowed, basis, reason}`; dipanggil dari Gate/Policy (`VisitPolicy`, `MedicalRecordPolicy`) dan oleh semua handler therapist. Lampiran: otorisasi lewat rantai `attachment → medical_record → visit → patient`.
- Query list ber-scope (`Visit::visibleTo($therapist)`) agar tidak ada daftar yang membocorkan.
- Mulai dengan **mode bayangan**: hitung keputusan dan catat "akan ditolak" tanpa menolak, selama 2–4 minggu, untuk melihat berapa akses sah yang akan terblokir sebelum enforce.
- Test: matriks therapist × pasien × skenario (assigned, care team, tidak berhubungan, cuti/cover, grant kedaluwarsa).

### Dampak ke workflow klinik
- **Pengganti saat cuti/sakit**: tanpa grant, therapist pengganti tidak bisa membuka riwayat. Mitigasi: tombol "Alihkan/handover" di admin (reassign visit/booking sudah ada) + grant bertenggat. Perlu SOP: siapa yang boleh memberi grant (admin) dan kapan.
- **Pasien baru/walk-in**: therapist yang dijadwalkan otomatis punya assignment; walk-in butuh admin membuat visit dulu (sudah alur normal).
- **Therapist senior/supervisor**: perlu peran/atribut `can_supervise` (grant standar baca) atau cukup grant owner.
- **Rujukan antar-therapist (second opinion)**: grant `read` oleh therapist asal lewat UI sederhana (opsional).
- **Gesekan**: sebagian therapist akan merasa kehilangan akses yang selama ini bebas; komunikasikan alasan (UU PDP/kerahasiaan medis) sebelum enforce.

### Kompleksitas
Sedang. **Mode bayangan + ClinicalAccess + scoping + test: 4–6 hari**; tabel grant + UI admin: +3–4 hari; migration: 1 tabel (grant). Risiko regresi: sedang (banyak handler therapist yang inline di `routes/web.php`).

---

## Bagian 2 — Akses lanjutan (continuity of care) dan emergency access tercatat

**Continuity of care (rutin)** ditangani oleh care team + grant bertenggat di atas; tidak perlu "break-glass".

**Emergency access (break-glass)** untuk kasus yang tidak punya hubungan pelayanan tetapi butuh data segera (pasien darurat, therapist pengganti mendadak di luar jam admin):
1. Therapist menekan "Akses darurat" pada pasien → wajib pilih **alasan terstruktur** (darurat klinis / pengganti mendadak / lainnya) + teks bebas ≥ 15 karakter.
2. Akses **baca-saja**, hanya pasien tersebut, kedaluwarsa otomatis (mis. 4 jam), banner merah "Akses darurat" di semua halaman terkait.
3. Tercatat di audit log dengan `access_basis = emergency` dan **prioritas tinggi**; notifikasi ke owner/admin (email/WhatsApp ringkas tanpa isi klinis, mis. "T. X mengakses pasien Y secara darurat 14:03").
4. **Tinjauan pasca-fakta**: antrean di dashboard owner, wajib ditandai "dibenarkan / dipertanyakan" dalam 72 jam; penyalahgunaan berulang memicu tindak lanjut HR.
5. Pembatas: maksimal N akses darurat per therapist per minggu (mis. 3), melebihi → butuh persetujuan admin.

Kompleksitas: sedang-rendah bila audit log (Bagian 3) sudah ada: **3–4 hari** (tabel `emergency_access_events` atau kolom di grant, UI, notifikasi, antrean tinjauan).

---

## Bagian 3 — Audit log pembacaan rekam medis

### Apa yang dicatat
Satu tabel append-only `medical_record_access_logs`:
`id, occurred_at (UTC), actor_type (admin|owner|therapist|patient), actor_id, actor_label, patient_id, visit_id, medical_record_id, attachment_id nullable, action (view|print|download|edit|export|denied), access_basis (assigned|care_team|grant|emergency|admin_role|self), grant_id nullable, reason nullable, ip, host, user_agent_hash, request_id`.

- Titik pencatatan: handler laporan/cetak/form rekam medis (admin dan therapist), `SupportingFile::response` (semua unduhan lampiran), dan halaman pasien (`self`) untuk detail visit. Cakup juga **penolakan** (`denied`).
- Tidak menyimpan isi klinis; hanya penanda. Hindari mencatat query string.
- **Deduplikasi** baca berulang (aktor+objek+aksi dalam 10 menit → satu baris) agar volume terkendali.
- **Append-only**: tidak ada UPDATE/DELETE dari aplikasi (tidak ada model deleter); pertimbangkan akun DB aplikasi tanpa hak UPDATE/DELETE pada tabel ini, atau hash berantai (`prev_hash`) untuk deteksi pengubahan.
- **Retensi**: ikuti ketentuan retensi rekam medis dan UU PDP yang berlaku (verifikasi dengan konsultan hukum/regulasi Kemenkes tentang rekam medis); jangan menetapkan angka tanpa itu. Arsipkan ke tabel/partisi bulanan setelah N bulan.
- Indeks: `(patient_id, occurred_at)`, `(actor_type, actor_id, occurred_at)`, `(access_basis, occurred_at)`.

### Pemakaian
- Halaman owner "Siapa mengakses pasien X" dan "Aktivitas therapist Y".
- **Deteksi anomali** sederhana (job harian): >N pasien berbeda per jam per therapist; akses tanpa hubungan pelayanan (saat mode bayangan); akses di luar jam shift; lonjakan unduhan lampiran; akses terhadap pasien yang namanya sama dengan staf (VIP).
- Hak pasien: kemampuan menjawab "siapa saja yang melihat data saya" (opsional, UU PDP).

### Kompleksitas
Rendah-sedang: **2–3 hari** (tabel, helper `AccessLog::record()`, sambungan ke ±8 titik, deduplikasi, test) + 2 hari untuk halaman owner dan job anomali. Beban tulis kecil (satu insert per aksi); tidak mengubah alur klinik. Migration: 1 tabel. **Ini prasyarat yang disarankan sebelum Bagian 1 di-enforce** (mode bayangan membutuhkannya).

---

## Bagian 4 — Autentikasi pasien: WhatsApp + tanggal lahir

### Kondisi saat ini
`POST /patient/login`: nomor WhatsApp (varian format 0/62/+62) + tanggal lahir → sesi. Throttle `LoginThrottle` (pair 5 / IP 20 / identitas 20 per 15 menit). Pesan galat sama untuk nomor tak terdaftar dan tanggal salah.
Kelemahan:
1. Tanggal lahir **bukan rahasia** dan berentropi rendah (~30 ribu kombinasi untuk satu nomor); nomor WhatsApp pasien sering diketahui pihak lain (keluarga, kontak, hasil cetak invoice). Akses berhasil membuka riwayat, invoice, dan lampiran/ringkasan medis pasien.
2. Pencarian memakai `->first()`: nomor yang dipakai bersama (keluarga) dengan tanggal lahir sama (kembar) dapat membuka pasien yang salah.
3. Throttle per IP bergantung IP klien yang benar (`docs/16`; saat ini kemungkinan semua IP = proxy).
4. Tidak ada notifikasi ke pemilik nomor saat login/percobaan, tidak ada pencabutan sesi terpusat.

### Opsi

| Opsi | Keamanan | Biaya/infra | Catatan |
|---|---|---|---|
| A. **OTP WhatsApp** (6 digit, 5 menit) | Tinggi (bukti kepemilikan nomor) | Perlu kanal kirim **resmi** (WhatsApp Business Cloud API via penyedia, template autentikasi disetujui; biaya per pesan). Aplikasi saat ini hanya membuat tautan `wa.me` (klik-untuk-kirim), **tidak ada** API kirim | Rentan SIM-swap/nomor ganti; ketergantungan penyedia; butuh antrean + status pengiriman |
| B. **PIN 6 digit** pilihan pasien (di atas WA + tgl lahir) | Sedang (faktor pengetahuan kedua, dikunci server-side) | Tanpa infra baru; data: hash PIN (bcrypt) + counter | Aktivasi awal harus dibuktikan identitasnya (di klinik) |
| C. **Kode aktivasi sekali pakai** dari admin saat kunjungan (8 karakter, 7 hari) → pasien membuat PIN | Tinggi untuk pendaftaran awal | Tanpa infra baru; langkah admin tambahan | Menutup masalah "PIN ditebak sebelum pemilik membuatnya" |
| D. Tautan sakti sekali pakai via WA (15–60 menit) | Sedang | Admin/otomasi mengirim; token hash di DB | Cocok untuk pasien yang tidak ingat PIN |
| E. Passkey/WebAuthn | Tinggi | Pengembangan lebih besar; dukungan perangkat | Tahap lanjut |

### Rekomendasi bertahap
**Tahap 1 (tanpa kanal kirim baru): C + B.**
- Admin menerbitkan *kode aktivasi* di halaman detail pasien (tercatat di audit log; kedaluwarsa 7 hari; sekali pakai; disimpan sebagai hash).
- Pasien login pertama: WA + tgl lahir + kode aktivasi → wajib membuat PIN 6 digit (tolak PIN lemah: 000000, 123456, tanggal lahir, nomor).
- Login berikutnya: WA + PIN (tanggal lahir tidak lagi menjadi kredensial; tetap bisa dipakai sebagai verifikasi di klinik). Pasien lama tanpa PIN **tetap bisa login dengan skema lama** sampai diaktivasi (masa transisi, dengan banner "amankan akun") lalu dinonaktifkan per tanggal yang diputuskan klinik.
- Jika satu nomor memiliki >1 pasien: setelah autentikasi tampilkan pemilih pasien (PIN per pasien), tidak lagi `->first()`.

**Tahap 2: OTP WhatsApp** untuk pemulihan (dan opsional untuk login berisiko: perangkat baru, setelah banyak kegagalan). Perlu keputusan penyedia WhatsApp Business API, biaya, dan template.

### Pemulihan (recovery) dan mitigasi brute force
- **Lupa PIN**: (1) OTP WA ke nomor terdaftar (Tahap 2) atau (2) reset oleh admin setelah verifikasi identitas di klinik/video, menghasilkan kode aktivasi baru. Tidak memakai "pertanyaan keamanan"/tanggal lahir sebagai pemulihan.
- Setelah reset PIN/nomor: **cabut semua sesi** pasien (perlu tabel/kolom `session_version` atau menyimpan `patient_credential_fp` seperti `AdminCredentials` supaya sesi lama tidak valid); kirim notifikasi WA ke nomor ("PIN akun Anda diubah").
- Perubahan nomor WhatsApp pasien hanya oleh admin dengan alasan tercatat (mitigasi pembajakan akun lewat nomor baru).
- **Brute force**: pertahankan `LoginThrottle` dan tambahkan (a) penguncian progresif per akun (5 gagal → 15 menit, 10 → 1 jam, 20 → 24 jam + notifikasi admin), (b) batas pengiriman OTP/kode (mis. 3/jam, 10/hari per nomor; cooldown 60 detik) untuk mencegah SMS-bombing dan biaya, (c) batas verifikasi OTP 5 percobaan per kode, (d) respons seragam (tidak membocorkan nomor terdaftar) dan waktu respons konstan (sudah ada pola `Hash::check` dummy di login therapist), (e) opsional CAPTCHA privasi-ramah (mis. Turnstile) setelah N kegagalan, (f) pantau pola (banyak nomor berbeda dari satu IP) ke dashboard owner.
- **Prasyarat**: IP klien benar (`docs/16`), jika tidak lapis IP justru mengunci semua pasien.

### Dampak ke workflow klinik
- Admin: langkah tambahan menerbitkan kode aktivasi saat pasien daftar/kunjungan pertama (≈ 15 detik) dan menangani reset PIN (permintaan baru di meja admin).
- Pasien: sekali membuat PIN; lansia/pasien yang tidak familiar perlu bantuan staf. Sediakan jalur bantuan dan pesan error yang jelas.
- Pesan WhatsApp invoice (sudah memuat tautan portal kanonis dengan PR ini) bisa menambahkan "gunakan PIN Anda" setelah Tahap 1.
- Pasien yang berbagi nomor (keluarga): alur pemilih pasien menggantikan perilaku `first()` yang ambigu.

### Kompleksitas
- **Tahap 1 (kode aktivasi + PIN + pemilih pasien + pencabutan sesi + audit log + test)**: sedang, **6–9 hari**. Migration: kolom `pin_hash`, `pin_set_at`, `failed_pin_attempts`, `locked_until`, `session_version` di `patients` + tabel `patient_activation_codes`.
- **Tahap 2 (OTP WhatsApp)**: sedang-tinggi, **5–8 hari** engineering + keputusan/penyedia/biaya/persetujuan template (waktu kalender bisa mingguan); tabel `patient_otps` (hash, expires, attempts) dan antrean pengiriman.
- Risiko: pasien terkunci di luar; mitigasi lewat masa transisi dan jalur reset admin.

---

## Urutan implementasi yang disarankan
1. `docs/16`: verifikasi topologi dan isi `TRUSTED_PROXIES` (menentukan efektivitas semua throttle) — **hari-orang minimal, risiko tinggi bila diabaikan**.
2. Audit log akses (Bagian 3) — prasyarat Bagian 1 dan 2.
3. Otorisasi therapist mode bayangan → enforce (Bagian 1) + grant + emergency access (Bagian 2).
4. PIN + kode aktivasi pasien (Bagian 4, Tahap 1).
5. OTP WhatsApp (Bagian 4, Tahap 2) setelah ada keputusan penyedia.

Tidak satu pun dari langkah ini disentuh di PR #9.
