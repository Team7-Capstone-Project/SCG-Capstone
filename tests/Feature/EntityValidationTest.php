<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Shipment;
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

        $this->customer = Customer::create([
            'name' => 'Valid Customer',
            'address' => '123 Customer St',
            'contact_person' => 'Customer Contact',
            'phone' => '+6212345',
            'email' => 'customer@example.com',
            'country' => 'Indonesia',
        ]);
    }

    public function test_customer_validation_rejects_invalid_values()
    {
        $this->actingAs($this->salesUser);

        // Test rejecting invalid name (emojis / weird characters)
        $response = $this->post(route('customers.store'), [
            'name' => 'Customer 🌟',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Test rejecting invalid contact person (numbers / symbols)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'address' => 'Valid Address',
            'contact_person' => 'Person 123',
        ]);
        $response->assertSessionHasErrors(['contact_person']);

        // Test rejecting invalid country (numbers / symbols)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'country' => 'Country #1',
        ]);
        $response->assertSessionHasErrors(['country']);

        // Test rejecting phone with dash '-'
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'phone' => '123-456-789',
        ]);
        $response->assertSessionHasErrors(['phone']);

        // Test rejecting phone exceeding 15 characters
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'phone' => '+628123456789012', // 16 characters
        ]);
        $response->assertSessionHasErrors(['phone']);
    }

    public function test_customer_validation_accepts_valid_values()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('customers.store'), [
            'name' => 'Customer Company Name',
            'address' => 'Customer Address One Two Three',
            'contact_person' => 'Jane Doe',
            'phone' => '+62 (21) 12345', // exactly 15 chars, containing plus, spaces, parentheses
            'email' => 'jane@example.com',
            'country' => 'U.S.A. (America)',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('customers', [
            'name' => 'Customer Company Name',
            'phone' => '+62 (21) 12345',
        ]);
    }

    public function test_supplier_validation_rejects_invalid_values()
    {
        $this->actingAs($this->salesUser);

        // Test rejecting invalid name
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Supplier @#$',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Test rejecting invalid contact person
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Valid Supplier Name',
            'address' => 'Valid Address',
            'contact_person' => 'Person 456',
        ]);
        $response->assertSessionHasErrors(['contact_person']);

        // Test rejecting phone with dash '-'
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Valid Supplier Name',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'phone' => '+1-234-567',
        ]);
        $response->assertSessionHasErrors(['phone']);

        // Test rejecting phone exceeding 15 characters
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Valid Supplier Name',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'phone' => '1234567890123456', // 16 characters
        ]);
        $response->assertSessionHasErrors(['phone']);
    }

    public function test_supplier_validation_accepts_valid_values()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('suppliers.store'), [
            'name' => 'Supplier Company Name',
            'address' => 'Supplier Road Ninety Nine',
            'contact_person' => 'John Doe',
            'phone' => '+62812345678',
            'email' => 'supplier@example.com',
            'country' => 'Indonesia',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Supplier Company Name',
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
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Phone too short (min:8)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'phone' => '123',
        ]);
        $response->assertSessionHasErrors(['phone']);

        // Country too short (min:2)
        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
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
            'contact_person' => 'Valid Contact',
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
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'country' => str_repeat('A', 61),
        ]);
        $response->assertSessionHasErrors(['country']);
    }

    /**
     * SRS.CST.001 - SRC.CST.001.001
     * Cek penambahan data Customer dengan data valid
     */
    public function test_case_src_cst_001_001_add_customer_valid()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'address' => 'Valid Address',
            'contact_person' => 'Jane Doe',
            'phone' => '+62812345678',
            'email' => 'customer@example.com',
            'country' => 'Indonesia',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('customers', [
            'name' => 'Valid Customer Name',
        ]);
    }

    /**
     * SRS.CST.001 - SRC.CST.001.002
     * Cek penambahan data Customer dengan field wajib kosong
     */
    public function test_case_src_cst_001_002_add_customer_missing_required()
    {
        $this->actingAs($this->salesUser);

        // Name is required but empty
        $response = $this->post(route('customers.store'), [
            'name' => '',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * SRS.CST.001 - SRC.CST.001.003
     * Cek penambahan data Customer dengan format email tidak valid
     */
    public function test_case_src_cst_001_003_add_customer_invalid_email()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('customers.store'), [
            'name' => 'Valid Customer Name',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'email' => 'invalid-email-format',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * SRS.CST.001 - SRC.CST.001.004
     * Cek perubahan data Customer
     */
    public function test_case_src_cst_001_004_update_customer()
    {
        $customer = Customer::create([
            'name' => 'Original Customer Name',
            'address' => 'Original Address',
            'contact_person' => 'Original Contact',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->put(route('customers.update', $customer), [
            'name' => 'Updated Customer Name',
            'address' => 'Original Address',
            'contact_person' => 'Original Contact',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer Name',
        ]);
    }

    /**
     * SRS.CST.001 - SRC.CST.001.005
     * Cek penghapusan data Customer
     */
    public function test_case_src_cst_001_005_delete_customer()
    {
        $customer = Customer::create([
            'name' => 'Customer to Delete',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->delete(route('customers.destroy', $customer));

        $response->assertRedirect(route('customers.index'));
        $this->assertSoftDeleted('customers', [
            'id' => $customer->id,
        ]);
    }

    /**
     * SRS.CST.001 - SRC.CST.001.007
     * Cek tampilan daftar data Customer
     */
    public function test_case_src_cst_001_007_view_customer_list()
    {
        $this->actingAs($this->salesUser);

        $response = $this->get(route('customers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('customers');
    }

    /**
     * SRS.CST.001 - SRC.CST.001.008
     * Cek tampilan detail data Customer
     */
    public function test_case_src_cst_001_008_view_customer_detail()
    {
        $customer = Customer::create([
            'name' => 'Customer Details Test',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('customers.show', $customer));

        $response->assertStatus(200);
        $response->assertSee('Customer Details Test');
    }

    /**
     * SRS.SUP.001 - SRC.SUP.001.001
     * Cek penambahan data Supplier dengan data valid
     */
    public function test_case_src_sup_001_001_add_supplier_valid()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('suppliers.store'), [
            'name' => 'Valid Supplier Name',
            'address' => 'Valid Address',
            'contact_person' => 'Supplier Contact',
            'phone' => '+62812345678',
            'email' => 'supplier@example.com',
            'country' => 'Indonesia',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Valid Supplier Name',
        ]);
    }

    /**
     * SRS.SUP.001 - SRC.SUP.001.002
     * Cek penambahan data Supplier dengan field wajib kosong
     */
    public function test_case_src_sup_001_002_add_supplier_missing_required()
    {
        $this->actingAs($this->salesUser);

        // Name is required but empty
        $response = $this->post(route('suppliers.store'), [
            'name' => '',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * SRS.SUP.001 - SRC.SUP.001.003
     * Cek penambahan data Supplier dengan format email tidak valid
     */
    public function test_case_src_sup_001_003_add_supplier_invalid_email()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('suppliers.store'), [
            'name' => 'Valid Supplier Name',
            'address' => 'Valid Address',
            'contact_person' => 'Valid Contact',
            'email' => 'invalid-email-format',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * SRS.SUP.001 - SRC.SUP.001.004
     * Cek perubahan data Supplier
     */
    public function test_case_src_sup_001_004_update_supplier()
    {
        $supplier = Supplier::create([
            'name' => 'Original Supplier Name',
            'address' => 'Original Address',
            'contact_person' => 'Original Contact',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->put(route('suppliers.update', $supplier), [
            'name' => 'Updated Supplier Name',
            'address' => 'Original Address',
            'contact_person' => 'Original Contact',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier Name',
        ]);
    }

    /**
     * SRS.SUP.001 - SRC.SUP.001.005
     * Cek penghapusan data Supplier
     */
    public function test_case_src_sup_001_005_delete_supplier()
    {
        $supplier = Supplier::create([
            'name' => 'Supplier to Delete',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));
        $this->assertSoftDeleted('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    /**
     * SRS.SUP.001 - SRC.SUP.001.007
     * Cek tampilan daftar data Supplier
     */
    public function test_case_src_sup_001_007_view_supplier_list()
    {
        $this->actingAs($this->salesUser);

        $response = $this->get(route('suppliers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('suppliers');
    }

    /**
     * SRS.SUP.001 - SRC.SUP.001.008
     * Cek tampilan detail data Supplier
     */
    public function test_case_src_sup_001_008_view_supplier_detail()
    {
        $supplier = Supplier::create([
            'name' => 'Supplier Details Test',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('suppliers.show', $supplier));

        $response->assertStatus(200);
        $response->assertSee('Supplier Details Test');
    }

    /**
     * SRS.PRD.001 - SRC.PRD.001.001
     * Cek penambahan data Product dengan data valid
     */
    public function test_case_src_prd_001_001_add_product_valid()
    {
        $this->actingAs($this->salesUser);

        $response = $this->post(route('products.store'), [
            'sku' => 'PROD-NEW-99',
            'name' => 'Valid Product Name',
            'description' => 'Valid Description',
            'unit_price' => 150000,
            'supplier_id' => $this->supplier->id,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', [
            'sku' => 'PROD-NEW-99',
            'name' => 'Valid Product Name',
        ]);
    }

    /**
     * SRS.PRD.001 - SRC.PRD.001.002
     * Cek penambahan data Product dengan field wajib kosong
     */
    public function test_case_src_prd_001_002_add_product_missing_required()
    {
        $this->actingAs($this->salesUser);

        // sku, name, unit_price are required
        $response = $this->post(route('products.store'), [
            'sku' => '',
            'name' => '',
            'unit_price' => '',
        ]);

        $response->assertSessionHasErrors(['sku', 'name', 'unit_price']);
    }

    /**
     * SRS.PRD.001 - SRC.PRD.001.003
     * Cek penambahan data Product dengan kode Product yang sudah terdaftar
     */
    public function test_case_src_prd_001_003_add_product_duplicate_sku()
    {
        // Precreate product with SKU
        Product::create([
            'sku' => 'PROD-DUPE-11',
            'name' => 'Original Product',
            'unit_price' => 100,
            'supplier_id' => $this->supplier->id,
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->post(route('products.store'), [
            'sku' => 'PROD-DUPE-11', // duplicate!
            'name' => 'Duplicate SKU Product',
            'unit_price' => 200,
            'supplier_id' => $this->supplier->id,
        ]);

        $response->assertSessionHasErrors(['sku']);
    }

    /**
     * SRS.PRD.001 - SRC.PRD.001.004
     * Cek perubahan data Product
     */
    public function test_case_src_prd_001_004_update_product()
    {
        $product = Product::create([
            'sku' => 'PROD-ORIG',
            'name' => 'Original Name',
            'unit_price' => 100,
            'supplier_id' => $this->supplier->id,
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->put(route('products.update', $product), [
            'sku' => 'PROD-ORIG', // same SKU is allowed on update
            'name' => 'Updated Name',
            'unit_price' => 250,
            'supplier_id' => $this->supplier->id,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'unit_price' => 250.00,
        ]);
    }

    /**
     * SRS.PRD.001 - SRC.PRD.001.005
     * Cek penghapusan data Product
     */
    public function test_case_src_prd_001_005_delete_product()
    {
        $product = Product::create([
            'sku' => 'PROD-DEL',
            'name' => 'Product to Delete',
            'unit_price' => 100,
            'supplier_id' => $this->supplier->id,
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    /**
     * SRS.PRD.001 - SRC.PRD.001.007
     * Cek tampilan daftar data Product
     */
    public function test_case_src_prd_001_007_view_product_list()
    {
        $this->actingAs($this->salesUser);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    /**
     * SRS.PRD.001 - SRC.PRD.001.008
     * Cek tampilan detail data Product
     */
    public function test_case_src_prd_001_008_view_product_detail()
    {
        $product = Product::create([
            'sku' => 'PROD-VIEW',
            'name' => 'Product Details Test',
            'unit_price' => 100,
            'supplier_id' => $this->supplier->id,
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('products.show', $product));

        $response->assertStatus(200);
        $response->assertSee('Product Details Test');
    }

    /**
     * SRS.HSC.001 - SRC.HSC.001.001
     * Menampilkan riwayat Shipment berdasarkan Customer dengan rentang waktu yang memiliki data
     */
    public function test_case_src_hsc_001_001_customer_history_with_data()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
            'customer_po' => 'PO-CUST-HIST-1',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('customers.show', [
            'customer' => $this->customer->id,
            'quick_filter' => 'custom',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-10',
        ]));

        $response->assertStatus(200);
        $response->assertSee('PO-CUST-HIST-1');
    }

    /**
     * SRS.HSC.001 - SRC.HSC.001.002
     * Menampilkan riwayat Shipment berdasarkan Customer dengan rentang waktu yang tidak memiliki data
     */
    public function test_case_src_hsc_001_002_customer_history_empty()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
            'customer_po' => 'PO-CUST-HIST-2',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('customers.show', [
            'customer' => $this->customer->id,
            'quick_filter' => 'custom',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('PO-CUST-HIST-2');
    }

    /**
     * SRS.HSS.001 - SRC.HSS.001.001
     * Menampilkan riwayat Shipment berdasarkan Supplier dengan rentang waktu yang memiliki data
     */
    public function test_case_src_hss_001_001_supplier_history_with_data()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
            'customer_po' => 'PO-SUPP-HIST-1',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('suppliers.show', [
            'supplier' => $this->supplier->id,
            'quick_filter' => 'custom',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-10',
        ]));

        $response->assertStatus(200);
        $response->assertSee('PO-SUPP-HIST-1');
    }

    /**
     * SRS.HSS.001 - SRC.HSS.001.002
     * Menampilkan riwayat Shipment berdasarkan Supplier dengan rentang waktu yang tidak memiliki data
     */
    public function test_case_src_hss_001_002_supplier_history_empty()
    {
        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
            'customer_po' => 'PO-SUPP-HIST-2',
        ]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('suppliers.show', [
            'supplier' => $this->supplier->id,
            'quick_filter' => 'custom',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('PO-SUPP-HIST-2');
    }

    /**
     * SRS.HSP.001 - SRC.HSP.001.001
     * Menampilkan riwayat Shipment berdasarkan Product dengan rentang waktu yang memiliki data
     */
    public function test_case_src_hsp_001_001_product_history_with_data()
    {
        $product = Product::create([
            'sku' => 'PROD-HIST-1',
            'name' => 'Product History 1',
            'unit_price' => 100,
            'supplier_id' => $this->supplier->id,
        ]);

        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
            'customer_po' => 'PO-PROD-HIST-1',
            'delivery_note_number' => 'DN-PROD-HIST-1',
        ]);

        // Attach product to shipment via pivot table
        $shipment->products()->attach($product->id, ['quantity' => 10, 'unit_price' => 100]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('products.show', [
            'product' => $product->id,
            'quick_filter' => 'custom',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-10',
        ]));

        $response->assertStatus(200);
        $response->assertSee('DN-PROD-HIST-1');
    }

    /**
     * SRS.HSP.001 - SRC.HSP.001.002
     * Menampilkan riwayat Shipment berdasarkan Product dengan rentang waktu yang tidak memiliki data
     */
    public function test_case_src_hsp_001_002_product_history_empty()
    {
        $product = Product::create([
            'sku' => 'PROD-HIST-2',
            'name' => 'Product History 2',
            'unit_price' => 100,
            'supplier_id' => $this->supplier->id,
        ]);

        $shipment = Shipment::create([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'created_by_user_id' => $this->salesUser->id,
            'etd_port' => '2026-06-05',
            'customer_receiving_schedule' => '2026-06-15',
            'customer_po' => 'PO-PROD-HIST-2',
            'delivery_note_number' => 'DN-PROD-HIST-2',
        ]);

        $shipment->products()->attach($product->id, ['quantity' => 10, 'unit_price' => 100]);

        $this->actingAs($this->salesUser);

        $response = $this->get(route('products.show', [
            'product' => $product->id,
            'quick_filter' => 'custom',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('DN-PROD-HIST-2');
    }
}

