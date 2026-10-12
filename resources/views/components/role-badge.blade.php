@props(['role'])

<span {{ $attributes->class(['badge', 'badge--admin' => $role === App\Enums\UserRole::Admin]) }}>{{ $role->label() }}</span>
