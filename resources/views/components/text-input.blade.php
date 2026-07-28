@props(['disabled' => false])

@php
    // Flag the field itself, not just the message below it. Named error bags
    // (updatePassword, userDeletion) still pass `class` explicitly.
    $field = $attributes->get('name');
    $invalid = $field && $errors->has($field);
@endphp

<input @disabled($disabled)
       @if ($invalid) aria-invalid="true" @endif
       {{ $attributes->merge(['class' => 'form-control'.($invalid ? ' is-invalid' : '')]) }}>
