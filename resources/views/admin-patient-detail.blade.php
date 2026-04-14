<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Detail - Khayra Admin</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 30%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px;
        }

        .page-title {
            margin: 0;
            font-size: 38px;
            color: #0f766e;
            line-height: 1.1;
        }

        .page-subtitle {
            margin: 10px 0 0;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            max-width: 860px;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .back-link,
        .edit-link {
            display: inline-block;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            font-weight: 700;
            white-space: nowrap;
            font-size: 14px;
        }

        .back-link {
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
        }

        .edit-link {
            background: #0f766e;
            color: white;
            border: 1px solid #0f766e;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.7;
        }

        .summary-card,
        .table-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .summary-card {
            margin-bottom: 22px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .summary-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .summary-box.full {
            grid-column: 1 / -1;
        }

        .summary-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .summary-value {
            font-size: 15px;
            color: #111827;
            line-height: 1.6;
            word-break: break-word;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .table-card + .table-card {
            margin-top: 22px;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid #e8f1ef;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th {
            background: #effaf7;
            color: #0f766e;
            text-align: left;
            padding: 16px;
            font-size: 14px;
            border-bottom: 1px solid #e5efec;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #eef2f1;
            font-size: 14px;
            color: #374151;
            vertical-align: top;
        }

        tr:hover td {
            background: #fafefd;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            text-transform: capitalize;
        }

        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .billing-paid { background: #dcfce7; color: #166534; }
        .billing-unpaid { background: #fee2e2; color: #b91c1c; }
        .billing-partial { background: #fef3c7; color: #92400e; }

        .record-link {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            background: #f0fdfa;
            color: #0f766e;
            border: 1px solid #cfe8e2;
            white-space: nowrap;
        }

        @media (max-width: 1024px) {
            .layout {
                display: block;
            }

            .main {
                padding: 20px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 32px;
            }

            .summary-card,
            .table-card {
                padding: 20px;
                border-radius: 22px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'patients'])

    <main class="main">
        <div class="topbar">
            <div>
                <h1 class="page-title">Patient Detail</h1>
                <p class="page-subtitle">Ringkasan data pasien, visit, dan billing.</p>
            </div>

            <div class="topbar-actions">
                <a href="/admin/patients/{{ $patient->id }}/edit" class="edit-link">Edit Patient</a>
                <a href="/admin/patients" class="back-link">← Kembali ke Patients</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <section class="summary-card">
            <div class="summary-grid">
                <div class="summary-box">
                    <div class="summary-label">Nama Pasien</div>
                    <div class="summary-value">{{ $patient->full_name ?: '-' }}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Gender</div>
                    <div class="summary-value">{{ $patient->gender ?: '-' }}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Tanggal Lahir</div>
                    <div class="summary-value">{{ $patient->birth_date ?: '-' }}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">WhatsApp</div>
                    <div class="summary-value">{{ $patient->whatsapp ?: '-' }}</div>
                </div>

                <div class="summary-box full">
                    <div class="summary-label">Alamat</div>
                    <div class="summary-value">{{ $patient->address ?: '-' }}</div>
                </div>
            </div>
        </section>

        <section class="table-card">
            <h2 class="section-title">Riwayat Visits</h2>
            <p class="section-subtitle">Daftar kunjungan pasien yang tercatat.</p>

            @if($patient->visits->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Visit ID</th>
                                <th>Tanggal</th>
                                <th>Therapist</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Medical Record</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patient->visits as $visit)
                                <tr>
                                    <td>#{{ $visit->id }}</td>
                                    <td>{{ $visit->visit_date ?: '-' }}</td>
                                    <td>{{ $visit->therapist ?: '-' }}</td>
                                    <td>
                                        <span class="status-pill status-{{ $visit->status }}">
                                            {{ str_replace('_', ' ', $visit->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $visit->notes ?: '-' }}</td>
                                    <td>
                                        <a href="/admin/visits/{{ $visit->id }}/medical-record" class="record-link">View Record</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color:#6b7280;">Belum ada visit untuk pasien ini.</p>
            @endif
        </section>

        <section class="table-card">
            <h2 class="section-title">Riwayat Billings</h2>
            <p class="section-subtitle">Daftar invoice / billing milik pasien ini.</p>

            @if($patient->billings->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Visit</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Payment Method</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patient->billings as $billing)
                                <tr>
                                    <td>{{ $billing->invoice_number ?: '-' }}</td>
                                    <td>{{ $billing->invoice_date ?: '-' }}</td>
                                    <td>{{ $billing->visit_id ? '#' . $billing->visit_id : '-' }}</td>
                                    <td>Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="status-pill billing-{{ $billing->payment_status }}">
                                            {{ $billing->payment_status }}
                                        </span>
                                    </td>
                                    <td>{{ $billing->payment_method ?: '-' }}</td>
                                    <td>
                                        <a href="/admin/billings/{{ $billing->id }}" class="record-link">View Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color:#6b7280;">Belum ada billing untuk pasien ini.</p>
            @endif
        </section>
    </main>
</div>
</body>
</html>