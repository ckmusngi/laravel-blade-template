<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Exchange credentials for a Sanctum personal access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->validate([
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $request->validateCredentials();

        $user = User::where('email', $request->string('email'))->firstOrFail();

        $token = $user->createToken($request->string('device_name'));

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Revoke the token used for the current request.
     */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
