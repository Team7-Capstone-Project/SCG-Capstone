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
                    ->orWhere('booking_number', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        if ($request->filled('sort') && $request->sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
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
            'customer_po' => 'nullable|string|max:255',
            'scg_po' => 'nullable|string|max:255',
            'scg_so' => 'nullable|string|max:255',
            'booking_number' => 'nullable|string|max:255',
            'delivery_note_number' => 'nullable|string|max:255',
            'supplier_invoice' => 'nullable|string|max:255',
            'etd_port' => 'required|date',
            'eta_port' => 'nullable|date|after_or_equal:etd_port',
            'ata_port' => 'nullable|date',
            'customer_receiving_schedule' => 'required|date|after_or_equal:eta_port',
            'shipping_cost' => 'nullable|numeric|min:0',
            'customs_cost' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ], [
            'customer_id.required' => 'Customer is required',
            'supplier_id.required' => 'Supplier is required',
            'etd_port.required' => 'ETD Port date is required',
            'customer_receiving_schedule.required' => 'Customer receiving schedule is required',
            'customer_receiving_schedule.after_or_equal' => 'Customer receiving schedule must be after or equal to ETA Port',
            'products.required' => 'At least one product is required',
            'products.min' => 'At least one product is required',
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

            // Attach products
            foreach ($validated['products'] as $productData) {
                $shipment->products()->attach($productData['product_id'], [
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                ]);
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
     */
    public function edit(Shipment $shipment)
    {
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
            'customer_po' => 'nullable|string|max:255',
            'scg_po' => 'nullable|string|max:255',
            'scg_so' => 'nullable|string|max:255',
            'booking_number' => 'nullable|string|max:255',
            'delivery_note_number' => 'nullable|string|max:255',
            'supplier_invoice' => 'nullable|string|max:255',
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
     * Update shipment status (FR-ST-03)
     * Critical method for Staf SCM to update status and ata_customer
     * Auto-sets status to 'Delivered' when ata_customer is filled
     */
    public function updateStatus(Request $request, Shipment $shipment)
    {
        $this->authorize('updateStatus', $shipment);

        $validated = $request->validate([
            'status' => 'nullable|in:Pending,In Transit,Delivered,Cancelled',
            'ata_port' => 'nullable|date',
            'ata_customer' => 'nullable|date',
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

            if (!empty($validated['ata_port'])) {
                $shipment->ata_port = $validated['ata_port'];
            }

            if (!empty($validated['status']) && empty($validated['ata_customer'])) {
                $shipment->status = $validated['status'];
            }

            $shipment->save();

            // Log activity (FR-L-01)
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
                    'message' => 'Status updated successfully',
                    'shipment' => $shipment->fresh(['customer', 'supplier']),
                ]);
            }

            return redirect()->route('shipments.show', $shipment)
                ->with('success', 'Status updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
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
