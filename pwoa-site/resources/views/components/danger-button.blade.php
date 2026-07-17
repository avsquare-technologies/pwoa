<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger py-2 px-4 fw-bold shadow-sm']) }}>
    {{ $slot }}
</button>
