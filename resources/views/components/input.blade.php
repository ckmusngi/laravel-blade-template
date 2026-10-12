@props(['name', 'label', 'type' => 'text'])

<div class="field">
    <label for="{{ $name }}" class="field__label">{{ $label }}</label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $attributes->get('value')) }}" @endif
        {{ $attributes->except('value')->class(['field__input', 'has-error' => $errors->has($name)]) }}
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
    >
    @error($name)
        <p id="{{ $name }}-error" class="field__error">{{ $message }}</p>
    @enderror
</div>
