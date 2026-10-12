<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_exchange_credentials_for_a_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']])
            ->assertJsonPath('user.email', $user->email);

        $this->assertSame(1, $user->tokens()->count());

        $this->withToken($response->json('token'))
            ->getJson(route('api.v1.user'))
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_invalid_credentials_do_not_issue_a_token(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'phpunit',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->getJson(route('api.v1.user'))->assertUnauthorized();
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('phpunit')->plainTextToken;

        $this->withToken($token)
            ->postJson(route('api.v1.logout'))
            ->assertNoContent();

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_only_admins_can_list_users_via_the_api(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson(route('api.v1.users.index'))->assertForbidden();

        Sanctum::actingAs(User::factory()->admin()->create());
        $this->getJson(route('api.v1.users.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_users_can_view_themselves_but_not_others(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson(route('api.v1.users.show', $user))->assertOk();
        $this->getJson(route('api.v1.users.show', $other))->assertForbidden();
    }
}
