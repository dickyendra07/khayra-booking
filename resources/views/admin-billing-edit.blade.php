<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Billing - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1180px; margin: 0 auto; }
        .top-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
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
        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        }
        .title {
            font-size: 38px;
            font-weight: 800;
            color: #22343a;
            margin: 0 0 10px;
        }
        .subtitle {
            font-size: 14px;
            line-height: 1.8;
            color: #6b7280;
            margin: 0 0 22px;
        }
        .error-box {
            background: #fff1f2;
            border: 1px solid #ffe0e6;
            color: #be123c;
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 18px;
            line-height: 1.8;
            font-size: 13px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        .field.full { grid-column: 1 / -1; }
        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }
        input, select, textarea {
            width: 100%;
            padding: 14px 14px;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 14px;
            background: #ffffff;
            color: #111827;
            font-family: Arial, sans-serif;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23,111,105,.08);
        }
        .readonly-box {
            width: 100%;
            padding: 14px 14px;
            border: 1px dashed #d9e4e1;
            border-radius: 14px;
            background: #f8fbfa;
            color: #334155;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 22px;
        }
        .submit-btn {
            border: none;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            padding: 14px 22px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(47,124,122,.16);
        }
        @media (max-width: 860px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'billings'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/billings/{{ $billing->id }}" class="ghost-link">← Kembali ke Detail Billing</a>
            </div>

            <section class="section-card">
                <h1 class="title">Edit Billing</h1>
                <p class="subtitle">
                    Perbarui data billing, metode pembayaran, dan detail catatan pembayaran agar pencatatan invoice lebih lengkap.
                </p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/billings/{{ $billing->id }}/update">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label>Invoice Number</label>
                            <div class="readonly-box">{{ $billing->invoice_number }}</div>
                        </div>

                        <div class="field">
                            <label>Patient</label>
                            <select name="patient_id" required>
                                <option value="">Pilih Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $billing->patient_id) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->full_name }}{{ $patient->medical_record_number ? ' - ' . $patient->medical_record_number : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Visit Terkait</label>
                            <select name="visit_id">
                                <option value="">Pilih Visit (opsional)</option>
                                @foreach($visits as $visit)
                                    <option value="{{ $visit->id }}" {{ old('visit_id', $billing->visit_id) == $visit->id ? 'selected' : '' }}>
                                        #{{ $visit->id }} - {{ optional($visit->patient)->full_name ?: 'Patient' }} - {{ $visit->visit_date ?: '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" value="{{ old('invoice_date', $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '') }}" required>
                        </div>

                        <div class="field">
                            <label>Amount</label>
                            <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $billing->amount) }}" required>
                        </div>

                        <div class="field">
                            <label>Payment Status</label>
                            <select name="payment_status" required>
                                <option value="paid" {{ old('payment_status', $billing->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ old('payment_status', $billing->payment_status) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ old('payment_status', $billing->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Payment Method</label>
                            <select name="payment_method">
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="qr" {{ old('payment_method', $billing->payment_method) == 'qr' ? 'selected' : '' }}>QR</option>
                                <option value="debit" {{ old('payment_method', $billing->payment_method) == 'debit' ? 'selected' : '' }}>Debit</option>
                                <option value="credit" {{ old('payment_method', $billing->payment_method) == 'credit' ? 'selected' : '' }}>Credit Card</option>
                                <option value="bank_bca" {{ old('payment_method', $billing->payment_method) == 'bank_bca' ? 'selected' : '' }}>Bank BCA</option>
                                <option value="bank_bni" {{ old('payment_method', $billing->payment_method) == 'bank_bni' ? 'selected' : '' }}>Bank BNI</option>
                                <option value="bank_mandiri" {{ old('payment_method', $billing->payment_method) == 'bank_mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                                <option value="insurance" {{ old('payment_method', $billing->payment_method) == 'insurance' ? 'selected' : '' }}>Insurance</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label>Payment Detail Notes</label>
                            <textarea name="payment_detail_notes" rows="4">{{ old('payment_detail_notes', $billing->payment_detail_notes) }}</textarea>
                        </div>

                        <div class="field full">
                            <label>General Notes</label>
                            <textarea name="notes" rows="4">{{ old('notes', $billing->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="submit-btn">Simpan Perubahan Billing</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>