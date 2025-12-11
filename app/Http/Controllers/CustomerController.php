<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('pic')->latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $picUsers = User::where('role', 'pic_sales')->orWhere('role', 'admin')->get();
        return view('customers.create', compact('picUsers'));
    }

    public function store(Request $request)
    {
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
        $customer->load('pic', 'shipments');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $picUsers = User::where('role', 'pic_sales')->orWhere('role', 'admin')->get();
        return view('customers.edit', compact('customer', 'picUsers'));
    }

    public function update(Request $request, Customer $customer)
    {
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
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully!');
    }
}
