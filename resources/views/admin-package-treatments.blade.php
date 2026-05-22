<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Pembelian Paket - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1240px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 8px 13px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .title { margin: 0; font-size: 40px; line-height: 1.05; color: #22343a; font-weight: 900; letter-spacing: -1px; }
        .subtitle { margin: 10px 0 0; color: #6b7280; font-size: 14px; line-height: 1.8; max-width: 820px; }
        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; white-space: nowrap; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: white; box-shadow: 0 12px 24px rgba(47,124,122,.16); }
        .btn-soft { color: #2f7c7a; background: #eef7f5; border: 1px solid #d8ebe7; }
        .btn-danger { background: #fff1f2; color: #be123c; border: 1px solid #ffe0e6; }
        .card { background: #ffffff; border: 1px solid #ecefef; border-radius: 28px; padding: 24px; box-shadow: 0 14px 34px rgba(15,23,42,.05); margin-bottom: 18px; }
        .filter { display: grid; grid-template-columns: 1fr auto; gap: 12px; margin-bottom: 18px; }
        input { width: 100%; padding: 14px 14px; border: 1px solid #dde5e3; border-radius: 14px; font-size: 14px; background: #ffffff; color: #111827; }
        input:focus { outline: none; border-color: #176f69; box-shadow: 0 0 0 4px rgba(23,111,105,.08); }
        .table-wrap { overflow-x: auto; border: 1px solid #edf1f0; border-radius: 22px; }
        table { width: 100%; border-collapse: collapse; min-width: 980px; }
        th { text-align: left; padding: 14px; background: #fbfcfc; color: #718096; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; }
        td { padding: 16px 14px; border-top: 1px solid #edf1f0; vertical-align: top; font-size: 14px; }
        .strong { font-weight: 900; color: #22343a; }
        .muted { color: #7b8794; font-size: 12px; line-height: 1.6; margin-top: 4px; }
        .pill { display: inline-flex; align-items: center; padding: 7px 11px; border-radius: 999px; background: #eef7f5; color: #2f7c7a; font-size: 12px; font-weight: 900; }
        .actions { display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap; }
        .empty { text-align: center; color: #7b8794; padding: 34px; }
        @media (max-width: 900px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .title { font-size: 30px; }
            .filter { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'package-treatments'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Dokumen Klinik</span>
                    <h1 class="title">Dokumen Pembelian Paket</h1>
                    <p class="subtitle">Arsip surat pembelian paket treatment lengkap dengan data pasien, harga paket, masa berlaku, dan tabel rekap session.</p>
                </div>
                <a href="/admin/package-treatments/create" class="btn btn-primary">+ Buat Dokumen Paket</a>
            </div>

            <section class="card">
                <form method="GET" action="/admin/package-treatments" class="filter">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama pasien, nomor rekam medis, nomor dokumen, atau paket">
                    <button class="btn btn-soft" type="submit">Filter</button>
                </form>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Dokumen</th>
                                <th>Pasien</th>
                                <th>Paket</th>
                                <th>Periode</th>
                                <th>Harga</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $document)
                                <tr>
                                    <td>
                                        <div class="strong">{{ $document->document_number ?: 'PKG-' . $document->id }}</div>
                                        <div class="muted">{{ optional($document->document_date)->format('d/m/Y') ?: '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="strong">{{ optional($document->patient)->full_name ?: '-' }}</div>
                                        <div class="muted">{{ optional($document->patient)->medical_record_number ?: '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="strong">{{ $document->package_name ?: '-' }}</div>
                                        <div class="muted">{{ $document->total_sessions }}x kedatangan</div>
                                    </td>
                                    <td>
                                        <div class="muted">Beli: {{ optional($document->buying_date)->format('d/m/Y') ?: '-' }}</div>
                                        <div class="muted">Valid: {{ optional($document->valid_until)->format('d/m/Y') ?: '-' }}</div>
                                    </td>
                                    <td><span class="pill">Rp {{ number_format($document->package_price, 0, ',', '.') }}</span></td>
                                    <td>
                                        <div class="actions">
                                            <a href="/admin/package-treatments/{{ $document->id }}/print" class="btn btn-soft" target="_blank">Print</a>
                                            <form method="POST" action="/admin/package-treatments/{{ $document->id }}/delete" onsubmit="return confirm('Hapus dokumen paket ini?')">
                                                @csrf
                                                <button class="btn btn-danger" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty">Belum ada dokumen pembelian paket.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
