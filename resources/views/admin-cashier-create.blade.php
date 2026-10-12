<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Checkout - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1320px; margin: 0 auto; }

        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 8px 13px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .title { margin: 0; font-size: 42px; line-height: 1.05; color: #22343a; font-weight: 900; }
        .subtitle { margin: 12px 0 0; max-width: 850px; color: #6b7280; font-size: 14px; line-height: 1.9; }

        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; white-space: nowrap; }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #ffffff; box-shadow: 0 12px 24px rgba(47,124,122,.16); }
        .btn-danger { background: #fff1f2; color: #be123c; border: 1px solid #ffe0e6; }

        .hero, .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            margin-bottom: 18px;
        }

        .hero-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 18px; }
        .hero-side { background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%); border-radius: 24px; color: #ffffff; padding: 24px; }
        .hero-side h3 { margin: 0 0 10px; font-size: 26px; }
        .hero-side p { margin: 0; font-size: 13px; line-height: 1.85; color: rgba(255,255,255,.92); }

        .section-title { margin: 0; font-size: 28px; font-weight: 900; color: #22343a; }
        .section-subtitle { margin: 8px 0 18px; color: #6b7280; font-size: 13px; line-height: 1.85; }

        .error-box { background: #fff1f2; border: 1px solid #ffe0e6; color: #be123c; padding: 14px 16px; border-radius: 16px; margin-bottom: 18px; font-size: 13px; line-height: 1.8; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field.full { grid-column: 1 / -1; }

        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 900; color: #334155; }
        input, select, textarea { width: 100%; padding: 14px 14px; border: 1px solid #dde5e3; border-radius: 14px; font-size: 14px; background: #ffffff; color: #111827; font-family: Arial, sans-serif; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #176f69; box-shadow: 0 0 0 4px rgba(23,111,105,.08); }

        .items-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }

        .line-item { border: 1px solid #edf1f0; background: #fbfcfc; border-radius: 20px; padding: 16px; margin-bottom: 12px; }
        .line-grid { display: grid; grid-template-columns: 120px 1.15fr 135px 1.15fr 90px 130px 130px auto; gap: 12px; align-items: end; }

        .category-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
        .category-row label { margin: 0; font-size: 12px; font-weight: 700; }
        .category-row select { width: auto; min-width: 180px; }
        .category-row select.locked { pointer-events: none; opacity: .75; }
        .discount-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .discount-badge.yes { background: #dcfce7; color: #166534; }
        .discount-badge.no { background: #f1f5f9; color: #475569; }
        .inventory-select-wrap { display: none; }
        .line-item[data-type="inventory"] .inventory-select-wrap { display: block; }
        .line-item[data-type="inventory"] .description-wrap { display: none; }

        .line-total-box { background: #ffffff; border: 1px solid #e6ebea; border-radius: 14px; padding: 13px 14px; min-height: 48px; font-weight: 900; color: #22343a; display: flex; align-items: center; justify-content: flex-end; }

        .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; align-items: start; }
        .summary-panel { background: #fbfcfc; border: 1px solid #edf1f0; border-radius: 22px; padding: 18px; }
        .summary-row { display: flex; justify-content: space-between; gap: 14px; padding: 12px 0; border-bottom: 1px solid #edf1f0; font-size: 14px; color: #334155; }
        .summary-row:last-child { border-bottom: 0; }
        .summary-row strong { color: #22343a; font-weight: 900; }
        .summary-row.total { margin-top: 8px; padding: 18px; border: 0; border-radius: 18px; background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%); color: #ffffff; }
        .summary-row.total strong { color: #ffffff; font-size: 28px; }

        .status-preview { display: inline-flex; padding: 9px 13px; border-radius: 999px; font-size: 12px; font-weight: 900; text-transform: uppercase; }
        .paid { background: #dcfce7; color: #166534; }
        .partial { background: #fef3c7; color: #92400e; }
        .unpaid { background: #fee2e2; color: #b91c1c; }

        .submit-row { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; flex-wrap: wrap; }
        .hint { margin-top: 6px; color: #94a3b8; font-size: 12px; line-height: 1.6; }

        @media (max-width: 1180px) {
            .hero-grid, .form-grid, .form-grid-3, .line-grid, .summary-grid { grid-template-columns: 1fr; }
            .line-total-box { justify-content: flex-start; }
        }

        @media (max-width: 900px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .title { font-size: 32px; }
        }
    
        .service-master-wrap { display: block; }
        .line-item[data-type="inventory"] .service-master-wrap { display: none; }
        .service-master-select {
            border-color: #cfe6e1;
            background: #fbfefd;
        }

    
        .package-price-wrap { display: block; }
        .line-item[data-type="inventory"] .package-price-wrap { display: none; }
        .booking-fee-box { margin-top: 12px; border: 1px solid #d8ebe7; background: #f7fbfa; border-radius: 18px; padding: 14px 16px; }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'cashier'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Kasir Advanced</span>
                    <h1 class="title">Kasir Checkout</h1>
                    <p class="subtitle">
                        Buat transaksi pasien dari layanan fisioterapi dan produk inventory. Sekarang sudah mendukung promo, diskon, jumlah bayar, kembalian, dan sisa tagihan.
                    </p>
                </div>

                <a href="/admin/billings" class="btn btn-soft">Lihat Billing</a>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="badge">Checkout Flow</span>
                        <h2 class="section-title">Subtotal, promo, grand total, payment amount, dan change dihitung otomatis.</h2>
                        <p class="section-subtitle">
                            Untuk promo, admin bisa input kode promo, lalu pilih diskon nominal atau persen. Status pembayaran akan otomatis mengikuti jumlah uang yang dibayar.
                        </p>
                    </div>

                    <div class="hero-side">
                        <h3>Contoh Promo</h3>
                        <p>
                            Gunakan kode seperti KHAYRA10 lalu pilih diskon persen 10, atau gunakan diskon nominal seperti 50000 untuk potongan langsung.
                            <strong>Diskon hanya berlaku untuk item Program / Treatment.</strong> Add-on, produk, biaya admin, booking fee, dan biaya ruangan tidak didiskon.
                        </p>
                    </div>
                </div>
            </section>

            <form method="POST" action="/admin/cashier/checkout" id="checkoutForm">
                @csrf
                {{-- Token anti klik-ganda: pengiriman ulang formulir yang sama tidak membuat invoice kedua. --}}
                <input type="hidden" name="checkout_token" value="{{ \Illuminate\Support\Str::uuid() }}">

                @if(isset($replaces) && $replaces)
                    <input type="hidden" name="replaces_billing_id" value="{{ $replaces->id }}">
                    <div class="error-box" style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;">
                        <strong>Invoice pengganti (koreksi).</strong>
                        Menggantikan invoice void {{ $replaces->invoice_number ?: '#' . $replaces->id }}. Periksa item dan harga, lalu simpan.
                        @if(($creditAvailable ?? 0) > 0)
                            <br><strong>Kredit tersedia Rp {{ number_format($creditAvailable, 0, ',', '.') }}</strong> dari invoice lama akan dipakai otomatis (bukan penerimaan kas baru).
                        @endif
                        Invoice lama tetap tersimpan sebagai riwayat dan tertaut ke invoice baru ini.
                    </div>
                @endif

                <section class="section-card">
                    <h2 class="section-title">Data Transaksi</h2>
                    <p class="section-subtitle">Pilih pasien, visit terkait, tanggal invoice, dan metode bayar.</p>

                    @if ($errors->any())
                        <div class="error-box">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-grid">
                        <div class="field">
                            <label>Patient</label>
                            <select name="patient_id" id="patientSelect" required onchange="refreshSuggestions()">
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
                            <select name="visit_id" id="visitSelect" onchange="refreshSuggestions()">
                                <option value="">Pilih Visit (opsional)</option>
                                @foreach($visits as $visit)
                                    <option value="{{ $visit->id }}" data-room="{{ $visit->room_name }}" {{ old('visit_id', $selectedVisitId) == $visit->id ? 'selected' : '' }}>
                                        #{{ $visit->id }} - {{ optional($visit->patient)->full_name ?: 'Patient' }} - {{ $visit->visit_date ?: '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" value="{{ old('invoice_date', \App\Support\ClinicTime::today()) }}" required>
                        </div>

                        <div class="field">
                            <label>Payment Method</label>
                            <select name="payment_method">
                                <option value="">Pilih Metode</option>
                                <option value="cash">Cash</option>
                                <option value="qr">QR</option>
                                <option value="debit">Debit</option>
                                <option value="credit">Credit Card</option>
                                <option value="bank_bca">Bank BCA</option>
                                <option value="bank_bni">Bank BNI</option>
                                <option value="bank_mandiri">Bank Mandiri</option>
                                <option value="insurance">Insurance</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label>Payment Detail Notes</label>
                            <input type="text" name="payment_detail_notes" value="{{ old('payment_detail_notes') }}" placeholder="Contoh: cash / QR ref / bank transfer">
                        </div>
                    </div>
                </section>

                <section class="section-card">
                    <div class="items-header">
                        <div>
                            <h2 class="section-title">Item Checkout</h2>
                            <p class="section-subtitle">Tambahkan layanan manual atau produk inventory.</p>
                        </div>

                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button type="button" class="btn btn-soft" onclick="addBookingFeeItem()">+ Booking Fee Rp50.000</button>
                            <button type="button" class="btn btn-primary" onclick="addLineItem()">+ Tambah Item</button>
                        </div>
                    </div>

                    <div id="adminFeeIndicator" style="display:none;margin-bottom:10px;padding:10px 14px;border-radius:12px;font-size:13px;line-height:1.6;border:1px solid #e2e8f0;background:#f8fafc;color:#334155;"></div>
                    <div id="chargeSuggestions" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;"></div>

                    <div id="lineItems"></div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Promo, Diskon & Pembayaran</h2>
                    <p class="section-subtitle">Isi kode promo bila ada, pilih tipe diskon, lalu masukkan jumlah uang yang dibayar pasien.</p>

                    <div class="summary-grid">
                        <div>
                            <div class="form-grid-3">
                                <div class="field">
                                    <label>Promo</label>
                                    <select name="promo_id" id="promoSelect" onchange="calculateTotals()">
                                        <option value="">Tanpa Promo</option>
                                        @foreach($promos as $promo)
                                            <option
                                                value="{{ $promo->id }}"
                                                data-code="{{ $promo->code }}"
                                                data-type="{{ $promo->discount_type }}"
                                                data-value="{{ $promo->discount_value }}"
                                                data-minimum="{{ $promo->minimum_purchase }}"
                                                data-max="{{ $promo->maximum_discount }}"
                                            >
                                                {{ $promo->code }} - {{ $promo->name }} ({{ $promo->discount_label }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="hint" id="promoHint">Pilih promo dari setting admin.</div>
                                </div>

                                <div class="field">
                                    <label>Uang Diserahkan Pasien</label>
                                    <input type="number" name="paid_amount" id="paidAmount" min="0" step="1" value="{{ old('paid_amount', 0) }}" oninput="calculateTotals()" placeholder="Jumlah diserahkan pasien">
                                    <div class="hint">Kembalian tidak dihitung sebagai pendapatan; yang tercatat adalah penerimaan bersih.</div>
                                </div>

                                <div class="field">
                                    <label>Status Otomatis</label>
                                    <div style="padding-top: 5px;">
                                        <span id="statusPreview" class="status-preview unpaid">Unpaid</span>
                                    </div>
                                    <div class="hint">Paid / Partial / Unpaid otomatis dari paid amount.</div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-panel">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <strong id="subtotalText">Rp 0</strong>
                            </div>

                            <div class="summary-row">
                                <span>Dasar Diskon (Program)</span>
                                <strong id="discountBaseText">Rp 0</strong>
                            </div>

                            <div class="summary-row">
                                <span>Diskon / Promo</span>
                                <strong id="discountText">Rp 0</strong>
                            </div>

                            <div class="summary-row">
                                <span>Paid Amount</span>
                                <strong id="paidText">Rp 0</strong>
                            </div>

                            <div class="summary-row">
                                <span>Kembalian</span>
                                <strong id="changeText">Rp 0</strong>
                            </div>

                            <div class="summary-row">
                                <span>Sisa Tagihan</span>
                                <strong id="remainingText">Rp 0</strong>
                            </div>

                            <div class="summary-row total">
                                <span>Grand Total</span>
                                <strong id="grandTotal">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Catatan Invoice</h2>
                    <p class="section-subtitle">Catatan umum yang akan disimpan ke billing.</p>

                    <textarea name="notes" rows="4" placeholder="Contoh: pembayaran lengkap, paket treatment, atau catatan administrasi.">{{ old('notes') }}</textarea>

                    <div class="submit-row">
                        <a href="/admin/billings" class="btn btn-soft">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Checkout</button>
                    </div>
                </section>
            </form>
        </div>
    </main>
</div>

<template id="lineItemTemplate">
    <div class="line-item" data-type="service">
        <div class="line-grid">
            <div class="field">
                <label>Type</label>
                <select name="item_type[]" class="line-type" onchange="handleTypeChange(this)">
                    <option value="service">Service</option>
                    <option value="inventory">Inventory</option>
                </select>
            </div>

            <div class="field service-master-wrap">
                <label>Master Layanan</label>
                <select name="clinic_service_id[]" class="service-master-select" onchange="handleClinicServiceChange(this)">
                    <option value="">Manual / pilih layanan</option>
                    @foreach(($clinicServices ?? collect()) as $service)
                        <option
                            value="{{ $service->id }}"
                            data-name="{{ $service->name }}"
                            data-category="{{ \App\Services\BillingCalculator::categoryForService($service->category) }}"
                            data-price="{{ $service->price_per_visit }}"
                            data-package-3x="{{ $service->package_3x_price }}"
                            data-package-6x="{{ $service->package_6x_price }}"
                            data-package-12x="{{ $service->package_12x_price }}"
                        >
                            {{ $service->name }} - Rp {{ number_format($service->price_per_visit, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field package-price-wrap">
                <label>Jenis Harga</label>
                <select class="package-price-select" onchange="handlePackagePriceChange(this)">
                    <option value="visit">Per Visit</option>
                    <option value="3x">Paket 3X</option>
                    <option value="6x">Paket 6X</option>
                    <option value="12x">Paket 12X</option>
                </select>
            </div>

            <div class="field description-wrap">
                <label>Deskripsi Layanan</label>
                <input type="text" name="description[]" class="description-input" value="Fisioterapi Umum">
            </div>

            <div class="field inventory-select-wrap">
                <label>Produk Inventory</label>
                <select name="inventory_item_id[]" class="inventory-select" onchange="handleInventoryChange(this)">
                    <option value="">Pilih produk</option>
                    @foreach($inventoryItems as $item)
                        <option
                            value="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-price="{{ $item->selling_price ?? 0 }}"
                            data-stock="{{ $item->stock ?? 0 }}"
                            data-unit="{{ $item->unit ?? 'unit' }}"
                        >
                            {{ $item->name }} - Stok {{ $item->stock }} {{ $item->unit }} - Rp {{ number_format($item->selling_price ?? 0, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Qty</label>
                <input type="number" name="quantity[]" class="qty-input" min="1" value="1" oninput="calculateTotals()">
            </div>

            <div class="field">
                <label>Harga</label>
                <input type="number" name="unit_price[]" class="price-input" min="0" step="1" value="150000" oninput="calculateTotals()">
            </div>

            <div class="field">
                <label>Subtotal</label>
                <div class="line-total-box">Rp 0</div>
            </div>

            <div class="field">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger" onclick="removeLineItem(this)">Hapus</button>
            </div>
        </div>

        <input type="hidden" name="reference_type[]" class="ref-type" value="">
        <input type="hidden" name="reference_id[]" class="ref-id" value="">

        <div class="price-reason-row" style="display:none; margin-top:10px;">
            <label style="font-size:12px;font-weight:700;">Alasan harga berbeda dari master (wajib, tercatat di audit)</label>
            <input type="text" name="price_reason[]" class="price-reason-input" maxlength="300" placeholder="Contoh: promo khusus disetujui owner / koreksi harga input">
        </div>

        <div class="category-row">
            <label>Kategori Item</label>
            <select name="item_category[]" class="category-select" onchange="calculateTotals()">
                @foreach(\App\Services\BillingCalculator::categories() as $categoryKey => $categoryLabel)
                    <option value="{{ $categoryKey }}" {{ $categoryKey === config('clinic.billing.manual_service_default_category') ? 'selected' : '' }}>{{ $categoryLabel }}</option>
                @endforeach
            </select>
            <span class="discount-badge no category-badge">Tidak kena diskon</span>
            <span class="hint category-hint" style="margin:0;"></span>
        </div>

        <div class="hint inventory-hint"></div>
    </div>
</template>

<script>
    const DISCOUNTABLE_CATEGORIES = @json(config('clinic.billing.discountable_categories'));
    const CREDIT_AVAILABLE = @json((float) ($creditAvailable ?? 0));
    const ROOM_RATES = @json($roomRates ?? new stdClass);
    const BILLED_VISITS = @json($billedVisitIds ?? []);
    const ADMIN_FEE = @json($adminFee);

    function hasReferenceLine(type, id) {
        return Array.from(document.querySelectorAll('.line-item')).some(row =>
            row.querySelector('.ref-type').value === type && String(row.querySelector('.ref-id').value) === String(id));
    }

    // Baris berreferensi: kategori, harga acuan, dan satuan divalidasi ulang oleh server.
    function addReferenceLine(opts) {
        addLineItem();
        const rows = document.querySelectorAll('.line-item');
        const row = rows[rows.length - 1];
        row.querySelector('.line-type').value = 'service';
        row.dataset.type = 'service';
        row.querySelector('.service-master-select').value = '';
        row.querySelector('.description-input').value = opts.description;
        row.querySelector('.qty-input').value = opts.quantity || 1;
        row.querySelector('.price-input').value = opts.price;
        row.querySelector('.ref-type').value = opts.type;
        row.querySelector('.ref-id').value = opts.id;
        setRowCategory(row, opts.category, true, opts.hint || 'Kategori ditentukan sistem.');
        calculateTotals();
        refreshSuggestions();
    }

    // Saran biaya (bukan penagihan otomatis): muncul hanya bila syaratnya terpenuhi.
    function refreshSuggestions() {
        const box = document.getElementById('chargeSuggestions');
        if (!box) { return; }
        box.innerHTML = '';

        const visitSelect = document.getElementById('visitSelect');
        const visitOption = visitSelect && visitSelect.options[visitSelect.selectedIndex];
        const roomName = visitOption && visitOption.value ? (visitOption.dataset.room || '') : '';
        const room = roomName ? ROOM_RATES[roomName.toLowerCase()] : null;

        if (room && visitOption.value && !BILLED_VISITS.includes(Number(visitOption.value)) && !hasReferenceLine('visit_room', visitOption.value)) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-soft';
            button.textContent = '+ Biaya ruangan ' + room.name + ' (Rp ' + Number(room.rate).toLocaleString('id-ID') + ' ' + room.unitLabel.toLowerCase() + ')';
            button.onclick = () => addReferenceLine({
                type: 'visit_room', id: visitOption.value, category: 'room_charge', price: room.rate, quantity: 1,
                description: 'Biaya ruangan ' + room.name + ' (' + room.unitLabel.toLowerCase() + ')',
                hint: 'Biaya ruangan: komponen tersendiri, tidak didiskon. Ubah jumlah sesuai satuan tarif.',
            });
            box.appendChild(button);
        }

        const patientSelect = document.getElementById('patientSelect');
        const indicator = document.getElementById('adminFeeIndicator');
        const patientState = patientSelect && patientSelect.value && ADMIN_FEE.patients ? ADMIN_FEE.patients[patientSelect.value] : null;

        // Indikator biaya administrasi (informasi saja): tagihan dibuat otomatis saat registrasi pasien, bukan di kasir.
        if (indicator) {
            indicator.style.display = patientState ? 'block' : 'none';

            if (patientState) {
                const tone = {
                    charged: ['#ecfdf5', '#bbf7d0', '#166534'], voided: ['#fffbeb', '#fde68a', '#92400e'],
                    not_applicable: ['#f8fafc', '#e2e8f0', '#475569'],
                }[patientState.state] || ['#f8fafc', '#e2e8f0', '#475569'];
                indicator.style.background = tone[0];
                indicator.style.borderColor = tone[1];
                indicator.style.color = tone[2];
                indicator.textContent = patientState.label;
            }
        }

        // Koreksi: bila invoice administrasi di-void, baris biayanya dapat diterbitkan ulang lewat Terbitkan Ulang
        // (halaman detail invoice), bukan lewat saran di kasir.
    }
    const MANUAL_DEFAULT_CATEGORY = @json(config('clinic.billing.manual_service_default_category'));

    function setRowCategory(row, category, locked, hint) {
        const select = row.querySelector('.category-select');
        select.value = category;
        select.classList.toggle('locked', !!locked);
        select.tabIndex = locked ? -1 : 0;
        row.querySelector('.category-hint').innerText = hint || '';
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function addLineItem() {
        const template = document.getElementById('lineItemTemplate');
        const clone = template.content.cloneNode(true);
        document.getElementById('lineItems').appendChild(clone);
        calculateTotals();
    }

    function removeLineItem(button) {
        const rows = document.querySelectorAll('.line-item');
        if (rows.length <= 1) {
            alert('Minimal harus ada 1 item checkout.');
            return;
        }

        button.closest('.line-item').remove();
        calculateTotals();
    }

    function handleTypeChange(select) {
        const row = select.closest('.line-item');
        const type = select.value;
        row.dataset.type = type;

        const descriptionInput = row.querySelector('.description-input');
        const inventorySelect = row.querySelector('.inventory-select');
        const serviceSelect = row.querySelector('.service-master-select');
        const priceInput = row.querySelector('.price-input');

        if (type === 'inventory') {
            if (serviceSelect) {
                serviceSelect.value = '';
            }
            descriptionInput.value = '';
            priceInput.value = 0;
            setRowCategory(row, 'product', true, 'Produk tidak termasuk diskon.');
            handleInventoryChange(inventorySelect);
        } else {
            setRowCategory(row, MANUAL_DEFAULT_CATEGORY, false, '');
            inventorySelect.value = '';
            descriptionInput.value = descriptionInput.value || 'Fisioterapi Umum';
            if (Number(priceInput.value || 0) === 0) {
                priceInput.value = 150000;
            }
            row.querySelector('.inventory-hint').innerText = '';
        }

        calculateTotals();
    }

    function syncPackageOptions(row) {
        const serviceSelect = row.querySelector('.service-master-select');
        const packageSelect = row.querySelector('.package-price-select');

        if (!serviceSelect || !packageSelect) {
            return;
        }

        const selected = serviceSelect.options[serviceSelect.selectedIndex];

        Array.from(packageSelect.options).forEach(option => {
            option.disabled = false;
        });

        if (!selected || !selected.value) {
            packageSelect.value = 'visit';
            return;
        }

        const packageMap = {
            '3x': selected.getAttribute('data-package-3x'),
            '6x': selected.getAttribute('data-package-6x'),
            '12x': selected.getAttribute('data-package-12x'),
        };

        Array.from(packageSelect.options).forEach(option => {
            if (option.value === 'visit') {
                option.disabled = false;
                return;
            }

            option.disabled = !Number(packageMap[option.value] || 0);
        });

        if (packageSelect.options[packageSelect.selectedIndex]?.disabled) {
            packageSelect.value = 'visit';
        }
    }

    function handleClinicServiceChange(select) {
        const row = select.closest('.line-item');
        const selected = select.options[select.selectedIndex];
        const descriptionInput = row.querySelector('.description-input');
        const priceInput = row.querySelector('.price-input');
        const packageSelect = row.querySelector('.package-price-select');

        if (!selected || !selected.value) {
            setRowCategory(row, MANUAL_DEFAULT_CATEGORY, false, '');
            calculateTotals();
            return;
        }

        setRowCategory(row, selected.dataset.category || 'other', true, 'Kategori mengikuti master layanan.');

        syncPackageOptions(row);

        const packageType = packageSelect ? packageSelect.value : 'visit';
        let selectedPrice = selected.dataset.price || 0;
        let suffix = '';

        if (packageType === '3x') {
            selectedPrice = selected.getAttribute('data-package-3x') || 0;
            suffix = ' - Paket 3X';
        } else if (packageType === '6x') {
            selectedPrice = selected.getAttribute('data-package-6x') || 0;
            suffix = ' - Paket 6X';
        } else if (packageType === '12x') {
            selectedPrice = selected.getAttribute('data-package-12x') || 0;
            suffix = ' - Paket 12X';
        }

        if (!Number(selectedPrice || 0)) {
            if (packageSelect) {
                packageSelect.value = 'visit';
            }

            selectedPrice = selected.dataset.price || 0;
            suffix = '';
        }

        descriptionInput.value = (selected.dataset.name || selected.textContent.trim()) + suffix;
        priceInput.value = selectedPrice || 0;

        calculateTotals();
    }

    function handlePackagePriceChange(select) {
        const row = select.closest('.line-item');
        const serviceSelect = row.querySelector('.service-master-select');

        syncPackageOptions(row);

        if (!serviceSelect || !serviceSelect.value) {
            select.value = 'visit';
            return;
        }

        if (select.options[select.selectedIndex]?.disabled) {
            select.value = 'visit';
        }

        handleClinicServiceChange(serviceSelect);
    }

    function addBookingFeeItem() {
        const existing = Array.from(document.querySelectorAll('.description-input')).some(input => {
            return (input.value || '').toLowerCase().includes('booking fee');
        });

        if (existing) {
            alert('Booking fee sudah ada di item checkout.');
            return;
        }

        addLineItem();

        const rows = document.querySelectorAll('.line-item');
        const row = rows[rows.length - 1];

        row.dataset.type = 'service';

        const typeSelect = row.querySelector('.line-type');
        const serviceSelect = row.querySelector('.service-master-select');
        const packageSelect = row.querySelector('.package-price-select');
        const descriptionInput = row.querySelector('.description-input');
        const qtyInput = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');

        if (typeSelect) typeSelect.value = 'service';
        if (serviceSelect) serviceSelect.value = '';
        if (packageSelect) packageSelect.value = 'visit';

        setRowCategory(row, 'booking_fee', false, '');
        descriptionInput.value = 'Booking Fee Non-Paket';
        qtyInput.value = 1;
        priceInput.value = 50000;

        calculateTotals();
    }

    function handleInventoryChange(select) {
        const row = select.closest('.line-item');
        const selected = select.options[select.selectedIndex];
        const descriptionInput = row.querySelector('.description-input');
        const priceInput = row.querySelector('.price-input');
        const hint = row.querySelector('.inventory-hint');

        if (!selected || !selected.value) {
            descriptionInput.value = '';
            priceInput.value = 0;
            hint.innerText = '';
            calculateTotals();
            return;
        }

        descriptionInput.value = selected.dataset.name || selected.textContent.trim();
        priceInput.value = selected.dataset.price || 0;

        const stock = selected.dataset.stock || 0;
        const unit = selected.dataset.unit || 'unit';
        hint.innerText = 'Stok tersedia: ' + stock + ' ' + unit + '. Stok akan otomatis berkurang setelah checkout disimpan.';

        calculateTotals();
    }

    function referencePrices(row) {
        if (row.dataset.type === 'inventory') {
            const inv = row.querySelector('.inventory-select');
            const opt = inv.options[inv.selectedIndex];
            return opt && opt.value ? [Number(opt.dataset.price || 0)] : [];
        }

        const svc = row.querySelector('.service-master-select');
        const opt = svc.options[svc.selectedIndex];

        if (!opt || !opt.value) {
            return [];
        }

        return [opt.dataset.price, opt.getAttribute('data-package-3x'), opt.getAttribute('data-package-6x'), opt.getAttribute('data-package-12x')]
            .filter(value => value !== null && value !== undefined && value !== '')
            .map(Number);
    }

    const PREFILL_ITEMS = @json($prefillItems ?? []);

    function fillRowFromPrefill(row, item) {
        const typeSelect = row.querySelector('.line-type');
        typeSelect.value = item.type;
        handleTypeChange(typeSelect);

        if (item.type === 'inventory') {
            const inventorySelect = row.querySelector('.inventory-select');

            if (item.inventory_item_id) {
                inventorySelect.value = String(item.inventory_item_id);

                if (inventorySelect.value) {
                    handleInventoryChange(inventorySelect);
                }
            }
        } else if (item.clinic_service_id) {
            const serviceSelect = row.querySelector('.service-master-select');
            serviceSelect.value = String(item.clinic_service_id);

            if (serviceSelect.value) {
                handleClinicServiceChange(serviceSelect);
            }
        }

        row.querySelector('.description-input').value = item.description;
        row.querySelector('.qty-input').value = item.quantity;
        row.querySelector('.price-input').value = item.unit_price;

        if (item.reference_type) {
            row.querySelector('.ref-type').value = item.reference_type;
            row.querySelector('.ref-id').value = item.reference_id;
            setRowCategory(row, item.category, true, 'Biaya berreferensi dari invoice sebelumnya; divalidasi ulang oleh sistem.');
        }

        const hasMaster = row.querySelector('.service-master-select').value !== '';

        if (item.type === 'service' && !hasMaster && item.category) {
            setRowCategory(row, item.category, false, 'Kategori dari invoice sebelumnya; periksa kembali.');
        }
    }

    function calculateTotals() {
        let subtotal = 0;
        let discountBase = 0;

        document.querySelectorAll('.line-item').forEach(row => {
            const qty = Number(row.querySelector('.qty-input').value || 0);
            const price = Number(row.querySelector('.price-input').value || 0);
            const lineTotal = qty * price;
            const category = row.querySelector('.category-select').value;
            const eligible = DISCOUNTABLE_CATEGORIES.includes(category);

            subtotal += lineTotal;

            if (eligible) {
                discountBase += lineTotal;
            }

            // Harga di luar daftar harga master: alasan wajib (dicatat di audit oleh server).
            const refs = referencePrices(row);
            const override = refs.length > 0 && !refs.includes(price);
            row.querySelector('.price-reason-row').style.display = override ? 'block' : 'none';
            row.querySelector('.price-reason-input').required = override;

            const badge = row.querySelector('.category-badge');
            badge.innerText = eligible ? 'Kena diskon promo' : 'Tidak kena diskon';
            badge.className = 'discount-badge category-badge ' + (eligible ? 'yes' : 'no');
            row.querySelector('.line-total-box').innerText = formatRupiah(lineTotal);
        });

        const promoSelect = document.getElementById('promoSelect');
        const promoOption = promoSelect.options[promoSelect.selectedIndex];
        const paidAmount = Number(document.getElementById('paidAmount').value || 0);

        let discountAmount = 0;
        let promoHint = 'Tanpa promo.';

        if (promoOption && promoOption.value) {
            const discountType = promoOption.dataset.type;
            const discountValue = Number(promoOption.dataset.value || 0);
            const minimum = Number(promoOption.dataset.minimum || 0);
            const maxDiscount = Number(promoOption.dataset.max || 0);
            const code = promoOption.dataset.code || '';

            if (discountBase <= 0) {
                discountAmount = 0;
                promoHint = 'Promo ' + code + ' hanya berlaku untuk item Program / Treatment. Belum ada item program.';
            } else if (minimum > 0 && discountBase < minimum) {
                discountAmount = 0;
                promoHint = 'Promo ' + code + ' butuh minimum item program ' + formatRupiah(minimum) + '.';
            } else {
                if (discountType === 'nominal') {
                    discountAmount = discountValue;
                }

                if (discountType === 'percent') {
                    discountAmount = discountBase * Math.min(discountValue, 100) / 100;
                }

                if (maxDiscount > 0) {
                    discountAmount = Math.min(discountAmount, maxDiscount);
                }

                discountAmount = Math.min(discountAmount, discountBase);
                promoHint = 'Promo ' + code + ' aktif. Potongan ' + formatRupiah(discountAmount) + ' dari dasar diskon (item program) ' + formatRupiah(discountBase) + '. Add-on, produk, dan biaya lain tidak didiskon.';
            }
        }

        document.getElementById('promoHint').innerText = promoHint;

        const grandTotal = Math.max(subtotal - discountAmount, 0);
        // Kredit dari invoice yang digantikan menutup sebagian tagihan (bukan kas baru).
        const credit = Math.min(CREDIT_AVAILABLE, grandTotal);
        const dueAfterCredit = grandTotal - credit;
        const changeAmount = Math.max(paidAmount - dueAfterCredit, 0);
        const remainingAmount = Math.max(dueAfterCredit - paidAmount, 0);

        let status = 'unpaid';
        if (grandTotal > 0 && (credit + Math.min(paidAmount, dueAfterCredit)) >= grandTotal) {
            status = 'paid';
        } else if (credit + paidAmount > 0) {
            status = 'partial';
        }

        const statusPreview = document.getElementById('statusPreview');
        statusPreview.innerText = status;
        statusPreview.className = 'status-preview ' + status;

        document.getElementById('subtotalText').innerText = formatRupiah(subtotal);
        document.getElementById('discountBaseText').innerText = formatRupiah(discountBase);
        document.getElementById('discountText').innerText = formatRupiah(discountAmount);
        document.getElementById('paidText').innerText = formatRupiah(paidAmount);
        document.getElementById('changeText').innerText = formatRupiah(changeAmount);
        document.getElementById('remainingText').innerText = formatRupiah(remainingAmount);
        document.getElementById('grandTotal').innerText = formatRupiah(grandTotal);
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (PREFILL_ITEMS.length) {
            PREFILL_ITEMS.forEach(function (item) {
                addLineItem();
                const rows = document.querySelectorAll('.line-item');
                fillRowFromPrefill(rows[rows.length - 1], item);
            });
        } else {
            addLineItem();
        }

        calculateTotals();
        refreshSuggestions();
    });
</script>
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
