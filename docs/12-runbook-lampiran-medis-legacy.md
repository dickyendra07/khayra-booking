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

## 7. Rollback
- Sebelum karantina dihapus: `mv ~/lampiran-migrasi/karantina-<tanggal> "$SRC"` mengembalikan keadaan semula; hapus aturan `location ^~` di vhost bila URL publik lama harus dipulihkan (tidak disarankan).
- Aplikasi tidak bergantung pada lokasi publik (rute mencari disk privat lalu publik); tidak ada perubahan database sehingga tidak ada rollback database.
- Salinan privat boleh dibiarkan; tidak mengganggu.
- Bila rute baru bermasalah dan kode harus di-rollback ke `e3b91e8`: kembalikan file ke `storage/app/public` (dari karantina/backup) dan hapus aturan nginx, karena kode lama hanya membaca `/storage/...`.

## 8. Selesai bila
- [ ] Backup offsite terenkripsi terverifikasi
- [ ] Manifest sebelum = sesudah (`IDENTIK`)
- [ ] Lampiran lama terbuka lewat aplikasi untuk staf login
- [ ] URL `/storage/medical-record-supporting-data/...` tanpa login → 404/403 dari luar
- [ ] Tidak ada salinan di bawah `storage/app/public`
- [ ] Catatan pelaksana, tanggal, hasil disimpan oleh owner
