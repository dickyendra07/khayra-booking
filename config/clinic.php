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
            'add_on' => 'Add-on',
            'consultation' => 'Konsultasi',
            'product' => 'Produk',
            'booking_fee' => 'Booking Fee',
            'admin_fee' => 'Biaya Admin',
            'room_charge' => 'Biaya Ruangan',
            'other' => 'Lainnya',
        ],

        // Explicit discount eligibility. Change here only after owner approval.
        'discountable_categories' => ['program'],

        // ClinicService.category (master data) => billing category.
        // Specialist services are treatment programs; unknown => other.
        'service_category_map' => [
            'Program' => 'program',
            'Specialist' => 'program',
            'Add-on' => 'add_on',
            'Consultation' => 'consultation',
        ],

        // Category used for a manually typed service line (no master service).
        'manual_service_default_category' => 'program',
    ],

];
