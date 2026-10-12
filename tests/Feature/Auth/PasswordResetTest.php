<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_reset_link_is_emailed_to_existing_users(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_unknown_emails_get_the_same_response_without_a_notification(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'nobody@example.com'])
            ->assertSessionHas('status', __('passwords.sent'))
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $this->get(route('password.reset', ['token' => $notification->token, 'email' => $user->email]))
                ->assertOk()
                ->assertSee($user->email);

            return true;
        });
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $user->createToken('phone');

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $this->post(route('password.store'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])->assertRedirect(route('login'))->assertSessionHasNoErrors();

            return true;
        });

        $user->refresh();
        $this->assertTrue(Hash::check('new-password-123', $user->password));
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_password_cannot_be_reset_with_an_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.store'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
