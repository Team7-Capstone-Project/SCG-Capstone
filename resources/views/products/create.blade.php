<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
            Add New Product
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <strong>Validation Errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="sku" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    SKU <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50"
                                    placeholder="e.g., CHM-001">
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Product Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Description
                                </label>
                                <textarea name="description" id="description" rows="3"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">{{ old('description') }}</textarea>
                            </div>

                            <div>
                                <label for="unit_price" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Unit Price (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" required
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50"
                                    placeholder="0.00">
                            </div>

                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Supplier
                                </label>
                                <select name="supplier_id" id="supplier_id"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded transition">
                                Cancel
                            </a>
                            <button type="submit" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-6 rounded transition">
                                Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
