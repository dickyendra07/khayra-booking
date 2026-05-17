@include('admin-promo-form', [
    'mode' => 'edit',
    'promo' => $promo,
    'action' => '/admin/promos/' . $promo->id . '/update',
    'title' => 'Edit Promo',
    'subtitle' => 'Perbarui detail promo yang sudah ada.'
])
