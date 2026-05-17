<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Exercise Library - Khayra Physio</title>
    <style>
        *{box-sizing:border-box;}
        body{margin:0;font-family:Arial,sans-serif;background:#f6f8f8;color:#17232b;}
        .layout{display:flex;min-height:100vh;}
        .main{flex:1;padding:28px;min-width:0;}
        .container{max-width:1320px;margin:0 auto;}
        .topbar{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px;}
        .badge{display:inline-flex;padding:8px 13px;border-radius:999px;background:#eef7f5;color:#2f7c7a;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;}
        h1{margin:0;font-size:42px;color:#22343a;line-height:1.05;}
        .subtitle{color:#6b7280;line-height:1.8;font-size:14px;margin:12px 0 0;}
        .card{background:white;border:1px solid #ecefef;border-radius:26px;padding:24px;box-shadow:0 10px 26px rgba(15,23,42,.04);margin-bottom:18px;}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        .field.full{grid-column:1/-1;}
        label{display:block;font-size:12px;font-weight:900;color:#334155;margin-bottom:8px;}
        input,select,textarea{width:100%;min-height:46px;border:1px solid #d7dedd;border-radius:14px;padding:0 14px;font-size:13px;}
        textarea{padding:14px;min-height:120px;resize:vertical;line-height:1.7;}
        .btn{min-height:42px;border:0;padding:0 16px;border-radius:14px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-weight:900;cursor:pointer;font-size:13px;}
        .btn-primary{background:linear-gradient(135deg,#3d8a89,#2f7c7a);color:white;}
        .btn-soft{background:white;color:#2f7c7a;border:1px solid #e6ebea;}
        .btn-danger{background:#fff1f2;color:#be123c;border:1px solid #ffe0e6;}
        .alert{padding:14px 16px;border-radius:16px;margin-bottom:18px;background:#ecfdf5;color:#166534;border:1px solid #bbf7d0;font-weight:800;}
        .filter{display:grid;grid-template-columns:1fr .5fr auto;gap:12px;align-items:end;}
        .table-wrap{overflow-x:auto;border:1px solid #edf1f0;border-radius:20px;}
        table{width:100%;min-width:900px;border-collapse:collapse;}
        th,td{padding:14px;border-bottom:1px solid #edf1f0;text-align:left;font-size:13px;vertical-align:top;}
        th{background:#f7faf9;color:#486168;text-transform:uppercase;font-size:11px;font-weight:900;}
        .primary{font-weight:900;color:#22343a;}
        .secondary{margin-top:4px;color:#94a3b8;font-size:11px;line-height:1.5;}
        .pill{display:inline-flex;padding:7px 11px;border-radius:999px;font-size:11px;font-weight:900;text-transform:uppercase;background:#eef7f5;color:#2f7c7a;}
        @media(max-width:980px){.layout{display:block}.main{padding:16px}.grid,.filter{grid-template-columns:1fr}h1{font-size:32px}}
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'exercise-library'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Patient Portal Plus</span>
                    <h1>Home Exercise Library</h1>
                    <p class="subtitle">Template latihan rumah untuk therapist agar edukasi pasien lebih rapi dan konsisten.</p>
                </div>
                <a href="/admin/reports/monthly-clinic" class="btn btn-soft">Reporting</a>
            </div>

            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            <section class="card">
                <h2>Tambah Template Exercise</h2>
                <form method="POST" action="/admin/exercise-library">
                    @csrf
                    <div class="grid">
                        <div><label>Nama Exercise</label><input name="name" required placeholder="Contoh: Glute Bridge"></div>
                        <div><label>Category</label><input name="category" placeholder="Lower Back / Knee / Shoulder"></div>
                        <div><label>Target Area</label><input name="target_area" placeholder="Hip, knee, ankle..."></div>
                        <div><label>Difficulty</label><select name="difficulty"><option value="easy">Easy</option><option value="medium">Medium</option><option value="hard">Hard</option></select></div>
                        <div><label>Dosage</label><input name="dosage" placeholder="3 set x 10 repetisi"></div>
                        <div><label>Video URL</label><input name="video_url" placeholder="Opsional"></div>
                        <div><label>Status</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                        <div class="field full"><label>Instructions</label><textarea name="instructions" required placeholder="Instruksi latihan..."></textarea></div>
                    </div>
                    <div style="margin-top:14px;"><button class="btn btn-primary">Simpan Template</button></div>
                </form>
            </section>

            <section class="card">
                <form method="GET" action="/admin/exercise-library" class="filter">
                    <div><label>Search</label><input name="search" value="{{ $search }}" placeholder="Cari exercise, category, target area..."></div>
                    <div><label>Category</label><select name="category"><option value="">Semua</option>@foreach($categories as $cat)<option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>@endforeach</select></div>
                    <button class="btn btn-primary">Filter</button>
                </form>
            </section>

            <section class="card">
                <h2>Exercise Templates</h2>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Exercise</th><th>Target</th><th>Difficulty</th><th>Dosage</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td><div class="primary">{{ $template->name }}</div><div class="secondary">{{ $template->instructions }}</div></td>
                                <td><div class="primary">{{ $template->target_area ?: '-' }}</div><div class="secondary">{{ $template->category ?: '-' }}</div></td>
                                <td><span class="pill">{{ $template->difficulty }}</span></td>
                                <td>{{ $template->dosage ?: '-' }}</td>
                                <td><span class="pill">{{ $template->status }}</span></td>
                                <td>
                                    <form method="POST" action="/admin/exercise-library/{{ $template->id }}/delete" onsubmit="return confirm('Hapus template ini?')">
                                        @csrf
                                        <button class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">Belum ada template exercise.</td></tr>
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
