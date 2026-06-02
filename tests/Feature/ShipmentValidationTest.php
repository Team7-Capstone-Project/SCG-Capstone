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

        $validPo = 'PO-123/ABC_DEF.G'; // valid alphanumeric, slash, dash, underscore, dot

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
}
