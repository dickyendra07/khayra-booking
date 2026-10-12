<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Billing item categories
    |--------------------------------------------------------------------------
    |
    | Every new billing line carries one of these categories. Only categories
    | listed in `discountable_categories` form the base for promo / discount
    | calculation. Add-ons, products, fees and room charges are never part of
    | the discount base.
    |
    */

    'billing' => [

        'categories' => [
            'program' => 'Program / Treatment',
            'specialist' => 'Specialist (diskon menunggu keputusan bisnis)',
            'add_on' => 'Add-on',
            'consultation' => 'Konsultasi',
            'product' => 'Produk',
            'booking_fee' => 'Booking Fee',
            'admin_fee' => 'Biaya Admin',
            'room_charge' => 'Biaya Ruangan',
            'other' => 'Lainnya',
        ],

        // Explicit discount eligibility. Change here only after owner approval.
        // 'specialist' is intentionally NOT eligible until the business decides.
        'discountable_categories' => ['program'],

        // ClinicService.category (master data, matched case-insensitively) => billing category.
        // Unknown / empty => other (never discounted).
        'service_category_map' => [
            'program' => 'program',
            'specialist' => 'specialist',
            'add-on' => 'add_on',
            'consultation' => 'consultation',
        ],

        // Category used for a manually typed service line (no master service).
        // Non-discountable by default: the admin must explicitly pick "Program".
        'manual_service_default_category' => 'other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Zona waktu operasional klinik
    |--------------------------------------------------------------------------
    |
    | Database tetap menyimpan timestamp dalam UTC (app.timezone). Tanggal bisnis
    | (tanggal invoice, tanggal pembayaran, periode closing, nomor invoice, "hari ini"
    | di dashboard) mengikuti zona waktu klinik melalui App\Support\ClinicTime.
    |
    */

    'timezone' => env('CLINIC_TIMEZONE', 'Asia/Jakarta'),

    /*
    |--------------------------------------------------------------------------
    | Aturan klinik (biaya admin sudah final; consent v2 masih menunggu persetujuan)
    |--------------------------------------------------------------------------
    */

    // REQ 6 (aturan FINAL): biaya administrasi pasien baru, SATU KALI saat pasien pertama kali didaftarkan.
    // Tagihan dibuat otomatis oleh App\Services\PatientRegistration (atau dicatat sebagai kewajiban tertunda bila periode
    // tutup buku); pasien lama tidak ditagih retroaktif.
    // KESELAMATAN: fitur hanya menyala bila NEW_PATIENT_ADMIN_FEE_ENABLED=true diisi EKSPLISIT di environment. Default MATI,
    // sehingga deploy ke production tidak mulai menagih tanpa keputusan sadar. Nominal tetap Rp35.000 (aturan klinik).
    'new_patient_admin_fee' => [
        'enabled' => (bool) env('NEW_PATIENT_ADMIN_FEE_ENABLED', false),
        'amount' => 35000,
    ],

    // REQ 7: versi template informed consent untuk dokumen BARU. 1 = template sekarang (dengan
    // tanda tangan fisioterapis). Ubah ke 2 hanya setelah template baru disetujui tertulis oleh klinik.
    'consent' => [
        'active_template_version' => 1,
    ],

];
