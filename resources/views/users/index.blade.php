<x-layouts.app title="Users">
    <x-slot:header>
        <x-page-header
            title="Users"
            description="Manage roles and accounts. You can't change or delete your own account here."
        />
    </x-slot:header>

    @if ($errors->any())
        <div class="alert alert--error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="card card--flush">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th class="table__actions"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $listedUser)
                        <tr>
                            <td>
                                {{ $listedUser->name }}
                                @if ($listedUser->is(auth()->user()))
                                    <span class="muted">(you)</span>
                                @endif
                            </td>
                            <td>{{ $listedUser->email }}</td>
                            <td>
                                @can('updateRole', $listedUser)
                                    <form method="POST" action="{{ route('users.role.update', $listedUser) }}" class="inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <label for="role-{{ $listedUser->id }}" class="sr-only">Role for {{ $listedUser->name }}</label>
                                        <select id="role-{{ $listedUser->id }}" name="role" class="select select--sm">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->value }}" @selected($listedUser->role === $role)>{{ $role->label() }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn--ghost btn--sm">Save</button>
                                    </form>
                                @else
                                    <x-role-badge :role="$listedUser->role" />
                                @endcan
                            </td>
                            <td class="muted">{{ $listedUser->created_at->format('M j, Y') }}</td>
                            <td class="table__actions">
                                @can('delete', $listedUser)
                                    <form method="POST" action="{{ route('users.destroy', $listedUser) }}"
                                          onsubmit="return confirm({{ Illuminate\Support\Js::from('Delete '.$listedUser->name.'? This cannot be undone.') }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn--danger btn--sm">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($users->hasPages())
        <nav class="pagination" aria-label="Pagination">
            @if ($users->onFirstPage())
                <span class="btn btn--ghost btn--sm is-disabled">&larr; Previous</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="btn btn--ghost btn--sm">&larr; Previous</a>
            @endif

            <span class="muted">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>

            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="btn btn--ghost btn--sm">Next &rarr;</a>
            @else
                <span class="btn btn--ghost btn--sm is-disabled">Next &rarr;</span>
            @endif
        </nav>
    @endif
</x-layouts.app>
