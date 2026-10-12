@props(['href', 'active' => false])

<a href="{{ $href }}" {{ $attributes->class(['sidebar-link', 'is-active' => $active]) }} @if ($active) aria-current="page" @endif>
    <svg class="sidebar-link__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        {{ $icon }}
    </svg>
    <span>{{ $slot }}</span>
</a>
