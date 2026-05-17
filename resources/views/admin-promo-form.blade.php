<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1080px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 8px 13px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .title { margin: 0; font-size: 42px; line-height: 1.05; color: #22343a; font-weight: 900; }
        .subtitle { margin: 12px 0 0; max-width: 850px; color: #6b7280; font-size: 14px; line-height: 1.9; }
        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; white-space: nowrap; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #ffffff; box-shadow: 0 12px 24px rgba(47,124,122,.16); }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }
        .section-card { background: #ffffff; border: 1px solid #ecefef; border-radius: 28px; padding: 24px; box-shadow: 0 14px 34px rgba(15,23,42,.05); }
        .error-box { background: #fff1f2; border: 1px solid #ffe0e6; color: #be123c; padding: 14px 16px; border-radius: 16px; margin-bottom: 18px; font-size: 13px; line-height: 1.8; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field.full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 900; color: #334155; }
        input, select, textarea { width: 100%; padding: 14px 14px; border: 1px solid #dde5e3; border-radius: 14px; font-size: 14px; background: #ffffff; color: #111827; font-family: Arial, sans-serif; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #176f69; box-shadow: 0 0 0 4px rgba(23,111,105,.08); }
        .hint { margin-top: 6px; color: #94a3b8; font-size: 12px; line-height: 1.6; }
        .submit-row { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; flex-wrap: wrap; }
        @media (max-width: 900px) { .layout { display:block; } .main { padding:16px; } .form-grid { grid-template-columns:1fr; } .title { font-size:32px; } }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'promos'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Promo Setting</span>
                    <h1 class="title">{{ $title }}</h1>
                    <p class="subtitle">{{ $subtitle }}</p>
                </div>

                <a href="/admin/promos" class="btn btn-soft">← Kembali</a>
            </div>

            <section class="section-card">
                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ $action }}">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label>Kode Promo</label>
                            <input type="text" name="code" value="{{ old('code', optional($promo)->code) }}" placeholder="Contoh: KHAYRA10" required>
                        </div>

                        <div class="field">
                            <label>Nama Promo</label>
                            <input type="text" name="name" value="{{ old('name', optional($promo)->name) }}" placeholder="Contoh: Diskon Launching 10%" required>
                        </div>

                        <div class="field">
                            <label>Tipe Diskon</label>
                            <select name="discount_type" required>
                                <option value="nominal" {{ old('discount_type', optional($promo)->discount_type) === 'nominal' ? 'selected' : '' }}>Nominal</option>
                                <option value="percent" {{ old('discount_type', optional($promo)->discount_type) === 'percent' ? 'selected' : '' }}>Persen</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Nilai Diskon</label>
                            <input type="number" name="discount_value" min="0" step="1" value="{{ old('discount_value', optional($promo)->discount_value ?? 0) }}" required>
                            <div class="hint">Nominal: 50000. Persen: 10.</div>
                        </div>

                        <div class="field">
                            <label>Minimum Transaksi</label>
                            <input type="number" name="minimum_purchase" min="0" step="1" value="{{ old('minimum_purchase', optional($promo)->minimum_purchase ?? 0) }}">
                        </div>

                        <div class="field">
                            <label>Maksimum Diskon</label>
                            <input type="number" name="maximum_discount" min="0" step="1" value="{{ old('maximum_discount', optional($promo)->maximum_discount ?? 0) }}">
                            <div class="hint">Isi 0 kalau tidak ada batas maksimum.</div>
                        </div>

                        <div class="field">
                            <label>Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date', optional(optional($promo)->start_date)->format('Y-m-d')) }}">
                        </div>

                        <div class="field">
                            <label>End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date', optional(optional($promo)->end_date)->format('Y-m-d')) }}">
                        </div>

                        <div class="field">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="active" {{ old('status', optional($promo)->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', optional($promo)->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label>Notes</label>
                            <textarea name="notes" rows="4" placeholder="Catatan promo">{{ old('notes', optional($promo)->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="submit-row">
                        <a href="/admin/promos" class="btn btn-soft">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Promo</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>
