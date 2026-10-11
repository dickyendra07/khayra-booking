{{-- Dropdown for a controlled patient field. A legacy free-text value stays visible and selected. --}}
@php
    $currentValue = old($name, $current ?? null);
    $isLegacy = filled($currentValue) && !in_array($currentValue, $options, true);
@endphp
<select name="{{ $name }}">
    <option value="">Pilih (opsional)</option>
    @foreach($options as $option)
        <option value="{{ $option }}" {{ $currentValue === $option ? 'selected' : '' }}>{{ $option }}</option>
    @endforeach
    @if($isLegacy)
        <option value="{{ $currentValue }}" selected>{{ $currentValue }} (data lama)</option>
    @endif
</select>
