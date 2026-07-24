@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'form-status form-status--success']) }}>
        {{ $status }}
    </div>
@endif
