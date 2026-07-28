@props(['field', 'label'])

@php
    $direction = request('direction') === 'desc' ? 'desc' : 'asc';
    $isActive = request('sort') === $field;
    $next = $isActive && $direction === 'asc' ? 'desc' : 'asc';
    $icon = $isActive ? ($direction === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up';
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'direction' => $next, 'page' => 1]) }}"
   aria-label="Urutkan {{ $label }} {{ $next === 'asc' ? 'menaik' : 'menurun' }}">
    {{ $label }}
    <i class="bi {{ $icon }} @if ($isActive) is-sorted @endif" aria-hidden="true"></i>
</a>
