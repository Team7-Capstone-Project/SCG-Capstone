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
            [
                'name' => 'PT Astra International Tbk',
                'address' => 'Jl. Gaya Motor Raya No. 8, Sunter II, Jakarta',
                'contact_person' => 'Budi Utomo',
                'phone' => '021-6522555',
                'email' => 'purchasing@astra.co.id',
                'country' => 'Indonesia',
            ],
            [
                'name' => 'Siam Cement Group (SCG)',
                'address' => '1 Siam Cement Road, Bangsue, Bangkok',
                'contact_person' => 'Somchai Somboon',
                'phone' => '+66-2-5864444',
                'email' => 'contact@scg.com',
                'country' => 'Thailand',
            ],
            [
                'name' => 'Petronas Chemicals Group Berhad',
                'address' => 'Tower 1, Petronas Twin Towers, Kuala Lumpur',
                'contact_person' => 'Mohd Azlan',
                'phone' => '+60-3-20515000',
                'email' => 'info.pcg@petronas.com',
                'country' => 'Malaysia',
            ],
            [
                'name' => 'JG Summit Holdings, Inc.',
                'address' => '43rd Floor, Robinsons Equitable Tower, ADB Ave, Pasig',
                'contact_person' => 'Maria Santos',
                'phone' => '+63-2-86337631',
                'email' => 'procurement@jgsummit.com.ph',
                'country' => 'Philippines',
            ],
            [
                'name' => 'Charoen Pokphand Group (CP Group)',
                'address' => '313 C.P. Tower, Silom Road, Bangrak, Bangkok',
                'contact_person' => 'Kiatnakin Prasert',
                'phone' => '+66-2-7667000',
                'email' => 'supply_chain@cpgroup.cn',
                'country' => 'Thailand',
            ],
            [
                'name' => 'PT Semen Indonesia Tbk',
                'address' => 'South Quarter Tower A, Jl. RA Kartini, Jakarta',
                'contact_person' => 'Agus Wijaya',
                'phone' => '021-5261254',
                'email' => 'marketing@sig.id',
                'country' => 'Indonesia',
            ],
            [
                'name' => 'Vietnam Dairy Products JSC (Vinamilk)',
                'address' => '10 Tan Trao, Tan Phu Ward, District 7, Ho Chi Minh City',
                'contact_person' => 'Nguyen Van A',
                'phone' => '+84-28-54155555',
                'email' => 'import_export@vinamilk.com.vn',
                'country' => 'Vietnam',
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
            [
                'name' => 'Mitsui & Co., Ltd.',
                'address' => '1-3, Marunouchi 1-chome, Chiyoda-ku, Tokyo',
                'contact_person' => 'Kenji Yamamoto',
                'phone' => '+81-3-32851111',
                'email' => 'chemical_dept@mitsui.com',
                'country' => 'Japan',
            ],
            [
                'name' => 'Hanwha Corporation',
                'address' => '86 Cheonggyecheon-ro, Jung-gu, Seoul',
                'contact_person' => 'Min-ho Kim',
                'phone' => '+82-2-7292114',
                'email' => 'global_trade@hanwha.com',
                'country' => 'South Korea',
            ],
            [
                'name' => 'BASF SE',
                'address' => 'Carl-Bosch-Strasse 38, Ludwigshafen',
                'contact_person' => 'Dieter Mueller',
                'phone' => '+49-621-600',
                'email' => 'orders@basf.com',
                'country' => 'Germany',
            ],
            [
                'name' => 'Dow Chemical Company',
                'address' => '2211 H.H. Dow Way, Midland, Michigan',
                'contact_person' => 'Sarah Jenkins',
                'phone' => '+1-989-6361000',
                'email' => 'custhelp@dow.com',
                'country' => 'United States',
            ],
            [
                'name' => 'Formosa Plastics Corporation',
                'address' => 'No. 201 Dunhua N. Road, Songshan District, Taipei',
                'contact_person' => 'Chao-Ming Chen',
                'phone' => '+886-2-27122211',
                'email' => 'sales@fpc.com.tw',
                'country' => 'Taiwan',
            ],
            [
                'name' => 'SABIC (Saudi Basic Industries Corp)',
                'address' => 'Qordoba, Airport Road, Riyadh',
                'contact_person' => 'Fahad Al-Harbi',
                'phone' => '+966-11-2258000',
                'email' => 'info@sabic.com',
                'country' => 'Saudi Arabia',
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
                'image' => 'images/products/scg_cement.png',
                'unit_price' => 95000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'CON-002',
                'name' => 'SCG Smartwood Wall Capping',
                'description' => 'Premium fiber cement decor wall paneling',
                'image' => 'images/products/scg_smartwood.png',
                'unit_price' => 120000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'CON-003',
                'name' => 'SCG Roofing Ceramic Tile',
                'description' => 'Premium terracotta ceramic tiles for roofing',
                'image' => 'images/products/scg_ceramic_tile.png',
                'unit_price' => 15000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'ENG-001',
                'name' => 'SCG Solar Panel System',
                'description' => 'High efficiency monocrystalline solar PV panel system',
                'image' => 'images/products/scg_solar_panel.png',
                'unit_price' => 4500000,
                'supplier_id' => 2,
            ],
            [
                'sku' => 'ENG-002',
                'name' => 'SCG Microgrid Energy Storage',
                'description' => 'Battery energy storage system for microgrid solution',
                'image' => 'images/products/scg_microgrid.png',
                'unit_price' => 12500000,
                'supplier_id' => 2,
            ],
            [
                'sku' => 'IND-001',
                'name' => 'Corn and Tapioca Starch',
                'description' => 'Premium tapioca starch for industrial chemical binder',
                'image' => 'images/products/tapioca_starch.png',
                'unit_price' => 12000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'REC-001',
                'name' => 'Recycled Plastic Resin',
                'description' => 'Eco-friendly recycled PCR polyethylene resin pellets',
                'image' => 'images/products/recycled_resin.png',
                'unit_price' => 18000,
                'supplier_id' => 3,
            ],
            [
                'sku' => 'REC-002',
                'name' => 'Recycled Paper Roll RCP',
                'description' => 'Recycled cardboard medium craft paper roll',
                'image' => 'images/products/recycled_paper_roll.png',
                'unit_price' => 8500,
                'supplier_id' => 3,
            ],
            [
                'sku' => 'CHE-001',
                'name' => 'High Purity Propylene Glycol',
                'description' => 'Industrial grade solvent and raw material',
                'image' => 'images/products/chemical_glycol.png',
                'unit_price' => 28000,
                'supplier_id' => 4,
            ],
            [
                'sku' => 'CHE-002',
                'name' => 'Caustic Soda Flakes',
                'description' => 'Sodium hydroxide flakes for soap and paper industries',
                'image' => 'images/products/caustic_soda.png',
                'unit_price' => 15000,
                'supplier_id' => 5,
            ],
            [
                'sku' => 'POL-001',
                'name' => 'Polyurethane Foam Catalyst',
                'description' => 'Polymer additive for high resiliency cushioning',
                'image' => 'images/products/polyurethane_catalyst.png',
                'unit_price' => 89000,
                'supplier_id' => 6,
            ],
            [
                'sku' => 'POL-002',
                'name' => 'Polyethylene Resin DOWLEX',
                'description' => 'Linear low density polyethylene film grade',
                'image' => 'images/products/polyethylene_resin.png',
                'unit_price' => 24500,
                'supplier_id' => 7,
            ],
            [
                'sku' => 'PLA-001',
                'name' => 'PVC Resin Suspension Grade',
                'description' => 'Polyvinyl chloride resin for pipe extrusion',
                'image' => 'images/products/pvc_resin.png',
                'unit_price' => 19500,
                'supplier_id' => 8,
            ],
            [
                'sku' => 'PLA-002',
                'name' => 'Polypropylene Homopolymer',
                'description' => 'High flow injection molding PP granules',
                'image' => 'images/products/polypropylene_homopolymer.png',
                'unit_price' => 22000,
                'supplier_id' => 9,
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
            // Shipment 11: On-Time Delivery
            [
                'customer_id' => 4, // PT Astra International Tbk
                'supplier_id' => 4, // Mitsui & Co.
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-AST-2025-011',
                'scg_po' => 'SCG-2025-011',
                'booking_number' => 'BK-011-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-04-10',
                'eta_port' => '2025-04-20',
                'ata_port' => '2025-04-19',
                'customer_receiving_schedule' => '2025-04-25',
                'ata_customer' => '2025-04-24', // Early (1 day early)
                'shipping_cost' => 22000000,
                'customs_cost' => 7500000,
                'other_costs' => 1500000,
                'products' => [
                    ['product_id' => 9, 'quantity' => 1000, 'unit_price' => 28000],
                ],
            ],
            // Shipment 12: In Transit
            [
                'customer_id' => 5, // Siam Cement Group (SCG)
                'supplier_id' => 5, // Hanwha Corporation
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-SCG-2025-012',
                'scg_po' => 'SCG-2025-012',
                'booking_number' => 'BK-012-2025',
                'status' => 'In Transit',
                'etd_port' => '2025-04-15',
                'eta_port' => '2025-04-25',
                'ata_port' => '2025-04-24',
                'customer_receiving_schedule' => '2025-05-01',
                'ata_customer' => null,
                'shipping_cost' => 13500000,
                'customs_cost' => 4500000,
                'other_costs' => 800000,
                'products' => [
                    ['product_id' => 10, 'quantity' => 500, 'unit_price' => 15000],
                ],
            ],
            // Shipment 13: Pending
            [
                'customer_id' => 6, // Petronas Chemicals Group
                'supplier_id' => 6, // BASF SE
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-PET-2025-013',
                'scg_po' => 'SCG-2025-013',
                'booking_number' => 'BK-013-2025',
                'status' => 'Pending',
                'etd_port' => '2025-05-01',
                'eta_port' => '2025-05-10',
                'ata_port' => null,
                'customer_receiving_schedule' => '2025-05-15',
                'ata_customer' => null,
                'shipping_cost' => 19000000,
                'customs_cost' => 6500000,
                'other_costs' => 1200000,
                'products' => [
                    ['product_id' => 11, 'quantity' => 200, 'unit_price' => 89000],
                ],
            ],
            // Shipment 14: Late Delivery
            [
                'customer_id' => 7, // JG Summit Holdings
                'supplier_id' => 7, // Dow Chemical Company
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-JGS-2025-014',
                'scg_po' => 'SCG-2025-014',
                'booking_number' => 'BK-014-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-04-05',
                'eta_port' => '2025-04-15',
                'ata_port' => '2025-04-18',
                'customer_receiving_schedule' => '2025-04-20',
                'ata_customer' => '2025-04-23', // Late (3 days late)
                'shipping_cost' => 26000000,
                'customs_cost' => 9000000,
                'other_costs' => 2000000,
                'products' => [
                    ['product_id' => 12, 'quantity' => 1500, 'unit_price' => 24500],
                ],
            ],
            // Shipment 15: Early Delivery
            [
                'customer_id' => 8, // CP Group
                'supplier_id' => 8, // Formosa Plastics Corporation
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-CPG-2025-015',
                'scg_po' => 'SCG-2025-015',
                'booking_number' => 'BK-015-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-04-12',
                'eta_port' => '2025-04-22',
                'ata_port' => '2025-04-19',
                'customer_receiving_schedule' => '2025-04-28',
                'ata_customer' => '2025-04-24', // Early (4 days early)
                'shipping_cost' => 15000000,
                'customs_cost' => 5000000,
                'other_costs' => 1000000,
                'products' => [
                    ['product_id' => 13, 'quantity' => 800, 'unit_price' => 19500],
                ],
            ],
            // Shipment 16: On-Time Delivery
            [
                'customer_id' => 9, // PT Semen Indonesia Tbk
                'supplier_id' => 9, // SABIC
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-SIG-2025-016',
                'scg_po' => 'SCG-2025-016',
                'booking_number' => 'BK-016-2025',
                'status' => 'Delivered',
                'etd_port' => '2025-04-20',
                'eta_port' => '2025-04-30',
                'ata_port' => '2025-04-29',
                'customer_receiving_schedule' => '2025-05-05',
                'ata_customer' => '2025-05-05', // On-time
                'shipping_cost' => 18500000,
                'customs_cost' => 6000000,
                'other_costs' => 1100000,
                'products' => [
                    ['product_id' => 14, 'quantity' => 1200, 'unit_price' => 22000],
                ],
            ],
            // Shipment 17: In Transit
            [
                'customer_id' => 10, // Vinamilk
                'supplier_id' => 3, // Singapore Trading
                'created_by_user_id' => $adminScm2->id,
                'customer_po' => 'PO-VNM-2025-017',
                'scg_po' => 'SCG-2025-017',
                'booking_number' => 'BK-017-2025',
                'status' => 'In Transit',
                'etd_port' => '2025-04-25',
                'eta_port' => '2025-05-05',
                'ata_port' => '2025-05-04',
                'customer_receiving_schedule' => '2025-05-10',
                'ata_customer' => null,
                'shipping_cost' => 11000000,
                'customs_cost' => 3500000,
                'other_costs' => 600000,
                'products' => [
                    ['product_id' => 7, 'quantity' => 400, 'unit_price' => 18000],
                ],
            ],
        ];

        foreach ($shipments as $index => $shipmentData) {
            $idx = sprintf('%03d', $index + 1);
            $shipmentData['customer_po'] = '51-PO2025-' . $idx;
            $shipmentData['scg_po'] = '42-PO2025-' . $idx;
            $shipmentData['scg_so'] = '45-SO2025-' . $idx;
            $shipmentData['supplier_invoice'] = '41-INV2025-' . $idx;
            $shipmentData['delivery_note_number'] = '45-DN2025-' . $idx;
            $shipmentData['booking_number'] = 'BKG-2025-' . $idx;

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
                'New shipment created with PO: ' . ($shipment->customer_po ?? 'N/A')
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
