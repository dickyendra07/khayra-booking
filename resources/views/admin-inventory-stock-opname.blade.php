<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Opname - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }

        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1280px; margin: 0 auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 10px;
        }

        .title {
            margin: 0;
            font-size: 42px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
        }

        .subtitle {
            margin: 12px 0 0;
            max-width: 860px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            min-height: 42px;
            border: 0;
            cursor: pointer;
            padding: 0 16px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
        }

        .btn-soft {
            color: #2f7c7a;
            background: #ffffff;
            border: 1px solid #e6ebea;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
        }

        .alert-success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #ffe0e6;
        }

        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            margin-bottom: 18px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 20px;
            align-items: stretch;
        }

        .info-panel {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            color: #ffffff;
            border-radius: 24px;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .info-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.12), transparent 28%),
                linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: auto, 56px 56px, 56px 56px;
            pointer-events: none;
        }

        .info-panel > * {
            position: relative;
            z-index: 1;
        }

        .info-panel h2 {
            margin: 0 0 10px;
            font-size: 28px;
            line-height: 1.2;
            color: #ffffff;
        }

        .info-panel p,
        .info-panel li {
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.92);
        }

        .info-panel ul {
            margin: 16px 0 0;
            padding-left: 18px;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 26px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
        }

        .section-title {
            margin: 0;
            font-size: 30px;
            color: #22343a;
            font-weight: 900;
        }

        .section-subtitle {
            margin: 8px 0 20px;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.85;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .field.full { grid-column: 1 / -1; }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 900;
            color: #334155;
        }

        input, select, textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.7;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2f7c7a;
            box-shadow: 0 0 0 4px rgba(47,124,122,.08);
        }

        .stock-preview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 18px 0;
        }

        .preview-box {
            border: 1px solid #edf1f0;
            background: #fbfcfc;
            border-radius: 18px;
            padding: 16px;
        }

        .preview-box.warning {
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .preview-box.success {
            background: #ecfdf5;
            border-color: #bbf7d0;
        }

        .preview-box.danger {
            background: #fff1f2;
            border-color: #fecdd3;
        }

        .opname-warning {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 13px;
            line-height: 1.75;
            font-weight: 800;
        }

        .helper {
            margin-top: 7px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.6;
        }

        .preview-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #7b8794;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .preview-value {
            font-size: 28px;
            color: #22343a;
            font-weight: 900;
            line-height: 1;
        }

        .preview-sub {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.6;
        }

        .submit-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .form-grid, .stock-preview { grid-template-columns: 1fr; }
            .title { font-size: 32px; }
        }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'inventory-opname'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Inventory Control</span>
                    <h1 class="title">Stock Opname / Adjustment</h1>
                    <p class="subtitle">
                        Gunakan halaman ini untuk mencatat hasil hitung stok fisik. Sistem akan menghitung selisih dari stok sistem dan membuat stock movement adjustment otomatis.
                    </p>
                </div>

                <div class="actions">
                    <a href="/admin/inventory" class="btn btn-soft">← Inventory Control</a>
                    <a href="/admin/inventory/monthly-summary" class="btn btn-primary">Monthly Summary</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

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

            <section class="hero">
                <div class="hero-grid">
                    <section class="section-card">
                        <h2 class="section-title">Form Stock Opname</h2>
                        <p class="section-subtitle">
                            Pilih barang, isi stok fisik hasil hitung, lalu simpan adjustment.
                        </p>

                        <form method="POST" action="/admin/inventory/stock-opname">
                            @csrf

                            <div class="form-grid">
                                <div class="field full">
                                    <label>Barang</label>
                                    <select name="inventory_item_id" id="itemSelect" required onchange="updateStockPreview()">
                                        <option value="">Pilih Barang</option>
                                        @foreach($items as $item)
                                            <option
                                                value="{{ $item->id }}"
                                                data-stock="{{ $item->stock }}"
                                                data-unit="{{ $item->unit }}"
                                                data-minimum="{{ $item->minimum_stock }}"
                                                {{ (string)($selectedItemId ?? '') === (string)$item->id ? 'selected' : '' }}
                                            >
                                                {{ $item->sku }} - {{ $item->name }} / Stok Sistem: {{ $item->stock }} {{ $item->unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label>Stok Fisik Hasil Hitung</label>
                                    <input type="number" min="0" name="physical_stock" id="physicalStock" value="{{ old('physical_stock', 0) }}" required oninput="updateStockPreview()">
                                    <div class="helper">Isi sesuai stok fisik yang benar-benar dihitung di klinik.</div>
                                </div>

                                <div>
                                    <label>Reference</label>
                                    <input type="text" name="reference" value="{{ old('reference', 'Stock Opname ' . now()->format('Y-m-d')) }}">
                                    <div class="helper">Contoh: Stock Opname akhir bulan, koreksi audit, atau opname shift pagi.</div>
                                </div>

                                <div class="field full">
                                    <label>Notes</label>
                                    <textarea name="notes" id="notesInput" placeholder="Contoh: Opname akhir bulan, koreksi karena pemakaian terapi belum tercatat, barang rusak, dll.">{{ old('notes') }}</textarea>
                                    <div class="helper" id="notesHelper">Wajib diisi jika ada selisih stok.</div>
                                </div>
                            </div>

                            <div class="stock-preview">
                                <div class="preview-box" id="systemBox">
                                    <div class="preview-label">Stok Sistem</div>
                                    <div class="preview-value" id="systemStock">0</div>
                                    <div class="preview-sub" id="systemStockUnit">Belum pilih barang</div>
                                </div>

                                <div class="preview-box" id="physicalBox">
                                    <div class="preview-label">Stok Fisik</div>
                                    <div class="preview-value" id="physicalPreview">0</div>
                                    <div class="preview-sub">Angka hasil hitung manual</div>
                                </div>

                                <div class="preview-box" id="differenceBox">
                                    <div class="preview-label">Selisih</div>
                                    <div class="preview-value" id="differencePreview">0</div>
                                    <div class="preview-sub" id="differenceLabel">Belum ada selisih</div>
                                </div>
                            </div>

                            <div class="opname-warning" id="opnameWarning">
                                Pilih barang terlebih dahulu. Setelah disimpan, sistem akan membuat stock movement adjustment sebagai audit trail.
                            </div>

                            <div class="submit-row">
                                <button type="submit" class="btn btn-primary">Simpan Stock Opname</button>
                            </div>
                        </form>
                    </section>

                    <aside class="info-panel">
                        <h2>Cara Pakai</h2>
                        <p>
                            Stock opname dipakai untuk menyamakan stok sistem dengan stok fisik yang benar di klinik.
                        </p>

                        <ul>
                            <li>Pilih barang yang ingin dihitung.</li>
                            <li>Isi stok fisik sesuai hasil hitung nyata.</li>
                            <li>Sistem membuat movement adjustment otomatis.</li>
                            <li>Riwayat adjustment tetap tersimpan untuk audit.</li>
                            <li>Gunakan catatan agar alasan perubahan stok jelas.</li>
                        </ul>
                    </aside>
                </div>
            </section>
        </div>
    </main>
</div>

<script>
function resetPreviewClasses() {
    ['systemBox', 'physicalBox', 'differenceBox'].forEach(function(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('success', 'warning', 'danger');
    });
}

function updateStockPreview(syncPhysical = false) {
    const select = document.getElementById('itemSelect');
    const physicalInput = document.getElementById('physicalStock');
    const option = select.options[select.selectedIndex];

    const hasItem = !!option?.value;
    const systemStock = Number(option?.dataset?.stock || 0);
    const unit = option?.dataset?.unit || '';

    if (syncPhysical && hasItem) {
        physicalInput.value = systemStock;
    }

    const physicalStock = Number(physicalInput.value || 0);
    const difference = physicalStock - systemStock;

    document.getElementById('systemStock').textContent = hasItem ? systemStock : 0;
    document.getElementById('systemStockUnit').textContent = hasItem && unit ? 'Unit: ' + unit : 'Belum pilih barang';
    document.getElementById('physicalPreview').textContent = physicalStock;
    document.getElementById('differencePreview').textContent = difference > 0 ? '+' + difference : difference;

    let label = 'Tidak ada selisih';
    let warning = 'Jika stok fisik berbeda dari stok sistem, isi catatan agar audit trail jelas.';

    resetPreviewClasses();

    if (!hasItem) {
        label = 'Belum pilih barang';
        warning = 'Pilih barang terlebih dahulu. Setelah disimpan, sistem akan membuat stock movement adjustment sebagai audit trail.';
    } else if (difference > 0) {
        label = 'Stok fisik lebih banyak dari sistem';
        warning = 'Ada selisih plus. Catatan wajib diisi sebelum simpan.';
        document.getElementById('differenceBox').classList.add('success');
    } else if (difference < 0) {
        label = 'Stok fisik lebih sedikit dari sistem';
        warning = 'Ada selisih minus. Catatan wajib diisi sebelum simpan.';
        document.getElementById('differenceBox').classList.add('danger');
    } else {
        label = 'Stok fisik sama dengan sistem';
        warning = 'Tidak ada selisih. Opname tetap bisa disimpan sebagai bukti pengecekan stok.';
        document.getElementById('differenceBox').classList.add('success');
    }

    document.getElementById('differenceLabel').textContent = label;
    document.getElementById('opnameWarning').textContent = warning;
}

document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('itemSelect');
    const hasSelectedItem = !!select.value;

    updateStockPreview(hasSelectedItem);

    select.addEventListener('change', function() {
        updateStockPreview(true);
    });
});
</script>
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
