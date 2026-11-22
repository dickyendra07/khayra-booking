# 15 — Rekomendasi Nginx defense-in-depth (belum diterapkan)

> Dokumen rekomendasi. **Tidak ada yang diterapkan ke VPS.** Topologi CloudPanel harus diverifikasi lebih dulu (`docs/16`). Sintaks di bawah contoh; sesuaikan dengan file vhost yang dihasilkan CloudPanel dan simpan lewat mekanisme "custom Nginx directives" CloudPanel agar tidak tertimpa. Selalu `nginx -t` sebelum reload.

Tujuan: menolak di tepi **sebelum** PHP berjalan, memakai pemetaan yang sama dengan `config/khayra_hosts.php`. Laravel tetap menjadi sumber kebenaran; Nginx hanya lapis kedua.

## 1. Server default (hostname asing / akses lewat IP)
Pastikan ada `default_server` yang menolak host tidak dikenal, untuk port 80 dan 443 (443 butuh sertifikat apa pun; boleh self-signed):
```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    listen 443 ssl default_server;
    listen [::]:443 ssl default_server;
    ssl_certificate     /etc/nginx/ssl-default/selfsigned.crt;
    ssl_certificate_key /etc/nginx/ssl-default/selfsigned.key;
    server_name _;
    return 444;      # tutup koneksi tanpa respons
}
```
Verifikasi setelah reload: `curl -k -sS -o /dev/null -w '%{http_code}\n' https://<IP-VPS>/admin/login` harus gagal/444.

## 2. Allow-list path per vhost
Di blok `server` HTTPS tiap hostname, **sebelum** `location /` yang proxy ke 8080. Contoh untuk `patient.khayraphysio.com`:
```nginx
location ~ ^/(patient|booking)(/|$) { proxy_pass http://127.0.0.1:8080; include <proxy-headers-bawaan-CloudPanel>; }
location = / { proxy_pass http://127.0.0.1:8080; include <proxy-headers-bawaan-CloudPanel>; }
location = /up { proxy_pass http://127.0.0.1:8080; include <proxy-headers-bawaan-CloudPanel>; }
location / { return 404; }      # asset statis (css/js/images/sw.js/manifest) tetap perlu diizinkan; lihat catatan
```
Pemetaan:
| vhost | path yang diteruskan |
|---|---|
| `app.khayraphysio.com` | `^/(admin\|owner)(/\|$)`, `= /`, `= /up` |
| `physio.khayraphysio.com` | `^/therapist(/\|$)`, `= /`, `= /up` |
| `patient.khayraphysio.com` | `^/(patient\|booking)(/\|$)`, `= /`, `= /up` |

Catatan penting:
- **Aset statis**: `public/css`, `public/images`, `favicon.ico`, `manifest.webmanifest`, `sw.js`, `robots.txt` harus tetap terlayani (dari `root` situs oleh Nginx) di ketiga hostname. Tambahkan `location` eksplisit untuk path tersebut atau gunakan `try_files` ke root sebelum `return 404`. Uji halaman login di browser (logo, CSS) setelah perubahan.
- Percent-encoding: Nginx menormalkan URI untuk `location` (dekode `%XX`, `//`, `..`) sebelum mencocokkan, sehingga `/%61dmin` tidak lolos ke vhost pasien. Tetap uji manual.
- **Jangan** menaruh path allow-list sebagai pengganti login: `/admin/*` tetap wajib sesi.
- Jangan membuka `/storage/*`. Lampiran medis hanya lewat route login; pastikan `public/storage` symlink tidak ada atau tidak melayani `medical-record-supporting-data` (`docs/12`).

## 3. Header yang diteruskan
Pastikan proxy depan mengirim `Host $host` (bukan `127.0.0.1:8080`), dan menimpa/menambah `X-Forwarded-For $proxy_add_x_forwarded_for`, `X-Forwarded-Proto $scheme`. Detail dan alasan di `docs/16`. Jangan meneruskan `X-Forwarded-Host` dari klien; Laravel mengabaikannya, dan sebaiknya dihapus: `proxy_set_header X-Forwarded-Host "";`.

## 4. Backend 8080
Port 8080 harus **hanya** `listen 127.0.0.1:8080;` (bukan `0.0.0.0`) dan firewall menutup 8080 dari luar (`ss -ltnp | grep 8080`; `ufw status`). Tanpa itu, klien luar melewati allow-list vhost.

## 5. Hardening ringan lainnya
- HSTS di ketiga vhost: `add_header Strict-Transport-Security "max-age=31536000" always;` (tanpa `includeSubDomains`/`preload` sampai semua subdomain `khayraphysio.com` dipastikan HTTPS).
- `server_tokens off;`
- Rate limit tepi (`limit_req_zone`) untuk `POST /patient/login`, `/therapist/login`, `/admin/login`, `/booking` sebagai lapis di luar throttle aplikasi (yang bergantung IP klien benar; lihat `docs/16`).
- `client_max_body_size 10m;` sejalan batas unggahan lampiran (10 MB).
- Log akses terpisah per vhost; pantau lonjakan 404 pada `/admin` dari hostname pasien/therapist.

## 6. Uji penerimaan (setelah diterapkan, dari luar)
```bash
for h in app physio patient; do for p in /admin/login /owner/dashboard /therapist/login /patient /booking; do
  printf '%-10s %-18s ' $h $p; curl -s -o /dev/null -w '%{http_code}\n' https://$h.khayraphysio.com$p; done; done
```
Yang diharapkan: hanya kombinasi sesuai tabel pemetaan yang bukan 404.
