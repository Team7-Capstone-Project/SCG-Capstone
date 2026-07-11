<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shipment;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected $salesUser;
    protected $customer;
    protected $supplier;
    protected $shipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->salesUser = User::create([
            'name' => 'PIC Sales',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
            'role' => 'pic_sales',
        ]);

        $this->customer = Customer::create([
            'name' => 'Customer A',
            'address' => 'Address A',
            'contact_person' => 'Person A',
            'phone' => '12345678',
            'email' => 'c@example.com',
            'country' => 'Indonesia',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Supplier A',
            'address' => 'Address B',
            'contact_person' => 'Person B',
            'phone' => '87654321',
            'email' => 's@example.com',
            'country' => 'Indonesia',
        ]);

        $this->shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);
    }

    /**
     * SRS.LOG.001 - SRC.LOG.001.001
     * Menampilkan halaman Activity Log
     */
    public function test_case_src_log_001_001_open_activity_log_authenticated(): void
    {
        $response = $this->actingAs($this->salesUser)->get('/activity-log');

        $response->assertStatus(200);
        $response->assertViewHas('activities');
    }

    /**
     * SRS.LOG.001 - SRC.LOG.001.002
     * Menampilkan daftar aktivitas secara kronologis
     */
    public function test_case_src_log_001_002_chronological_order(): void
    {
        // Create 2 logs with different times
        $log1 = ActivityLog::create([
            'user_id' => $this->salesUser->id,
            'shipment_id' => $this->shipment->id,
            'action' => 'created',
            'description' => 'Log lama',
            'created_at' => now()->subHour(),
        ]);

        $log2 = ActivityLog::create([
            'user_id' => $this->salesUser->id,
            'shipment_id' => $this->shipment->id,
            'action' => 'updated',
            'description' => 'Log baru',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->salesUser)->get('/activity-log');

        $response->assertStatus(200);
        $activities = $response->viewData('activities');
        
        // Assert descending order (newest log2 first)
        $this->assertEquals($log2->id, $activities->first()->id);
    }

    /**
     * SRS.LOG.001 - SRC.LOG.001.003
     * Menampilkan detail informasi Activity Log
     */
    public function test_case_src_log_001_003_details(): void
    {
        $log = ActivityLog::create([
            'user_id' => $this->salesUser->id,
            'shipment_id' => $this->shipment->id,
            'action' => 'updated',
            'description' => 'Perubahan detail log',
        ]);

        $response = $this->actingAs($this->salesUser)->get('/activity-log');

        $response->assertStatus(200);
        $response->assertSee('PIC Sales');
        $response->assertSee('updated');
        $response->assertSee('Perubahan detail log');
    }

    /**
     * SRS.LOG.001 - SRC.LOG.001.004
     * Berpindah halaman menggunakan pagination
     */
    public function test_case_src_log_001_004_pagination(): void
    {
        // Create 22 logs (per page limit is 20)
        for ($i = 0; $i < 22; $i++) {
            ActivityLog::create([
                'user_id' => $this->salesUser->id,
                'shipment_id' => $this->shipment->id,
                'action' => 'updated',
                'description' => 'Log ' . $i,
            ]);
        }

        $response = $this->actingAs($this->salesUser)->get('/activity-log?page=2');

        $response->assertStatus(200);
        $activities = $response->viewData('activities');
        $this->assertCount(2, $activities); // Page 2 has the remaining 2 logs
    }

    /**
     * SRS.LOG.001 - SRC.LOG.001.005
     * Membuka Activity Log saat sesi login telah berakhir (guest)
     */
    public function test_case_src_log_001_005_unauthenticated(): void
    {
        $response = $this->get('/activity-log');

        $response->assertRedirect('/login');
    }
}
