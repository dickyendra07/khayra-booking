# UI Design System — Tombol & Komponen Dasar (Sprint UI Konsistensi)

Sumber tunggal: `public/css/khayra-ui.css`, dimuat lewat `resources/views/partials/ui-assets.blade.php`
(`@include('partials.ui-assets')` paling akhir di `<head>`). Hanya lapisan presentasi; tidak ada perubahan route,
method HTTP, CSRF, otorisasi, schema, atau logika bisnis.

## Pemakaian
```html
<a|button class="kx-btn kx-btn--primary">Simpan</a>
<button class="kx-btn kx-btn--outline kx-btn--sm">Edit</button>
<div class="kx-btn-group kx-btn-group--end"> ... </div>
```
| Varian | Kapan dipakai |
|---|---|
| `--primary` | Satu aksi utama per grup (Simpan, Kirim, Checkout, Terapkan Filter, "+ Tambah …") |
| `--secondary` | Aksi pendukung yang menonjol (Detail, Buka, Kirim WA) |
| `--outline` | Aksi netral (Batal, Kembali, Edit, Print, Reset) |
| `--danger` | Aksi destruktif (Hapus, Void, Refund, Tolak). Alur konfirmasi tidak diubah |
| `--icon` | Tombol persegi hanya ikon (wajib `aria-label`) |
| `--on-dark` | Di atas latar teal gelap (sidebar) |

Ukuran: `--sm` 34px, default 42px, `--lg` 48px (di layar ≤768px: 40/44/48px agar nyaman disentuh).
State: `:disabled`, `[aria-disabled="true"]`, `.is-loading` (spinner; submit form otomatis memasang loading dan mencegah klik ganda
tanpa mengubah nilai/CSRF/method form).

Komponen tambahan: `.kx-alert--{info,success,warning,danger}`, `.kx-help`, `.kx-input-group`, `.kx-check`,
`.kx-badge--{success,warning,danger,info}`, `.kx-col-actions` (kolom aksi tabel yang menempel di kanan).

## Aturan
1. Satu tombol Primary per grup aksi. 2. Makna sama = varian sama (tidak ada lagi hitam/biru/oranye/hijau acak).
3. Class lama (`btn-soft`, `submit-btn`, dll.) dipertahankan di markup hanya sebagai hook JS/layout; gaya visualnya kini milik `kx-btn`.
4. `tests/Feature/UiDesignSystemTest.php` memeriksa: semua halaman utama tetap render, setiap `<button>`/submit memakai `kx-btn`,
   dan tidak ada definisi visual tombol legacy baru di `<style>` halaman.

Pengecualian disengaja: `admin-informed-consent-print.blade.php` (dibekukan oleh golden-file consent v1) dan tab
`mr-tab-button`/`anatomy-side-tab` (komponen tab, bukan tombol aksi).

Style guide visual: `docs/ui-design-system-guide.html`.
