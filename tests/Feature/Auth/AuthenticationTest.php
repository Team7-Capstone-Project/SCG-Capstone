<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * SRS.Log.002 - SRC.Log.002.001
     * Login menggunakan email dan password yang valid
     */
    public function test_case_src_log_002_001_valid_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /**
     * SRS.Log.002 - SRC.Log.002.002
     * Login tanpa mengisi email dan password
     */
    public function test_case_src_log_002_002_empty_login(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * SRS.Log.002 - SRC.Log.002.003
     * Login menggunakan format email yang tidak valid
     */
    public function test_case_src_log_002_003_invalid_email_format(): void
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * SRS.Log.002 - SRC.Log.002.004
     * Login menggunakan password yang salah
     */
    public function test_case_src_log_002_004_wrong_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * SRS.Log.002 - SRC.Log.002.005
     * Login menggunakan email yang belum terdaftar
     */
    public function test_case_src_log_002_005_unregistered_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'unregistered.sales@scg.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * SRS.Log.002 - SRC.Log.002.006
     * Login hanya mengisi password
     */
    public function test_case_src_log_002_006_only_password(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * SRS.Log.002 - SRC.Log.002.007
     * Login hanya mengisi email
     */
    public function test_case_src_log_002_007_only_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'test.sales@scg.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }
}
