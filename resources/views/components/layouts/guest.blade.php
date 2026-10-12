@props(['title' => null, 'heading', 'subheading' => null])

<x-layouts.base :title="$title" class="guest">
    <x-theme-toggle class="theme-toggle--floating" />

    <main class="auth-card">
        <div class="auth-card__brand">
            <span class="logo" aria-hidden="true">{{ str(config('app.name'))->substr(0, 1) }}</span>
            <span>{{ config('app.name') }}</span>
        </div>

        <h1 class="auth-card__heading">{{ $heading }}</h1>

        @if ($subheading)
            <p class="auth-card__subheading">{{ $subheading }}</p>
        @endif

        <x-flash />

        {{ $slot }}
    </main>
</x-layouts.base>
