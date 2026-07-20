<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $salesUser;
    protected $adminUser;
    protected $customer;
    protected $supplier;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->salesUser = User::create([
            'name' => 'PIC Sales',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
            'role' => 'pic_sales',
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin SCM',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin_scm',
        ]);

        $this->customer = Customer::create([
            'name' => 'Customer A',
            'address' => 'Address A',
            'contact_person' => 'Person A',
            'phone' => '123',
            'email' => 'c@example.com',
            'country' => 'Indonesia',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Supplier A',
            'address' => 'Address B',
            'contact_person' => 'Person B',
            'phone' => '456',
            'email' => 's@example.com',
            'country' => 'Indonesia',
        ]);

        $this->product = Product::create([
            'sku' => 'PROD-01',
            'name' => 'Product A',
            'unit_price' => 10000,
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_cannot_create_shipment_with_invalid_characters_in_document_numbers()
    {
        $this->actingAs($this->salesUser);

        $invalidPo = 'PO-#@$%^'; // invalid special characters

        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'customer_po' => $invalidPo,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        $response->assertSessionHasErrors(['customer_po']);
    }

    public function test_can_create_shipment_with_valid_characters_in_document_numbers()
    {
        $this->actingAs($this->salesUser);

        $validPo = 'PO-123/ABC_DEF'; // valid 14 chars (within 10-15 range)

        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'customer_po' => $validPo,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'products' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_price' => 10000,
                ]
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('shipments', [
            'customer_po' => $validPo,
        ]);
    }

    public function test_cannot_create_shipment_with_illogical_dates()
    {
        $this->actingAs($this->salesUser);

        // Schedule must be after or equal to ETD Port
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'etd_port' => '2026-06-10',
            'customer_receiving_schedule' => '2026-06-01', // earlier than ETD
        ]);

        $response->assertSessionHasErrors(['customer_receiving_schedule']);

        // If ETA Port is provided, Customer Receiving Schedule must be after or equal to ETA Port
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'etd_port' => '2026-06-01',
            'eta_port' => '2026-06-10',
            'customer_receiving_schedule' => '2026-06-05', // earlier than ETA
        ]);

        $response->assertSessionHasErrors(['customer_receiving_schedule']);
    }

    public function test_can_sort_shipments_by_customer_receiving_schedule()
    {
        $this->actingAs($this->salesUser);

        // Create shipments with different deadlines
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-20',
        ]);

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        // Sort deadline_asc
        $response = $this->get(route('shipments.index', ['sort' => 'deadline_asc']));
        $response->assertStatus(200);
        
        $shipments = $response->viewData('shipments');
        $this->assertEquals($shipment2->id, $shipments[0]->id);
        $this->assertEquals($shipment1->id, $shipments[1]->id);

        // Sort deadline_desc
        $response = $this->get(route('shipments.index', ['sort' => 'deadline_desc']));
        $response->assertStatus(200);
        $response->assertViewHas('shipments');
        $shipments = $response->viewData('shipments');
        $this->assertEquals($shipment1->id, $shipments[0]->id);
        $this->assertEquals($shipment2->id, $shipments[1]->id);
    }

    public function test_shipment_update_requires_ata_port_and_ata_customer_when_delivered()
    {
        $this->actingAs($this->salesUser);

        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'status' => 'Pending',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        // Attempt to update status to Delivered without ATA Port or ATA Customer
        $response = $this->put(route('shipments.update', $shipment), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'status' => 'Delivered',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_port' => '',
            'ata_customer' => '',
        ]);

        $response->assertSessionHasErrors(['ata_port', 'ata_customer']);
    }

    public function test_shipment_update_rejects_ata_port_or_ata_customer_when_pending()
    {
        $this->actingAs($this->salesUser);

        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'status' => 'Pending',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        // Pending shipment cannot have arrival dates
        $response = $this->put(route('shipments.update', $shipment), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'status' => 'Pending',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_port' => '2026-06-05',
        ]);

        $response->assertSessionHasErrors(['ata_port']);
    }

    public function test_shipment_update_rejects_ata_customer_when_in_transit()
    {
        $this->actingAs($this->salesUser);

        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'status' => 'Pending',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        // In Transit shipment cannot have ATA Customer
        $response = $this->put(route('shipments.update', $shipment), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'status' => 'In Transit',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_customer' => '2026-06-08',
        ]);

        $response->assertSessionHasErrors(['ata_customer']);
    }

    public function test_shipment_update_status_requires_ata_port_and_ata_customer_when_delivered()
    {
        $this->actingAs($this->adminUser);

        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'status' => 'Pending',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        $response = $this->post(route('shipments.update-status', $shipment), [
            'status' => 'Delivered',
            'ata_port' => '',
            'ata_customer' => '',
        ]);

        $response->assertSessionHasErrors(['ata_port', 'ata_customer']);
    }

    public function test_shipment_validation_date_range_limits()
    {
        $this->actingAs($this->salesUser);

        // etd_port before 2020
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'etd_port' => '2019-12-31',
            'customer_receiving_schedule' => '2020-01-05',
        ]);
        $response->assertSessionHasErrors(['etd_port']);

        // eta_port more than 365 days after etd_port
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'etd_port' => '2026-06-01',
            'eta_port' => '2027-06-03', // 367 days
            'customer_receiving_schedule' => '2026-06-10',
        ]);
        $response->assertSessionHasErrors(['eta_port']);

        // customer_receiving_schedule more than 365 days after etd_port
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2027-06-05', // 369 days
        ]);
        $response->assertSessionHasErrors(['customer_receiving_schedule']);
    }

    public function test_shipment_validation_costs_and_notes_overflow()
    {
        $this->actingAs($this->salesUser);

        // shipping_cost too large (over 999,999,999,999.99)
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'shipping_cost' => 1000000000000,
        ]);
        $response->assertSessionHasErrors(['shipping_cost']);

        // notes contains html tags
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'notes' => '<div>malicious payload</div>',
        ]);
        $response->assertSessionHasErrors(['notes']);
    }

    public function test_shipment_update_status_enforces_document_uniqueness()
    {
        $this->actingAs($this->adminUser);

        // Create first shipment with a delivery note number
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'delivery_note_number' => 'DELNOTE-777',
        ]);

        // Create second shipment
        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        // Admin updates second shipment status and inputs the duplicate delivery note
        $response = $this->post(route('shipments.update-status', $shipment2), [
            'status' => 'In Transit',
            'delivery_note_number' => 'DELNOTE-777', // duplicate!
        ]);

        $response->assertSessionHasErrors(['delivery_note_number']);
    }

    public function test_shipment_validation_document_number_length_limits()
    {
        $this->actingAs($this->salesUser);

        // customer_po less than min:10 characters
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'customer_po' => 'PO-123',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);
        $response->assertSessionHasErrors(['customer_po']);

        // customer_po exceeds max:15 characters
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'customer_po' => str_repeat('A', 16),
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);
        $response->assertSessionHasErrors(['customer_po']);

        // booking_number exceeds max:15 characters
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'booking_number' => str_repeat('A', 16),
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);
        $response->assertSessionHasErrors(['booking_number']);
    }

    /**
     * SRS.SHP.001 - SRC.SHP.001.001
     * Cek penambahan data Shipment dengan data valid
     */
    public function test_case_src_shp_001_001_add_shipment_valid()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'customer_po' => 'PO-NEW-123',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('shipments', [
            'customer_po' => 'PO-NEW-123',
        ]);
    }

    /**
     * SRS.SHP.001 - SRC.SHP.001.002
     * Cek penambahan data Shipment dengan field wajib kosong
     */
    public function test_case_src_shp_001_002_add_shipment_missing_required()
    {
        $this->actingAs($this->salesUser);

        // etd_port and customer_receiving_schedule are required but missing
        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
        ]);

        $response->assertSessionHasErrors(['etd_port', 'customer_receiving_schedule']);
    }

    /**
     * SRS.SHP.001 - SRC.SHP.001.003
     * Cek penambahan data Shipment dengan nomor Customer PO yang sudah terdaftar
     */
    public function test_case_src_shp_001_003_add_shipment_duplicate_customer_po()
    {
        // Precreate a shipment with a customer PO
        Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-DUPLICATE-999',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->post(route('shipments.store'), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'customer_po' => 'PO-DUPLICATE-999', // duplicate!
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        $response->assertSessionHasErrors(['customer_po']);
    }

    /**
     * SRS.SHP.001 - SRC.SHP.001.004
     * Cek perubahan data Shipment
     */
    public function test_case_src_shp_001_004_update_shipment()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-ORIGINAL',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->put(route('shipments.update', $shipment), [
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'type' => 'Import',
            'customer_po' => 'PO-UPDATED',
            'status' => 'Pending',
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'customer_po' => 'PO-UPDATED',
        ]);
    }

    /**
     * SRS.SHP.001 - SRC.SHP.001.005
     * Cek penghapusan data Shipment
     */
    public function test_case_src_shp_001_005_delete_shipment()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-TO-DELETE',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->delete(route('shipments.destroy', $shipment));

        $response->assertRedirect(route('shipments.index'));
        $this->assertSoftDeleted('shipments', [
            'id' => $shipment->id,
        ]);
    }

    /**
     * SRS.SHP.001 - SRC.SHP.001.007
     * Cek tampilan daftar data Shipment
     */
    public function test_case_src_shp_001_007_view_shipment_list()
    {
        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('shipments');
    }

    /**
     * SRS.SHP.001 - SRC.SHP.001.008
     * Cek tampilan detail data Shipment
     */
    public function test_case_src_shp_001_008_view_shipment_detail()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-DETAIL-VIEW',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.show', $shipment));

        $response->assertStatus(200);
        $response->assertSee('PO-DETAIL-VIEW');
    }

    /**
     * SRS.MON.001 - SRS.MON.001.001
     * Memperbarui data monitoring shipment dengan data yang valid
     */
    public function test_case_srs_mon_001_001_update_monitoring_valid()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->post(route('shipments.update-status', $shipment), [
            'status' => 'In Transit',
            'ata_port' => '2026-06-03',
            'delivery_note_number' => 'DN-VALID-123',
            'supplier_invoice' => 'INV-VALID-123',
            'shipping_cost' => 1000,
            'customs_cost' => 500,
            'other_costs' => 200,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'In Transit',
            'ata_port' => '2026-06-03 00:00:00',
            'delivery_note_number' => 'DN-VALID-123',
            'supplier_invoice' => 'INV-VALID-123',
            'shipping_cost' => 1000.00,
            'customs_cost' => 500.00,
            'other_costs' => 200.00,
        ]);
    }

    /**
     * SRS.MON.001 - SRS.MON.001.002
     * Memperbarui monitoring shipment dengan format tanggal yang tidak valid
     */
    public function test_case_srs_mon_001_002_update_monitoring_invalid_date()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->post(route('shipments.update-status', $shipment), [
            'status' => 'In Transit',
            'ata_port' => 'invalid-date-format',
        ]);

        $response->assertSessionHasErrors(['ata_port']);
    }

    /**
     * SRS.MON.001 - SRS.MON.001.003
     * Memperbarui monitoring shipment dengan field wajib yang kosong
     */
    public function test_case_srs_mon_001_003_update_monitoring_empty_status()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->post(route('shipments.update-status', $shipment), [
            'status' => '', // status is required
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    /**
     * SRS.MON.001 - SRS.MON.001.004
     * Memperbarui monitoring shipment dengan Shipping Cost bernilai negatif
     */
    public function test_case_srs_mon_001_004_update_monitoring_negative_shipping_cost()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->post(route('shipments.update-status', $shipment), [
            'status' => 'In Transit',
            'shipping_cost' => -100,
        ]);

        $response->assertSessionHasErrors(['shipping_cost']);
    }

    /**
     * SRS.MON.001 - SRS.MON.001.005
     * Memperbarui monitoring shipment dengan Customs Cost bernilai negatif
     */
    public function test_case_srs_mon_001_005_update_monitoring_negative_customs_cost()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->post(route('shipments.update-status', $shipment), [
            'status' => 'In Transit',
            'customs_cost' => -50,
        ]);

        $response->assertSessionHasErrors(['customs_cost']);
    }

    /**
     * SRS.MON.001 - SRS.MON.001.006
     * Memperbarui monitoring shipment dengan ATA Customer yang valid
     */
    public function test_case_srs_mon_001_006_update_monitoring_ata_customer()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->post(route('shipments.update-status', $shipment), [
            'status' => 'In Transit',
            'ata_port' => '2026-06-03',
            'ata_customer' => '2026-06-07', // valid ATA Customer
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'Delivered', // automatically changed to Delivered
            'ata_customer' => '2026-06-07 00:00:00',
        ]);
    }

    /**
     * SRS.SRC.001 - SRC.SRC.001.001
     * Melakukan pencarian berdasarkan Customer PO yang valid
     */
    public function test_case_src_src_001_001_search_by_customer_po()
    {
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-FIND-ME-111',
        ]);

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-OTHER-999',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['search' => 'PO-FIND-ME-111']));

        $response->assertStatus(200);
        $response->assertSee('PO-FIND-ME-111');
        $response->assertDontSee('PO-OTHER-999');
    }

    /**
     * SRS.SRC.001 - SRC.SRC.001.002
     * Melakukan pencarian berdasarkan nama Customer
     */
    public function test_case_src_src_001_002_search_by_customer_name()
    {
        $otherCustomer = Customer::create([
            'name' => 'Unique Customer Name X',
            'address' => 'Addr X',
            'contact_person' => 'Contact X',
            'phone' => '12345',
            'email' => 'x@example.com',
            'country' => 'Indonesia',
        ]);

        $shipment1 = Shipment::create([
            'customer_id' => $otherCustomer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-CUST-MATCH',
        ]);

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id, // Customer A
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-CUST-NOMATCH',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['search' => 'Unique Customer Name X']));

        $response->assertStatus(200);
        $response->assertSee('PO-CUST-MATCH');
        $response->assertDontSee('PO-CUST-NOMATCH');
    }

    /**
     * SRS.SRC.001 - SRC.SRC.001.003
     * Melakukan pencarian berdasarkan nama Supplier
     */
    public function test_case_src_src_001_003_search_by_supplier_name()
    {
        $otherSupplier = Supplier::create([
            'name' => 'Unique Supplier Name Y',
            'address' => 'Addr Y',
            'contact_person' => 'Contact Y',
            'phone' => '54321',
            'email' => 'y@example.com',
            'country' => 'Indonesia',
        ]);

        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $otherSupplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-SUPP-MATCH',
        ]);

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id, // Supplier A
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-SUPP-NOMATCH',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['search' => 'Unique Supplier Name Y']));

        $response->assertStatus(200);
        $response->assertSee('PO-SUPP-MATCH');
        $response->assertDontSee('PO-SUPP-NOMATCH');
    }

    /**
     * SRS.SRC.001 - SRC.SRC.001.004
     * Melakukan pencarian berdasarkan nomor Booking
     */
    public function test_case_src_src_001_004_search_by_booking_number()
    {
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'booking_number' => 'BOOKING-FIND-111',
            'customer_po' => 'PO-BOOKING-1',
        ]);

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'booking_number' => 'BOOKING-OTHER-222',
            'customer_po' => 'PO-BOOKING-2',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['search' => 'BOOKING-FIND-111']));

        $response->assertStatus(200);
        $response->assertSee('PO-BOOKING-1');
        $response->assertDontSee('PO-BOOKING-2');
    }

    /**
     * SRS.SRC.001 - SRC.SRC.001.005
     * Melakukan pencarian berdasarkan nomor SCG PO atau SCG SO
     */
    public function test_case_src_src_001_005_search_by_scg_po_or_so()
    {
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'scg_po' => 'SCGPO-FIND-111',
            'customer_po' => 'PO-SCGPO-1',
        ]);

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'scg_so' => 'SCGSO-FIND-222',
            'customer_po' => 'PO-SCGSO-2',
        ]);

        $this->actingAs($this->salesUser);

        // Search by SCG PO
        $response1 = $this->get(route('shipments.index', ['search' => 'SCGPO-FIND-111']));
        $response1->assertStatus(200);
        $response1->assertSee('PO-SCGPO-1');
        $response1->assertDontSee('PO-SCGSO-2');

        // Search by SCG SO
        $response2 = $this->get(route('shipments.index', ['search' => 'SCGSO-FIND-222']));
        $response2->assertStatus(200);
        $response2->assertSee('PO-SCGSO-2');
        $response2->assertDontSee('PO-SCGPO-1');
    }

    /**
     * SRS.SRC.001 - SRC.SRC.001.006
     * Melakukan pencarian dengan kata kunci yang tidak terdaftar
     */
    public function test_case_src_src_001_006_search_unregistered_keyword()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-EXISTING',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['search' => 'KEYWORD-NOT-EXISTS']));

        $response->assertStatus(200);
        $response->assertDontSee('PO-EXISTING');
    }

    /**
     * SRS.SRC.001 - SRC.SRC.001.007
     * Melakukan pencarian dengan kolom pencarian kosong
     */
    public function test_case_src_src_001_007_search_empty()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-ANYTHING',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['search' => '']));

        $response->assertStatus(200);
        $response->assertSee('PO-ANYTHING');
    }

    /**
     * SRS.SRC.001 - SRC.SRC.001.008
     * Melakukan pencarian ketika sesi login telah berakhir (guest)
     */
    public function test_case_src_src_001_008_search_unauthenticated()
    {
        $response = $this->get(route('shipments.index', ['search' => 'test']));

        $response->assertRedirect('/login');
    }

    /**
     * SRS.FLT.001 - SRC.FLT.001.001
     * Menyaring data berdasarkan Status
     */
    public function test_case_src_flt_001_001_filter_by_status()
    {
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-STATUS-DELIVERED',
            'status' => 'Delivered',
        ]);

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-STATUS-PENDING',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['status' => 'Delivered']));

        $response->assertStatus(200);
        $response->assertSee('PO-STATUS-DELIVERED');
        $response->assertDontSee('PO-STATUS-PENDING');
    }

    /**
     * SRS.FLT.001 - SRC.FLT.001.002
     * Menyaring data berdasarkan Type
     */
    public function test_case_src_flt_001_002_filter_by_type()
    {
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-TYPE-EXPORT',
            'type' => 'Export',
        ]);

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-TYPE-IMPORT',
            'type' => 'Import',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['type' => 'Export']));

        $response->assertStatus(200);
        $response->assertSee('PO-TYPE-EXPORT');
        $response->assertDontSee('PO-TYPE-IMPORT');
    }

    /**
     * SRS.FLT.001 - SRC.FLT.001.003
     * Menyaring data berdasarkan Performance (Late)
     */
    public function test_case_src_flt_001_003_filter_by_performance_late()
    {
        // 1. Late
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_port' => '2026-06-03',
            'ata_customer' => '2026-06-12', // Late
            'customer_po' => 'PO-PERF-LATE',
            'status' => 'Delivered',
        ]);

        // 2. On-Time
        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_port' => '2026-06-03',
            'ata_customer' => '2026-06-10', // On-time
            'customer_po' => 'PO-PERF-ONTIME',
            'status' => 'Delivered',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['late' => 1]));

        $response->assertStatus(200);
        $response->assertSee('PO-PERF-LATE');
        $response->assertDontSee('PO-PERF-ONTIME');
    }

    /**
     * SRS.FLT.001 - SRC.FLT.001.004
     * Mengurutkan data berdasarkan Newest
     */
    public function test_case_src_flt_001_004_sort_by_newest()
    {
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-SORT-NEWEST-OLD',
        ]);
        $shipment1->created_at = now()->subDay();
        $shipment1->save();

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-SORT-NEWEST-NEW',
        ]);
        $shipment2->created_at = now();
        $shipment2->save();

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['sort' => 'newest']));

        $response->assertStatus(200);
        $shipments = $response->viewData('shipments');
        $this->assertEquals($shipment2->id, $shipments->first()->id);
    }

    /**
     * SRS.FLT.001 - SRC.FLT.001.005
     * Mengurutkan data berdasarkan Oldest
     */
    public function test_case_src_flt_001_005_sort_by_oldest()
    {
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-SORT-OLDEST-OLD',
        ]);
        $shipment1->created_at = now()->subDay();
        $shipment1->save();

        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-SORT-OLDEST-NEW',
        ]);
        $shipment2->created_at = now();
        $shipment2->save();

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['sort' => 'oldest']));

        $response->assertStatus(200);
        $shipments = $response->viewData('shipments');
        $this->assertEquals($shipment1->id, $shipments->first()->id);
    }

    /**
     * SRS.FLT.001 - SRC.FLT.001.006
     * Menggunakan kombinasi beberapa filter
     */
    public function test_case_src_flt_001_006_combine_filters()
    {
        // 1. Matches all criteria (Delivered, Import, On-Time)
        $shipment1 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'ata_port' => '2026-06-03',
            'ata_customer' => '2026-06-10', // On-time
            'customer_po' => 'PO-COMB-MATCH',
            'status' => 'Delivered',
            'type' => 'Import',
        ]);

        // 2. Pending, Import
        $shipment2 = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-COMB-NOMATCH',
            'status' => 'Pending',
            'type' => 'Import',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', [
            'status' => 'Delivered',
            'type' => 'Import',
            'on_time' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee('PO-COMB-MATCH');
        $response->assertDontSee('PO-COMB-NOMATCH');
    }

    /**
     * SRS.FLT.001 - SRC.FLT.001.008
     * Menggunakan filter ketika tidak ada data yang sesuai
     */
    public function test_case_src_flt_001_008_filter_empty_results()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-01',
            'customer_receiving_schedule' => '2026-06-10',
            'customer_po' => 'PO-FILTER-EMPTY',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.index', ['status' => 'Delivered']));

        $response->assertStatus(200);
        $response->assertDontSee('PO-FILTER-EMPTY');
    }

    /**
     * SRS.FLT.001 - SRC.FLT.001.009
     * Menggunakan filter ketika sesi login telah berakhir (guest)
     */
    public function test_case_src_flt_001_009_filter_unauthenticated()
    {
        $response = $this->get(route('shipments.index', ['status' => 'Delivered']));

        $response->assertRedirect('/login');
    }

    /**
     * SRS.EXP.001 - SRC.EXP.001.001
     * Cek ekspor data Shipment ke file Excel
     */
    public function test_case_src_exp_001_001_export_all_shipments()
    {
        \Maatwebsite\Excel\Facades\Excel::fake();
        \Maatwebsite\Excel\Facades\Excel::matchByRegex();

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.export'));

        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('/^shipments-\d{4}-\d{2}-\d{2}-\d{6}\.xlsx$/', function (\App\Exports\ShipmentsExport $export) {
            return true;
        });
    }

    /**
     * SRS.EXP.001 - SRC.EXP.001.002
     * Cek ekspor data Shipment setelah menggunakan filter
     */
    public function test_case_src_exp_001_002_export_filtered_shipments()
    {
        \Maatwebsite\Excel\Facades\Excel::fake();
        \Maatwebsite\Excel\Facades\Excel::matchByRegex();

        $this->actingAs($this->salesUser);

        $response = $this->get(route('shipments.export', ['status' => 'Delivered']));

        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('/^shipments-\d{4}-\d{2}-\d{2}-\d{6}\.xlsx$/', function (\App\Exports\ShipmentsExport $export) {
            return $export->query() !== null;
        });
    }

    /**
     * SRS.EXP.001 - SRC.EXP.001.003
     * Cek ekspor data ketika tidak terdapat data yang ditampilkan
     */
    public function test_case_src_exp_001_003_export_empty_shipments()
    {
        \Maatwebsite\Excel\Facades\Excel::fake();
        \Maatwebsite\Excel\Facades\Excel::matchByRegex();

        $this->actingAs($this->salesUser);

        // Apply a filter that returns no results
        $response = $this->get(route('shipments.export', ['status' => 'NonExistentStatus']));

        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('/^shipments-\d{4}-\d{2}-\d{2}-\d{6}\.xlsx$/', function (\App\Exports\ShipmentsExport $export) {
            return $export->query()->count() === 0;
        });
    }
}
