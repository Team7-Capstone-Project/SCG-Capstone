<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Product::class);
        
        $products = Product::with('supplier')->latest()->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        
        $suppliers = Supplier::orderBy('name')->get();
        return view('products.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);
        
        $this->cleanPriceInputs($request);
        
        $validated = $request->validate([
            'sku' => 'required|string|min:3|max:30|regex:/^[a-zA-Z0-9\-_]+$/|unique:products,sku',
            'name' => 'required|string|min:3|max:100|regex:/^[a-zA-Z0-9\s\.,&\'\-\(\)\/\+]+$/',
            'description' => [
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The description must not contain HTML or script tags.'));
                    }
                }
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'unit_price' => 'required|numeric|min:0|max:999999999999.99',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ], [
            'sku.regex' => 'SKU can only contain letters, numbers, dashes, and underscores.',
            'name.regex' => 'Product Name can only contain letters, numbers, spaces, dots, commas, ampersands, quotes, dashes, slashes, pluses, and parentheses.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product, Request $request)
    {
        $this->authorize('view', $product);
        
        $query = $product->shipments()->with(['supplier', 'customer']);
        
        $quickFilter = $request->input('quick_filter', 'all');
        
        if ($quickFilter == 'this_month') {
            $query->whereDate('shipments.etd_port', '>=', now()->startOfMonth()->toDateString())
                  ->whereDate('shipments.etd_port', '<=', now()->endOfMonth()->toDateString());
        } elseif ($quickFilter == 'prev_month') {
            $query->whereDate('shipments.etd_port', '>=', now()->subMonth()->startOfMonth()->toDateString())
                  ->whereDate('shipments.etd_port', '<=', now()->subMonth()->endOfMonth()->toDateString());
        } elseif ($quickFilter == 'custom') {
            if ($request->filled('start_date')) {
                $query->whereDate('shipments.etd_port', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('shipments.etd_port', '<=', $request->end_date);
            }
        }
        
        $shipments = $query->get();
        return view('products.show', compact('product', 'shipments'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        
        $suppliers = Supplier::orderBy('name')->get();
        return view('products.edit', compact('product', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        
        $this->cleanPriceInputs($request);
        
        $validated = $request->validate([
            'sku' => 'required|string|min:3|max:30|regex:/^[a-zA-Z0-9\-_]+$/|unique:products,sku,' . $product->id,
            'name' => 'required|string|min:3|max:100|regex:/^[a-zA-Z0-9\s\.,&\'\-\(\)\/\+]+$/',
            'description' => [
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if ($value !== strip_tags($value)) {
                        $fail(__('The description must not contain HTML or script tags.'));
                    }
                }
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'unit_price' => 'required|numeric|min:0|max:999999999999.99',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ], [
            'sku.regex' => 'SKU can only contain letters, numbers, dashes, and underscores.',
            'name.regex' => 'Product Name can only contain letters, numbers, spaces, dots, commas, ampersands, quotes, dashes, slashes, pluses, and parentheses.',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle remove image checkbox
        if ($request->boolean('remove_image') && !$request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        // Delete product image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    protected function cleanPriceInputs(Request $request)
    {
        if ($request->has('unit_price') && is_string($request->input('unit_price'))) {
            $value = $request->input('unit_price');
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            $request->merge(['unit_price' => $value]);
        }
    }
}
