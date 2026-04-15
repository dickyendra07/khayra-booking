<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Billing - Khayra Physio</title>
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
        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 26px;
            padding: 26px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.05);
            margin-bottom: 18px;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 18px;
        }
        .hero-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 14px;
        }
        .hero-title {
            margin: 0;
            font-size: 38px;
            line-height: 1.08;
            color: #22343a;
            font-weight: 800;
        }
        .hero-text {
            margin: 14px 0 0;
            font-size: 14px;
            line-height: 1.9;
            color: #6b7280;
        }
        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 22px;
            padding: 22px;
            color: #ffffff;
        }
        .hero-side h3 {
            margin: 0 0 8px;
            font-size: 24px;
        }
        .hero-side p {
            margin: 0 0 14px;
            font-size: 13px;
            line-height: 1.8;
            color: rgba(255,255,255,.92);
        }
        .hero-list {
            margin: 0;
            padding-left: 18px;
            font-size: 13px;
            line-height: 1.9;
            color: rgba(255,255,255,.95);
        }
        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        }
        .section-title {
            margin: 0;
            font-size: 30px;
            color: #22343a;
            font-weight: 800;
        }
        .section-subtitle {
            margin: 8px 0 18px;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
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
        .hint {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.6;
            color: #7b8794;
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
        @media (max-width: 960px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'billings'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/billings" class="ghost-link">← Kembali ke Billing List</a>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Create Billing</div>
                        <h1 class="hero-title">Create a cleaner billing entry with richer payment options.</h1>
                        <p class="hero-text">
                            Form billing ini sudah disesuaikan agar admin bisa memilih metode pembayaran yang lebih lengkap dan menambahkan catatan detail pembayaran secara manual.
                        </p>
                    </div>

                    <div class="hero-side">
                        <h3>Payment Setup</h3>
                        <p>Gunakan notes pembayaran untuk menulis detail rekening, nomor referensi, atau nama asuransi bila dibutuhkan.</p>
                        <ul class="hero-list">
                            <li>QR</li>
                            <li>Debit / Credit Card</li>
                            <li>Bank BCA / BNI / Mandiri</li>
                            <li>Insurance</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Billing Form</h2>
                <p class="section-subtitle">Isi data invoice, patient, nominal, dan informasi pembayaran dengan format yang lebih lengkap.</p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/billings">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label>Patient</label>
                            <select name="patient_id" required>
                                <option value="">Pilih Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatientId) == $patient->id ? 'selected' : '' }}>
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
                                    <option value="{{ $visit->id }}" {{ old('visit_id', optional($selectedVisit)->id) == $visit->id ? 'selected' : '' }}>
                                        #{{ $visit->id }} - {{ optional($visit->patient)->full_name ?: 'Patient' }} - {{ $visit->visit_date ?: '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
                        </div>

                        <div class="field">
                            <label>Amount</label>
                            <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" required>
                        </div>

                        <div class="field">
                            <label>Payment Status</label>
                            <select name="payment_status" required>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Payment Method</label>
                            <select name="payment_method">
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="qr" {{ old('payment_method') == 'qr' ? 'selected' : '' }}>QR</option>
                                <option value="debit" {{ old('payment_method') == 'debit' ? 'selected' : '' }}>Debit</option>
                                <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>Credit Card</option>
                                <option value="bank_bca" {{ old('payment_method') == 'bank_bca' ? 'selected' : '' }}>Bank BCA</option>
                                <option value="bank_bni" {{ old('payment_method') == 'bank_bni' ? 'selected' : '' }}>Bank BNI</option>
                                <option value="bank_mandiri" {{ old('payment_method') == 'bank_mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                                <option value="insurance" {{ old('payment_method') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label>Payment Detail Notes</label>
                            <textarea name="payment_detail_notes" rows="4">{{ old('payment_detail_notes') }}</textarea>
                            <div class="hint">
                                Contoh: transfer ke BCA a.n. Khayra Physio, ref no. 12345 / Insurance: Allianz corporate plan.
                            </div>
                        </div>

                        <div class="field full">
                            <label>General Notes</label>
                            <textarea name="notes" rows="4">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="submit-btn">Simpan Billing</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>