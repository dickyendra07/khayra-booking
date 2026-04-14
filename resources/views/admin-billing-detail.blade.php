<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Detail - Khayra</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 30%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
            color: #1f2937;
        }

        .layout { min-height: 100vh; display: flex; }

        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background: linear-gradient(180deg, #0f766e 0%, #115e59 100%);
            color: white;
            padding: 28px 22px;
            box-shadow: 8px 0 30px rgba(15,118,110,.12);
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 14px;
            opacity: .88;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .nav-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: .72;
            margin-bottom: 12px;
        }

        .nav-item {
            display: block;
            text-decoration: none;
            color: white;
            padding: 13px 14px;
            border-radius: 14px;
            margin-bottom: 10px;
            background: rgba(255,255,255,.07);
            font-weight: 600;
        }

        .nav-item.active {
            background: rgba(255,255,255,.18);
        }

        .logout-form { margin-top: 18px; }

        .logout-btn {
            width: 100%;
            border: none;
            padding: 13px 14px;
            border-radius: 14px;
            background: rgba(255,255,255,.14);
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .title h1 {
            margin: 0;
            font-size: 34px;
            color: #0f766e;
        }

        .title p {
            margin: 8px 0 0;
            color: #6b7280;
        }

        .top-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ghost-link, .print-link {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .ghost-link {
            border: 1px solid #d7ebe6;
            color: #0f766e;
            background: #f8fffd;
        }

        .print-link {
            background: #0f766e;
            color: white;
            box-shadow: 0 12px 26px rgba(15,118,110,.16);
        }

        .detail-card {
            background: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-paid { background: #dcfce7; color: #166534; }
        .status-unpaid { background: #fee2e2; color: #b91c1c; }
        .status-partial { background: #fef3c7; color: #92400e; }

        .invoice-hero {
            margin-top: 18px;
            padding: 24px;
            border-radius: 22px;
            background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%);
            border: 1px solid #d7ebe6;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .invoice-hero-left h2 {
            margin: 0 0 10px;
            font-size: 30px;
            color: #0f766e;
        }

        .invoice-hero-left p {
            margin: 6px 0;
            color: #374151;
            line-height: 1.6;
        }

        .amount-box {
            min-width: 240px;
            background: white;
            border: 1px solid #d7ebe6;
            border-radius: 18px;
            padding: 18px;
        }

        .amount-box .label {
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .amount-box .amount {
            font-size: 32px;
            font-weight: 800;
            color: #0f766e;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-top: 24px;
        }

        .field {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 18px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .field-label {
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .field-value {
            font-size: 18px;
            color: #111827;
            line-height: 1.7;
            word-break: break-word;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        .actions form { margin: 0; }

        .actions button {
            border: none;
            padding: 10px 14px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
        }

        .btn-paid { background: #dcfce7; color: #166534; }
        .btn-unpaid { background: #fee2e2; color: #b91c1c; }
        .btn-partial { background: #fef3c7; color: #92400e; }

        @media (max-width: 1024px) {
            .layout { display: block; }
            .sidebar {
                width: 100%;
                border-radius: 0 0 24px 24px;
            }
            .main { padding: 20px; }
        }

        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .invoice-hero {
                flex-direction: column;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .amount-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'billings'])

    <main class="main">
        <div class="topbar">
            <div class="title">
                <h1>Billing Detail</h1>
                <p>Detail invoice / billing pasien dengan tampilan yang lebih rapi.</p>
            </div>

            <div class="top-actions">
                <a href="/admin/billings" class="ghost-link">← Kembali ke Billings</a>
                <a href="/admin/billings/{{ $billing->id }}/print" target="_blank" class="print-link">Print Invoice</a>
            </div>
        </div>

        <div class="detail-card">
            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif

            <span class="status-pill status-{{ $billing->payment_status }}">
                {{ $billing->payment_status }}
            </span>

            <div class="invoice-hero">
                <div class="invoice-hero-left">
                    <h2>{{ $billing->invoice_number ?: 'Invoice Belum Ada Nomor' }}</h2>
                    <p><strong>Invoice Date:</strong> {{ $billing->invoice_date ?: '-' }}</p>
                    <p><strong>Patient:</strong> {{ $billing->patient->full_name ?? '-' }}</p>
                    <p><strong>Visit ID:</strong> #{{ $billing->visit_id ?: '-' }}</p>
                </div>

                <div class="amount-box">
                    <div class="label">Total Amount</div>
                    <div class="amount">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="grid">
                <div class="field">
                    <div class="field-label">Payment Status</div>
                    <div class="field-value">{{ ucfirst($billing->payment_status) }}</div>
                </div>

                <div class="field">
                    <div class="field-label">Payment Method</div>
                    <div class="field-value">{{ $billing->payment_method ?: '-' }}</div>
                </div>

                <div class="field">
                    <div class="field-label">Patient</div>
                    <div class="field-value">{{ $billing->patient->full_name ?? '-' }}</div>
                </div>

                <div class="field">
                    <div class="field-label">Visit Reference</div>
                    <div class="field-value">#{{ $billing->visit_id ?: '-' }}</div>
                </div>

                <div class="field">
                    <div class="field-label">Created At</div>
                    <div class="field-value">{{ $billing->created_at }}</div>
                </div>

                <div class="field">
                    <div class="field-label">Updated At</div>
                    <div class="field-value">{{ $billing->updated_at }}</div>
                </div>

                <div class="field full">
                    <div class="field-label">Notes</div>
                    <div class="field-value">{{ $billing->notes ?: '-' }}</div>
                </div>
            </div>

            <div class="actions">
                <form method="POST" action="/admin/billings/{{ $billing->id }}/status">
                    @csrf
                    <button class="btn-paid" type="submit" name="payment_status" value="paid">Set Paid</button>
                </form>

                <form method="POST" action="/admin/billings/{{ $billing->id }}/status">
                    @csrf
                    <button class="btn-unpaid" type="submit" name="payment_status" value="unpaid">Set Unpaid</button>
                </form>

                <form method="POST" action="/admin/billings/{{ $billing->id }}/status">
                    @csrf
                    <button class="btn-partial" type="submit" name="payment_status" value="partial">Set Partial</button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>