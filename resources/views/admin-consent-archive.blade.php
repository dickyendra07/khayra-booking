<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Digital Consent Archive - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;background:#f6f8f8;color:#17232b}
        .layout{display:flex;min-height:100vh}
        .main{flex:1;padding:28px;min-width:0}
        .container{max-width:1280px;margin:0 auto}
        .topbar{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;flex-wrap:wrap;margin-bottom:18px}
        .badge{display:inline-flex;padding:8px 13px;border-radius:999px;background:#eef7f5;color:#2f7c7a;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px}
        h1{margin:0;font-size:42px;color:#22343a;line-height:1.05}
        .subtitle{color:#64748b;line-height:1.8;font-size:14px;margin:12px 0 0}
        .btn{min-height:42px;padding:0 16px;border-radius:14px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-weight:900;font-size:13px;border:0;cursor:pointer}
        .btn-soft{background:#fff;color:#2f7c7a;border:1px solid #dfe8e6}
        .btn-primary{background:linear-gradient(135deg,#3d8a89,#2f7c7a);color:#fff}
        .card{background:#fff;border:1px solid #ecefef;border-radius:26px;padding:24px;box-shadow:0 10px 26px rgba(15,23,42,.04);margin-bottom:18px}
        .filter{display:grid;grid-template-columns:1fr .35fr auto;gap:12px;align-items:end}
        label{display:block;font-size:12px;font-weight:900;color:#334155;margin-bottom:8px}
        input,select{width:100%;min-height:46px;border:1px solid #d7dedd;border-radius:14px;padding:0 14px;font-size:13px}
        .table-wrap{overflow-x:auto;border:1px solid #edf1f0;border-radius:20px}
        table{width:100%;min-width:980px;border-collapse:collapse}
        th,td{padding:15px 14px;border-bottom:1px solid #edf1f0;text-align:left;font-size:13px;vertical-align:top}
        th{background:#f7faf9;color:#486168;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.05em}
        .primary{font-weight:900;color:#22343a;line-height:1.45}
        .secondary{margin-top:4px;color:#94a3b8;font-size:11px;line-height:1.55}
        .pill{display:inline-flex;padding:7px 11px;border-radius:999px;font-size:11px;font-weight:900;text-transform:uppercase}
        .pill-green{background:#dcfce7;color:#166534}
        .pill-orange{background:#fef3c7;color:#92400e}
        .empty{padding:18px;background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;border-radius:16px;font-weight:800}
        @media(max-width:900px){.layout{display:block}.main{padding:16px}.filter{grid-template-columns:1fr}h1{font-size:34px}}
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
    @include('partials.admin-sidebar', ['activeMenu' => 'documents'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Digital Consent Archive</span>
                    <h1>Arsip Informed Consent</h1>
                    <p class="subtitle">Consent tersimpan per pasien dan bisa dicetak ulang kapan pun.</p>
                </div>
                <a href="/admin/documents" class="btn btn-soft">← Dokumen Klinik</a>
            </div>

            <section class="card">
                <form method="GET" action="/admin/consent-archive" class="filter">
                    <div>
                        <label>Search Patient</label>
                        <input name="search" value="{{ $search }}" placeholder="Nama, MR number, WhatsApp...">
                    </div>
                    <div>
                        <label>Status</label>
                        <select name="status">
                            <option value="">Semua</option>
                            <option value="signed" {{ $status === 'signed' ? 'selected' : '' }}>Signed</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <button class="btn btn-primary">Filter</button>
                </form>
            </section>

            <section class="card">
                @if($consents->count())
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Consent</th>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Visit</th>
                                    <th>Physiotherapy</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consents as $consent)
                                    <tr>
                                        <td>
                                            <div class="primary">Consent #{{ $consent->id }}</div>
                                            <div class="secondary">Created {{ $consent->created_at ? $consent->created_at->format('Y-m-d H:i') : '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="primary">{{ optional($consent->patient)->full_name ?: '-' }}</div>
                                            <div class="secondary">{{ optional($consent->patient)->medical_record_number ?: 'MR not available' }}</div>
                                        </td>
                                        <td>{{ $consent->consent_date ? $consent->consent_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            @if($consent->visit)
                                                <div class="primary">Visit #{{ $consent->visit->id }}</div>
                                                <div class="secondary">{{ $consent->visit->visit_date ?: '-' }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $consent->physiotherapy_name ?: '-' }}</td>
                                        <td><span class="pill {{ ($consent->status ?: 'signed') === 'signed' ? 'pill-green' : 'pill-orange' }}">{{ $consent->status ?: 'signed' }}</span></td>
                                        <td>
                                            <a href="/admin/informed-consents/{{ $consent->id }}/print" target="_blank" class="btn btn-soft">Print</a>
                                            @if($consent->patient)
                                                <a href="/admin/patients/{{ $consent->patient->id }}/informed-consent" class="btn btn-soft">New Consent</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty">Belum ada informed consent sesuai filter.</div>
                @endif
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
