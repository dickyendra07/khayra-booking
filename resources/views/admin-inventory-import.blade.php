<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Inventory - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 980px; margin: 0 auto; }
        .top-actions { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #fff; }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }
        .card { background: #fff; border: 1px solid #ecefef; border-radius: 28px; padding: 28px; box-shadow: 0 14px 34px rgba(15,23,42,.05); }
        .kicker { display: inline-flex; padding: 8px 12px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; letter-spacing: .7px; text-transform: uppercase; margin-bottom: 14px; }
        h1 { margin: 0; font-size: 40px; line-height: 1.05; color: #22343a; font-weight: 900; }
        p { margin: 12px 0 22px; color: #6b7280; font-size: 14px; line-height: 1.9; }
        .format-box { background: #f7faf9; border: 1px solid #e5efec; border-radius: 18px; padding: 16px; margin-bottom: 18px; font-size: 13px; line-height: 1.8; color: #334155; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 900; color: #334155; }
        input[type="file"] { width: 100%; padding: 18px; border: 1px dashed #aac9c4; border-radius: 18px; background: #fbfdfd; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; flex-wrap: wrap; }
        .alert { padding: 14px 16px; border-radius: 14px; margin-bottom: 16px; font-size: 14px; line-height: 1.7; }
        .alert-error { background: #fff1f2; color: #be123c; border: 1px solid #ffe0e6; }
        @media (max-width: 760px) { .layout { display: block; } .main { padding: 16px; } h1 { font-size: 32px; } .btn { width: 100%; } }
    </style>
    <!-- Khayra PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2f7c7a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Khayra ERM">
    <meta name="apple-mobile-web-app-title" content="Khayra ERM">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/images/khayra-logo.png">
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'inventory'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/inventory" class="btn btn-soft">← Inventory Control</a>
                <a href="/admin/inventory/import/template" class="btn btn-soft">Download Template</a>
                <a href="/admin/inventory/export/csv" class="btn btn-primary">Export Existing Data</a>
            </div>

            <section class="card">
                <span class="kicker">CSV Import</span>
                <h1>Import inventory dari CSV.</h1>
                <p>
                    Upload file CSV untuk membuat barang baru atau update barang lama berdasarkan SKU.
                    Jika SKU sudah ada, sistem akan update datanya dan membuat adjustment movement bila stok berubah.
                </p>

                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="format-box">
                    Header wajib:<br>
                    <strong>sku, name, category, unit, stock, minimum_stock, purchase_price, selling_price, supplier, storage_location, status, notes</strong>
                </div>

                <form method="POST" action="/admin/inventory/import" enctype="multipart/form-data">
                    @csrf

                    <label>CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,text/csv" required>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Upload & Import</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
<script>
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    var deferredInstallPrompt = null;

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredInstallPrompt = event;

        if (document.getElementById('khayraInstallAppButton')) {
            return;
        }

        var button = document.createElement('button');
        button.id = 'khayraInstallAppButton';
        button.type = 'button';
        button.innerText = 'Install App';
        button.style.position = 'fixed';
        button.style.right = '18px';
        button.style.bottom = '18px';
        button.style.zIndex = '99999';
        button.style.border = '0';
        button.style.borderRadius = '999px';
        button.style.padding = '12px 16px';
        button.style.background = '#2f7c7a';
        button.style.color = '#ffffff';
        button.style.fontWeight = '900';
        button.style.fontFamily = 'Arial, sans-serif';
        button.style.fontSize = '13px';
        button.style.boxShadow = '0 14px 30px rgba(47,124,122,.24)';
        button.style.cursor = 'pointer';

        button.addEventListener('click', function () {
            if (!deferredInstallPrompt) {
                return;
            }

            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.finally(function () {
                deferredInstallPrompt = null;
                button.remove();
            });
        });

        document.body.appendChild(button);
    });
})();
</script>

</body>
</html>
