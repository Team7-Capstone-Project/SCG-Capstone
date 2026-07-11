<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntityValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $salesUser;
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

        $this->supplier = Supplier::create([
            'name' => 'Valid Supplier',
            'address' => '123 Supplier St',
            'contact_person' => 'Supplier Contact',
            'phone' => '+123456',
            'email' => 'supplier@example.com',
            'country' => 'United States',
        ]);
    }

    public function test_customer_validation_rejects_invalid_values()
    {
        $this->actingAs($this->salesUser);

        // Test rejecting invalid name (emojis / weird characters)
        $response = $this->post(route('customers.store'), [
            'name' => 'Customer 🌟',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Test rejecting invalid contact person (numbers / symbols)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'contact_person' => 'Person 123',
        ]);
        $response->assertSessionHasErrors(['contact_person']);

        // Test rejecting invalid country (numbers / symbols)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'country' => 'Country #1',
        ]);
        $response->assertSessionHasErrors(['country']);

        // Test rejecting phone with dash '-'
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'phone' => '123-456-789',
        ]);
        $response->assertSessionHasErrors(['phone']);

        // Test rejecting phone exceeding 15 characters
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'phone' => '+628123456789012', // 16 characters
        ]);
        $response->assertSessionHasErrors(['phone']);
    }

    public function test_customer_validation_accepts_valid_values()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('customers.store'), [
            'name' => 'Customer A & B Co., Ltd. (Branch)',
            'address' => 'Customer Address 123',
            'contact_person' => 'Jane O\'Connor-Smith.',
            'phone' => '+62 (21) 12345', // exactly 15 chars, containing plus, spaces, parentheses
            'email' => 'jane@example.com',
            'country' => 'U.S.A. (America)',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('customers', [
            'name' => 'Customer A & B Co., Ltd. (Branch)',
            'phone' => '+62 (21) 12345',
        ]);
    }

    public function test_supplier_validation_rejects_invalid_values()
    {
        $this->actingAs($this->salesUser);

        // Test rejecting invalid name
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Supplier @#$',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Test rejecting invalid contact person
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Valid Supplier Name',
            'contact_person' => 'Person 456',
        ]);
        $response->assertSessionHasErrors(['contact_person']);

        // Test rejecting phone with dash '-'
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Valid Supplier Name',
            'phone' => '+1-234-567',
        ]);
        $response->assertSessionHasErrors(['phone']);

        // Test rejecting phone exceeding 15 characters
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Valid Supplier Name',
            'phone' => '1234567890123456', // 16 characters
        ]);
        $response->assertSessionHasErrors(['phone']);
    }

    public function test_supplier_validation_accepts_valid_values()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('suppliers.store'), [
            'name' => 'Supplier X & Y Inc. (M)',
            'address' => 'Supplier Road 99',
            'contact_person' => 'Mr. John O\'Brien',
            'phone' => '+62812345678',
            'email' => 'supplier@example.com',
            'country' => 'Indonesia',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Supplier X & Y Inc. (M)',
            'phone' => '+62812345678',
        ]);
    }

    public function test_product_validation_rejects_invalid_values()
    {
        $this->actingAs($this->salesUser);

        // Test rejecting SKU with spaces or special chars
        $response = $this->post(route('products.store'), [
            'sku' => 'SKU 123',
            'name' => 'Valid Name',
            'unit_price' => 100,
        ]);
        $response->assertSessionHasErrors(['sku']);

        // Test rejecting Product Name with invalid chars (e.g. @)
        $response = $this->post(route('products.store'), [
            'sku' => 'SKU-123_45',
            'name' => 'Product Name @ Brand',
            'unit_price' => 100,
        ]);
        $response->assertSessionHasErrors(['name']);

        // Test rejecting negative price
        $response = $this->post(route('products.store'), [
            'sku' => 'SKU-123_45',
            'name' => 'Product Name',
            'unit_price' => -10,
        ]);
        $response->assertSessionHasErrors(['unit_price']);
    }

    public function test_product_validation_accepts_valid_values()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('products.store'), [
            'sku' => 'PROD-999_X',
            'name' => 'Product / Box + Item, A & B (New Edition)\'',
            'unit_price' => 12500.50,
            'supplier_id' => $this->supplier->id,
            'description' => 'Great product',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', [
            'sku' => 'PROD-999_X',
            'name' => 'Product / Box + Item, A & B (New Edition)\'',
        ]);
    }

    public function test_localized_delete_confirmation_warnings()
    {
        $this->actingAs($this->salesUser);

        // Create a customer
        $customer = Customer::create([
            'name' => 'Test Customer',
            'contact_person' => 'Contact',
            'phone' => '+12345',
            'country' => 'Indonesia',
        ]);

        // Create a product
        $product = Product::create([
            'sku' => 'TEST-PROD-DEL',
            'name' => 'Delete Test Product',
            'unit_price' => 100,
            'supplier_id' => $this->supplier->id,
        ]);

        // 1. English Locale (Default)
        $response = $this->get(route('customers.show', $customer));
        $response->assertStatus(200);
        $response->assertSee('Are you sure you want to delete this customer? This is a fatal action and will delete all associated shipments.');

        $response = $this->get(route('suppliers.show', $this->supplier));
        $response->assertStatus(200);
        $response->assertSee('Are you sure you want to delete this supplier? This is a fatal action and will delete all associated shipments.');

        $response = $this->get(route('products.show', $product));
        $response->assertStatus(200);
        $response->assertSee('Are you sure you want to delete this product? This is a fatal action and will affect all associated shipments.');

        // 2. Indonesian Locale
        $response = $this->withSession(['locale' => 'id'])->get(route('customers.show', $customer));
        $response->assertStatus(200);
        $response->assertSee('Apakah Anda yakin ingin menghapus pelanggan ini? Ini adalah tindakan fatal dan akan menghapus semua pengiriman terkait.');

        $response = $this->withSession(['locale' => 'id'])->get(route('suppliers.show', $this->supplier));
        $response->assertStatus(200);
        $response->assertSee('Apakah Anda yakin ingin menghapus pemasok ini? Ini adalah tindakan fatal dan akan menghapus semua pengiriman terkait.');

        $response = $this->withSession(['locale' => 'id'])->get(route('products.show', $product));
        $response->assertStatus(200);
        $response->assertSee('Apakah Anda yakin ingin menghapus produk ini? Ini adalah tindakan fatal dan akan mempengaruhi semua pengiriman terkait.');

        // 3. Thai Locale
        $response = $this->withSession(['locale' => 'th'])->get(route('customers.show', $customer));
        $response->assertStatus(200);
        $response->assertSee('คุณแน่ใจหรือไม่ว่าต้องการลบลูกค้ารายนี้? การดำเนินการนี้เป็นเรื่องร้ายแรงและจะลบการจัดส่งทั้งหมดที่เกี่ยวข้อง', false);

        $response = $this->withSession(['locale' => 'th'])->get(route('suppliers.show', $this->supplier));
        $response->assertStatus(200);
        $response->assertSee('คุณแน่ใจหรือไม่ว่าต้องการลบซัพพลายเออร์รายนี้? การดำเนินการนี้เป็นเรื่องร้ายแรงและจะลบการจัดส่งทั้งหมดที่เกี่ยวข้อง', false);

        $response = $this->withSession(['locale' => 'th'])->get(route('products.show', $product));
        $response->assertStatus(200);
        $response->assertSee('คุณแน่ใจหรือไม่ว่าต้องการลบผลิตภัณฑ์นี้? การดำเนินการนี้เป็นเรื่องร้ายแรงและจะส่งผลกระทบต่อการจัดส่งทั้งหมดที่เกี่ยวข้อง', false);
    }

    public function test_entity_validation_min_length_rules()
    {
        $this->actingAs($this->salesUser);

        // Name too short (min:3)
        $response = $this->post(route('customers.store'), [
            'name' => 'Ab',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Phone too short (min:8)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer',
            'phone' => '123',
        ]);
        $response->assertSessionHasErrors(['phone']);

        // Country too short (min:2)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer',
            'country' => 'A',
        ]);
        $response->assertSessionHasErrors(['country']);
    }

    public function test_entity_validation_rejects_html_xss_payloads()
    {
        $this->actingAs($this->salesUser);

        // Address with script tags
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer',
            'address' => '<script>alert("hack")</script>',
        ]);
        $response->assertSessionHasErrors(['address']);

        // Description with script tags
        $response = $this->post(route('products.store'), [
            'sku' => 'PROD-101',
            'name' => 'Valid Product Name',
            'unit_price' => 100,
            'description' => '<p>Some text</p><iframe src="dangerous.html"></iframe>',
        ]);
        $response->assertSessionHasErrors(['description']);
    }

    public function test_product_validation_rejects_price_overflow()
    {
        $this->actingAs($this->salesUser);

        // Price exceeds decimal limit (max:999999999999.99)
        $response = $this->post(route('products.store'), [
            'sku' => 'PROD-102',
            'name' => 'Valid Product Name',
            'unit_price' => 1000000000000, // 1 trillion
        ]);
        $response->assertSessionHasErrors(['unit_price']);
    }

    public function test_user_registration_max_length_rule()
    {
        // User registration name too long (max:50)
        $response = $this->post(route('register'), [
            'name' => str_repeat('A', 51),
            'email' => 'test.sales@scg.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['name']);
    }

    public function test_entity_validation_max_length_rules()
    {
        $this->actingAs($this->salesUser);

        // Product SKU too long (max:30)
        $response = $this->post(route('products.store'), [
            'sku' => str_repeat('A', 31),
            'name' => 'Valid Name',
            'unit_price' => 100,
        ]);
        $response->assertSessionHasErrors(['sku']);

        // Product Name too long (max:100)
        $response = $this->post(route('products.store'), [
            'sku' => 'PROD-103',
            'name' => str_repeat('A', 101),
            'unit_price' => 100,
        ]);
        $response->assertSessionHasErrors(['name']);

        // Customer Country too long (max:60)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer',
            'country' => str_repeat('A', 61),
        ]);
        $response->assertSessionHasErrors(['country']);
    }
}

