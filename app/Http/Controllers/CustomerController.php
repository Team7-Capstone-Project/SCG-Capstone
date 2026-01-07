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
        
        $customers = Customer::with('pic')->latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $this->authorize('create', Customer::class);
        
        $picUsers = User::where('role', 'pic_sales')->orWhere('role', 'admin')->get();
        return view('customers.create', compact('picUsers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully!');
    }

    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);
        
        $customer->load('pic', 'shipments');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        
        $picUsers = User::where('role', 'pic_sales')->orWhere('role', 'admin')->get();
        return view('customers.edit', compact('customer', 'picUsers'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pic_user_id' => 'nullable|exists:users,id',
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
