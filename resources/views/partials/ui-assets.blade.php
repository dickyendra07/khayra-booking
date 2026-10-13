{{-- Aset UI bersama (design system tombol & komponen). Sertakan paling akhir di <head>. --}}
<link rel="stylesheet" href="{{ asset('css/khayra-ui.css') }}?v={{ @filemtime(public_path('css/khayra-ui.css')) }}">
<script>
    // Loading state tombol submit: mencegah klik ganda tanpa mengubah method/CSRF/nilai form.
    (function () {
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || e.defaultPrevented || form.hasAttribute('data-no-loading') || form.target === '_blank') { return; }
            var btn = e.submitter || form.querySelector('button[type="submit"].kx-btn, button:not([type]).kx-btn');
            if (!btn || !btn.classList || !btn.classList.contains('kx-btn') || btn.formTarget === '_blank') { return; }
            if (form.__kxSubmitting) { e.preventDefault(); return; }
            form.__kxSubmitting = true;
            btn.classList.add('is-loading');
            btn.setAttribute('aria-busy', 'true');
            // Respons berupa unduhan/cetak tidak meninggalkan halaman: pulihkan tombol.
            setTimeout(function () { form.__kxSubmitting = false; btn.classList.remove('is-loading'); btn.removeAttribute('aria-busy'); }, 12000);
        });
        window.addEventListener('pageshow', function (e) {
            if (!e.persisted) { return; }
            document.querySelectorAll('.kx-btn.is-loading').forEach(function (b) { b.classList.remove('is-loading'); b.removeAttribute('aria-busy'); });
            document.querySelectorAll('form').forEach(function (f) { f.__kxSubmitting = false; });
        });
    })();
</script>
