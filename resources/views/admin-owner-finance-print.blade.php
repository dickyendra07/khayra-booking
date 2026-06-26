<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Finance Monthly Report - {{ $monthLabel }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px;
            font-family: Arial, sans-serif;
            color: #17232b;
            background: #ffffff;
        }

        .report {
            max-width: 1080px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            border-bottom: 3px solid #2f7c7a;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .brand {
            font-size: 13px;
            font-weight: 900;
            color: #2f7c7a;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 8px;
        }

        h1 {
            margin: 0;
            font-size: 30px;
            color: #17232b;
        }

        .meta {
            text-align: right;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }

        .status {
            display: inline-block;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            background: {{ $closing ? '#dcfce7' : '#f1f5f9' }};
            color: {{ $closing ? '#166534' : '#475569' }};
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 22px;
        }

        .summary-card {
            border: 1px solid #dbe7e5;
            border-radius: 14px;
            padding: 14px;
            background: #fbfdfd;
        }

        .summary-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 17px;
            font-weight: 900;
            color: #17232b;
        }

        .green { color: #0f766e; }
        .red { color: #b91c1c; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 12px;
        }

        th {
            background: #eef5f4;
            color: #334155;
            text-align: left;
            padding: 10px;
            border: 1px solid #dbe7e5;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        td {
            padding: 10px;
            border: 1px solid #e5eeee;
            vertical-align: top;
        }

        .trx-title {
            font-weight: 900;
            color: #17232b;
            margin-bottom: 3px;
        }

        .trx-meta {
            color: #64748b;
            line-height: 1.45;
        }

        .footer {
            margin-top: 28px;
            padding-top: 14px;
            border-top: 1px solid #dbe7e5;
            display: grid;
            grid-template-columns: 1fr 240px;
            gap: 24px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }

        .signature {
            text-align: center;
            color: #17232b;
        }

        .signature-box {
            height: 70px;
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 8px;
        }

        .no-print {
            margin-bottom: 18px;
            display: flex;
            gap: 10px;
        }

        .btn {
            min-height: 40px;
            border: 0;
            border-radius: 12px;
            padding: 0 14px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #0f766e;
            color: #ffffff;
        }

        .btn-soft {
            background: #ffffff;
            color: #0f766e;
            border: 1px solid #d8ebe7;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .report { max-width: none; }
            @page { size: A4 landscape; margin: 12mm; }
        }
    </style>
</head>
<body>
<div class="report">
    <div class="no-print">
        <button onclick="window.print()" class="btn">Print / Save PDF</button>
        <a href="/admin/owner-finance?month={{ $month }}" class="btn btn-soft">Back to Finance</a>
    </div>

    <div class="header">
        <div>
            <div class="brand">Khayra Physio · Owner Finance Report</div>
            <h1>Finance Monthly Report</h1>
            <div style="margin-top:8px;color:#64748b;font-size:13px;">Period: {{ $monthLabel }}</div>
        </div>

        <div class="meta">
            <span class="status">{{ $closing ? 'Closed Book' : 'Draft Report' }}</span><br>
            Generated: {{ now()->format('Y-m-d H:i') }}<br>
            @if($closing)
                Closed: {{ optional($closing->closed_at)->format('Y-m-d H:i') ?: '-' }}<br>
                Closed by: {{ $closing->closed_by ?: '-' }}
            @endif
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Billing Income</div>
            <div class="summary-value green">Rp {{ number_format($summary['billing_income'], 0, ',', '.') }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Manual Income</div>
            <div class="summary-value green">Rp {{ number_format($summary['manual_income'], 0, ',', '.') }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Total Income</div>
            <div class="summary-value green">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Total Expense</div>
            <div class="summary-value red">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Net Cashflow</div>
            <div class="summary-value {{ $summary['net_cashflow'] >= 0 ? 'green' : 'red' }}">
                Rp {{ number_format($summary['net_cashflow'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    <h2 style="margin:0 0 6px;font-size:18px;">Cashflow Ledger</h2>
    <div style="font-size:12px;color:#64748b;margin-bottom:10px;">
        Gabungan auto billing income dan transaksi manual pada periode {{ $monthLabel }}.
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:90px;">Date</th>
                <th>Transaction</th>
                <th style="width:80px;">Type</th>
                <th style="width:120px;">Source</th>
                <th style="width:100px;">Method</th>
                <th style="width:140px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction['date'] ? \Carbon\Carbon::parse($transaction['date'])->format('Y-m-d') : '-' }}</td>
                    <td>
                        <div class="trx-title">{{ $transaction['title'] }}</div>
                        <div class="trx-meta">
                            {{ $transaction['category'] }}
                            @if(!empty($transaction['description']))
                                · {{ $transaction['description'] }}
                            @endif
                        </div>
                    </td>
                    <td>{{ strtoupper($transaction['type']) }}</td>
                    <td>{{ $transaction['source'] }}</td>
                    <td>{{ $transaction['payment_method'] ?: '-' }}</td>
                    <td class="{{ $transaction['type'] === 'income' ? 'green' : 'red' }}" style="font-weight:900;">
                        {{ $transaction['type'] === 'income' ? '+' : '-' }}
                        Rp {{ number_format($transaction['amount'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada transaksi finance pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div>
            Notes:<br>
            {{ $closing && $closing->notes ? $closing->notes : 'Laporan ini dibuat dari data billing valid dan transaksi finance manual pada periode terkait.' }}
        </div>

        <div class="signature">
            <div class="signature-box"></div>
            Owner / Management
        </div>
    </div>
</div>
</body>
</html>
