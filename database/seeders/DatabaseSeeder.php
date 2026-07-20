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
            'name' => 'Agung Tanjung',
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

        $adminScm3 = User::create([
            'name' => 'Fahmi Zal',
            'email' => 'fahmi.scm@scg.com',
            'password' => Hash::make('password'),
            'role' => 'admin_scm',
        ]);

        $picSales3 = User::create([
            'name' => 'Ve Nissa',
            'email' => 'venissa.sales@scg.com',
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
                'unit_price' => 195000000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'CON-002',
                'name' => 'SCG Smartwood Wall Capping',
                'description' => 'Premium fiber cement decor wall paneling',
                'image' => 'images/products/scg_smartwood.png',
                'unit_price' => 220000000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'CON-003',
                'name' => 'SCG Roofing Ceramic Tile',
                'description' => 'Premium terracotta ceramic tiles for roofing',
                'image' => 'images/products/scg_ceramic_tile.png',
                'unit_price' => 150000000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'ENG-001',
                'name' => 'SCG Solar Panel System',
                'description' => 'High efficiency monocrystalline solar PV panel system',
                'image' => 'images/products/scg_solar_panel.png',
                'unit_price' => 280000000,
                'supplier_id' => 2,
            ],
            [
                'sku' => 'ENG-002',
                'name' => 'SCG Microgrid Energy Storage',
                'description' => 'Battery energy storage system for microgrid solution',
                'image' => 'images/products/scg_microgrid.png',
                'unit_price' => 350000000,
                'supplier_id' => 2,
            ],
            [
                'sku' => 'IND-001',
                'name' => 'Corn and Tapioca Starch',
                'description' => 'Premium tapioca starch for industrial chemical binder',
                'image' => 'images/products/tapioca_starch.png',
                'unit_price' => 120000000,
                'supplier_id' => 1,
            ],
            [
                'sku' => 'REC-001',
                'name' => 'Recycled Plastic Resin',
                'description' => 'Eco-friendly recycled PCR polyethylene resin pellets',
                'image' => 'images/products/recycled_resin.png',
                'unit_price' => 180000000,
                'supplier_id' => 3,
            ],
            [
                'sku' => 'REC-002',
                'name' => 'Recycled Paper Roll RCP',
                'description' => 'Recycled cardboard medium craft paper roll',
                'image' => 'images/products/recycled_paper_roll.png',
                'unit_price' => 135000000,
                'supplier_id' => 3,
            ],
            [
                'sku' => 'CHE-001',
                'name' => 'High Purity Propylene Glycol',
                'description' => 'Industrial grade solvent and raw material',
                'image' => 'images/products/chemical_glycol.png',
                'unit_price' => 210000000,
                'supplier_id' => 4,
            ],
            [
                'sku' => 'CHE-002',
                'name' => 'Caustic Soda Flakes',
                'description' => 'Sodium hydroxide flakes for soap and paper industries',
                'image' => 'images/products/caustic_soda.png',
                'unit_price' => 165000000,
                'supplier_id' => 5,
            ],
            [
                'sku' => 'POL-001',
                'name' => 'Polyurethane Foam Catalyst',
                'description' => 'Polymer additive for high resiliency cushioning',
                'image' => 'images/products/polyurethane_catalyst.png',
                'unit_price' => 245000000,
                'supplier_id' => 6,
            ],
            [
                'sku' => 'POL-002',
                'name' => 'Polyethylene Resin DOWLEX',
                'description' => 'Linear low density polyethylene film grade',
                'image' => 'images/products/polyethylene_resin.png',
                'unit_price' => 175000000,
                'supplier_id' => 7,
            ],
            [
                'sku' => 'PLA-001',
                'name' => 'PVC Resin Suspension Grade',
                'description' => 'Polyvinyl chloride resin for pipe extrusion',
                'image' => 'images/products/pvc_resin.png',
                'unit_price' => 190000000,
                'supplier_id' => 8,
            ],
            [
                'sku' => 'PLA-002',
                'name' => 'Polypropylene Homopolymer',
                'description' => 'High flow injection molding PP granules',
                'image' => 'images/products/polypropylene_homopolymer.png',
                'unit_price' => 230000000,
                'supplier_id' => 9,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create 62 shipments distributed across January - July 2026
        $monthlyCounts = [
            '2026-01' => 9,
            '2026-02' => 8,
            '2026-03' => 10,
            '2026-04' => 11,
            '2026-05' => 10,
            '2026-06' => 12, // Increased June shipments
            '2026-07' => 2,  // Only early July shipments (ETD on/before July 4th)
        ];

        // Specific PO notes and activity logs requested by user
        $specificNotes = [
            '5102667005' => ['note' => 'Lembang', 'user' => $adminScm1->id, 'time' => '2026-07-20 16:58:00'],
            '5102333003' => ['note' => 'Tol Cikampek', 'user' => $adminScm1->id, 'time' => '2026-07-20 16:57:00'],
            '5102490004' => ['note' => 'Pelabuhan Singapura', 'user' => $adminScm1->id, 'time' => '2026-07-20 16:57:00'],
            '5100064006' => ['note' => 'Tol Jagorawi', 'user' => $adminScm1->id, 'time' => '2026-07-20 16:57:00'],
            '5102228001' => ['note' => 'Tegal', 'user' => $adminScm1->id, 'time' => '2026-07-20 16:56:00'],
        ];

        // Weighted outcomes pool (OnTime, Early with varied days, Late with varied days, In Transit, Pending, Cancelled)
        $outcomesPool = [
            ['status' => 'Delivered', 'type_perf' => 'ontime', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'early', 'offset' => -2],
            ['status' => 'Delivered', 'type_perf' => 'ontime', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'late', 'offset' => 3],
            ['status' => 'Delivered', 'type_perf' => 'ontime', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'early', 'offset' => -4],
            ['status' => 'In Transit', 'type_perf' => 'transit', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'ontime', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'late', 'offset' => 5],
            ['status' => 'Delivered', 'type_perf' => 'early', 'offset' => -1],
            ['status' => 'Delivered', 'type_perf' => 'ontime', 'offset' => 0],
            ['status' => 'Pending', 'type_perf' => 'pending', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'ontime', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'early', 'offset' => -3],
            ['status' => 'Delivered', 'type_perf' => 'late', 'offset' => 2],
            ['status' => 'Cancelled', 'type_perf' => 'cancelled', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'ontime', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'early', 'offset' => -5],
            ['status' => 'In Transit', 'type_perf' => 'transit', 'offset' => 0],
            ['status' => 'Delivered', 'type_perf' => 'late', 'offset' => 7],
        ];

        $types = ['Import', 'Export'];
        $salesUsers = [$picSales1, $picSales2, $picSales3];
        $scmUsers = [$adminScm1, $adminScm2, $adminScm3];

        $usedCustomerPos = [];
        $usedScgPos = [];
        $usedScgSos = [];
        $usedInvoices = [];
        $usedDeliveryNotes = [];

        $shipmentCounter = 0;

        foreach ($monthlyCounts as $yearMonth => $countInMonth) {
            for ($i = 1; $i <= $countInMonth; $i++) {
                $shipmentCounter++;

                $salesUser = $salesUsers[($shipmentCounter - 1) % count($salesUsers)];
                $scmUser = $scmUsers[($shipmentCounter - 1) % count($scmUsers)];
                $customerId = (($shipmentCounter - 1) % count($customers)) + 1;
                $supplierId = (($shipmentCounter - 1) % count($suppliers)) + 1;

                // Dates calculation (July shipments capped at July 4 ETD so no schedule reaches August)
                if ($yearMonth === '2026-07') {
                    $dayStart = sprintf('%02d', min($i * 2, 4));
                    $etdPort = "{$yearMonth}-{$dayStart}";
                    $etaPort = date('Y-m-d', strtotime("{$etdPort} +4 days"));
                    $customerSchedule = date('Y-m-d', strtotime("{$etdPort} +10 days"));
                } else {
                    $dayStart = sprintf('%02d', min($i * 2 + 1, 27));
                    $etdPort = "{$yearMonth}-{$dayStart}";
                    $etaPort = date('Y-m-d', strtotime("{$etdPort} +7 days"));
                    $customerSchedule = date('Y-m-d', strtotime("{$etdPort} +15 days"));
                }

                $outcome = $outcomesPool[($shipmentCounter - 1) % count($outcomesPool)];
                $status = $outcome['status'];
                $type = $types[($shipmentCounter - 1) % count($types)];

                $ataPort = null;
                $ataCustomer = null;

                if ($status === 'Delivered') {
                    $ataPort = date('Y-m-d', strtotime("{$etdPort} +6 days"));
                    $offset = $outcome['offset'];
                    $ataCustomer = date('Y-m-d', strtotime("{$customerSchedule} {$offset} days"));
                } elseif ($status === 'In Transit') {
                    $ataPort = date('Y-m-d', strtotime("{$etdPort} +6 days"));
                }

                // Document numbers (customer PO starting with 51, SCG PO starting with 42, SCG SO starting with 45)
                if ($shipmentCounter === 1) {
                    $customerPo = '5102228001';
                } elseif ($shipmentCounter === 2) {
                    $customerPo = '5102252002';
                } elseif ($shipmentCounter === 3) {
                    $customerPo = '5102333003';
                } elseif ($shipmentCounter === 4) {
                    $customerPo = '5102490004';
                } elseif ($shipmentCounter === 5) {
                    $customerPo = '5102667005';
                } elseif ($shipmentCounter === 6) {
                    $customerPo = '5100064006';
                } else {
                    do {
                        $customerPo = '510' . sprintf('%07d', rand(1000000, 9999999));
                    } while (in_array($customerPo, $usedCustomerPos));
                }
                $usedCustomerPos[] = $customerPo;

                do {
                    $scgPo = '420' . sprintf('%07d', rand(1000000, 9999999));
                } while (in_array($scgPo, $usedScgPos));
                $usedScgPos[] = $scgPo;

                do {
                    $scgSo = '450' . sprintf('%07d', rand(1000000, 9999999));
                } while (in_array($scgSo, $usedScgSos));
                $usedScgSos[] = $scgSo;

                do {
                    $invoice = '418' . sprintf('%07d', rand(1000000, 9999999));
                } while (in_array($invoice, $usedInvoices));
                $usedInvoices[] = $invoice;

                do {
                    $deliveryNote = '451' . sprintf('%07d', rand(1000000, 9999999));
                } while (in_array($deliveryNote, $usedDeliveryNotes));
                $usedDeliveryNotes[] = $deliveryNote;

                $bookingNumber = 'BK-' . sprintf('%03d', $shipmentCounter) . '-2026';

                // Check note for specific POs
                $note = isset($specificNotes[$customerPo]) ? $specificNotes[$customerPo]['note'] : null;

                $shipment = Shipment::create([
                    'customer_id' => $customerId,
                    'supplier_id' => $supplierId,
                    'created_by_user_id' => $salesUser->id,
                    'type' => $type,
                    'customer_po' => $customerPo,
                    'scg_po' => $scgPo,
                    'scg_so' => $scgSo,
                    'booking_number' => $bookingNumber,
                    'supplier_invoice' => $invoice,
                    'delivery_note_number' => $deliveryNote,
                    'status' => $status,
                    'etd_port' => $etdPort,
                    'eta_port' => $etaPort,
                    'ata_port' => $ataPort,
                    'customer_receiving_schedule' => $customerSchedule,
                    'ata_customer' => $ataCustomer,
                    'shipping_cost' => rand(10, 20) * 1000000,
                    'customs_cost' => rand(3, 8) * 1000000,
                    'other_costs' => rand(5, 15) * 100000,
                    'notes' => $note,
                    'created_at' => "{$etdPort} 09:00:00",
                    'updated_at' => "{$etdPort} 09:00:00",
                ]);

                // Attach product so total cost is in > 100jt range
                $productId = (($shipmentCounter - 1) % 8) + 1;
                $productModel = Product::find($productId);
                $unitPrice = $productModel ? $productModel->unit_price : 150000000;
                $shipment->products()->attach($productId, [
                    'quantity' => rand(1, 2),
                    'unit_price' => $unitPrice,
                ]);

                // Creation Log (Sales)
                ActivityLog::create([
                    'user_id' => $salesUser->id,
                    'shipment_id' => $shipment->id,
                    'action' => 'created',
                    'old_value' => null,
                    'new_value' => 'Shipment created',
                    'description' => 'New shipment created with PO: ' . $customerPo,
                    'created_at' => "{$etdPort} 09:00:00",
                    'updated_at' => "{$etdPort} 09:00:00",
                ]);

                // Status Update Log (SCM)
                if (in_array($status, ['Delivered', 'In Transit'])) {
                    ActivityLog::create([
                        'user_id' => $scmUser->id,
                        'shipment_id' => $shipment->id,
                        'action' => 'updated_status',
                        'old_value' => 'Pending',
                        'new_value' => $status,
                        'description' => 'Status changed to ' . $status,
                        'created_at' => "{$etdPort} 14:00:00",
                        'updated_at' => "{$etdPort} 14:00:00",
                    ]);
                }

                // Specific Note Log (Agung Tanjung)
                if (isset($specificNotes[$customerPo])) {
                    $noteData = $specificNotes[$customerPo];
                    ActivityLog::create([
                        'user_id' => $noteData['user'],
                        'shipment_id' => $shipment->id,
                        'action' => 'updated',
                        'old_value' => null,
                        'new_value' => $noteData['note'],
                        'description' => 'Notes: (empty) → ' . $noteData['note'],
                        'created_at' => $noteData['time'],
                        'updated_at' => $noteData['time'],
                    ]);
                }
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Test Users:');
        $this->command->info('Admin SCM 1: admin.scm@scg.com / password (Agung Tanjung)');
        $this->command->info('Admin SCM 2: budi.scm@scg.com / password (Budi Santoso)');
        $this->command->info('Admin SCM 3: fahmi.scm@scg.com / password (Fahmi Zal)');
        $this->command->info('PIC Sales 1: siti.sales@scg.com / password (Siti Nurhaliza)');
        $this->command->info('PIC Sales 2: ahmad.sales@scg.com / password (Ahmad Wijaya)');
        $this->command->info('PIC Sales 3: venissa.sales@scg.com / password (Ve Nissa)');
    }
}
