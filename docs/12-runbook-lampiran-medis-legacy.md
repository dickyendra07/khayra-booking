# Runbook Operator — Lampiran Rekam Medis Legacy di Disk Publik

Untuk operator berwenang di VPS (Hostinger + CloudPanel). **Bukan untuk dijalankan oleh Claude/CI.** Tidak ada langkah yang menghapus data
sebelum salinan terverifikasi; penghapusan akhir butuh persetujuan tertulis owner.

**Masalah:** lampiran lama (hasil lab/X-ray) ada di `storage/app/public/medical-record-supporting-data/` dan terbuka lewat
`https://app.khayraphysio.com/storage/medical-record-supporting-data/<nama-acak>` tanpa login. Perubahan kode (PR #3) hanya mengamankan
unggahan **baru** dan menyediakan rute berautentikasi; file lama tetap terbuka sampai runbook ini selesai.

Notasi: `APP=/path/ke/aplikasi` (root Laravel, bukan `public/`). Jalankan sebagai user situs CloudPanel (bukan root) kecuali dinyatakan lain.
Keluaran yang berisi nama file/jumlah boleh dicatat; **jangan** menyalin isi file, nama pasien, atau log akses mentah ke tiket/chat.

## 0. Prasyarat
- [ ] Kode PR #3 sudah ter-deploy dan lolos smoke test (`/up`, login admin, buka satu rekam medis).
- [ ] Persetujuan tertulis owner; jendela kerja; orang kedua sebagai pemeriksa.
- [ ] Ruang disk cukup: `du -sh $APP/storage/app/public/medical-record-supporting-data` × 3 (asli + salinan + arsip backup).

## 1. Inventaris (baca-saja)
```bash
SRC=$APP/storage/app/public/medical-record-supporting-data
DST=$APP/storage/app/private/medical-record-supporting-data
mkdir -p ~/lampiran-migrasi && chmod 700 ~/lampiran-migrasi      # di luar web root
find "$SRC" -type f | wc -l                                       # jumlah file
du -sh "$SRC"
( cd "$SRC" && find . -type f -print0 | sort -z | xargs -0 sha256sum ) > ~/lampiran-migrasi/manifest-sebelum.sha256
chmod 600 ~/lampiran-migrasi/manifest-sebelum.sha256
ls -ld "$SRC" "$APP/storage/app/private" 2>&1                      # pemilik/izin
ls -l "$APP/public/storage"                                        # symlink harus menuju storage/app/public
```
Cocokkan dengan database (baca-saja, tanpa mencetak data pasien):
```sql
SELECT COUNT(*) AS baris_dengan_file FROM medical_record_supporting_data WHERE file_path IS NOT NULL AND file_path <> '';
SELECT COUNT(DISTINCT file_path) AS file_unik FROM medical_record_supporting_data WHERE file_path IS NOT NULL AND file_path <> '';
```
Catat selisih: file di disk yang tidak direferensikan database (yatim) tetap dipindahkan (jangan dihapus), dan baris database yang filenya
tidak ada dilaporkan ke owner (kemungkinan sudah hilang sebelumnya).

## 2. Backup terenkripsi (wajib sebelum mengubah apa pun)
```bash
tar -C "$APP/storage/app/public" -czf - medical-record-supporting-data \
  | gpg --symmetric --cipher-algo AES256 -o ~/lampiran-migrasi/lampiran-$(date +%F).tar.gz.gpg
```
(atau `age -p`). Kata sandi/kunci disimpan oleh owner terpisah dari server. Salin berkas terenkripsi ke penyimpanan **offsite**, catat ukuran dan
`sha256sum`. **Uji pemulihan:** ekstrak ke direktori sementara, bandingkan `manifest` untuk 5 file acak, hapus direktori sementara.
- [ ] Backup offsite terenkripsi ada dan terverifikasi (tanggal, ukuran, checksum dicatat).

## 3. Dry-run penyalinan
```bash
mkdir -p "$DST" && chmod 750 "$DST"
rsync -a --dry-run --itemize-changes "$SRC"/ "$DST"/ | head -50
rsync -a --dry-run --itemize-changes "$SRC"/ "$DST"/ | grep -c '^>f'   # harus sama dengan jumlah file inventaris (atau lebih kecil bila sebagian sudah ada)
```

## 4. Salin (BUKAN pindah) dan verifikasi
```bash
rsync -a --checksum "$SRC"/ "$DST"/
( cd "$DST" && find . -type f -print0 | sort -z | xargs -0 sha256sum ) > ~/lampiran-migrasi/manifest-sesudah.sha256
diff ~/lampiran-migrasi/manifest-sebelum.sha256 ~/lampiran-migrasi/manifest-sesudah.sha256 && echo "IDENTIK"
find "$DST" -type f ! -perm 640 -exec chmod 640 {} + ; find "$DST" -type d -exec chmod 750 {} +
```
- [ ] `IDENTIK`; izin file 640/direktori 750; pemilik sama dengan user PHP-FPM situs.
- [ ] Uji aplikasi: login terapis, buka 3 rekam medis berlampiran lama, klik lampiran — berhasil (rute mencari disk privat dulu, jadi ini membuktikan salinan privat dipakai).

## 5. Tutup akses publik (urutan: blokir dulu, karantina kemudian)
1. CloudPanel → Sites → situs → **Vhost** → tambahkan di server block (sebelum `location /`):
   ```nginx
   location ^~ /storage/medical-record-supporting-data/ { deny all; return 404; }
   ```
   `sudo nginx -t` lalu reload dari CloudPanel/`sudo systemctl reload nginx`. (Catatan: file tertentu di `/storage/` lain tidak terpengaruh.)
2. **Verifikasi dari luar jaringan klinik, tanpa login** (3 nama file sampel dari manifest):
   ```bash
   curl -s -o /dev/null -w '%{http_code}\n' "https://app.khayraphysio.com/storage/medical-record-supporting-data/<nama-sampel>"
   ```
   Harus **404/403**. Cek juga `https://app.khayraphysio.com/storage/medical-record-supporting-data/` (tanpa listing).
3. Karantina sumber (jangan hapus): `mv "$SRC" ~/lampiran-migrasi/karantina-$(date +%F)` — langsung ke luar `storage/app/public` dan luar docroot. Ulangi uji aplikasi (langkah 4) dan uji URL luar.
4. Simpan karantina minimal 14 hari + backup offsite; hapus permanen hanya dengan persetujuan tertulis owner (`shred -u`/hapus aman + catat).

## 6. Penilaian keterpaparan (opsional, baca-saja)
Periksa log akses nginx untuk permintaan `/storage/medical-record-supporting-data/` dari IP di luar klinik (hitung saja, jangan menyalin baris log ke tiket).
Bila ada akses dari IP asing/bot, laporkan ke owner untuk penilaian insiden data pribadi.

## 7. Rollback — tidak pernah membuka kembali akses tanpa login

**Aturan tetap (tidak ada pengecualian operasional):**
- Aturan nginx `location ^~ /storage/medical-record-supporting-data/ { deny all; return 404; }` **tidak pernah dihapus** sebagai bagian dari rollback, perbaikan, atau pemecahan masalah. Menghapusnya hanya boleh lewat persetujuan tertulis owner **dan** penutupan insiden/penilaian data pribadi — dan bukan untuk memulihkan layanan.
- Tidak ada file medis yang dikembalikan ke `storage/app/public` atau ke direktori yang dilayani web (`public/`, symlink `public/storage`). Salinan privat (`storage/app/private/medical-record-supporting-data/`) adalah sumber utama selamanya.
- Tidak ada perubahan database pada runbook ini, jadi tidak ada rollback database.

**Skenario A — masalah pada salinan/prosedur (kode PR #3 tetap berjalan):**
1. Hentikan di langkah yang bermasalah; jangan melepas blokir nginx.
2. Bila ada berkas privat yang rusak/hilang (checksum tidak cocok dengan `manifest-sebelum.sha256`): pulihkan **hanya file itu, ke direktori privat**, dari karantina (`~/lampiran-migrasi/karantina-<tanggal>`) atau dari backup terenkripsi (ekstrak di direktori sementara privat mode 700 di luar docroot, salin file terkait ke `$DST`, hapus direktori sementara secara aman).
3. Ulangi verifikasi: `sha256sum -c` terhadap manifest, izin 640/750, buka lampiran lewat aplikasi, uji URL publik → 404/403.

**Skenario B — kode harus di-rollback ke versi yang tidak punya rute lampiran (`e3b91e8` atau lebih lama):**
Kode lama hanya membangun tautan `/storage/...` dan tidak dapat melayani file privat. Pilihan yang diizinkan, berurutan:
1. **Roll-forward (disarankan):** perbaiki masalah pada kode baru, atau terapkan *compatibility hotfix* pada basis lama. Cakupan hotfix minimum: `app/Support/SupportingFile.php`, dua rute `*/medical-record-files/{id}`, tautan di `therapist-medical-record.blade.php`, dan `serve=false` pada `config/filesystems.php` (commit `8603dd0` dan bagian lampiran dari `fbd6dbf`), diuji di staging dengan data sintetis (`SupportingFileAccessTest`) sebelum diterapkan.
2. **Bila rollback penuh tidak dapat dihindari sebelum hotfix siap:** **lampiran dinyatakan sementara TIDAK TERSEDIA** di aplikasi (tautan lama menghasilkan 404 karena blokir nginx; ini disengaja). Rekam medis lain tetap berfungsi. Blokir nginx tetap terpasang, file privat tetap di tempatnya.
   - Unggahan baru oleh kode lama jatuh ke `storage/app/public/medical-record-supporting-data/` (tetap diblokir nginx, sehingga tidak publik tetapi juga tidak tampil). Instruksikan klinisi **tidak mengunggah lampiran** selama rollback; catat setiap unggahan yang terlanjur dibuat.
   - Setelah kode baru/hotfix kembali: salin file baru dari direktori publik itu ke direktori privat dengan prosedur langkah 3–4 (dry-run, `rsync --checksum`, manifest, izin), verifikasi, lalu karantina direktori publik sesuai langkah 5.3. Jangan membiarkan file medis di direktori publik.
3. **Kebutuhan klinis mendesak saat lampiran tidak tersedia (akses darurat):** operator mengambil **satu file spesifik** dari salinan privat setelah permintaan tertulis dari klinisi yang berwenang dan persetujuan owner/penanggung jawab klinik. Kirim lewat kanal terenkripsi/terautentikasi yang disetujui klinik (bukan tautan publik, bukan chat umum). Verifikasi `sha256sum` file terhadap manifest sebelum dan sesudah pengambilan (integritas); jangan mengubah file asli.

**Jejak audit untuk setiap tindakan rollback/akses darurat** (dicatat oleh operator dan disimpan owner, tanpa isi file/nama pasien di catatan umum): tanggal-jam, pelaksana, pemberi persetujuan, alasan, ID lampiran (bukan nama pasien), checksum file, penerima, kanal pengiriman, dan konfirmasi bahwa blokir nginx tetap aktif. Simpan juga keluaran `diff` manifest dan hasil uji URL publik (404/403) setelah setiap perubahan.

**Verifikasi akhir setiap rollback (wajib sebelum dinyatakan selesai):** (1) blokir nginx masih ada (`sudo nginx -T | grep medical-record-supporting-data`); (2) tiga URL sampel tanpa login dari luar → 404/403; (3) `find storage/app/public -path '*medical-record-supporting-data*' -type f` kosong atau hanya berisi unggahan yang menunggu dipindahkan (dicatat); (4) manifest privat identik; (5) backup terenkripsi offsite tetap utuh.

## 8. Selesai bila
- [ ] Backup offsite terenkripsi terverifikasi
- [ ] Manifest sebelum = sesudah (`IDENTIK`)
- [ ] Lampiran lama terbuka lewat aplikasi untuk staf login
- [ ] URL `/storage/medical-record-supporting-data/...` tanpa login → 404/403 dari luar
- [ ] Tidak ada salinan di bawah `storage/app/public`
- [ ] Catatan pelaksana, tanggal, hasil disimpan oleh owner
- [ ] Seluruh pelaksana memahami aturan §7: blokir nginx tidak pernah dilepas untuk rollback
