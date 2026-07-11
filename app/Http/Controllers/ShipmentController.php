<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ShipmentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of shipments with filters
     */
    /**
     * Get filtered query builder
     */
    private function getFilteredQuery(Request $request)
    {
        $query = Shipment::with(['customer', 'supplier', 'createdBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('late')) {
            $query->late();
        }

        if ($request->filled('early')) {
            $query->early();
        }

        if ($request->filled('on_time')) {
            $query->onTime();
        }

        if ($request->filled('month')) {
            $month = $request->month;
            if (preg_match('/^\d{4}-\d{2}$/', $month)) {
                list($year, $monthNum) = explode('-', $month);
                $query->whereYear('etd_port', $year)->whereMonth('etd_port', $monthNum);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_po', 'like', "%{$search}%")
                    ->orWhere('scg_po', 'like', "%{$search}%")
                    ->orWhere('scg_so', 'like', "%{$search}%")
                    ->orWhere('booking_number', 'like', "%{$search}%")
                    ->orWhere('supplier_invoice', 'like', "%{$search}%")
                    ->orWhere('delivery_note_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supplier', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'month_asc':
                    $query->orderBy('etd_port', 'asc');
                    break;
                case 'month_desc':
                    $query->orderBy('etd_port', 'desc');
                    break;
                case 'deadline_asc':
                    $query->orderBy('customer_receiving_schedule', 'asc');
                    break;
                case 'deadline_desc':
                    $query->orderBy('customer_receiving_schedule', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Display a listing of shipments with filters
     */
    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $shipments = $query->paginate(15)->withQueryString();
        $customers = Customer::orderBy('name')->get();

        $dateExpression = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', etd_port) as month_val"
            : "DATE_FORMAT(etd_port, '%Y-%m') as month_val";

        $availableMonths = Shipment::select(DB::raw($dateExpression))
            ->groupBy('month_val')
            ->orderBy('month_val', 'desc')
            ->pluck('month_val')
            ->map(function ($value) {
                $carbonDate = \Carbon\Carbon::createFromFormat('Y-m', $value);
                return [
                    'value' => $value,
                    'label' => $carbonDate->translatedFormat('F Y'),
                ];
            })
            ->toArray();

        return view('shipments.index', compact('shipments', 'customers', 'availableMonths'));
    }

    /**
     * Export shipments to CSV
     */
    /**
     * Export shipments to XLSX
     */
    public function export(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $filename = 'shipments-' . date('Y-m-d-His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShipmentsExport($query), $filename);
    }

    /**
     * Show the form for creating a new shipment
     */
    public function create()
    {
        $this->authorize('create', Shipment::class);

        $customers = Customer::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with('supplier')->orderBy('name')->get();

        return view('shipments.create', compact('customers', 'suppliers', 'products'));
    }

    /**
     * Store a newly created shipment in database
     * FR-ST-01: Create New Shipment
     */
    public function store(Request $request)
    {
        $this->authorize('create', Shipment::class);

        // Validation
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:Import,Export',
            // Document numbers - must be unique globally, and contain valid format
            'customer_po' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,customer_po,NULL,id,deleted_at,NULL',
            'scg_po' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,scg_po,NULL,id,deleted_at,NULL',
            'scg_so' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,scg_so,NULL,id,deleted_at,NULL',
            'booking_number' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,booking_number,NULL,id,deleted_at,NULL',
            'delivery_note_number' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,delivery_note_number,NULL,id,deleted_at,NULL',
            'supplier_invoice' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,supplier_invoice,NULL,id,deleted_at,NULL',
            'etd_port' => 'required|date|after_or_equal:2020-01-01|before_or_equal:2035-12-31',
            'eta_port' => [
                'nullable',
                'date',
                'after_or_equal:etd_port',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('etd_port')) {
                        $etd = strtotime($request->etd_port);
                        $eta = strtotime($value);
                        if ($eta > $etd + (365 * 24 * 60 * 60)) {
                            $fail(__('The ETA Port must be at most 1 year (365 days) after ETD Port.'));
                        }
                    }
                }
            ],
            'ata_port' => [
                'nullable',
                'date',
                'after_or_equal:etd_port',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('etd_port')) {
                        $etd = strtotime($request->etd_port);
                        $ata = strtotime($value);
                        if ($ata > $etd + (365 * 24 * 60 * 60)) {
                            $fail(__('The ATA Port must be at most 1 year (365 days) after ETD Port.'));
                        }
                    }
                }
            ],
            'customer_receiving_schedule' => [
                'required',
                'date',
                'after_or_equal:etd_port',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('etd_port')) {
                        $etd = strtotime($request->etd_port);
                        $schedule = strtotime($value);
                        if ($schedule > $etd + (365 * 24 * 60 * 60)) {
                            $fail(__('The Customer Receiving Schedule must be at most 1 year (365 days) after ETD Port.'));
                        }
                    }
                    if ($request->filled('eta_port')) {
                        $eta = strtotime($request->eta_port);
                        $schedule = strtotime($value);
                        if ($schedule < $eta) {
                            $fail(__('The Customer Receiving Schedule must be after or equal to ETA Port.'));
                        }
                    }
                }
            ],
            'shipping_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'customs_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'other_costs' => 'nullable|numeric|min:0|max:999999999999.99',
            'notes' => [
                'nullable',
                'string',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The notes must not contain HTML or script tags.'));
                    }
                }
            ],
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:10000000',
            'products.*.unit_price' => 'required|numeric|min:0|max:999999999999.99',
        ], [
            'customer_id.required' => 'Customer is required',
            'supplier_id.required' => 'Supplier is required',
            'etd_port.required' => 'ETD Port date is required',
            'customer_receiving_schedule.required' => 'Customer receiving schedule is required',
            'customer_receiving_schedule.after_or_equal' => 'Customer receiving schedule must be after or equal to ETD Port',
            // Unique validation error messages
            'customer_po.unique' => 'Customer PO already exists in the system',
            'scg_po.unique' => 'SCG PO already exists in the system',
            'scg_so.unique' => 'SCG SO already exists in the system',
            'booking_number.unique' => 'Booking Number already exists in the system',
            'delivery_note_number.unique' => 'Delivery Note Number already exists in the system',
            'supplier_invoice.unique' => 'Supplier Invoice already exists in the system',
            // Format validation error messages
            'customer_po.regex' => 'Customer PO can only contain letters, numbers, spaces, and - / _ .',
            'scg_po.regex' => 'SCG PO can only contain letters, numbers, spaces, and - / _ .',
            'scg_so.regex' => 'SCG SO can only contain letters, numbers, spaces, and - / _ .',
            'booking_number.regex' => 'Booking Number can only contain letters, numbers, spaces, and - / _ .',
            'delivery_note_number.regex' => 'Delivery Note can only contain letters, numbers, spaces, and - / _ .',
            'supplier_invoice.regex' => 'Supplier Invoice can only contain letters, numbers, spaces, and - / _ .',
        ]);

        DB::beginTransaction();
        try {
            // Create shipment
            $shipment = Shipment::create([
                'customer_id' => $validated['customer_id'],
                'supplier_id' => $validated['supplier_id'],
                'type' => $validated['type'],
                'created_by_user_id' => Auth::id(),
                'customer_po' => $validated['customer_po'] ?? null,
                'scg_po' => $validated['scg_po'] ?? null,
                'scg_so' => $validated['scg_so'] ?? null,
                'booking_number' => $validated['booking_number'] ?? null,
                'delivery_note_number' => $validated['delivery_note_number'] ?? null,
                'supplier_invoice' => $validated['supplier_invoice'] ?? null,
                'status' => 'Pending',
                'etd_port' => $validated['etd_port'],
                'eta_port' => $validated['eta_port'] ?? null,
                'ata_port' => $validated['ata_port'] ?? null,
                'customer_receiving_schedule' => $validated['customer_receiving_schedule'],
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'customs_cost' => $validated['customs_cost'] ?? 0,
                'other_costs' => $validated['other_costs'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Attach products (if any)
            if (!empty($validated['products'])) {
                foreach ($validated['products'] as $productData) {
                    $shipment->products()->attach($productData['product_id'], [
                        'quantity' => $productData['quantity'],
                        'unit_price' => $productData['unit_price'],
                    ]);
                }
            }

            // Log activity (FR-L-01)
            ActivityLog::logActivity(
                Auth::id(),
                $shipment->id,
                'created',
                null,
                'Shipment created',
                'New shipment created with PO: ' . ($validated['customer_po'] ?? 'N/A')
            );

            DB::commit();

            return redirect()->route('shipments.show', $shipment)
                ->with('success', 'Shipment created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create shipment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified shipment
     */
    public function show(Shipment $shipment)
    {
        $this->authorize('view', $shipment);

        $shipment->load(['customer', 'supplier', 'createdBy', 'products', 'activityLogs.user']);

        return view('shipments.show', compact('shipment'));
    }

    /**
     * Show the form for editing the specified shipment
     * NEW SYSTEM: Different forms for different roles
     * - Admin SCM: Limited monitoring form (edit-admin.blade.php)
     * - PIC Sales: Full edit form (edit.blade.php)
     */
    public function edit(Shipment $shipment)
    {
        // Check if user is Admin SCM (monitoring only)
        if (Auth::user()->isAdminSCM()) {
            $this->authorize('updateStatus', $shipment);
            return view('shipments.edit-admin', compact('shipment'));
        }
        
        // PIC Sales gets full edit access
        $this->authorize('update', $shipment);
        
        $customers = Customer::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with('supplier')->orderBy('name')->get();

        return view('shipments.edit', compact('shipment', 'customers', 'suppliers', 'products'));
    }

    /**
     * Update the specified shipment in database
     */
    public function update(Request $request, Shipment $shipment)
    {
        $this->authorize('update', $shipment);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:Import,Export',
            // Document numbers - must be unique globally (ignore current shipment)
            'customer_po' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,customer_po,' . $shipment->id . ',id,deleted_at,NULL',
            'scg_po' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,scg_po,' . $shipment->id . ',id,deleted_at,NULL',
            'scg_so' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,scg_so,' . $shipment->id . ',id,deleted_at,NULL',
            'booking_number' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,booking_number,' . $shipment->id . ',id,deleted_at,NULL',
            'delivery_note_number' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,delivery_note_number,' . $shipment->id . ',id,deleted_at,NULL',
            'supplier_invoice' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,supplier_invoice,' . $shipment->id . ',id,deleted_at,NULL',
            'status' => 'required|in:Pending,In Transit,Delivered,Cancelled',
            'etd_port' => 'required|date|after_or_equal:2020-01-01|before_or_equal:2035-12-31',
            'eta_port' => [
                'nullable',
                'date',
                'after_or_equal:etd_port',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('etd_port')) {
                        $etd = strtotime($request->etd_port);
                        $eta = strtotime($value);
                        if ($eta > $etd + (365 * 24 * 60 * 60)) {
                            $fail(__('The ETA Port must be at most 1 year (365 days) after ETD Port.'));
                        }
                    }
                }
            ],
            'ata_port' => [
                'required_if:status,Delivered',
                'nullable',
                'date',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request) {
                    $status = $request->status;
                    if (in_array($status, ['Pending', 'Cancelled']) && !empty($value)) {
                        $fail(__('Actual Time Arrival at Port (ATA Port) cannot be set for ' . $status . ' shipments.'));
                        return;
                    }
                    if (!empty($value)) {
                        $ataPort = strtotime($value);
                        if ($request->filled('etd_port')) {
                            $etd = strtotime($request->etd_port);
                            if ($ataPort < $etd) {
                                $fail(__('The Actual Time Arrival at Port (ATA Port) must be after or equal to Departure from Port (ETD Port).'));
                            }
                            if ($ataPort > $etd + (365 * 24 * 60 * 60)) {
                                $fail(__('The ATA Port must be at most 1 year (365 days) after ETD Port.'));
                            }
                        }
                    }
                }
            ],
            'customer_receiving_schedule' => [
                'required',
                'date',
                'after_or_equal:etd_port',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('etd_port')) {
                        $etd = strtotime($request->etd_port);
                        $schedule = strtotime($value);
                        if ($schedule > $etd + (365 * 24 * 60 * 60)) {
                            $fail(__('The Customer Receiving Schedule must be at most 1 year (365 days) after ETD Port.'));
                        }
                    }
                    if ($request->filled('eta_port')) {
                        $eta = strtotime($request->eta_port);
                        $schedule = strtotime($value);
                        if ($schedule < $eta) {
                            $fail(__('The Customer Receiving Schedule must be after or equal to ETA Port.'));
                        }
                    }
                }
            ],
            'ata_customer' => [
                'required_if:status,Delivered',
                'nullable',
                'date',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request) {
                    $status = $request->status;
                    if (in_array($status, ['Pending', 'In Transit', 'Cancelled']) && !empty($value)) {
                        $fail(__('Actual Time Arrival at Customer (ATA Customer) cannot be set for ' . $status . ' shipments.'));
                        return;
                    }
                    if (!empty($value)) {
                        $ataCustomer = strtotime($value);
                        if ($request->filled('etd_port')) {
                            $etd = strtotime($request->etd_port);
                            if ($ataCustomer > $etd + (365 * 24 * 60 * 60)) {
                                    $fail(__('The Actual Time Arrival at Customer (ATA Customer) must be at most 1 year (365 days) after ETD Port.'));
                                    return;
                            }
                        }
                        if ($request->filled('ata_port')) {
                            $ataPort = strtotime($request->ata_port);
                            if ($ataCustomer < $ataPort) {
                                $fail(__('The Actual Time Arrival at Customer (ATA Customer) must be after or equal to Actual Time Arrival at Port (ATA Port).'));
                            }
                        } elseif ($request->filled('etd_port')) {
                            $etd = strtotime($request->etd_port);
                            if ($ataCustomer < $etd) {
                                $fail(__('The Actual Time Arrival at Customer (ATA Customer) must be after or equal to Departure from Port (ETD Port).'));
                            }
                        }
                    }
                }
            ],
            'shipping_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'customs_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'other_costs' => 'nullable|numeric|min:0|max:999999999999.99',
            'notes' => [
                'nullable',
                'string',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The notes must not contain HTML or script tags.'));
                    }
                }
            ],
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:10000000',
            'products.*.unit_price' => 'required|numeric|min:0|max:999999999999.99',
        ], [
            'customer_id.required' => 'Customer is required',
            'supplier_id.required' => 'Supplier is required',
            'etd_port.required' => 'ETD Port date is required',
            'customer_receiving_schedule.required' => 'Customer receiving schedule is required',
            'customer_receiving_schedule.after_or_equal' => 'Customer receiving schedule must be after or equal to ETD Port',
            // Unique messages
            'customer_po.unique' => 'Customer PO already exists in the system',
            'scg_po.unique' => 'SCG PO already exists in the system',
            'scg_so.unique' => 'SCG SO already exists in the system',
            'booking_number.unique' => 'Booking Number already exists in the system',
            'delivery_note_number.unique' => 'Delivery Note Number already exists in the system',
            'supplier_invoice.unique' => 'Supplier Invoice already exists in the system',
            // Regex messages
            'customer_po.regex' => 'Customer PO can only contain letters, numbers, spaces, and - / _ .',
            'scg_po.regex' => 'SCG PO can only contain letters, numbers, spaces, and - / _ .',
            'scg_so.regex' => 'SCG SO can only contain letters, numbers, spaces, and - / _ .',
            'booking_number.regex' => 'Booking Number can only contain letters, numbers, spaces, and - / _ .',
            'delivery_note_number.regex' => 'Delivery Note can only contain letters, numbers, spaces, and - / _ .',
            'supplier_invoice.regex' => 'Supplier Invoice can only contain letters, numbers, spaces, and - / _ .',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $shipment->status;
            
            // Extract products from validated data
            $productsData = $validated['products'] ?? [];
            unset($validated['products']);
            
            $shipment->update($validated);

            // Sync products
            $syncData = [];
            foreach ($productsData as $product) {
                $syncData[$product['product_id']] = [
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price'],
                ];
            }
            $shipment->products()->sync($syncData);

            // Log status change if changed
            if ($oldStatus !== $validated['status']) {
                ActivityLog::logActivity(
                    Auth::id(),
                    $shipment->id,
                    'updated_status',
                    $oldStatus,
                    $validated['status'],
                    "Status changed from {$oldStatus} to {$validated['status']}"
                );
            }

            DB::commit();

            return redirect()->route('shipments.show', $shipment)
                ->with('success', 'Shipment updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update shipment: ' . $e->getMessage());
        }
    }

    /**
     * Update shipment status and monitoring fields (Admin SCM only)
     * NEW SYSTEM: Admin SCM can update monitoring fields only
     * Fields: Status, ATA Port, ATA Customer, Delivery Note, Supplier Invoice, All Costs
     */
    public function updateStatus(Request $request, Shipment $shipment)
    {
        $this->authorize('updateStatus', $shipment);
        $validated = $request->validate([
            'status' => 'required|in:Pending,In Transit,Delivered,Cancelled',
            'ata_port' => [
                'required_if:status,Delivered',
                'required_with:ata_customer',
                'nullable',
                'date',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request, $shipment) {
                    $targetStatus = $request->input('status') ?: $shipment->status;
                    if ($request->filled('ata_customer')) {
                        $targetStatus = 'Delivered';
                    }

                    if (in_array($targetStatus, ['Pending', 'Cancelled']) && !empty($value)) {
                        $fail(__('Actual Time Arrival at Port (ATA Port) cannot be set for ' . $targetStatus . ' shipments.'));
                        return;
                    }

                    if (!empty($value)) {
                        $ataPort = strtotime($value);
                        if ($shipment->etd_port) {
                            $etd = strtotime($shipment->etd_port);
                            if ($ataPort < $etd) {
                                $fail(__('The Actual Time Arrival at Port (ATA Port) must be after or equal to Departure from Port (ETD Port).'));
                                return;
                            }
                            if ($ataPort > $etd + (365 * 24 * 60 * 60)) {
                                $fail(__('The ATA Port must be at most 1 year (365 days) after ETD Port.'));
                                return;
                            }
                        }

                        // Check against ata_customer
                        if ($request->filled('ata_customer')) {
                            if ($ataPort > strtotime($request->ata_customer)) {
                                $fail(__('The Actual Time Arrival at Port (ATA Port) must be before or equal to Actual Time Arrival at Customer (ATA Customer).'));
                            }
                        } elseif ($shipment->ata_customer) {
                            if ($ataPort > strtotime($shipment->ata_customer)) {
                                $fail(__('The Actual Time Arrival at Port (ATA Port) must be before or equal to Actual Time Arrival at Customer (ATA Customer).'));
                            }
                        }
                    }
                }
            ],
            'ata_customer' => [
                'required_if:status,Delivered',
                'nullable',
                'date',
                'before_or_equal:2035-12-31',
                function ($attribute, $value, $fail) use ($request, $shipment) {
                    $targetStatus = $request->input('status') ?: $shipment->status;
                    if (!empty($value)) {
                        $targetStatus = 'Delivered';
                    }

                    if (in_array($targetStatus, ['Pending', 'In Transit', 'Cancelled']) && !empty($value)) {
                        $fail(__('Actual Time Arrival at Customer (ATA Customer) cannot be set for ' . $targetStatus . ' shipments.'));
                        return;
                    }

                    if (!empty($value)) {
                        $ataCustomer = strtotime($value);
                        if ($shipment->etd_port) {
                            $etd = strtotime($shipment->etd_port);
                            if ($ataCustomer > $etd + (365 * 24 * 60 * 60)) {
                                $fail(__('The Actual Time Arrival at Customer (ATA Customer) must be at most 1 year (365 days) after ETD Port.'));
                                return;
                            }
                        }
                        if ($request->filled('ata_port')) {
                            $ataPort = strtotime($request->ata_port);
                            if ($ataCustomer < $ataPort) {
                                $fail(__('The Actual Time Arrival at Customer (ATA Customer) must be after or equal to Actual Time Arrival at Port (ATA Port).'));
                            }
                        } elseif ($shipment->ata_port) {
                            if ($ataCustomer < strtotime($shipment->ata_port)) {
                                $fail(__('The Actual Time Arrival at Customer (ATA Customer) must be after or equal to Actual Time Arrival at Port (ATA Port).'));
                            }
                        } elseif ($shipment->etd_port) {
                            if ($ataCustomer < strtotime($shipment->etd_port)) {
                                $fail(__('The Actual Time Arrival at Customer (ATA Customer) must be after or equal to Departure from Port (ETD Port).'));
                            }
                        }
                    }
                }
            ],
            'delivery_note_number' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,delivery_note_number,' . $shipment->id . ',id,deleted_at,NULL',
            'supplier_invoice' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\/\-_\.\s]+$/|unique:shipments,supplier_invoice,' . $shipment->id . ',id,deleted_at,NULL',
            'shipping_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'customs_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'other_costs' => 'nullable|numeric|min:0|max:999999999999.99',
            'notes' => [
                'nullable',
                'string',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The notes must not contain HTML or script tags.'));
                    }
                }
            ],
        ], [
            'delivery_note_number.regex' => 'Delivery Note Number can only contain letters, numbers, spaces, and - / _ .',
            'supplier_invoice.regex' => 'Supplier Invoice can only contain letters, numbers, spaces, and - / _ .',
            'delivery_note_number.unique' => 'Delivery Note Number already exists in the system',
            'supplier_invoice.unique' => 'Supplier Invoice already exists in the system',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $shipment->status;
            $oldAtaCustomer = $shipment->ata_customer?->toDateString();

            // Auto-set status to Delivered if ata_customer is provided
            if (!empty($validated['ata_customer'])) {
                $shipment->status = 'Delivered';
                $shipment->ata_customer = $validated['ata_customer'];
            }

            // Update ATA Port
            if (isset($validated['ata_port'])) {
                $shipment->ata_port = $validated['ata_port'];
            }

            // Update status if provided and ata_customer is not set
            if (!empty($validated['status']) && empty($validated['ata_customer'])) {
                $shipment->status = $validated['status'];
            }

            // Update notes if provided (either text or cleared/empty)
            if (array_key_exists('notes', $validated)) {
                $shipment->notes = $validated['notes'];
            }

            // Update document numbers
            if (isset($validated['delivery_note_number'])) {
                $shipment->delivery_note_number = $validated['delivery_note_number'];
            }
            if (isset($validated['supplier_invoice'])) {
                $shipment->supplier_invoice = $validated['supplier_invoice'];
            }

            // Update costs
            if (isset($validated['shipping_cost'])) {
                $shipment->shipping_cost = $validated['shipping_cost'];
            }
            if (isset($validated['customs_cost'])) {
                $shipment->customs_cost = $validated['customs_cost'];
            }
            if (isset($validated['other_costs'])) {
                $shipment->other_costs = $validated['other_costs'];
            }

            $shipment->save();

            // Log activity
            $description = [];
            if ($oldStatus !== $shipment->status) {
                $description[] = "Status: {$oldStatus} → {$shipment->status}";
            }
            if ($oldAtaCustomer !== $shipment->ata_customer?->toDateString()) {
                $description[] = "ATA Customer: " . ($oldAtaCustomer ?? 'null') . " → " . ($shipment->ata_customer?->toDateString() ?? 'null');
            }

            if (!empty($description)) {
                ActivityLog::logActivity(
                    Auth::id(),
                    $shipment->id,
                    'updated_status',
                    $oldStatus,
                    $shipment->status,
                    implode(', ', $description)
                );
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Monitoring data updated successfully',
                    'shipment' => $shipment->fresh(['customer', 'supplier']),
                ]);
            }

            return redirect()->route('shipments.show', $shipment)
                ->with('success', 'Monitoring data updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update monitoring data: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to update monitoring data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified shipment from database (soft delete)
     */
    public function destroy(Shipment $shipment)
    {
        $this->authorize('delete', $shipment);

        try {
            ActivityLog::logActivity(
                Auth::id(),
                $shipment->id,
                'deleted',
                $shipment->status,
                'Deleted',
                'Shipment deleted'
            );

            $shipment->delete();

            return redirect()->route('shipments.index')
                ->with('success', 'Shipment deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete shipment: ' . $e->getMessage());
        }
    }
}
