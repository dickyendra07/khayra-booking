<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1120px; margin: 0 auto; }
        .top-actions { display: flex; justify-content: flex-end; margin-bottom: 18px; }
        .ghost-link { display: inline-flex; align-items: center; text-decoration: none; padding: 11px 14px; border-radius: 12px; background: #ffffff; border: 1px solid #e6ebea; color: #2c5b5a; font-size: 13px; font-weight: 900; }
        .section-card { background: #ffffff; border: 1px solid #ecefef; border-radius: 24px; padding: 24px; box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04); }
        .badge { display: inline-block; padding: 8px 14px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; margin-bottom: 14px; }
        .title { font-size: 38px; font-weight: 900; color: #22343a; margin: 0 0 10px; line-height: 1.08; }
        .subtitle { font-size: 14px; line-height: 1.8; color: #6b7280; margin: 0 0 22px; max-width: 860px; }
        .error-box { background: #fff1f2; border: 1px solid #ffe0e6; color: #be123c; padding: 14px 16px; border-radius: 14px; margin-bottom: 18px; line-height: 1.8; font-size: 13px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .field.full { grid-column: 1 / -1; }
        .field label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 900; color: #334155; }
        input, select, textarea { width: 100%; padding: 14px; border: 1px solid #dde5e3; border-radius: 14px; font-size: 14px; background: #ffffff; color: #111827; font-family: Arial, sans-serif; }
        textarea { min-height: 120px; resize: vertical; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #176f69; box-shadow: 0 0 0 4px rgba(23,111,105,.08); }
        .hint { margin-top: 7px; font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; margin-top: 22px; }
        .submit-btn { border: none; background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #ffffff; padding: 14px 22px; border-radius: 14px; font-size: 14px; font-weight: 900; cursor: pointer; }
        .secondary-btn { display: inline-flex; align-items: center; text-decoration: none; padding: 14px 18px; border-radius: 14px; background: #f7faf9; border: 1px solid #e6ebea; color: #2c5b5a; font-size: 14px; font-weight: 900; }
        @media (max-width: 900px) { .layout { display: block; } .main { padding: 16px; } .form-grid { grid-template-columns: 1fr; } .title { font-size: 32px; } }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'inventory'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/inventory/{{ $item->id }}" class="ghost-link">← Kembali ke Detail</a>
            </div>

            <section class="section-card">
                <span class="badge">Edit Inventory Item</span>
                <h1 class="title">Edit Barang Inventory</h1>
                <p class="subtitle">Perbarui data master barang. Perubahan jumlah stok dilakukan dari halaman detail melalui stock movement.</p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/inventory/{{ $item->id }}/update">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label>SKU / Kode Barang</label>
                            <input type="text" name="sku" value="{{ old('sku', $item->sku) }}" required>
                        </div>

                        <div class="field">
                            <label>Nama Barang</label>
                            <input type="text" name="name" value="{{ old('name', $item->name) }}" required>
                        </div>

                        <div class="field">
                            <label>Kategori</label>
                            <input type="text" name="category" value="{{ old('category', $item->category) }}">
                        </div>

                        <div class="field">
                            <label>Unit</label>
                            <input type="text" name="unit" value="{{ old('unit', $item->unit) }}" required>
                        </div>

                        <div class="field">
                            <label>Current Stock</label>
                            <input type="text" value="{{ $item->stock }} {{ $item->unit }}" disabled>
                            <div class="hint">Stok hanya bisa diubah lewat stock movement agar ada riwayat.</div>
                        </div>

                        <div class="field">
                            <label>Minimum Stok</label>
                            <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" min="0" required>
                        </div>

                        <div class="field">
                            <label>Harga Beli</label>
                            <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}" min="0" required>
                        </div>

                        <div class="field">
                            <label>Harga Jual</label>
                            <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $item->selling_price) }}" min="0" required>
                        </div>

                        <div class="field">
                            <label>Supplier</label>
                            <input type="text" name="supplier" value="{{ old('supplier', $item->supplier) }}">
                        </div>

                        <div class="field">
                            <label>Lokasi Penyimpanan</label>
                            <input type="text" name="storage_location" value="{{ old('storage_location', $item->storage_location) }}">
                        </div>

                        <div class="field">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="active" {{ old('status', $item->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $item->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label>Catatan</label>
                            <textarea name="notes">{{ old('notes', $item->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <a href="/admin/inventory/{{ $item->id }}" class="secondary-btn">Batal</a>
                        <button type="submit" class="submit-btn">Simpan Perubahan</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>
