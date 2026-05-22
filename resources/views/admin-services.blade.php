<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Layanan - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f4f7f6;
            color: #22343a;
        }
        .layout { display: flex; align-items: flex-start; min-height: 100vh; }
        .main { flex: 1; padding: 28px; min-width: 0; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px;
        }
        .eyebrow {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef7f5;
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .65px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        h1 { margin: 0; font-size: 42px; line-height: 1.05; letter-spacing: -1.4px; }
        .subtitle { margin: 12px 0 0; color: #64748b; font-size: 15px; line-height: 1.7; max-width: 900px; }
        .card {
            background: #ffffff;
            border: 1px solid #e8eeee;
            border-radius: 26px;
            padding: 24px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.045);
            margin-bottom: 18px;
        }
        .hero {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 18px;
            align-items: stretch;
        }
        .hero-panel {
            border-radius: 22px;
            padding: 24px;
            background: linear-gradient(135deg, #f9fdfc 0%, #eef8f6 100%);
            border: 1px solid #dfecea;
        }
        .dark-panel {
            border-radius: 22px;
            padding: 24px;
            background:
                linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(135deg, #2f7c7a 0%, #1d5961 100%);
            background-size: 44px 44px, 44px 44px, auto;
            color: white;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }
        .stat {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 18px;
            background: #ffffff;
        }
        .stat-label {
            color: #7b8794;
            text-transform: uppercase;
            letter-spacing: .65px;
            font-size: 11px;
            font-weight: 900;
        }
        .stat-value {
            margin-top: 8px;
            font-size: 28px;
            font-weight: 950;
            letter-spacing: -.8px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1.3fr .7fr .7fr .7fr .7fr;
            gap: 12px;
            align-items: end;
        }
        .form-row-2 {
            display: grid;
            grid-template-columns: .8fr 1.4fr .5fr;
            gap: 12px;
            margin-top: 12px;
            align-items: end;
        }
        label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: #334155;
            margin-bottom: 8px;
        }
        input, select, textarea {
            width: 100%;
            border: 1px solid #dce5e3;
            border-radius: 14px;
            padding: 12px 13px;
            font-size: 14px;
            outline: none;
            background: #ffffff;
        }
        textarea { min-height: 46px; resize: vertical; }
        input:focus, select:focus, textarea:focus {
            border-color: #2f7c7a;
            box-shadow: 0 0 0 4px rgba(47,124,122,.10);
        }
        .btn {
            border: 0;
            border-radius: 14px;
            padding: 13px 16px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        .btn-primary { background: #2f7c7a; color: #ffffff; }
        .btn-soft { background: #eef7f5; color: #2f7c7a; border: 1px solid #d8ebe7; }
        .btn-danger { background: #fff1f2; color: #be123c; border: 1px solid #ffe4e6; }
        .filter-grid {
            display: grid;
            grid-template-columns: 1fr .35fr auto;
            gap: 12px;
            align-items: end;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border: 1px solid #edf1f0;
            border-radius: 18px;
            background: #ffffff;
        }
        th {
            background: #f8fbfa;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .55px;
            font-size: 11px;
            font-weight: 950;
            text-align: left;
            padding: 14px;
            border-bottom: 1px solid #edf1f0;
        }
        td {
            padding: 14px;
            border-bottom: 1px solid #edf1f0;
            vertical-align: top;
            font-size: 14px;
        }
        tr:last-child td { border-bottom: 0; }
        .service-name { font-weight: 950; color: #22343a; font-size: 15px; }
        .muted { color: #7b8794; font-size: 12px; line-height: 1.5; margin-top: 4px; }
        .money { font-weight: 900; color: #22343a; white-space: nowrap; }
        .badge {
            display: inline-flex;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            background: #eef7f5;
            color: #2f7c7a;
        }
        .badge.inactive { background: #f1f5f9; color: #64748b; }
        .service-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
        }
        .table-action-cell {
            width: 190px;
            min-width: 190px;
            text-align: right;
        }
        .edit-box {
            border: 1px solid #d8ebe7;
            border-radius: 22px;
            padding: 20px;
            background: linear-gradient(145deg, #ffffff 0%, #f7fbfa 100%);
            margin-bottom: 18px;
        }
        .edit-form {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            align-items: end;
        }
        .edit-form .span-2 { grid-column: span 2; }
        .edit-form .span-4 { grid-column: 1 / -1; }
        .edit-form input,
        .edit-form select {
            min-width: 0;
        }
        .table-scroll table {
            table-layout: fixed;
        }
        .table-scroll th:nth-child(1),
        .table-scroll td:nth-child(1) { width: 28%; }
        .table-scroll th:nth-child(2),
        .table-scroll td:nth-child(2),
        .table-scroll th:nth-child(3),
        .table-scroll td:nth-child(3),
        .table-scroll th:nth-child(4),
        .table-scroll td:nth-child(4),
        .table-scroll th:nth-child(5),
        .table-scroll td:nth-child(5) { width: 13%; }
        .table-scroll th:nth-child(6),
        .table-scroll td:nth-child(6) { width: 9%; }
        .table-scroll th:nth-child(7),
        .table-scroll td:nth-child(7) { width: 150px; }
        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
            font-weight: 800;
            margin-bottom: 18px;
        }
        .table-scroll { overflow-x: auto; }
        @media (max-width: 1100px) {
            .hero { grid-template-columns: 1fr; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .form-grid, .form-row-2, .filter-grid, .edit-form { grid-template-columns: 1fr; }
            .edit-form .span-2,
            .edit-form .span-4 { grid-column: auto; }
            h1 { font-size: 34px; }
        }
        @media (max-width: 760px) {
            .layout { display: block; }
            .main { padding: 18px; }
            .stat-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'services'])

    <main class="main">
        <div class="page-header">
            <div>
                <div class="eyebrow">Master Price List</div>
                <h1>Master Layanan Klinik</h1>
                <p class="subtitle">
                    Pusat data layanan, harga per visit, paket 3X, 6X, 12X, dan keterangan layanan. Promo tidak dimasukkan di sini karena sudah dikelola lewat modul Promo Setting.
                </p>
            </div>

            <a href="/admin/cashier" class="btn btn-soft">Ke Kasir Checkout</a>
        </div>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <section class="card hero">
            <div class="hero-panel">
                <div class="eyebrow">Service Engine</div>
                <h2 style="font-size: 28px; margin: 0 0 10px; letter-spacing: -.8px;">Harga layanan dibuat konsisten dari satu master data.</h2>
                <p class="subtitle" style="margin: 0;">
                    Admin bisa menambah layanan baru, mengubah harga paket, dan menonaktifkan layanan tanpa menghapus histori transaksi.
                </p>
            </div>

            <div class="dark-panel">
                <h2 style="font-size: 26px; margin: 0 0 10px;">Ringkasan Master</h2>
                <p style="font-size: 14px; line-height: 1.7; opacity: .88; margin: 0;">
                    Data ini menjadi dasar untuk checkout, dokumen pembelian paket, dan analisis layanan yang paling sering dipilih pasien.
                </p>
            </div>
        </section>

        <section class="stat-grid">
            <div class="stat">
                <div class="stat-label">Total Layanan</div>
                <div class="stat-value">{{ $services->count() }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Active</div>
                <div class="stat-value">{{ $activeServices }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Paket Ready</div>
                <div class="stat-value">{{ $packageReadyServices }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Kategori</div>
                <div class="stat-value">{{ $categories->count() }}</div>
            </div>
        </section>

        <section class="card">
            <h2 style="margin: 0 0 6px; font-size: 26px;">Tambah Layanan</h2>
            <p class="subtitle" style="margin-top: 0;">Masukkan harga normal. Promo atau diskon tetap dibuat dari Promo Setting.</p>

            <form method="POST" action="/admin/services">
                @csrf

                <div class="form-grid">
                    <div>
                        <label>Nama Layanan</label>
                        <input type="text" name="name" placeholder="Contoh: Pain Management Program" required>
                    </div>
                    <div>
                        <label>Harga / Visit</label>
                        <input type="number" name="price_per_visit" placeholder="250000" min="0" required>
                    </div>
                    <div>
                        <label>Paket 3X</label>
                        <input type="number" name="package_3x_price" placeholder="Opsional" min="0">
                    </div>
                    <div>
                        <label>Paket 6X</label>
                        <input type="number" name="package_6x_price" placeholder="Opsional" min="0">
                    </div>
                    <div>
                        <label>Paket 12X</label>
                        <input type="number" name="package_12x_price" placeholder="Opsional" min="0">
                    </div>
                </div>

                <div class="form-row-2">
                    <div>
                        <label>Kategori</label>
                        <input type="text" name="category" placeholder="Program / Add-on / Specialist">
                    </div>
                    <div>
                        <label>Keterangan</label>
                        <input type="text" name="notes" placeholder="Contoh: Hanya untuk kondisi akut">
                    </div>
                    <div>
                        <label>Status</label>
                        <select name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 14px;">
                    <button type="submit" class="btn btn-primary">Simpan Layanan</button>
                </div>
            </form>
        </section>

        @php
            $editingService = request('edit')
                ? $services->firstWhere('id', (int) request('edit'))
                : null;
        @endphp

        @if($editingService)
            <section class="edit-box" id="edit-service">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; margin-bottom: 18px;">
                    <div>
                        <div class="eyebrow" style="margin-bottom: 10px;">Edit Layanan</div>
                        <h2 style="margin: 0; font-size: 26px; letter-spacing: -.7px;">{{ $editingService->name }}</h2>
                        <p class="subtitle" style="margin-top: 8px;">Ubah harga, paket, kategori, status, dan keterangan layanan dari panel ini.</p>
                    </div>

                    <a href="/admin/services" class="btn btn-soft">Tutup Edit</a>
                </div>

                <form method="POST" action="/admin/services/{{ $editingService->id }}/update" class="edit-form">
                    @csrf

                    <div class="span-2">
                        <label>Nama Layanan</label>
                        <input type="text" name="name" value="{{ $editingService->name }}" required>
                    </div>

                    <div>
                        <label>Harga / Visit</label>
                        <input type="number" name="price_per_visit" value="{{ $editingService->price_per_visit }}" min="0" required>
                    </div>

                    <div>
                        <label>Status</label>
                        <select name="status">
                            <option value="active" {{ $editingService->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $editingService->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label>Paket 3X</label>
                        <input type="number" name="package_3x_price" value="{{ $editingService->package_3x_price }}" placeholder="N/A" min="0">
                    </div>

                    <div>
                        <label>Paket 6X</label>
                        <input type="number" name="package_6x_price" value="{{ $editingService->package_6x_price }}" placeholder="N/A" min="0">
                    </div>

                    <div>
                        <label>Paket 12X</label>
                        <input type="number" name="package_12x_price" value="{{ $editingService->package_12x_price }}" placeholder="N/A" min="0">
                    </div>

                    <div>
                        <label>Kategori</label>
                        <input type="text" name="category" value="{{ $editingService->category }}" placeholder="Program / Add-on / Specialist">
                    </div>

                    <div class="span-4">
                        <label>Keterangan</label>
                        <input type="text" name="notes" value="{{ $editingService->notes }}" placeholder="Keterangan layanan">
                    </div>

                    <div class="span-4" style="display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
                        <a href="/admin/services" class="btn btn-soft">Batal</a>
                        <button class="btn btn-primary" type="submit">Update Layanan</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="card">
            <h2 style="margin: 0 0 6px; font-size: 26px;">Daftar Layanan</h2>
            <p class="subtitle" style="margin-top: 0;">Filter dan edit layanan dari satu halaman.</p>

            <form method="GET" action="/admin/services" class="filter-grid" style="margin: 18px 0;">
                <div>
                    <label>Search</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama layanan, kategori, keterangan">
                </div>
                <div>
                    <label>Kategori</label>
                    <select name="category">
                        <option value="">Semua</option>
                        @foreach($categories as $item)
                            <option value="{{ $item }}" {{ $category === $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary" type="submit">Filter</button>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th>Harga / Visit</th>
                            <th>Paket 3X</th>
                            <th>Paket 6X</th>
                            <th>Paket 12X</th>
                            <th>Status</th>
                            <th style="width: 190px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>
                                    <div class="service-name">{{ $service->name }}</div>
                                    <div class="muted">
                                        {{ $service->category ?: 'Tanpa kategori' }}
                                        @if($service->notes)
                                            · {{ $service->notes }}
                                        @endif
                                    </div>
                                </td>
                                <td><span class="money">Rp {{ number_format($service->price_per_visit, 0, ',', '.') }}</span></td>
                                <td><span class="money">{{ $service->package_3x_price ? 'Rp ' . number_format($service->package_3x_price, 0, ',', '.') : 'N/A' }}</span></td>
                                <td><span class="money">{{ $service->package_6x_price ? 'Rp ' . number_format($service->package_6x_price, 0, ',', '.') : 'N/A' }}</span></td>
                                <td><span class="money">{{ $service->package_12x_price ? 'Rp ' . number_format($service->package_12x_price, 0, ',', '.') : 'N/A' }}</span></td>
                                <td>
                                    <span class="badge {{ $service->status === 'active' ? '' : 'inactive' }}">
                                        {{ $service->status_label }}
                                    </span>
                                </td>
                                <td class="table-action-cell">
                                    <div class="service-actions">
                                        <a href="/admin/services?edit={{ $service->id }}#edit-service" class="btn btn-soft">Edit</a>

                                        <form method="POST" action="/admin/services/{{ $service->id }}/delete" onsubmit="return confirm('Hapus layanan ini?')">
                                            @csrf
                                            <button class="btn btn-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #64748b; padding: 28px;">
                                    Belum ada layanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
