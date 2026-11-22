# Finalisasi Khayra Physio & Kesiapan Rilis (audit 26 Okt 2026)

Basis: `e3b91e8` (= `origin/main`, baseline historis rilis 13 Okt 2026). Branch kerja: `claude/khayra-physio-finalization-46778d`
(6 commit kode + 1 commit dokumen, lokal, di atas baseline). **Belum ada merge atau deployment (branch sudah di-push untuk PR #3). Tidak ada akses ke VPS/production; tidak ada migration baru.**
Dokumen ini menggantikan status usang pada `docs/05` (PIC), `docs/06` (risiko "dua pendaftaran = dua tagihan") dan `docs/09` (belum ada deployment).

## 1. Matriks status

| Item | Status | Dasar (kode/test, bukan dokumen) |
|---|---|---|
| Dropdown agama/pendidikan, Invoice WhatsApp, Diskon hanya Program | **DONE** | `PatientDropdownTest`, `InvoiceWhatsappTest`, `CashierCheckoutDiscountTest` lulus |
| Payment ledger, idempotensi, nomor invoice unik, closing race, invoice pengganti | **DONE** (verifikasi lokal) | `Integrity/*`; harness paralel MySQL 8.4.11: **39/39 lulus** |
| Biaya admin pasien baru Rp35.000 | **DONE, DISABLED** (`NEW_PATIENT_ADMIN_FEE_ENABLED` default false) | `PatientRegistrationFeeTest` 23+ test; tidak mengubah flag |
| Master ruangan + fondasi billing ruangan | **DONE, DISABLED** (semua non-billable, tanpa tarif) | `ClientRevisionsTest`; **baru:** audit perubahan tarif/billable |
| Consent v2 (preview) | **DONE, DISABLED** (aktif = v1; golden file v1) | `ClientRevisionsTest`; aktivasi = ubah `config/clinic.php` setelah persetujuan tertulis |
| PIC — sisi "penanggung jawab" | **DONE (tampilan)**: PIC = terapis pada booking (`therapist_id`) di daftar/detail booking, visit, jadwal, kalender (commit `7c70079`; `docs/05` usang) | `UiDesignSystemTest` |
| PIC — sisi "kolom shift" | **BLOCKED BY CLIENT** (definisi shift/jam/siapa pengisi belum ada) | `docs/05` §5 |
| Pendaftaran pasien ganda (orang sama dua kali) | **PARTIAL**: biaya admin tidak ganda untuk submit ulang (token), tetapi dua registrasi berbeda = dua pasien. **Baru:** detektor read-only + laporan; UI peringatan menunggu persetujuan | `PatientDuplicateFinderTest` |
| Hardening admin auth | **DONE (kode)**; rotasi kredensial production **PENDING operator** | `AdminCredentialHardeningTest` |
| Invoice legacy paid/paid_amount 0 | **DONE (laporan benar; data tidak diubah)**; rekonsiliasi data **PENDING keputusan owner** | `LegacyPaidZeroReconciliationTest` |
| Lampiran rekam medis terbuka tanpa login | **DONE (kode)**; file lama **PENDING operator** (§5.3) | `SupportingFileAccessTest` |
| Overflow mobile (kalender booking, laporan revenue) | **DONE** | `UiDesignSystemTest` + sweep 375px 27 halaman |
| Offsite encrypted backup, HTTPS/cookie secure, izin file, log VPS | **NEEDS VERIFICATION** (evidence operator) | §6 |
| Isolasi data antar-terapis (terapis A membuka rekam medis visit terapis B) | **BLOCKED BY CLIENT** (keputusan bisnis) | §4 |
| Hapus database `khayrarestore26` | **PENDING operator** | §7 |
| Tes end-to-end di staging/production | **NEEDS VERIFICATION** (belum pernah dilakukan di lingkungan nyata) | `docs/09` §I |

## 2. Perubahan di branch ini

| Commit | Cakupan |
|---|---|
| `0a06c6a` | **P0.1** hardening kredensial admin dan sesi |
| `236daf9` | **P0.2** tampilan/laporan invoice legacy + kartu Net Revenue dashboard |
| `08ce7ef` | **P1.2** audit perubahan master ruangan |
| `8603dd0` | **P0.4** lampiran rekam medis hanya untuk staf yang login |
| `99134b3` | **P1.5** detektor kandidat pasien ganda (read-only) |
| `750611c` | **P2.2** perbaikan overflow mobile |

### 2.1 P0.1 — Autentikasi admin
Audit: hash bcrypt selalu menang atas `ADMIN_PASSWORD`; hash rusak = login ditolak (tidak jatuh ke plaintext); email dibandingkan `hash_equals`
dan kedua cek selalu dijalankan; sesi diregenerasi saat login; logout `invalidate` + ganti token (GET logout 405); throttle 5/pasangan, 20/IP.
Tambahan:
- Sidik jari kredensial (HMAC-SHA256 dengan `APP_KEY`, bukan password/hash) disimpan di sesi server. **Rotasi kredensial otomatis membatalkan semua sesi admin lama.** **Validasi FAIL-CLOSED (revisi review):** sesi admin tanpa sidik jari, dengan sidik jari kosong/bukan string/salah, atau saat kredensial tidak terkonfigurasi **ditolak**; hanya kunci `admin_logged_in`, `admin_email`, `admin_credential_fp` yang dihapus dari sesi (sesi terapis/pasien tidak disentuh) dan pengguna diarahkan ke login. Tidak ada jalur khusus untuk sesi legacy. **Konsekuensi deploy yang diterima: semua admin yang masih memakai sesi dari versi sebelumnya harus login ulang sekali setelah deployment** (beri tahu staf).
- Aktor audit memakai email kanonik dari config (bukan teks yang diketik).
- `php artisan khayra:admin-hash` (prompt tersembunyi, min. 12 karakter) dan `php artisan khayra:admin-verify` (uji email+password terhadap config aktif, tanpa sesi/throttle, tanpa mencetak rahasia).
- Preflight: **FAIL** bila hash bukan bcrypt valid; **WARN** bila `ADMIN_PASSWORD` plaintext masih tersisa walau hash terisi.
- Temuan yang **tidak** diubah (butuh keputusan/operator): throttle admin tidak punya lapis per-identitas lintas IP (sengaja: satu akun admin bersama, lapis itu memungkinkan pihak luar mengunci admin); `SESSION_SECURE_COOKIE` default `null` → wajib `true` di production (preflight WARN); IP throttle bergantung pada IP klien yang benar (§6).
- **Riwayat Git memuat kredensial default lama** (email dan password bawaan sebelum commit `aff9a45`). Anggap kredensial production mana pun yang pernah sama dengan default itu **sudah bocor**: ganti email dan password.

**Prosedur rotasi (dijalankan operator berwenang, bukan Claude).** Diuji end-to-end lokal (test + server nyata dengan CSRF): rotasi menutup semua sesi admin lama, password lama ditolak, password baru masuk.
1. Cadangkan `.env` ke lokasi aman di luar web root (izin 600). Cadangan ini adalah jalan keluar: memulihkan kredensial lama juga memulihkan sidik jari lama sehingga sesi lama berlaku lagi.
2. `php artisan khayra:admin-hash` (prompt tersembunyi, tidak ada password di argumen/riwayat/log) → tempel baris `ADMIN_PASSWORD_HASH='...'` ke `.env` (kutip tunggal), **hapus** `ADMIN_PASSWORD`, ganti `ADMIN_EMAIL` bila masih default.
3. `php artisan config:cache`, lalu `php artisan khayra:preflight` ("Format password admin" harus PASS; "ADMIN_PASSWORD sisa" tidak boleh muncul) dan `php artisan khayra:admin-verify` (harus OK). Bila **gagal: segera pulihkan `.env` dari cadangan + `config:cache`** sebelum menutup apa pun.
4. Login di jendela privat dengan kredensial baru. Sesi admin lama gugur pada permintaan berikutnya (ini disengaja). Throttle (5 gagal/15 menit per email+IP) tidak permanen; lockout permanen hanya mungkin bila `.env` salah, yang dicegah langkah 3 dan dipulihkan dengan cadangan.
5. Jangan menempel password/hash ke chat, tiket, atau log; bersihkan riwayat shell. Karena kredensial default lama ada di riwayat Git (repo tidak ditulis ulang), **rotasi email DAN password wajib**, dan setiap password lama dianggap bocor.

### 2.2 P0.2 — Invoice legacy (#2, #3, #7: `paid`, `paid_amount=0`, `payment_model=legacy`)
Hasil audit (kode, bukan data production — data tidak diakses):
- **Arus kas, omzet, closing, audit integritas: benar.** `BillingMoney::paid` memakai `amount` sebagai fallback; invoice legacy dihitung sekali pada `invoice_date`; baris ledger hanya untuk invoice `ledger`; buku pembayaran menolak invoice legacy (tidak ada jalur ganda). Void legacy keluar dari kas dan omzet. Terbukti test (kas, omzet, snapshot closing, void, campuran legacy+ledger).
- **Cacat ditemukan dan diperbaiki (tampilan/laporan saja; tanpa perubahan data):** layar admin (detail, cetak, daftar kasir, laporan revenue, detail pasien) dan ekspor CSV menampilkan "Dibayar Rp 0" dan "Sisa" mentah untuk invoice tersebut; total Paid/Outstanding di detail pasien under-reporting; kartu **Net Revenue dashboard = arus kas − diskon** (mengurangi diskon dua kali karena `amount` sudah net) sehingga omzet terlalu kecil; kini = omzet invoice non-void. **Ini mengubah angka kartu dashboard** bila bulan itu ada diskon — beri tahu owner.
- Dicatat, tidak diubah: laporan "Owner Dashboard" menghitung paid berdasarkan tanggal invoice (basis berbeda dari Owner Finance yang berbasis tanggal bayar) dan laporan kinerja terapis membaca `billings.therapist_id` yang tidak ada (nilai selalu 0, kode mati).

**Rekomendasi rekonsiliasi (proposal; BUKAN dieksekusi):**
1. Operator menjalankan baca-saja (id saja): `SELECT id, invoice_number, invoice_date, amount, remaining_amount, payment_status, payment_method FROM billings WHERE payment_model='legacy' AND payment_status='paid' AND (paid_amount IS NULL OR paid_amount<=0);` dan `php artisan billing:audit-integrity` (bagian 2).
2. Owner memverifikasi tiap invoice terhadap bukti pembayaran fisik (kuitansi/mutasi). Bila terbukti lunas: **tidak perlu koreksi** — laporan sudah memperlakukannya lunas. Bila tidak ada bukti: keputusan owner (jangan diubah otomatis).
3. Jika owner tetap ingin `paid_amount` terisi (kosmetik): proposal terpisah berisi backup tabel `billings`, daftar id, `UPDATE` per id dengan `paid_amount=amount, remaining_amount=0`, entri `billing_audit_logs`, checksum sebelum/sesudah, dan persetujuan tertulis owner. Dampak pada laporan: nol (fallback sudah menghitung `amount`). Closing yang sudah tutup tidak berubah.

### 2.3 P1 — hasil audit
- **P1.2 Room charge (siap teknis, tidak diaktifkan):** guard `ReferencedChargeGuard` (satu `visit_room` per visit, kunci baris + cek di transaksi + unique index referensi), snapshot tarif, tidak didiskon (`room_charge` bukan kategori diskon), peringatan saat ruangan visit berubah, void+terbitkan ulang membawa referensi. Ditambahkan: audit perubahan master ruangan. Yang **belum ada keputusan klien**: tarif, satuan, ruangan mana, kapan dikenakan (booking/hadir/selesai), batal/reschedule, hubungan dengan `+ VVIP` Rp70.000 dan Booking Fee Rp50.000, ikut paket atau tidak. Aktivasi nanti = isi tarif+satuan di Master Ruangan (tanpa deploy), diuji dengan data sintetis di staging dulu.
- **P1.3 Consent v2:** versi disimpan per dokumen (`template_version`), v1 identik byte-per-byte (golden file), v2 hanya preview, `recorded_by` tercatat. Aktivasi membutuhkan ubah `active_template_version` di kode **setelah persetujuan tertulis klinik/legal**; tidak diaktifkan. Keputusan tertunda: nasib consent `pending`, pengganti identifikasi fisioterapis.
- **P1.4 Biaya admin:** implementasi diverifikasi ulang lewat suite (idempotensi, periode tutup, kewajiban tertunda, pasien lama, tanpa diskon, ledger). Flag tidak diubah. Perilaku tepi yang masih benar: dua registrasi orang yang sama = dua tagihan (lihat P1.5).
- **P1.5 Pasien ganda:** desain di §8. Tidak ada merge/hapus otomatis.

## 3. Temuan keamanan

| # | Sev. | Temuan | Status |
|---|---|---|---|
| S1 | **High** | Lampiran rekam medis (lab/X-ray) tersimpan di disk `public` dan terbuka lewat `/storage/...` **tanpa login** (hanya bergantung pada nama acak) | Kode diperbaiki (unggahan baru privat; rute terautentikasi, header nosniff/no-store/CSP). **File lama masih di disk publik → tindakan operator §5.3** |
| S2 | High | Kredensial default lama ada di riwayat Git; kredensial production memakai email default dan password plaintext (release gate) | Rotasi oleh operator (§2.1) |
| S3 | Medium | Terapis mana pun yang login dapat membuka rekam medis visit milik terapis lain (`$therapistId` dibaca tapi tidak dipakai) | **BLOCKED BY CLIENT**: apakah terapis dibatasi pada pasien/visit sendiri (mungkin ada kebutuhan cover)? |
| S4 | Medium | Log aplikasi mencatat pesan `QueryException` beserta nilai binding (nama/telepon pasien) bila terjadi error SQL | Mitigasi konfigurasi (§6); tidak diubah di kode |
| S5 | Medium | IP throttle memakai `request->ip()`; bila ada proxy/CDN tanpa trusted-proxy, semua klien tampak satu IP → 20 gagal login siapa pun mengunci admin untuk semua | Perlu verifikasi operator (§6) |
| S6 | Low | `referral_source`, `documentation_consent(_notes)` pada registrasi tidak divalidasi (panjang/nilai) | Catatan; bukan eksploitasi |
| S7 | Info | Service worker hanya cache aset statis dan tidak menyimpan halaman; setelah S1 lampiran tidak lagi lewat jalur cache `.png/.jpg` | OK |

## 4. Keputusan klien / owner yang masih tertunda
1. PIC/shift: siapa PIC, per shift atau per hari, jam shift, layar tampil (5 pertanyaan `docs/05`).
2. Ruangan berbayar: ruangan, tarif, satuan, saat dikenakan, batal/reschedule, relasi VVIP/Booking Fee, paket.
3. Consent v2: persetujuan tertulis teks; TTD pasien/wali/saksi; pengganti identifikasi fisioterapis; consent pending.
4. Pasien ganda: izinkan peringatan non-blok saat registrasi? identifier yang dipakai (NIK sering kosong; WhatsApp dipakai bersama keluarga)? siapa yang meninjau?
5. Isolasi rekam medis antar-terapis (S3).
6. Rekonsiliasi invoice legacy #2/#3/#7 (§2.2) dan pemberitahuan perubahan angka kartu Net Revenue.

## 5. Tindakan yang membutuhkan akses VPS/operator (dipisahkan dari pekerjaan kode)
### 5.1 Sebelum/ saat deploy
Ikuti `docs/04` §3–§7 dan `docs/09` §F–§K (backup penuh, checksum baseline, `migrate --pretend`, `khayra:preflight --release-gate`, `billing:audit-integrity --strict`). Rilis ini **tidak menambah migration** (tetap 15 migration additive dari baseline bila production belum memilikinya; status deployment `e3b91e8` mencatat 15 sudah berhasil).
### 5.2 Konfigurasi `.env` yang wajib ditinjau (nilai tidak ditulis di sini)
`APP_ENV=production`, `APP_DEBUG=false`, `SESSION_DRIVER=database`, `CACHE_STORE=database`, `SESSION_SECURE_COOKIE=true`, `APP_URL=https://app.khayraphysio.com`, `CLINIC_TIMEZONE=Asia/Jakarta` (`app.timezone` tetap UTC), `LOG_STACK=daily`, `LOG_DAILY_DAYS=14`, `LOG_LEVEL=warning`, `NEW_PATIENT_ADMIN_FEE_ENABLED` **tidak diubah**, `ADMIN_*` sesuai §2.1.
### 5.3 Lampiran rekam medis lama (S1) — ringkas; prosedur lengkap di `docs/12-runbook-lampiran-medis-legacy.md`
Setelah kode ini live (rute `/admin|therapist/medical-record-files/{id}` bekerja untuk file di disk publik maupun privat):
1. Verifikasi tautan lampiran lama terbuka dari halaman rekam medis (login terapis).
2. Salin lalu karantina (bukan hapus): `storage/app/public/medical-record-supporting-data/` → `storage/app/private/medical-record-supporting-data/` (path di database tidak berubah; rute mencari disk privat dulu). Pertahankan pemilik/izin `www-data`/user CloudPanel dan mode 640/750. **Jangan hapus tanpa salinan.**
3. Blokir **wajib dan permanen**: `location ^~ /storage/medical-record-supporting-data/ { deny all; return 404; }` di vhost nginx CloudPanel (tidak dilepas, bahkan saat rollback; lihat `docs/12` §7).
4. Uji dari jendela tanpa login: `https://app.khayraphysio.com/storage/medical-record-supporting-data/<nama>` harus 403/404.
5. Pertimbangkan apakah URL lama pernah dibagikan di luar klinik; bila ya, anggap file itu terpapar.
### 5.4 Evidence yang diminta dari operator (tidak dianggap terverifikasi dari teks historis)
- Backup: bukti job offsite **terenkripsi** (lokasi, jadwal, retensi 30 hari, siapa yang punya kunci), hasil uji pemulihan terakhir (tanggal, ukuran, checksum — tanpa data pasien).
- Izin: `storage/` dan `bootstrap/cache` writable oleh user PHP-FPM saja; `.env` mode 600 di luar web root publik; `.git` dan `vendor` tidak dapat diakses via HTTP (uji `GET /.git/config`, `/.env`, `/vendor/autoload.php` → 403/404); docroot = `public/`.
- HTTPS: sertifikat valid, HTTP → HTTPS, cookie sesi `Secure; HttpOnly; SameSite=Lax` (cek di DevTools).
- Proxy/CDN: bila ada Cloudflare/load balancer, konfigurasi trusted proxy agar IP klien benar; bila tidak ada, `REMOTE_ADDR` fastcgi sudah IP klien. Uji: dua IP berbeda gagal login → tidak saling mengunci.
- Log: `storage/logs/*.log` mode 640 dan tidak terlayani web; tidak dikirim ke layanan pihak ketiga.
## 6. Catatan operasional log
Laravel mencatat exception beserta SQL dan binding. Karena data pasien bisa muncul, batasi `LOG_LEVEL`, retensi harian 14 hari, akses berkas log hanya untuk operator, dan jangan menempel log ke tiket/chat tanpa menyamarkan.

## 7. Checklist penghapusan `khayrarestore26` (jangan dijalankan oleh Claude)
1. Konfirmasi tertulis dari operator bahwa uji pemulihan selesai dan hasilnya tercatat (tanggal, ukuran, checksum, hasil `billing:audit-integrity`).
2. Pastikan tidak ada koneksi aktif dan `.env` aplikasi tidak menunjuk database itu (`DB_DATABASE`).
3. Pastikan database itu **bukan** database production (nama, jumlah baris `billings`/`patients` dibandingkan hanya sebagai angka).
4. Ekspor ulang cadangan bila masih diperlukan (terenkripsi) sebelum menghapus; database restore berisi data pasien nyata → hapus segera setelah dipakai.
5. `DROP DATABASE` oleh operator berwenang dengan hak terbatas, lalu hapus berkas dump sementara di server dan bersihkan riwayat shell. Catat tanggal dan pelaksana.

## 8. Desain pencegahan pasien ganda (P1.5)
- **Pencocokan (sudah dibuat, read-only, `PatientDuplicateFinder`):** `high` = NIK sama (angka saja, ≥10 digit); `medium` = nama ternormalisasi + tanggal lahir sama; `low` = WhatsApp sama **dan** nama ≥80% mirip. WhatsApp saja tidak cukup (orang tua/anak sering berbagi nomor) → tidak ditandai.
- **Laporan:** `php artisan khayra:patients-duplicates` mencetak hanya id pasien, tingkat, alasan (tanpa nama/NIK/telepon). Jalankan di production hanya baca-saja oleh operator untuk melihat duplikat yang sudah ada.
- **Usulan UI (menunggu persetujuan, belum dibuat):** pada form registrasi dan Create Patient dari booking, tampilkan peringatan **non-blok** dengan tautan ke kandidat; wajib centang "ini orang berbeda" untuk lanjut bila ada kandidat `high`/`medium`; alasan dicatat di audit. Token registrasi dan jalur `PatientRegistration` tidak berubah.
- **Penanganan registrasi ganda yang sudah terjadi:** tanpa merge otomatis. Pasien yang tidak dipakai ditandai manual oleh staf; invoice administrasinya di-void lewat alur Void yang ada (audit). Penggabungan rekam medis = prosedur terpisah dengan backup, peninjauan klinis, dan persetujuan; **tidak** diimplementasikan.
- **Biaya admin ganda:** tidak dicegah otomatis pada orang yang benar-benar sama (dua pasien = dua tagihan) sampai peringatan UI disetujui; koreksi via Void.

## 9. Pengujian
| Lingkungan | Hasil |
|---|---|
| SQLite (`:memory:`) — baseline sebelum perubahan | 321 lulus, 2 skip |
| SQLite — akhir (setelah review PR #3) | **383 lulus**, 2 skip (2.547 assertion; skip = test MySQL-only) |
| MySQL 8.4.11 lokal (database sementara, sudah dihapus) — suite penuh, akhir | **377 lulus**, 8 skip (test khusus SQLite dilewati) |
| Harness konkurensi paralel `tests/Concurrency/run.php` (MySQL 8.4.11 lokal) | **39/39 lulus**, database sementara dihapus otomatis |
| Mutation check P0.2 | tanpa perbaikan, 4 dari 10 test baru gagal (pembuktian cacat) |
| Browser QA | sweep 27 halaman pada 375px; 2 cacat overflow ditemukan, diperbaiki, diverifikasi ulang; login admin lewat browser nyata (CSRF) OK. **Tidak diuji:** wa.me nyata, cetak ke printer, browser selain Chromium, staging/production |
Semua data sintetis; database sementara SQLite di scratchpad dan MySQL `khayra_qa_*`/`khayra_race_*` (dihapus). Tidak ada akses ke production.

## 10. Dampak migration dan rollback
- **Migration baru: tidak ada.** Rollback kode = deploy ulang commit sebelumnya; tidak perlu rollback database.
- **Lampiran medis: rollback TIDAK PERNAH boleh membuka kembali akses tanpa login.** Kode `e3b91e8` tidak dapat melayani file di disk privat. Jangan memindahkan file balik ke disk publik dan jangan melepas blokir nginx `/storage/medical-record-supporting-data/`. Pilihan: (1) roll-forward / *compatibility hotfix* yang menyertakan rute lampiran berautentikasi (diuji di staging), atau (2) bila rollback penuh tak terhindarkan, **lampiran sementara tidak tersedia** sampai hotfix diterapkan, dengan jalur akses darurat berpersetujuan dan jejak audit. Unggahan yang dibuat kode lama selama rollback jatuh ke direktori publik yang tetap diblokir; wajib disalin ke disk privat dan dikarantina setelah kode baru kembali. Prosedur lengkap: `docs/12` §7.
- Sidik jari sesi diabaikan kode lama, dan lampiran yang diunggah sejak rilis ini ada di `storage/app/private`. Audit `room_*` di `billing_audit_logs` diabaikan kode lama (tidak ada skema baru).
- Dampak deploy: kartu "Net Revenue" dashboard berubah definisi (omzet invoice); **semua admin harus login ulang sekali** (sesi lama tanpa sidik jari ditolak); rotasi kredensial berikutnya menutup semua sesi admin.

## 11. Review keamanan PR #3 (revisi)
### 11.1 Revocation sesi admin (fail-closed)
Cacat: `sessionStillValid(null)` mengembalikan `true`, sehingga sesi admin versi lama (tanpa `admin_credential_fp`) tetap lolos setelah kredensial dirotasi. Perbaikan: `AdminCredentials::sessionStillValid(mixed)` menolak apa pun selain string non-kosong yang `hash_equals` dengan sidik jari kredensial berlaku, dan menolak bila kredensial tidak terkonfigurasi. Test baru: login menyimpan sidik jari; tanpa sidik jari ditolak (flag admin dihapus; permintaan berikutnya juga ditolak); kosong/null/array/angka/palsu/huruf-besar ditolak; cocok diterima; rotasi password **dan** email membatalkan sesi; sesi terapis/pasien utuh; rotasi end-to-end; password tidak masuk log. Mutation check: mengembalikan `null => true` menggagalkan 2 test baru. Test lama yang memakai `['admin_logged_in' => true]` dipindah ke helper `adminSession()` (membawa sidik jari); harness konkurensi ikut diperbarui.
Tidak berubah: regenerasi sesi saat login, CSRF (curl nyata tanpa token → 419), cookie HttpOnly, throttling, hash invalid tanpa fallback plaintext.
### 11.2 Review lampiran rekam medis
| Jalur | Hasil |
|---|---|
| Upload | validasi `file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx`; nama file acak (`hashName`), disimpan di disk `local` (`storage/app/private`); nama asli hanya metadata (disanitasi saat dikirim) |
| Akses staf | `/admin|therapist/medical-record-files/{id}` (`whereNumber`), sesi admin/terapis wajib (dijaga juga oleh `RequireAreaLogin`); tamu dan sesi pasien → redirect login |
| File lama (disk publik) | tetap terbaca lewat rute yang sama (disk privat dicari dulu) — URL lama `/storage/...` **tetap terbuka tanpa login sampai operator bertindak** (runbook `docs/12`) |
| Path traversal / ID | path wajib berawalan `medical-record-supporting-data/`, tanpa `..`/NUL; ID non-numerik 404; ID tidak ada 404; file hilang 404 |
| Header | `X-Content-Type-Options: nosniff`, `Cache-Control: private, no-store`, CSP `sandbox`; **hanya gambar inline, PDF/dokumen selalu `attachment`**; nama file di header disanitasi (CR/LF tidak lolos) |
| Rute bawaan Laravel | disk `local` punya `serve=true` → rute `GET/PUT /storage/{path}` (bertanda tangan). **Dinonaktifkan** (`serve=false`); test memastikan `/storage/...` privat 404 dan PUT 404 |
| Symlink | `public/storage` → `storage/app/public` saja; disk privat tidak ditautkan |
| Antar-record | `supporting_data_id` hanya dicocokkan dengan lampiran record yang sama (test manipulasi ID lain) |
| Akses antar-pasien/antar-terapis | **Tidak ada pembatasan**: staf mana pun yang login dapat membuka lampiran mana pun (setara hak membuka rekam medisnya). Lihat 11.3 |
Catatan: ID lampiran berubah tiap rekam medis disimpan (baris dibuat ulang; file tetap). File tidak pernah dihapus saat item dibuang dari form (retensi; perlu kebijakan).
**Status: kode SELESAI; risiko S1 BELUM selesai sampai file legacy ditangani dan akses tanpa login diverifikasi dari luar (`docs/12`).**
### 11.3 Akses antar-terapis (keputusan klien, tidak diubah)
Sekarang terapis mana pun dapat membuka rekam medis & lampiran semua pasien (`$therapistId` tidak dipakai). Opsi: **A** tetap (operasional fleksibel, risiko akses tidak perlu, tidak ada jejak); **B** hanya visit sendiri (+admin) — paling ketat, mengganggu cover/pengganti dan terapis yang lanjut dari terapis lain; **C** visit sendiri + pasien yang pernah ditangani/penugasan cover tercatat — butuh data penugasan; **D** tetap terbuka tetapi mencatat log akses (siapa, kapan, record) dan ditinjau berkala — kompromi tercepat. Rekomendasi: D sekarang, B/C setelah klien menjawab alur cover. Dampak B/C: perlu migration/aturan dan uji alur kerja klinis.

## 12. Verdict kesiapan rilis
**Kode PR #3: siap merge (teknis).** **Production: BELUM aman/direkomendasikan** — merge tidak sama dengan aman dipakai. Syarat sebelum production: (1) staging lulus QA `docs/09` §I dengan data sintetis, (2) kredensial admin dirotasi (§2.1), (3) lampiran lama dipindah/diblokir dan akses tanpa login diverifikasi dari luar (§5.3, `docs/12`), (4) evidence backup offsite terenkripsi dan izin/HTTPS diterima (§5.4), (5) keputusan owner untuk rekonsiliasi legacy dan perubahan angka Net Revenue. Fitur finansial pending (biaya admin, ruangan berbayar, consent v2) tetap **tidak** diaktifkan.
