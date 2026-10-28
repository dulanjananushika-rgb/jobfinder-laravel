<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class SecurityAndAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_link_sends_notification()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'seeker@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'seeker@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'resetuser@example.com',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'resetuser@example.com',
            'password' => 'NewSecretPass123!',
            'password_confirmation' => 'NewSecretPass123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NewSecretPass123!', $user->fresh()->password));
    }

    public function test_email_verification_notice_rendered_for_unverified_user()
    {
        $user = User::factory()->unverified()->create([
            'role' => 'job_seeker',
        ]);

        $response = $this->actingAs($user)->get(route('verification.notice'));
        $response->assertStatus(200);
        $response->assertSee('Verify Your Email Address');
    }
}
