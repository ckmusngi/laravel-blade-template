<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_view_the_users_page(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee($other->email);
    }

    public function test_regular_users_cannot_view_the_users_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_users_link_is_only_shown_to_admins(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertDontSee(route('users.index'));

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('dashboard'))
            ->assertSee(route('users.index'));
    }

    public function test_admins_can_change_another_users_role(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->patch(route('users.role.update', $user), ['role' => UserRole::Admin->value])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame(UserRole::Admin, $user->fresh()->role);
    }

    public function test_role_must_be_a_valid_value(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->patch(route('users.role.update', $user), ['role' => 'superuser'])
            ->assertSessionHasErrors('role');

        $this->assertSame(UserRole::User, $user->fresh()->role);
    }

    public function test_admins_cannot_change_their_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('users.role.update', $admin), ['role' => UserRole::User->value])
            ->assertForbidden();

        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
    }

    public function test_regular_users_cannot_change_roles(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('users.role.update', $user), ['role' => UserRole::Admin->value])
            ->assertForbidden();

        $this->assertSame(UserRole::User, $user->fresh()->role);
    }

    public function test_admins_can_delete_other_users(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $user))
            ->assertRedirect();

        $this->assertModelMissing($user);
    }

    public function test_admins_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertForbidden();

        $this->assertModelExists($admin);
    }

    public function test_regular_users_cannot_delete_users(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('users.destroy', $other))
            ->assertForbidden();

        $this->assertModelExists($other);
    }
}
