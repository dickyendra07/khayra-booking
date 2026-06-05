<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Leave - Khayra Physio</title>
    <style>
        * { box-sizing:border-box; }
        body { margin:0; font-family:Arial,sans-serif; background:linear-gradient(180deg,#f6fbfa 0%,#eef7f5 100%); color:#17232b; }
        .page { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
        .card { width:100%; max-width:720px; background:#fff; border:1px solid #e5efec; border-radius:30px; padding:28px; box-shadow:0 18px 42px rgba(15,118,110,.08); }
        .badge { display:inline-flex; padding:8px 13px; border-radius:999px; background:#eef7f5; color:#0f766e; font-size:12px; font-weight:900; text-transform:uppercase; margin-bottom:14px; }
        h1 { margin:0; font-size:38px; color:#0f766e; line-height:1.05; }
        p { margin:12px 0 22px; color:#64748b; line-height:1.8; }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .field.full { grid-column:1/-1; }
        label { display:block; margin-bottom:8px; font-size:13px; font-weight:900; color:#334155; }
        input, select, textarea { width:100%; padding:14px; border:1px solid #dde5e3; border-radius:14px; font-size:14px; font-family:Arial,sans-serif; }
        textarea { min-height:120px; line-height:1.7; }
        .actions { display:flex; justify-content:flex-end; gap:10px; margin-top:18px; flex-wrap:wrap; }
        .btn { min-height:44px; padding:0 16px; border-radius:14px; border:0; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; font-weight:900; font-size:13px; font-family:Arial,sans-serif; cursor:pointer; }
        .primary { background:#0f766e; color:#fff; }
        .soft { background:#fff; color:#0f766e; border:1px solid #d7ebe6; }
        .error { background:#fff1f2; color:#be123c; border:1px solid #ffe0e6; padding:14px; border-radius:14px; margin-bottom:16px; line-height:1.7; }
        @media(max-width:720px){ .grid{grid-template-columns:1fr}.field.full{grid-column:auto}h1{font-size:30px}.card{padding:22px;border-radius:24px} }
    </style>
</head>
<body>
<div class="page">
    <form method="POST" action="/therapist/leaves" class="card">
        @csrf
        <span class="badge">Staff Leave Request</span>
        <h1>Ajukan Cuti Staff</h1>
        <p>Isi tanggal cuti dan alasan. Request akan masuk ke admin untuk proses approval.</p>

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="grid">
            <div class="field">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required>
            </div>

            <div class="field">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required>
            </div>

            <div class="field full">
                <label>Leave Type</label>
                <select name="leave_type">
                    <option value="Cuti Tahunan" {{ old('leave_type') === 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                    <option value="Sakit" {{ old('leave_type') === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Izin Pribadi" {{ old('leave_type') === 'Izin Pribadi' ? 'selected' : '' }}>Izin Pribadi</option>
                    <option value="Lainnya" {{ old('leave_type') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="field full">
                <label>Reason</label>
                <textarea name="reason" placeholder="Tulis alasan cuti">{{ old('reason') }}</textarea>
            </div>
        </div>

        <div class="actions">
            <a href="/therapist/dashboard" class="btn soft">Batal</a>
            <button type="submit" class="btn primary">Submit Request</button>
        </div>
    </form>
</div>
</body>
</html>
