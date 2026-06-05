<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Leave Requests - Khayra Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { margin:0; font-family:Arial,sans-serif; background:#f6f8f8; color:#17232b; }
        .layout { min-height:100vh; display:flex; }
        .main { flex:1; min-width:0; padding:28px; }
        .container { max-width:1280px; margin:0 auto; }
        .topbar { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; margin-bottom:18px; flex-wrap:wrap; }
        .badge { display:inline-flex; padding:8px 13px; border-radius:999px; background:#eef5f4; color:#35565d; font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; margin-bottom:12px; }
        .title { margin:0; font-size:40px; line-height:1.05; color:#22343a; font-weight:900; }
        .subtitle { margin:10px 0 0; color:#6b7280; font-size:14px; line-height:1.8; max-width:820px; }
        .alert { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; border-radius:16px; padding:14px 16px; margin-bottom:18px; }
        .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:18px; }
        .stat { background:#fff; border:1px solid #ecefef; border-radius:22px; padding:20px; box-shadow:0 10px 26px rgba(15,23,42,.04); }
        .stat-label { font-size:11px; color:#7b8794; text-transform:uppercase; letter-spacing:.06em; font-weight:900; margin-bottom:10px; }
        .stat-value { font-size:32px; color:#22343a; font-weight:900; }
        .card { background:#fff; border:1px solid #ecefef; border-radius:26px; padding:22px; box-shadow:0 10px 26px rgba(15,23,42,.04); }
        .leave-list { display:grid; gap:14px; }
        .leave-item { border:1px solid #edf1f0; background:#fbfcfc; border-radius:20px; padding:18px; }
        .leave-top { display:flex; justify-content:space-between; gap:14px; align-items:flex-start; flex-wrap:wrap; }
        .staff-name { font-size:18px; font-weight:900; color:#22343a; }
        .meta { margin-top:6px; color:#64748b; font-size:13px; line-height:1.7; }
        .pill { display:inline-flex; padding:8px 11px; border-radius:999px; font-size:11px; font-weight:900; text-transform:uppercase; }
        .pending { background:#fef3c7; color:#92400e; }
        .approved { background:#dcfce7; color:#166534; }
        .rejected { background:#fee2e2; color:#b91c1c; }
        .review-form { display:grid; grid-template-columns:1fr auto auto; gap:10px; align-items:end; margin-top:14px; }
        textarea, select { width:100%; padding:12px 14px; border:1px solid #dde5e3; border-radius:14px; font-family:Arial,sans-serif; }
        button { border:0; border-radius:14px; padding:12px 15px; font-weight:900; cursor:pointer; font-family:Arial,sans-serif; }
        .approve { background:#2f7c7a; color:#fff; }
        .reject { background:#fee2e2; color:#b91c1c; }
        .empty { padding:24px; text-align:center; color:#64748b; }
        @media(max-width:900px){ .layout{display:block}.main{padding:16px}.stats,.review-form{grid-template-columns:1fr}.title{font-size:32px} }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'staff-leaves'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Staff Leave Approval</span>
                    <h1 class="title">Staff Leave Requests</h1>
                    <p class="subtitle">Pantau request cuti physiotherapist/staff, lalu approve atau reject agar operasional klinik tetap terkendali.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            <section class="stats">
                <div class="stat"><div class="stat-label">Pending</div><div class="stat-value">{{ $pendingCount }}</div></div>
                <div class="stat"><div class="stat-label">Approved</div><div class="stat-value">{{ $approvedCount }}</div></div>
                <div class="stat"><div class="stat-label">Rejected</div><div class="stat-value">{{ $rejectedCount }}</div></div>
            </section>

            <section class="card">
                <div class="leave-list">
                    @forelse($leaveRequests as $leave)
                        <div class="leave-item">
                            <div class="leave-top">
                                <div>
                                    <div class="staff-name">{{ optional($leave->therapist)->full_name ?: 'Staff' }}</div>
                                    <div class="meta">
                                        {{ optional($leave->start_date)->format('Y-m-d') }} sampai {{ optional($leave->end_date)->format('Y-m-d') }}<br>
                                        Type: {{ $leave->leave_type ?: 'Cuti' }}<br>
                                        Reason: {{ $leave->reason ?: '-' }}
                                        @if($leave->admin_note)
                                            <br>Admin note: {{ $leave->admin_note }}
                                        @endif
                                    </div>
                                </div>
                                <span class="pill {{ $leave->status }}">{{ $leave->status }}</span>
                            </div>

                            @if($leave->status === 'pending')
                                <form method="POST" action="/admin/staff-leaves/{{ $leave->id }}/review" class="review-form">
                                    @csrf
                                    <textarea name="admin_note" rows="2" placeholder="Catatan admin opsional"></textarea>
                                    <button type="submit" name="status" value="approved" class="approve">Approve</button>
                                    <button type="submit" name="status" value="rejected" class="reject">Reject</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="empty">Belum ada request cuti staff.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
