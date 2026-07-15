<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Supplier::class);
        
        $suppliers = Supplier::latest()->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorize('create', Supplier::class);
        
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Supplier::class);
        
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100|regex:/^[a-zA-Z\s]+$/',
            'address' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The address must not contain HTML or script tags.'));
                    }
                }
            ],
            'contact_person' => 'required|string|min:3|max:100|regex:/^[a-zA-Z\s]+$/',
            'phone' => 'nullable|string|min:8|max:15|regex:/^[0-9+\s\(\)]+$/',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|min:2|max:60|regex:/^[a-zA-Z\s\.\-\(\)]+$/',
        ], [
            'name.regex' => 'Name can only contain letters and spaces.',
            'contact_person.regex' => 'Contact Person can only contain letters and spaces.',
            'phone.regex' => 'Phone can only contain numbers, spaces, plus signs, and parentheses.',
            'country.regex' => 'Country can only contain letters, spaces, dots, dashes, and parentheses.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    public function show(Supplier $supplier, Request $request)
    {
        $this->authorize('view', $supplier);
        
        $query = $supplier->shipments()->with(['customer']);
        
        $quickFilter = $request->input('quick_filter', 'all');
        
        if ($quickFilter == 'this_month') {
            $query->whereDate('etd_port', '>=', now()->startOfMonth()->toDateString())
                  ->whereDate('etd_port', '<=', now()->endOfMonth()->toDateString());
        } elseif ($quickFilter == 'prev_month') {
            $query->whereDate('etd_port', '>=', now()->subMonth()->startOfMonth()->toDateString())
                  ->whereDate('etd_port', '<=', now()->subMonth()->endOfMonth()->toDateString());
        } elseif ($quickFilter == 'custom') {
            if ($request->filled('start_date')) {
                $query->whereDate('etd_port', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('etd_port', '<=', $request->end_date);
            }
        }
        
        $shipments = $query->get();
        return view('suppliers.show', compact('supplier', 'shipments'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);
        
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('update', $supplier);
        
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100|regex:/^[a-zA-Z\s]+$/',
            'address' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The address must not contain HTML or script tags.'));
                    }
                }
            ],
            'contact_person' => 'required|string|min:3|max:100|regex:/^[a-zA-Z\s]+$/',
            'phone' => 'nullable|string|min:8|max:15|regex:/^[0-9+\s\(\)]+$/',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|min:2|max:60|regex:/^[a-zA-Z\s\.\-\(\)]+$/',
        ], [
            'name.regex' => 'Name can only contain letters and spaces.',
            'contact_person.regex' => 'Contact Person can only contain letters and spaces.',
            'phone.regex' => 'Phone can only contain numbers, spaces, plus signs, and parentheses.',
            'country.regex' => 'Country can only contain letters, spaces, dots, dashes, and parentheses.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', $supplier);
        
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }
}
