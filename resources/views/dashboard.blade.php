<x-layouts.app title="Dashboard">
    <x-slot:header>
        <x-page-header
            eyebrow="Dashboard"
            :title="'Hello, '.$user->name"
            description="Here's an overview of your account."
        />
    </x-slot:header>

    <div class="stats">
        <div class="card stat">
            <span class="stat__label">Role</span>
            <span class="stat__value"><x-role-badge :role="$user->role" /></span>
        </div>
        <div class="card stat">
            <span class="stat__label">API tokens</span>
            <span class="stat__value">{{ $tokenCount }}</span>
        </div>
        <div class="card stat">
            <span class="stat__label">Member since</span>
            <span class="stat__value stat__value--sm">{{ $user->created_at->format('M j, Y') }}</span>
        </div>

        @can('viewAny', App\Models\User::class)
            <div class="card stat">
                <span class="stat__label">Total users</span>
                <span class="stat__value">{{ $userCount }}</span>
            </div>
            <div class="card stat">
                <span class="stat__label">Administrators</span>
                <span class="stat__value">{{ $adminCount }}</span>
            </div>
        @endcan
    </div>

    <div class="card">
        <h2 class="card__title">Account details</h2>
        <dl class="details">
            <dt>Name</dt>
            <dd>{{ $user->name }}</dd>
            <dt>Email</dt>
            <dd>{{ $user->email }}</dd>
            <dt>Email verified</dt>
            <dd>{{ $user->email_verified_at?->format('M j, Y') ?? 'Not verified' }}</dd>
        </dl>
    </div>

    @can('viewAny', App\Models\User::class)
        <div class="card card--accent">
            <h2 class="card__title">Administration</h2>
            <p>You're an administrator, so you can manage user roles and accounts.</p>
            <a href="{{ route('users.index') }}" class="btn btn--primary">Manage users</a>
        </div>
    @endcan
</x-layouts.app>
