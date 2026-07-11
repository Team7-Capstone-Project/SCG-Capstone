<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shipment;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $salesUser;
    protected $customer;
    protected $supplier;

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
    }

    /**
     * SRS.DSH.001 - SRS.DSH.001.001
     * Membuka halaman Dashboard
     */
    public function test_case_src_dsh_001_001_open_dashboard_authenticated(): void
    {
        $response = $this->actingAs($this->salesUser)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * SRS.DSH.001 - SRS.DSH.001.002 & SRS.DSH.001.005
     * Menampilkan lima metrik utama Dashboard dan datanya sesuai kondisi database
     */
    public function test_case_src_dsh_001_002_dashboard_metrics(): void
    {
        // Create sample shipments
        // 1. Pending
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'status' => 'Pending',
        ]);

        // 2. In Transit
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'status' => 'In Transit',
        ]);

        // 3. Delivered (On-Time: ATA == Receiving Schedule)
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_port' => '2026-06-03',
            'ata_customer' => '2026-06-10',
            'status' => 'Delivered',
        ]);

        // 4. Delivered (Late: ATA > Receiving Schedule)
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_port' => '2026-06-03',
            'ata_customer' => '2026-06-12', // Late
            'status' => 'Delivered',
        ]);

        // 5. Delivered (Early: ATA < Receiving Schedule)
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_port' => '2026-06-03',
            'ata_customer' => '2026-06-08', // Early
            'status' => 'Delivered',
        ]);

        $response = $this->actingAs($this->salesUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalShipments', 5);
        $response->assertViewHas('deliveredShipments', 3);
        $response->assertViewHas('inTransitShipments', 1);
        $response->assertViewHas('lateShipments', 1);
        $response->assertViewHas('onTimeShipments', 1);
        $response->assertViewHas('earlyShipments', 1);
        $response->assertViewHas('otdRate', 66.7); // (1 on-time + 1 early) / 3 delivered * 100
    }

    /**
     * SRS.DSH.001 - SRS.DSH.001.003
     * Menampilkan grafik tren shipment
     */
    public function test_case_src_dsh_001_003_dashboard_monthly_trend_chart(): void
    {
        $response = $this->actingAs($this->salesUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('monthlyData');
        $monthlyData = $response->viewData('monthlyData');
        $this->assertArrayHasKey('months', $monthlyData);
        $this->assertArrayHasKey('total', $monthlyData);
        $this->assertCount(6, $monthlyData['months']); // Shows last 6 months
    }

    /**
     * SRS.DSH.001 - SRS.DSH.001.004
     * Menampilkan daftar 10 shipment terbaru
     */
    public function test_case_src_dsh_001_004_dashboard_latest_10_shipments(): void
    {
        // Create 12 shipments
        for ($i = 0; $i < 12; $i++) {
            Shipment::create([
                'customer_id' => $this->customer->id,
                'supplier_id' => $this->supplier->id,
                'created_by_user_id' => $this->salesUser->id,
                'etd_port' => '2026-06-01',
                'customer_receiving_schedule' => '2026-06-10',
                'customer_po' => 'PO-' . $i,
            ]);
        }

        $response = $this->actingAs($this->salesUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('recentShipments');
        $this->assertCount(10, $response->viewData('recentShipments')); // Limit to 10
    }

    /**
     * SRS.DSH.001 - SRS.DSH.001.006
     * Dashboard ketika data shipment belum tersedia
     */
    public function test_case_src_dsh_001_006_dashboard_empty_state(): void
    {
        $response = $this->actingAs($this->salesUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalShipments', 0);
        $response->assertViewHas('recentShipments');
        $this->assertCount(0, $response->viewData('recentShipments'));
    }

    /**
     * SRS.DSH.001 - SRS.DSH.001.007
     * Membuka Dashboard ketika sesi login telah berakhir (guest)
     */
    public function test_case_src_dsh_001_007_dashboard_unauthenticated(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * SRS.Dsh.003 - SRC.Dsh.003.001
     * Menampilkan seluruh data menggunakan filter All Time
     */
    public function test_case_src_dsh_003_001_dashboard_filter_all_time(): void
    {
        // 1. May shipment
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-05-15',
            'customer_receiving_schedule' => '2026-05-25',
        ]);

        // 2. June shipment
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
        ]);

        // Get dashboard without parameter (All Time)
        $response = $this->actingAs($this->salesUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalShipments', 2);
    }

    /**
     * SRS.Dsh.003 - SRC.Dsh.003.002
     * Menampilkan data berdasarkan bulan yang dipilih
     */
    public function test_case_src_dsh_003_002_dashboard_filter_by_month(): void
    {
        // 1. May shipment
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-05-15',
            'customer_receiving_schedule' => '2026-05-25',
        ]);

        // 2. June shipment
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
        ]);

        // Get dashboard filtered by June 2026
        $response = $this->actingAs($this->salesUser)->get('/dashboard?month=2026-06');

        $response->assertStatus(200);
        $response->assertViewHas('totalShipments', 1);
    }

    /**
     * SRS.Dsh.003 - SRC.Dsh.003.005
     * Memilih bulan yang tidak memiliki data shipment
     */
    public function test_case_src_dsh_003_005_dashboard_filter_empty_month(): void
    {
        // Create a shipment in June 2026
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
        ]);

        // Get dashboard filtered by July 2026 (no data)
        $response = $this->actingAs($this->salesUser)->get('/dashboard?month=2026-07');

        $response->assertStatus(200);
        $response->assertViewHas('totalShipments', 0);
    }
}
