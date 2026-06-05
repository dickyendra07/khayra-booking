<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Physiotherapy Visits - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1280px; margin: 0 auto; }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .brand-title { font-size: 18px; font-weight: 800; color: #22343a; }
        .brand-kicker {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #7b8794;
            margin-bottom: 4px;
        }
        .ghost-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e6ebea;
            color: #2c5b5a;
            font-size: 13px;
            font-weight: 700;
        }
        .hero, .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
        }
        .hero-title {
            margin: 0;
            font-size: 38px;
            font-weight: 800;
            color: #22343a;
            line-height: 1.08;
        }
        .hero-text {
            margin: 12px 0 0;
            font-size: 14px;
            line-height: 1.9;
            color: #6b7280;
            max-width: 800px;
        }
        .section-title {
            margin: 0;
            font-size: 24px;
            color: #22343a;
            font-weight: 800;
        }
        .section-subtitle {
            margin: 8px 0 18px;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }
        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            background: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }
        th {
            text-align: left;
            padding: 15px 16px;
            background: #f7faf9;
            color: #486168;
            font-size: 12px;
            font-weight: 800;
            border-bottom: 1px solid #edf1f0;
        }
        td {
            padding: 16px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f2f5f5;
            vertical-align: top;
        }
        tr:last-child td { border-bottom: none; }
        .primary-text {
            font-weight: 800;
            color: #22343a;
            line-height: 1.5;
        }
        .secondary-text {
            margin-top: 4px;
            font-size: 11px;
            line-height: 1.6;
            color: #94a3b8;
        }
        .status-pill {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: capitalize;
        }
        .scheduled { background: #dbeafe; color: #1d4ed8; }
        .in_progress { background: #fef3c7; color: #92400e; }
        .completed { background: #dcfce7; color: #166534; }
        .cancelled { background: #fee2e2; color: #b91c1c; }
        .action-stack {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            min-width: 156px;
        }
        .action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            text-decoration: none;
            padding: 10px 12px;
            min-height: 38px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 900;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .btn-edit,
        .btn-record,
        .btn-billing,
        .btn-referral {
            background: #eef7f5;
            color: #2f7c7a;
            border-color: #d8ebe7;
        }
        .btn-record {
            background: #2f7c7a;
            color: #ffffff;
            border-color: #2f7c7a;
        }

        @media (max-width: 900px) {
            .layout { display: block; }
            .main { padding: 16px; }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'visits'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <div class="brand-kicker">Clinical Workflow</div>
                    <div class="brand-title">Physiotherapy Visits</div>
                </div>

                <a href="/admin/visits/create" class="ghost-link">+ Create Visit</a>
            </div>

            <section class="hero">
                <h1 class="hero-title">Track physiotherapy sessions with a cleaner and more clinical view.</h1>
                <p class="hero-text">
                    Halaman ini menampilkan seluruh visit layanan fisioterapi yang terhubung ke patient, booking, dan medical record agar operasional klinik terasa lebih rapi dan profesional.
                </p>
            </section>

            <section class="section-card">
                <h2 class="section-title">Visit List</h2>
                <p class="section-subtitle">Daftar seluruh physiotherapy visit beserta pasien, jadwal, physiotherapist, status, medical record, dan action operasional.</p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Visit</th>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Physiotherapy Staff</th>
                                <th>Booking</th>
                                <th>Status</th>
                                <th>Medical Record</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visits as $visit)
                                <tr>
                                    <td>
                                        <div class="primary-text">Visit #{{ $visit->id }}</div>
                                        <div class="secondary-text">{{ $visit->notes ?: 'No notes yet' }}</div>
                                    </td>
                                    <td>
                                        <div class="primary-text">{{ optional($visit->patient)->full_name ?: '-' }}</div>
                                        <div class="secondary-text">{{ optional($visit->patient)->medical_record_number ?: 'MR not available' }}</div>
                                    </td>
                                    <td>
                                        <div class="primary-text">{{ $visit->visit_date ?: '-' }}</div>
                                        <div class="secondary-text">Room: {{ $visit->room_name ?: optional($visit->booking)->room_name ?: 'Belum ditentukan' }}</div>
                                    </td>
                                    <td>
                                        <div class="primary-text">{{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: '-' }}</div>
                                        <div class="secondary-text">Physiotherapist</div>
                                    </td>
                                    <td>
                                        @if($visit->booking)
                                            <div class="primary-text">Booking #{{ $visit->booking->id }}</div>
                                            <div class="secondary-text">{{ $visit->booking->service ?: '-' }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $visit->status }}">{{ str_replace('_', ' ', $visit->status ?: '-') }}</span>
                                    </td>
                                    <td>
                                        @if($visit->medicalRecord)
                                            <span class="secondary-text">Record available</span>
                                        @else
                                            <span class="secondary-text">Not created yet</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-stack">
                                            <a href="/admin/visits/{{ $visit->id }}/edit" class="action-link btn-edit">Edit</a>
                                            <a href="/admin/visits/{{ $visit->id }}/medical-record" class="action-link btn-record">Medical Record</a>
                                            <a href="/admin/billings/create?visit_id={{ $visit->id }}" class="action-link btn-billing">Buat Invoice</a>
                                            <a href="/admin/referral-letters/create?visit_id={{ $visit->id }}" class="action-link btn-referral">Buat Surat Rujukan</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">Belum ada physiotherapy visit yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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