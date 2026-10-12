<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List all users. Admin only.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        return view('users.index', [
            'users' => User::orderBy('name')->paginate(15),
            'roles' => UserRole::cases(),
        ]);
    }

    /**
     * Change a user's role.
     */
    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $user->role = $request->enum('role', UserRole::class);
        $user->save();

        return back()->with('status', __(':name is now :role.', [
            'name' => $user->name,
            'role' => $user->role->label(),
        ]));
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $user->tokens()->delete();
        $user->delete();

        return back()->with('status', __(':name has been deleted.', ['name' => $user->name]));
    }
}
