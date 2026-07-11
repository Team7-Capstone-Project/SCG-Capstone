<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'PIC Sales',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
            'role' => 'pic_sales',
        ]);
    }

    /**
     * SRS.LNG.001 - SRC.LNG.001.002
     * Mengubah bahasa ke Bahasa Indonesia
     */
    public function test_case_src_lng_001_002_switch_to_indonesian(): void
    {
        $response = $this->actingAs($this->user)->get(route('lang.switch', ['locale' => 'id']));

        $response->assertSessionHas('locale', 'id');
    }

    /**
     * SRS.LNG.001 - SRC.LNG.001.003
     * Mengubah bahasa ke English
     */
    public function test_case_src_lng_001_003_switch_to_english(): void
    {
        $response = $this->actingAs($this->user)->get(route('lang.switch', ['locale' => 'en']));

        $response->assertSessionHas('locale', 'en');
    }

    /**
     * SRS.LNG.001 - SRC.LNG.001.004
     * Mengubah bahasa ke Thailand
     */
    public function test_case_src_lng_001_004_switch_to_thai(): void
    {
        $response = $this->actingAs($this->user)->get(route('lang.switch', ['locale' => 'th']));

        $response->assertSessionHas('locale', 'th');
    }

    /**
     * SRS.LNG.001 - SRC.LNG.001.005
     * Bahasa tetap digunakan setelah pengguna berpindah halaman
     */
    public function test_case_src_lng_001_005_persistence(): void
    {
        $this->actingAs($this->user);

        // Switch to Indonesian
        $this->get(route('lang.switch', ['locale' => 'id']));

        // Access dashboard
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $this->assertEquals('id', app()->getLocale());
    }

    /**
     * SRS.LNG.001 - SRC.LNG.001.006
     * Memilih bahasa yang tidak tersedia
     */
    public function test_case_src_lng_001_006_unsupported_language(): void
    {
        $this->actingAs($this->user);

        // Try to switch to French ('fr')
        $response = $this->get(route('lang.switch', ['locale' => 'fr']));

        // Assert it does NOT set 'locale' to 'fr' (since 'fr' is not in ['en', 'id', 'th'])
        $response->assertSessionMissing('locale');
    }
}
