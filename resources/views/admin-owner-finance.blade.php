<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Finance / Cashflow - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(47,124,122,.10), transparent 28%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
            color: #17232b;
        }

        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1480px; margin: 0 auto; }

        .hero, .section-card, .stat-card {
            background: #ffffff;
            border: 1px solid #e6eeee;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .04);
        }

        .hero {
            border-radius: 30px;
            padding: 28px;
            margin-bottom: 18px;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 18px;
            align-items: stretch;
        }

        .badge {
            display: inline-flex;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef5f4;
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 14px;
        }

        .title {
            margin: 0;
            font-size: 42px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
            letter-spacing: -1px;
        }

        .subtitle {
            margin: 14px 0 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
            max-width: 820px;
        }

        .hero-panel {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            padding: 24px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .hero-panel h2 { margin: 0 0 10px; font-size: 25px; line-height: 1.2; }
        .hero-panel p { margin: 0; font-size: 13px; line-height: 1.85; color: rgba(255,255,255,.92); }

        .period-form, .quick-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        input, select, textarea {
            width: 100%;
            min-height: 44px;
            border-radius: 14px;
            border: 1px solid #dbe7e5;
            background: #ffffff;
            padding: 0 13px;
            font: inherit;
            font-size: 13px;
            color: #17232b;
        }

        textarea { min-height: 86px; padding-top: 12px; resize: vertical; }
        input[type="month"] {
            border: 1px solid rgba(255,255,255,.26);
            background: rgba(255,255,255,.12);
            color: #ffffff;
            font-weight: 800;
        }
        input[type="month"]::-webkit-calendar-picker-indicator { filter: invert(1); }

        label {
            display: block;
            margin-bottom: 7px;
            color: #5f6f78;
            font-size: 12px;
            font-weight: 900;
        }

        .btn {
            min-height: 44px;
            border: 0;
            border-radius: 14px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }

        .btn-white { background: #ffffff; color: #2f7c7a; }
        .btn-soft { background: #ffffff; color: #2f7c7a; border: 1px solid #d8ebe7; }
        .btn-dark { background: #111827; color: #ffffff; }
        .btn-red { background: #fee2e2; color: #b91c1c; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            border-radius: 24px;
            padding: 22px;
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #7b8794;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 26px;
            line-height: 1.08;
            color: #22343a;
            font-weight: 900;
            word-break: break-word;
        }

        .stat-sub {
            margin-top: 8px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.65;
        }

        .money-positive { color: #0f766e; }
        .money-negative { color: #b91c1c; }
        .money-warning { color: #c2410c; }

        .grid-2 {
            display: grid;
            grid-template-columns: .9fr 1.1fr;
            gap: 18px;
            align-items: start;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .field-full { grid-column: 1 / -1; }

        .section-card {
            border-radius: 26px;
            padding: 24px;
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            color: #22343a;
            font-weight: 900;
            line-height: 1.2;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.8;
        }

        .alert {
            margin-bottom: 18px;
            border-radius: 18px;
            padding: 14px 16px;
            background: #dcfce7;
            color: #166534;
            font-size: 13px;
            font-weight: 800;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
            background: #ffffff;
        }

        th, td {
            padding: 14px 15px;
            border-bottom: 1px solid #edf1f0;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }

        th {
            background: #f8fbfa;
            color: #607179;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 900;
        }

        .trx-title {
            font-weight: 900;
            color: #22343a;
            margin-bottom: 4px;
        }

        .trx-meta {
            color: #7b8794;
            font-size: 12px;
            line-height: 1.55;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .pill-green { background: #dcfce7; color: #166534; }
        .pill-red { background: #fee2e2; color: #b91c1c; }
        .pill-blue { background: #dbeafe; color: #1d4ed8; }
        .pill-slate { background: #f1f5f9; color: #475569; }

        .empty {
            padding: 20px;
            border-radius: 18px;
            background: #fbfcfc;
            border: 1px dashed #d8e5e3;
            color: #7b8794;
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 1180px) {
            .layout { display: block; }
            .main { padding: 18px; }
            .hero, .grid-2 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .title { font-size: 34px; }
        }

        @media (max-width: 720px) {
            .stats-grid, .form-grid { grid-template-columns: 1fr; }
            .field-full { grid-column: auto; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'owner-finance'])

    <main class="main">
        <div class="container">
            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            <section class="hero">
                <div>
                    <span class="badge">Owner Finance / Cashflow</span>
                    <h1 class="title">Pencatatan uang masuk dan keluar klinik.</h1>
                    <p class="subtitle">
                        Halaman ini menggabungkan pemasukan otomatis dari billing yang sudah dibayar dengan pencatatan manual
                        seperti owner tambah modal, pemasukan lain, dan seluruh pengeluaran operasional.
                    </p>

                    <div class="quick-links">
                        <a href="/admin/owner-dashboard?month={{ $month }}" class="btn btn-soft">Back to Owner Dashboard</a>
                        <a href="/admin/billings" class="btn btn-soft">Open Billing Ledger</a>
                    </div>
                </div>

                <div class="hero-panel">
                    <h2>Period: {{ $monthLabel }}</h2>
                    <p>
                        Billing void otomatis dikecualikan. Transaksi manual bisa digunakan untuk modal owner dan biaya operasional.
                    </p>

                    <form method="GET" action="/admin/owner-finance" class="period-form">
                        <input type="month" name="month" value="{{ $month }}">
                        <button type="submit" class="btn btn-white">Update Period</button>
                    </form>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Billing Income</div>
                    <div class="stat-value money-positive">Rp {{ number_format($summary['billing_income'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Auto dari billing paid/partial.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Manual Income</div>
                    <div class="stat-value money-positive">Rp {{ number_format($summary['manual_income'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Modal owner / pemasukan lain.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Expense</div>
                    <div class="stat-value money-negative">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Pengeluaran operasional.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Net Cashflow</div>
                    <div class="stat-value {{ $summary['net_cashflow'] >= 0 ? 'money-positive' : 'money-negative' }}">
                        Rp {{ number_format($summary['net_cashflow'], 0, ',', '.') }}
                    </div>
                    <div class="stat-sub">Total income - expense.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Transactions</div>
                    <div class="stat-value">{{ $summary['transaction_count'] }}</div>
                    <div class="stat-sub">Gabungan auto billing dan manual.</div>
                </div>
            </section>

            <section class="grid-2">
                <div class="section-card">
                    <h2 class="section-title">Tambah Transaksi Manual</h2>
                    <p class="section-subtitle">
                        Gunakan untuk owner tambah modal, pemasukan lain, atau semua pengeluaran klinik.
                    </p>

                    <form method="POST" action="/admin/owner-finance/transactions">
                        @csrf

                        <div class="form-grid">
                            <div>
                                <label>Tanggal</label>
                                <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                            </div>

                            <div>
                                <label>Tipe</label>
                                <select name="type" required>
                                    <option value="income">Income / Uang Masuk</option>
                                    <option value="expense">Expense / Uang Keluar</option>
                                </select>
                            </div>

                            <div>
                                <label>Source</label>
                                <select name="source">
                                    <option value="">Auto by Type</option>
                                    <option value="owner_capital">Owner Capital / Tambah Modal</option>
                                    <option value="other_income">Other Income</option>
                                    <option value="operational">Operational</option>
                                    <option value="salary">Salary</option>
                                    <option value="rent">Rent</option>
                                    <option value="utility">Utility</option>
                                    <option value="equipment">Equipment</option>
                                    <option value="consumable">Consumable</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="adjustment">Adjustment</option>
                                </select>
                            </div>

                            <div>
                                <label>Kategori</label>
                                <input type="text" name="category" value="{{ old('category') }}" placeholder="Contoh: Gaji, Sewa, Modal, Listrik">
                            </div>

                            <div class="field-full">
                                <label>Judul Transaksi</label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Owner tambah modal / Bayar listrik" required>
                            </div>

                            <div>
                                <label>Nominal</label>
                                <input type="number" name="amount" value="{{ old('amount') }}" min="0" step="1000" placeholder="0" required>
                            </div>

                            <div>
                                <label>Payment Method</label>
                                <select name="payment_method">
                                    <option value="">-</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qr">QR</option>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Credit Card</option>
                                    <option value="bank_bca">Bank BCA</option>
                                    <option value="bank_bni">Bank BNI</option>
                                    <option value="bank_mandiri">Bank Mandiri</option>
                                </select>
                            </div>

                            <div class="field-full">
                                <label>Notes</label>
                                <textarea name="notes" placeholder="Catatan tambahan bila ada">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark" style="margin-top:16px;">Save Transaction</button>
                    </form>
                </div>

                <div class="section-card">
                    <h2 class="section-title">Cashflow Ledger</h2>
                    <p class="section-subtitle">
                        Pemasukan billing otomatis digabung dengan transaksi manual dalam periode ini.
                    </p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Action</th>
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
                                        <td>
                                            <span class="pill {{ $transaction['type'] === 'income' ? 'pill-green' : 'pill-red' }}">
                                                {{ $transaction['type'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="pill {{ $transaction['kind'] === 'auto_billing' ? 'pill-blue' : 'pill-slate' }}">
                                                {{ $transaction['source'] }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction['payment_method'] ?: '-' }}</td>
                                        <td class="{{ $transaction['type'] === 'income' ? 'money-positive' : 'money-negative' }}" style="font-weight:900;">
                                            {{ $transaction['type'] === 'income' ? '+' : '-' }}
                                            Rp {{ number_format($transaction['amount'], 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if($transaction['kind'] === 'auto_billing')
                                                <a class="btn btn-soft" href="{{ $transaction['url'] }}">Open Billing</a>
                                            @else
                                                <form method="POST" action="/admin/owner-finance/transactions/{{ $transaction['id'] }}/delete" onsubmit="return confirm('Hapus transaksi manual ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-red">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty">Belum ada transaksi finance pada periode ini.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
