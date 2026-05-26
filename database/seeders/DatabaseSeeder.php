<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for SCM Dashboard
     */
    public function run(): void
    {
        // Create Users with different roles (NEW SYSTEM: only admin_scm and pic_sales)
        $adminScm1 = User::create([
            'name' => 'Admin SCM 1',
            'email' => 'admin.scm@scg.com',
            'password' => Hash::make('password'),
            'role' => 'admin_scm',
        ]);

        $adminScm2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.scm@scg.com',
            'password' => Hash::make('password'),
            'role' => 'admin_scm',
        ]);

        $picSales1 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti.sales@scg.com',
            'password' => Hash::make('password'),
            'role' => 'pic_sales',
        ]);

        $picSales2 = User::create([
            'name' => 'Ahmad Wijaya',
            'email' => 'ahmad.sales@scg.com',
            'password' => Hash::make('password'),
            'role' => 'pic_sales',
        ]);

        // Create Customers
        $customers = [
            [
                'name' => 'PT Unilever Indonesia',
                'address' => 'Jl. BSD Boulevard Barat, Tangerang',
                'contact_person' => 'John Doe',
                'phone' => '021-5123456',
                'email' => 'procurement@unilever.co.id',
                'country' => 'Indonesia',
            ],
            [
                'name' => 'PT Indofood Sukses Makmur',
                'address' => 'Jl. Sudirman Kav 76-78, Jakarta',
                'contact_person' => 'Jane Smith',
                'phone' => '021-5789012',
                'email' => 'purchasing@indofood.co.id',
                'country' => 'Indonesia',
            ],
            [
                'name' => 'PT Nestle Indonesia',
                'address' => 'Jl. Pulo Lentut No. 3, Jakarta',
                'contact_person' => 'Robert Chen',
                'phone' => '021-4567890',
                'email' => 'supply@nestle.co.id',
                'country' => 'Indonesia',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Create Suppliers
        $suppliers = [
            [
                'name' => 'Shanghai Chemical Co., Ltd',
                'address' => 'No. 123 Huangpu Road, Shanghai',
                'contact_person' => 'Li Wei',
                'phone' => '+86-21-12345678',
                'email' => 'export@shchemical.com',
                'country' => 'China',
            ],
            [
                'name' => 'Tokyo Industrial Materials',
                'address' => '1-2-3 Shibuya, Tokyo',
                'contact_person' => 'Tanaka Hiroshi',
                'phone' => '+81-3-98765432',
                'email' => 'sales@tokyoind.jp',
                'country' => 'Japan',
            ],
            [
                'name' => 'Singapore Trading Pte Ltd',
                'address' => '50 Raffles Place, Singapore',
                'contact_person' => 'David Tan',
                'phone' => '+65-6234-5678',
                'email' => 'info@sgtrading.com.sg',
                'country' => 'Singapore',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Create Products
        $products = [
            [
                'sku' => 'CON-001',
                'name' => 'SCG Low Carbon Cement',
                'description' => 'Eco-friendly and high-strength construction cement',
                'unit_price' => 95000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'CON-002',
                'name' => 'SCG Smartwood Wall Capping',
                'description' => 'Premium fiber cement decor wall paneling',
                'unit_price' => 120000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'CON-003',
                'name' => 'SCG Roofing Ceramic Tile',
                'description' => 'Premium terracotta ceramic tiles for roofing',
                'unit_price' => 15000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'ENG-001',
                'name' => 'SCG Solar Panel System',
                'description' => 'High efficiency monocrystalline solar PV panel system',
                'unit_price' => 4500000,
                'supplier_id' => 2,
            ],
            [
                'sku' => 'ENG-002',
                'name' => 'SCG Microgrid Energy Storage',
                'description' => 'Battery energy storage system for microgrid solution',
                'unit_price' => 12500000,
                'supplier_id' => 2,
            ],
            [
                'sku' => 'IND-001',
                'name' => 'Corn and Tapioca Starch',
                'description' => 'Premium tapioca starch for industrial chemical binder',
                'unit_price' => 12000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'REC-001',
                'name' => 'Recycled Plastic Resin',
                'description' => 'Eco-friendly recycled PCR polyethylene resin pellets',
                'unit_price' => 18000,
                'supplier_id' => 3,
            ],
            [
                'sku' => 'REC-002',
                'name' => 'Recycled Paper Roll RCP',
                'description' => 'Recycled cardboard medium craft paper roll',
                'unit_price' => 8500,
                'supplier_id' => 3,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create Sample Shipments with various OTD scenarios
        $shipments = [
            // Shipment 1: On-Time Delivery
            [
                'customer_id' => 1,
                'supplier_id' => 1,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-UNI-2025-001',
                'scg_po' => 'SCG-2025-001',
                'booking_number' => 'BK-001-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-01-05',
                'eta_port' => '2025-01-15',
                'ata_port' => '2025-01-14',
                'customer_receiving_schedule' => '2025-01-20',
                'ata_customer' => '2025-01-18', // Early (2 days early)
                'shipping_cost' => 15000000,
                'customs_cost' => 5000000,
                'other_costs' => 1000000,
                'products' => [
                    ['product_id' => 1, 'quantity' => 100, 'unit_price' => 95000],
                    ['product_id' => 2, 'quantity' => 50, 'unit_price' => 120000],
                ],
            ],
            // Shipment 2: Late Delivery
            [
                'customer_id' => 2,
                'supplier_id' => 1,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-IND-2025-002',
                'scg_po' => 'SCG-2025-002',
                'booking_number' => 'BK-002-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-01-10',
                'eta_port' => '2025-01-20',
                'ata_port' => '2025-01-22',
                'customer_receiving_schedule' => '2025-01-25',
                'ata_customer' => '2025-01-28', // Late
                'shipping_cost' => 12000000,
                'customs_cost' => 4000000,
                'other_costs' => 800000,
                'products' => [
                    ['product_id' => 2, 'quantity' => 75, 'unit_price' => 120000],
                ],
            ],
            // Shipment 3: In Transit
            [
                'customer_id' => 3,
                'supplier_id' => 2,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-NES-2025-003',
                'scg_po' => 'SCG-2025-003',
                'booking_number' => 'BK-003-2025',
                'status' => 'In Transit',
                'etd_port' => '2025-02-01',
                'eta_port' => '2025-02-10',
                'ata_port' => '2025-02-09',
                'customer_receiving_schedule' => '2025-02-15',
                'ata_customer' => null,
                'shipping_cost' => 18000000,
                'customs_cost' => 6000000,
                'other_costs' => 1200000,
                'products' => [
                    ['product_id' => 4, 'quantity' => 200, 'unit_price' => 4500000],
                ],
            ],
            // Shipment 4: Pending
            [
                'customer_id' => 1,
                'supplier_id' => 3,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-UNI-2025-004',
                'scg_po' => 'SCG-2025-004',
                'booking_number' => 'BK-004-2025',
                'status' => 'Pending',
                'etd_port' => '2025-02-15',
                'eta_port' => '2025-02-25',
                'ata_port' => null,
                'customer_receiving_schedule' => '2025-03-01',
                'ata_customer' => null,
                'shipping_cost' => 8000000,
                'customs_cost' => 2500000,
                'other_costs' => 500000,
                'products' => [
                    ['product_id' => 7, 'quantity' => 500, 'unit_price' => 18000],
                    ['product_id' => 8, 'quantity' => 20, 'unit_price' => 8500],
                ],
            ],
            // Shipment 5: On-Time Delivery
            [
                'customer_id' => 2,
                'supplier_id' => 2,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-IND-2025-005',
                'scg_po' => 'SCG-2025-005',
                'booking_number' => 'BK-005-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-01-20',
                'eta_port' => '2025-01-30',
                'ata_port' => '2025-01-29',
                'customer_receiving_schedule' => '2025-02-05',
                'ata_customer' => '2025-02-05', // On-time (Ideal)
                'shipping_cost' => 16000000,
                'customs_cost' => 5500000,
                'other_costs' => 1100000,
                'products' => [
                    ['product_id' => 5, 'quantity' => 150, 'unit_price' => 12500000],
                ],
            ],
            // Shipment 6: Early Delivery
            [
                'customer_id' => 3,
                'supplier_id' => 1,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-NES-2025-006',
                'scg_po' => 'SCG-2025-006',
                'booking_number' => 'BK-006-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-02-10',
                'eta_port' => '2025-02-20',
                'ata_port' => '2025-02-19',
                'customer_receiving_schedule' => '2025-02-28',
                'ata_customer' => '2025-02-25', // Early (3 days early)
                'shipping_cost' => 14000000,
                'customs_cost' => 4500000,
                'other_costs' => 900000,
                'products' => [
                    ['product_id' => 3, 'quantity' => 120, 'unit_price' => 2100000],
                ],
            ],
            // Shipment 7: Late Delivery
            [
                'customer_id' => 1,
                'supplier_id' => 2,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-UNI-2025-007',
                'scg_po' => 'SCG-2025-007',
                'booking_number' => 'BK-007-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-02-20',
                'eta_port' => '2025-03-02',
                'ata_port' => '2025-03-05',
                'customer_receiving_schedule' => '2025-03-10',
                'ata_customer' => '2025-03-15', // Late (5 days late)
                'shipping_cost' => 17000000,
                'customs_cost' => 5800000,
                'other_costs' => 1300000,
                'products' => [
                    ['product_id' => 6, 'quantity' => 80, 'unit_price' => 850000],
                ],
            ],
            // Shipment 8: On-Time Delivery
            [
                'customer_id' => 3,
                'supplier_id' => 3,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-NES-2025-008',
                'scg_po' => 'SCG-2025-008',
                'booking_number' => 'BK-008-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-02-15',
                'eta_port' => '2025-02-25',
                'ata_port' => '2025-02-24',
                'customer_receiving_schedule' => '2025-03-05',
                'ata_customer' => '2025-03-05', // On-time (Ideal)
                'shipping_cost' => 9000000,
                'customs_cost' => 3000000,
                'other_costs' => 600000,
                'products' => [
                    ['product_id' => 7, 'quantity' => 300, 'unit_price' => 18000],
                ],
            ],
            // Shipment 9: Early Delivery
            [
                'customer_id' => 2,
                'supplier_id' => 3,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-IND-2025-009',
                'scg_po' => 'SCG-2025-009',
                'booking_number' => 'BK-009-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-03-25',
                'eta_port' => '2025-04-05',
                'ata_port' => '2025-04-04',
                'customer_receiving_schedule' => '2025-04-12',
                'ata_customer' => '2025-04-10', // Early (2 days early)
                'shipping_cost' => 11000000,
                'customs_cost' => 3800000,
                'other_costs' => 700000,
                'products' => [
                    ['product_id' => 8, 'quantity' => 150, 'unit_price' => 8500],
                ],
            ],
            // Shipment 10: On-Time Delivery
            [
                'customer_id' => 1,
                'supplier_id' => 2,
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-UNI-2025-010',
                'scg_po' => 'SCG-2025-010',
                'booking_number' => 'BK-010-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-04-01',
                'eta_port' => '2025-04-10',
                'ata_port' => '2025-04-09',
                'customer_receiving_schedule' => '2025-04-20',
                'ata_customer' => '2025-04-20', // On-time (Ideal)
                'shipping_cost' => 15500000,
                'customs_cost' => 5200000,
                'other_costs' => 95000,
                'products' => [
                    ['product_id' => 1, 'quantity' => 250, 'unit_price' => 95000],
                ],
            ],
        ];

        foreach ($shipments as $shipmentData) {
            $products = $shipmentData['products'];
            unset($shipmentData['products']);

            $shipment = Shipment::create($shipmentData);

            // Attach products
            foreach ($products as $productData) {
                $shipment->products()->attach($productData['product_id'], [
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                ]);
            }

            // Create activity log
            ActivityLog::logActivity(
                $adminScm2->id,
                $shipment->id,
                'created',
                null,
                'Shipment created',
                'Shipment created via seeder'
            );

            // Add status change log for delivered shipments
            if ($shipment->status === 'Delivered') {
                ActivityLog::logActivity(
                    $adminScm2->id,
                    $shipment->id,
                    'updated_status',
                    'Pending',
                    'Delivered',
                    'Status changed to Delivered'
                );
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Test Users:');
        $this->command->info('Admin SCM 1: admin.scm@scg.com / password');
        $this->command->info('Admin SCM 2: budi.scm@scg.com / password');
        $this->command->info('PIC Sales 1: siti.sales@scg.com / password');
        $this->command->info('PIC Sales 2: ahmad.sales@scg.com / password');
    }
}
