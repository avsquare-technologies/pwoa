@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'list-unstyled mt-2']) }}>
        @foreach ((array) $messages as $message)
            <li class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</li>
        @endforeach
    </ul>
@endif
