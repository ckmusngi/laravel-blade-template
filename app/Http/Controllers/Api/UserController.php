<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * List all users. Admin only.
     */
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', User::class);

        return UserResource::collection(User::orderBy('name')->paginate(15));
    }

    /**
     * Show a single user. Admins can view anyone, users can view themselves.
     */
    public function show(User $user): UserResource
    {
        Gate::authorize('view', $user);

        return new UserResource($user);
    }
}
