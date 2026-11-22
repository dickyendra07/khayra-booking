{{-- Category + discount note for a billing line. Legacy lines (no category) render nothing. --}}
@if($item->item_category)
    @php($rateUnitLabel = \App\Models\Room::RATE_UNITS[$item->rate_unit ?? ''] ?? null)
    <div class="{{ $class ?? 'secondary-text' }}">
        {{ $item->category_label }}@if($rateUnitLabel) · Tarif {{ strtolower($rateUnitLabel) }}@endif
        @if($item->is_discount_eligible && (float) $item->discount_amount > 0)
            · Diskon Rp {{ number_format($item->discount_amount, 0, ',', '.') }}
        @elseif(!$item->is_discount_eligible && (float) ($billing->discount_amount ?? 0) > 0)
            · Tidak termasuk diskon
        @endif
    </div>
@endif
