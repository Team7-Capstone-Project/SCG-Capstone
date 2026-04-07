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

        if ($request->filled('on_time')) {
            $query->onTime();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_po', 'like', "%{$search}%")
                    ->orWhere('scg_po', 'like', "%{$search}%")
                    ->orWhere('scg_so', 'like', "%{$search}%")
                    ->orWhere('booking_number', 'like', "%{$search}%")
                    ->orWhere('supplier_invoice', 'like', "%{$search}%")
                    ->orWhere('delivery_note_number', 'like', "%{$search}%");
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
        $customers = Customer::orderBy('name')->get();

        return view('shipments.index', compact('shipments', 'customers'));
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
            // Document numbers - must be unique globally
            'customer_po' => 'nullable|string|max:255|unique:shipments,customer_po,NULL,id,deleted_at,NULL',
            'scg_po' => 'nullable|string|max:255|unique:shipments,scg_po,NULL,id,deleted_at,NULL',
            'scg_so' => 'nullable|string|max:255|unique:shipments,scg_so,NULL,id,deleted_at,NULL',
            'booking_number' => 'nullable|string|max:255|unique:shipments,booking_number,NULL,id,deleted_at,NULL',
            'delivery_note_number' => 'nullable|string|max:255|unique:shipments,delivery_note_number,NULL,id,deleted_at,NULL',
            'supplier_invoice' => 'nullable|string|max:255|unique:shipments,supplier_invoice,NULL,id,deleted_at,NULL',
            'etd_port' => 'required|date',
            'eta_port' => 'nullable|date|after_or_equal:etd_port',
            'ata_port' => 'nullable|date',
            'customer_receiving_schedule' => 'required|date|after_or_equal:eta_port',
            'shipping_cost' => 'nullable|numeric|min:0',
            'customs_cost' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ], [
            'customer_id.required' => 'Customer is required',
            'supplier_id.required' => 'Supplier is required',
            'etd_port.required' => 'ETD Port date is required',
            'customer_receiving_schedule.required' => 'Customer receiving schedule is required',
            'customer_receiving_schedule.after_or_equal' => 'Customer receiving schedule must be after or equal to ETA Port',
            // Unique validation error messages
            'customer_po.unique' => 'Customer PO already exists in the system',
            'scg_po.unique' => 'SCG PO already exists in the system',
            'scg_so.unique' => 'SCG SO already exists in the system',
            'booking_number.unique' => 'Booking Number already exists in the system',
            'delivery_note_number.unique' => 'Delivery Note Number already exists in the system',
            'supplier_invoice.unique' => 'Supplier Invoice already exists in the system',
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
            'customer_po' => 'nullable|string|max:255|unique:shipments,customer_po,' . $shipment->id . ',id,deleted_at,NULL',
            'scg_po' => 'nullable|string|max:255|unique:shipments,scg_po,' . $shipment->id . ',id,deleted_at,NULL',
            'scg_so' => 'nullable|string|max:255|unique:shipments,scg_so,' . $shipment->id . ',id,deleted_at,NULL',
            'booking_number' => 'nullable|string|max:255|unique:shipments,booking_number,' . $shipment->id . ',id,deleted_at,NULL',
            'delivery_note_number' => 'nullable|string|max:255|unique:shipments,delivery_note_number,' . $shipment->id . ',id,deleted_at,NULL',
            'supplier_invoice' => 'nullable|string|max:255|unique:shipments,supplier_invoice,' . $shipment->id . ',id,deleted_at,NULL',
            'status' => 'required|in:Pending,In Transit,Delivered,Cancelled',
            'etd_port' => 'required|date',
            'eta_port' => 'nullable|date|after_or_equal:etd_port',
            'ata_port' => 'nullable|date',
            'customer_receiving_schedule' => 'required|date',
            'ata_customer' => 'nullable|date',
            'shipping_cost' => 'nullable|numeric|min:0',
            'customs_cost' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $shipment->status;
            $shipment->update($validated);

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
            'status' => 'nullable|in:Pending,In Transit,Delivered,Cancelled',
            'ata_port' => 'nullable|date',
            'ata_customer' => 'nullable|date',
            'delivery_note_number' => 'nullable|string|max:255',
            'supplier_invoice' => 'nullable|string|max:255',
            'shipping_cost' => 'nullable|numeric|min:0',
            'customs_cost' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $shipment->status;
            $oldAtaCustomer = $shipment->ata_customer?->format('Y-m-d');

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
            if ($oldAtaCustomer !== $shipment->ata_customer?->format('Y-m-d')) {
                $description[] = "ATA Customer: " . ($oldAtaCustomer ?? 'null') . " → " . ($shipment->ata_customer?->format('Y-m-d') ?? 'null');
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
