<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Billing - Khayra Admin</title>
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

        .back-link {
            display: inline-block;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
            font-weight: 700;
            white-space: nowrap;
            font-size: 14px;
        }

        .form-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.7;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #374151;
        }

        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            font-size: 14px;
            background: #fcfefd;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15,118,110,.08);
        }

        .helper {
            margin-top: 7px;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.6;
        }

        .submit-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .submit-btn {
            border: none;
            border-radius: 14px;
            padding: 14px 22px;
            background: #0f766e;
            color: white;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 26px rgba(15,118,110,.16);
        }

        @media (max-width: 1024px) {
            .layout {
                display: block;
            }

            .main {
                padding: 20px;
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

            .field-grid {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 20px;
                border-radius: 22px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'billings'])

    <main class="main">
        <div class="topbar">
            <div>
                <h1 class="page-title">Edit Billing</h1>
                <p class="page-subtitle">Perbarui data invoice agar sesuai dengan visit, pasien, dan status pembayaran terbaru.</p>
            </div>

            <a href="/admin/billings/{{ $billing->id }}" class="back-link">← Kembali ke Detail Billing</a>
        </div>

        <section class="form-card">
            @if($errors->any())
                <div class="alert-error">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul style="margin:8px 0 0 18px; padding:0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/admin/billings/{{ $billing->id }}/update">
                @csrf

                <div class="field-grid">
                    <div class="field">
                        <label>Patient</label>
                        <select name="patient_id">
                            <option value="">Pilih Patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $billing->patient_id) == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>Visit</label>
                        <select name="visit_id">
                            <option value="">Tanpa Visit</option>
                            @foreach($visits as $visit)
                                <option value="{{ $visit->id }}" {{ old('visit_id', $billing->visit_id) == $visit->id ? 'selected' : '' }}>
                                    #{{ $visit->id }} - {{ $visit->patient->full_name ?? 'No Patient' }} - {{ $visit->visit_date }}
                                </option>
                            @endforeach
                        </select>
                        <div class="helper">Boleh dikosongkan jika invoice tidak terhubung langsung ke visit tertentu.</div>
                    </div>

                    <div class="field">
                        <label>Invoice Date</label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', $billing->invoice_date) }}">
                    </div>

                    <div class="field">
                        <label>Amount</label>
                        <input type="number" name="amount" min="0" step="0.01" value="{{ old('amount', $billing->amount) }}">
                    </div>

                    <div class="field">
                        <label>Payment Status</label>
                        <select name="payment_status">
                            <option value="paid" {{ old('payment_status', $billing->payment_status) == 'paid' ? 'selected' : '' }}>paid</option>
                            <option value="unpaid" {{ old('payment_status', $billing->payment_status) == 'unpaid' ? 'selected' : '' }}>unpaid</option>
                            <option value="partial" {{ old('payment_status', $billing->payment_status) == 'partial' ? 'selected' : '' }}>partial</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Payment Method</label>
                        <select name="payment_method">
                            <option value="">Pilih Payment Method</option>
                            <option value="cash" {{ old('payment_method', $billing->payment_method) == 'cash' ? 'selected' : '' }}>cash</option>
                            <option value="transfer" {{ old('payment_method', $billing->payment_method) == 'transfer' ? 'selected' : '' }}>transfer</option>
                            <option value="qris" {{ old('payment_method', $billing->payment_method) == 'qris' ? 'selected' : '' }}>qris</option>
                            <option value="debit" {{ old('payment_method', $billing->payment_method) == 'debit' ? 'selected' : '' }}>debit</option>
                            <option value="credit_card" {{ old('payment_method', $billing->payment_method) == 'credit_card' ? 'selected' : '' }}>credit_card</option>
                        </select>
                    </div>

                    <div class="field full">
                        <label>Notes</label>
                        <textarea name="notes">{{ old('notes', $billing->notes) }}</textarea>
                    </div>
                </div>

                <div class="submit-row">
                    <button type="submit" class="submit-btn">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>