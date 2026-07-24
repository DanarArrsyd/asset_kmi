@props(['field', 'label'])

@php
    $current = request('sort', 'created_at');
    $direction = request('direction', 'desc');
    $nextDirection = ($current === $field && $direction === 'asc') ? 'desc' : 'asc';
    $icon = $current === $field ? ($direction === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up';
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'direction' => $nextDirection]) }}">
    {{ $label }} <i class="bi {{ $icon }}"></i>
</a>
