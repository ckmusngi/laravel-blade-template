@props(['title' => null])

<x-layouts.base :title="$title" class="app">
    <div class="shell">
        <aside id="sidebar" class="sidebar" aria-label="Sidebar">
            <div class="sidebar__header">
                <a href="{{ route('dashboard') }}" class="sidebar__brand">
                    <span class="logo" aria-hidden="true">{{ str(config('app.name'))->substr(0, 1) }}</span>
                    <span>{{ config('app.name') }}</span>
                </a>

                <button type="button" class="icon-button sidebar__close" data-sidebar-close aria-label="Close menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="sidebar__nav" aria-label="Main">
                <p class="sidebar__section">Menu</p>

                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-slot:icon>
                        <rect x="3" y="3" width="7" height="9" rx="1" />
                        <rect x="14" y="3" width="7" height="5" rx="1" />
                        <rect x="14" y="12" width="7" height="9" rx="1" />
                        <rect x="3" y="16" width="7" height="5" rx="1" />
                    </x-slot:icon>
                    Dashboard
                </x-sidebar-link>

                @can('viewAny', App\Models\User::class)
                    <p class="sidebar__section">Administration</p>

                    <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        <x-slot:icon>
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </x-slot:icon>
                        Users
                    </x-sidebar-link>
                @endcan
            </nav>

            <div class="sidebar__footer">
                <div class="sidebar__user">
                    <span class="avatar" aria-hidden="true">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                    <div class="sidebar__user-info">
                        <span class="sidebar__user-name">{{ auth()->user()->name }}</span>
                        <x-role-badge :role="auth()->user()->role" />
                    </div>
                    <x-theme-toggle />
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn--ghost btn--sm btn--block">
                        <svg class="btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
                        </svg>
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        <div class="sidebar-backdrop" data-sidebar-close></div>

        <div class="shell__main">
            <header class="mobile-bar">
                <button type="button" class="icon-button" data-sidebar-open aria-label="Open menu" aria-controls="sidebar" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M3 6h18M3 12h18M3 18h18" />
                    </svg>
                </button>

                <a href="{{ route('dashboard') }}" class="sidebar__brand">
                    <span class="logo" aria-hidden="true">{{ str(config('app.name'))->substr(0, 1) }}</span>
                    <span>{{ config('app.name') }}</span>
                </a>

                <x-theme-toggle />
            </header>

            {{ $header ?? '' }}

            <main class="page">
                <x-flash />

                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
