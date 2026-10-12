@props(['title', 'description' => null, 'eyebrow' => null])

<header {{ $attributes->class(['page-header']) }}>
    <div class="page-header__inner">
        <div class="page-header__text">
            @if ($eyebrow)
                <p class="page-header__eyebrow">{{ $eyebrow }}</p>
            @endif

            <h1 class="page-header__title">{{ $title }}</h1>

            @if ($description)
                <p class="page-header__description">{{ $description }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="page-header__actions">{{ $actions }}</div>
        @endisset
    </div>
</header>
