<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_sales_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Sales User',
            'email' => 'test.sales@scg.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));

        $user = \App\Models\User::where('email', 'test.sales@scg.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isPICSales());
    }

    public function test_new_scm_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test SCM User',
            'email' => 'test.scm@scg.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));

        $user = \App\Models\User::where('email', 'test.scm@scg.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isAdminSCM());
    }

    public function test_registration_fails_with_invalid_email_suffix(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@scg.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registration_normalizes_uppercase_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Upper User',
            'email' => 'TEST.Sales@SCG.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));

        // The email should be stored in lowercase
        $user = \App\Models\User::where('email', 'test.sales@scg.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isPICSales());
    }

    /**
     * SRS.Reg.001 - SRC.Reg.001.001
     * Cek registrasi dengan email dan password valid
     */
    public function test_case_src_reg_001_001_valid_registration(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test.sales@scg.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
    }

    /**
     * SRS.Reg.001 - SRC.Reg.001.002
     * Memeriksa registrasi dengan password yang terlalu pendek
     */
    public function test_case_src_reg_001_002_short_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test.sales@scg.com',
            'password' => 'short', // 5 characters (less than 8)
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * SRS.Reg.001 - SRC.Reg.001.003
     * Cek registrasi dengan email yang tidak valid
     */
    public function test_case_src_reg_001_003_invalid_email_format(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid-email-format',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * SRS.Reg.001 - SRC.Reg.001.004
     * Cek registrasi dengan email yang sudah terdaftar
     */
    public function test_case_src_reg_001_004_already_registered_email(): void
    {
        // Register the first user
        \App\Models\User::create([
            'name' => 'Existing User',
            'email' => 'test.sales@scg.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // Attempt to register again with same email
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test.sales@scg.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * SRS.Reg.001 - SRC.Reg.001.005
     * Cek registrasi menggunakan password kombinasi huruf dan angka
     */
    public function test_case_src_reg_001_005_password_letters_and_numbers(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test.sales@scg.com',
            'password' => 'pass1234',
            'password_confirmation' => 'pass1234',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
    }
}
