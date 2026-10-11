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

];
