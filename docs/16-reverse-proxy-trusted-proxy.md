# 16 — Reverse proxy dan trusted proxy (CloudPanel: Nginx depan → 8080 → PHP-FPM)

> **Rekomendasi. Tidak ada konfigurasi VPS yang diubah dan topologi belum diverifikasi.** Default PR ini: `TRUSTED_PROXIES` kosong (perilaku sama seperti sebelumnya). Jangan mengisinya sebelum langkah verifikasi di bawah dikerjakan operator.

## Arsitektur yang diasumsikan (harus dikonfirmasi)
```
Internet ─► Nginx depan :443 (TLS, per-hostname) ─► Nginx backend 127.0.0.1:8080 ─► PHP-FPM (socket/port lokal) ─► Laravel
```
Pada topologi ini Laravel melihat `REMOTE_ADDR` = alamat proxy (127.0.0.1), bukan klien. Tanpa trusted proxy:
1. **`$request->ip()` = 127.0.0.1 untuk semua orang.** Seluruh throttle per IP (`LoginThrottle` lapis `ip` 20/15 mnt untuk admin/therapist/patient; rate limit POST `/booking` 10/menit/IP) menjadi satu ember global: 20 kegagalan login dari siapa pun mengunci login **semua** pasien/therapist selama 15 menit (DoS login), dan 10 booking/menit global. Sebaliknya lapis `pair` (identitas+IP) tidak efektif membedakan penyerang.
2. **Skema HTTPS tidak terdeteksi** bila backend 8080 berupa HTTP dan `fastcgi_param HTTPS` kosong: `redirect()`/`url()` menghasilkan `http://…` (satu lompatan 301 ekstra dan jejak downgrade). Cookie tetap `Secure` karena `SESSION_SECURE_COOKIE=true` eksplisit.

Dugaan ini harus dibuktikan (langkah A), bukan diasumsikan.

## A. Verifikasi topologi (read-only, dijalankan operator; belum dilakukan)
```bash
# 1. Siapa yang listen di mana (8080 harus 127.0.0.1, bukan 0.0.0.0)
ss -ltnp | grep -E ':(80|443|8080|6081)\b'
# 2. Konfigurasi efektif vhost situs (cari proxy_pass, header, fastcgi_param)
nginx -T 2>/dev/null | grep -nE "server_name|listen|proxy_pass|proxy_set_header|set_real_ip_from|real_ip_header|fastcgi_pass|fastcgi_param (HTTPS|REMOTE_ADDR|HTTP_HOST)"
# 3. Apakah DNS dibelakang CDN/proxy (Cloudflare dsb.)? Bila ya, IP klien datang dari header CDN.
dig +short app.khayraphysio.com patient.khayraphysio.com physio.khayraphysio.com
# 4. Firewall: 8080 tidak boleh terbuka dari luar
ufw status 2>/dev/null; iptables -S 2>/dev/null | grep 8080
```
Catat jawaban untuk: (a) apakah `Host` diteruskan apa adanya (`proxy_set_header Host $host`); (b) apakah ada `X-Forwarded-For` / `X-Forwarded-Proto`; (c) apakah ada CDN/Varnish di depan; (d) IP/alamat sumber koneksi ke backend; (e) apakah 8080 hanya loopback.

Uji dari dalam server setelah sebuah hostname dites (pakai route yang tidak mengubah data):
```bash
curl -sS -o /dev/null -w '%{http_code} %{redirect_url}\n' -H 'Host: app.khayraphysio.com' http://127.0.0.1:8080/admin/login
```
Bila hasil bukan 200 setelah penegakan hostname diaktifkan, `Host` tidak diteruskan benar.

## B. Konfigurasi yang direkomendasikan setelah A terkonfirmasi
Aplikasi sudah siap (PR ini): `config/trustedproxy.php` membaca `TRUSTED_PROXIES`, membuang wildcard, dan `bootstrap/app.php` hanya mempercayai header `X-Forwarded-For`, `-Port`, `-Proto` (**tidak** `-Host`, `-Prefix`, AWS-ELB).

Untuk topologi CloudPanel di atas (proxy depan dan backend sama-sama di host ini, koneksi dari loopback):
```dotenv
TRUSTED_PROXIES=127.0.0.1
```
Alasan dan batas:
- Hanya `127.0.0.1` (dan `::1` bila backend mendengarkan IPv6 loopback): proxy depan satu mesin. Tidak ada `*`, tidak ada rentang privat luas (`10.0.0.0/8`) — dengan begitu hanya proses lokal yang boleh menentukan IP klien.
- Syarat keamanan: **8080 hanya loopback** dan proxy depan **menimpa/menambahkan** `X-Forwarded-For` (`$proxy_add_x_forwarded_for`). Symfony memakai entri paling kanan yang bukan proxy tepercaya, jadi klien yang mengirim `X-Forwarded-For` palsu hanya menambah entri kiri yang diabaikan.
- Bila ada CDN (mis. Cloudflare) di depan Nginx: IP klien harus dipulihkan di Nginx depan lebih dulu (`set_real_ip_from <rentang CDN resmi>; real_ip_header CF-Connecting-IP;`) sehingga `$remote_addr` yang ditambahkan ke XFF adalah klien asli; `TRUSTED_PROXIES` di Laravel tetap `127.0.0.1`. Jangan mempercayai rentang CDN langsung di Laravel kecuali backend dapat dijangkau langsung dari CDN.
- Dampak positif: throttle per IP kembali bermakna, URL absolut dan redirect memakai `https`, `request()->secure()` benar.
- Dampak yang perlu diuji: setelah `TRUSTED_PROXIES` diisi, hitungan throttle lama di cache (kunci `127.0.0.1`) tidak relevan; tidak perlu dibersihkan.

Setelah diisi: `php artisan config:clear && php artisan config:cache`, lalu `php artisan khayra:preflight` (WARN `TRUSTED_PROXIES` hilang) dan uji: 6 login pasien salah dari dua jaringan berbeda tidak saling mengunci.

## C. Yang tidak boleh dilakukan
- `TRUSTED_PROXIES=*` / `**` (config membuangnya; bila dipakai, siapa pun yang menjangkau 8080 dapat memalsukan IP klien).
- Mempercayai `X-Forwarded-Host` (tidak didukung sengaja; hostname hanya dari `Host`).
- Mengaktifkan pada lingkungan yang topologinya belum dikonfirmasi.
