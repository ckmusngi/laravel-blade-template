<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard for the authenticated user.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'user' => $user,
            'tokenCount' => $user->tokens()->count(),
            'userCount' => $user->can('viewAny', User::class) ? User::count() : null,
            'adminCount' => $user->can('viewAny', User::class) ? User::where('role', UserRole::Admin)->count() : null,
        ]);
    }
}
