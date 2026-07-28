@props(['field', 'label'])

@php
    $direction = request('direction') === 'desc' ? 'desc' : 'asc';
    $isActive = request('sort') === $field;
@endphp

<th aria-sort="{{ $isActive ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
    <x-sort-link :field="$field" :label="$label" />
</th>
