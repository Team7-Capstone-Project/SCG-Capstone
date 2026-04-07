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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-scg-gray-dark mb-4">Shipments using this Product ({{ $product->shipments->count() }})</h3>
                    @if($product->shipments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-scg-gray-light">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Delivery Note</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($product->shipments as $shipment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shipment->customer->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shipment->supplier->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shipment->delivery_note_number ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ $shipment->pivot->quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs bg-{{ $shipment->status == 'Delivered' ? 'green' : ($shipment->status == 'Pending' ? 'yellow' : 'blue') }}-100">
                                                    {{ $shipment->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="text-scg-red hover:text-red-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">This product hasn't been included in any shipments yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
