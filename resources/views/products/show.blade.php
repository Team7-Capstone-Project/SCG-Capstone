<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
            Product Details
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">SKU</label>
                            <p class="mt-1 text-lg font-semibold text-scg-gray-dark">{{ $product->sku }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Name</label>
                            <p class="mt-1 text-lg font-semibold">{{ $product->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Supplier</label>
                            <p class="mt-1">{{ $product->supplier->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Unit Price</label>
                            <p class="mt-1 text-lg font-bold text-green-600">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Description</label>
                            <p class="mt-1">{{ $product->description ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <a href="{{ route('products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
