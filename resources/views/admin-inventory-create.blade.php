<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Inventory - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1280px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .kicker { display: inline-flex; padding: 8px 12px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; letter-spacing: .7px; text-transform: uppercase; margin-bottom: 10px; }
        .page-title { margin: 0; font-size: 40px; line-height: 1.04; color: #22343a; font-weight: 900; letter-spacing: -.8px; }
        .page-subtitle { margin: 12px 0 0; max-width: 840px; color: #6b7280; font-size: 14px; line-height: 1.9; }
        .top-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #fff; }
        .btn-dark { background: linear-gradient(135deg, #22343a 0%, #17232b 100%); color: #fff; }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }
        .grid { display: grid; grid-template-columns: 1.05fr .95fr; gap: 18px; align-items: start; }
        .card { background: #ffffff; border: 1px solid #ecefef; border-radius: 28px; padding: 24px; box-shadow: 0 14px 34px rgba(15,23,42,.05); }
        .card-title { margin: 0; font-size: 28px; color: #22343a; font-weight: 900; }
        .card-subtitle { margin: 8px 0 20px; color: #6b7280; font-size: 13px; line-height: 1.8; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field.full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 900; color: #334155; }
        input, select, textarea {
            width: 100%; padding: 14px 14px; border: 1px solid #dde5e3; border-radius: 14px;
            font-size: 14px; background: #fff; color: #111827; font-family: Arial, sans-serif;
        }
        textarea { min-height: 148px; resize: vertical; line-height: 1.7; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #2f7c7a; box-shadow: 0 0 0 4px rgba(47,124,122,.08); }
        .hint { margin-top: 6px; font-size: 12px; line-height: 1.6; color: #7b8794; }
        .actions { display: flex; justify-content: flex-end; margin-top: 22px; gap: 10px; flex-wrap: wrap; }
        .error-box { background: #fff1f2; border: 1px solid #ffe0e6; color: #be123c; padding: 14px 16px; border-radius: 14px; margin-bottom: 18px; line-height: 1.8; font-size: 13px; }
        .info-card { background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%); color: #fff; }
        .info-card .card-title { color: #fff; }
        .info-card .card-subtitle { color: rgba(255,255,255,.92); }
        .format-box { background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.16); border-radius: 18px; padding: 16px; color: #fff; font-size: 13px; line-height: 1.8; margin-bottom: 18px; }
        .quick-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
        .quick-btn { border: 1px solid #dfe8e6; background: #fbfdfd; color: #2f7c7a; border-radius: 14px; padding: 12px; text-align: left; font-weight: 900; cursor: pointer; }
        @media (max-width: 1100px) { .grid { grid-template-columns: 1fr; } }
        @media (max-width: 760px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .page-title { font-size: 32px; }
            .form-grid, .quick-grid { grid-template-columns: 1fr; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'inventory'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="kicker">Inventory Item</span>
                    <h1 class="page-title">Tambah barang inventory lebih cepat.</h1>
                    <p class="page-subtitle">
                        Input satu barang, bulk input banyak barang, atau import CSV seperti flow inventory control POS.
                    </p>
                </div>

                <div class="top-actions">
                    <a href="/admin/inventory" class="btn btn-soft">← Inventory Control</a>
                    <a href="/admin/inventory/import" class="btn btn-dark">Import CSV</a>
                    <a href="/admin/inventory/export/csv" class="btn btn-primary">Export CSV</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="grid">
                <section class="card">
                    <h2 class="card-title">Single Item</h2>
                    <p class="card-subtitle">Untuk tambah satu barang klinik, alat terapi, consumable, atau produk jual.</p>

                    <div class="quick-grid">
                        <button type="button" class="quick-btn" onclick="fillExample('tape')">Template Kinesio Tape</button>
                        <button type="button" class="quick-btn" onclick="fillExample('gel')">Template Gel Terapi</button>
                        <button type="button" class="quick-btn" onclick="fillExample('band')">Template Resistance Band</button>
                        <button type="button" class="quick-btn" onclick="fillExample('ice')">Template Ice Pack</button>
                    </div>

                    <form method="POST" action="/admin/inventory">
                        @csrf

                        <div class="form-grid">
                            <div class="field">
                                <label>SKU / Kode Barang</label>
                                <input id="sku" type="text" name="sku" value="{{ old('sku') }}" placeholder="Contoh: PHY-001" required>
                            </div>

                            <div class="field">
                                <label>Nama Barang</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Kinesio Tape" required>
                            </div>

                            <div class="field">
                                <label>Kategori</label>
                                <input id="category" type="text" name="category" value="{{ old('category') }}" placeholder="Consumable / Alat Terapi / Produk">
                            </div>

                            <div class="field">
                                <label>Unit</label>
                                <input id="unit" type="text" name="unit" value="{{ old('unit', 'pcs') }}" placeholder="pcs / box / roll">
                            </div>

                            <div class="field">
                                <label>Stok Awal</label>
                                <input id="stock" type="number" name="stock" value="{{ old('stock', 0) }}" min="0">
                                <div class="hint">Akan otomatis membuat riwayat stok masuk awal.</div>
                            </div>

                            <div class="field">
                                <label>Minimum Stok</label>
                                <input id="minimum_stock" type="number" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" min="0">
                            </div>

                            <div class="field">
                                <label>Harga Beli</label>
                                <input id="purchase_price" type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', 0) }}" min="0">
                            </div>

                            <div class="field">
                                <label>Harga Jual</label>
                                <input id="selling_price" type="number" step="0.01" name="selling_price" value="{{ old('selling_price', 0) }}" min="0">
                            </div>

                            <div class="field">
                                <label>Supplier</label>
                                <input id="supplier" type="text" name="supplier" value="{{ old('supplier') }}" placeholder="Nama supplier">
                            </div>

                            <div class="field">
                                <label>Lokasi Penyimpanan</label>
                                <input id="storage_location" type="text" name="storage_location" value="{{ old('storage_location') }}" placeholder="Lemari A / Gudang / Front desk">
                            </div>

                            <div class="field">
                                <label>Status</label>
                                <select name="status">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="field full">
                                <label>Catatan</label>
                                <textarea name="notes" placeholder="Catatan tambahan tentang barang.">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Simpan Barang</button>
                        </div>
                    </form>
                </section>

                <section class="card info-card">
                    <h2 class="card-title">Bulk Input</h2>
                    <p class="card-subtitle">
                        Cocok kalau client kasih list barang banyak tapi belum dalam format CSV.
                    </p>

                    <div class="format-box">
                        Format per baris:<br>
                        <strong>SKU | Nama | Kategori | Unit | Stok | Minimum | Harga Beli | Harga Jual | Supplier | Lokasi</strong>
                    </div>

                    <form method="POST" action="/admin/inventory/bulk-create">
                        @csrf

                        <div class="field">
                            <label style="color:#fff;">Bulk Items</label>
                            <textarea name="bulk_items" placeholder="PHY-001 | Kinesio Tape | Consumable | pcs | 20 | 5 | 35000 | 55000 | Supplier A | Lemari A
PHY-002 | Gel Terapi | Consumable | tube | 12 | 3 | 25000 | 45000 | Supplier B | Lemari B"></textarea>
                        </div>

                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Import dari Text</button>
                            <a href="/admin/inventory/import/template" class="btn btn-soft">Download CSV Template</a>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
</div>

<script>
function fillExample(type) {
    const samples = {
        tape: {
            sku: 'PHY-001',
            name: 'Kinesio Tape',
            category: 'Consumable',
            unit: 'roll',
            stock: 20,
            minimum_stock: 5,
            purchase_price: 35000,
            selling_price: 55000,
            supplier: 'Supplier Alat Fisio',
            storage_location: 'Lemari Consumable'
        },
        gel: {
            sku: 'PHY-002',
            name: 'Gel Terapi',
            category: 'Consumable',
            unit: 'tube',
            stock: 12,
            minimum_stock: 3,
            purchase_price: 25000,
            selling_price: 45000,
            supplier: 'Supplier Klinik',
            storage_location: 'Lemari Terapi'
        },
        band: {
            sku: 'PHY-003',
            name: 'Resistance Band',
            category: 'Alat Terapi',
            unit: 'pcs',
            stock: 15,
            minimum_stock: 4,
            purchase_price: 45000,
            selling_price: 75000,
            supplier: 'Supplier Rehab',
            storage_location: 'Rak Alat'
        },
        ice: {
            sku: 'PHY-004',
            name: 'Ice Pack',
            category: 'Alat Terapi',
            unit: 'pcs',
            stock: 8,
            minimum_stock: 2,
            purchase_price: 30000,
            selling_price: 50000,
            supplier: 'Supplier Rehab',
            storage_location: 'Freezer / Rak Alat'
        }
    };

    const item = samples[type];

    Object.keys(item).forEach(function (key) {
        const element = document.getElementById(key);
        if (element) {
            element.value = item[key];
        }
    });
}
</script>
</body>
</html>
