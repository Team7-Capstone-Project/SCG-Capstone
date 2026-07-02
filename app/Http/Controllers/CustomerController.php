<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Customer::class);
        
        $customers = Customer::latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $this->authorize('create', Customer::class);
        
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);
        
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255|regex:/^[a-zA-Z0-9\s\.,&\'\-\(\)]+$/',
            'address' => [
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The address must not contain HTML or script tags.'));
                    }
                }
            ],
            'contact_person' => 'nullable|string|min:2|max:100|regex:/^[a-zA-Z\s\.\'\-]+$/',
            'phone' => 'nullable|string|min:8|max:15|regex:/^[0-9+\s\(\)]+$/',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|min:2|max:60|regex:/^[a-zA-Z\s\.\-\(\)]+$/',
        ], [
            'name.regex' => 'Name can only contain letters, numbers, spaces, dots, commas, ampersands, quotes, dashes, and parentheses.',
            'contact_person.regex' => 'Contact Person can only contain letters, spaces, dots, quotes, and dashes.',
            'phone.regex' => 'Phone can only contain numbers, spaces, plus signs, and parentheses.',
            'country.regex' => 'Country can only contain letters, spaces, dots, dashes, and parentheses.',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully!');
    }

    public function show(Customer $customer, Request $request)
    {
        $this->authorize('view', $customer);
        
        $query = $customer->shipments()->with(['supplier']);
        
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
        return view('customers.show', compact('customer', 'shipments'));
    }

    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);
        
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255|regex:/^[a-zA-Z0-9\s\.,&\'\-\(\)]+$/',
            'address' => [
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The address must not contain HTML or script tags.'));
                    }
                }
            ],
            'contact_person' => 'nullable|string|min:2|max:100|regex:/^[a-zA-Z\s\.\'\-]+$/',
            'phone' => 'nullable|string|min:8|max:15|regex:/^[0-9+\s\(\)]+$/',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|min:2|max:60|regex:/^[a-zA-Z\s\.\-\(\)]+$/',
        ], [
            'name.regex' => 'Name can only contain letters, numbers, spaces, dots, commas, ampersands, quotes, dashes, and parentheses.',
            'contact_person.regex' => 'Contact Person can only contain letters, spaces, dots, quotes, and dashes.',
            'phone.regex' => 'Phone can only contain numbers, spaces, plus signs, and parentheses.',
            'country.regex' => 'Country can only contain letters, spaces, dots, dashes, and parentheses.',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully!');
    }
}
