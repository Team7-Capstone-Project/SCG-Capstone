<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
            Customer Details
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Name</label>
                            <p class="mt-1 text-lg font-semibold text-scg-gray-dark">{{ $customer->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">PIC (Sales)</label>
                            <p class="mt-1 text-lg">{{ $customer->pic->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Contact Person</label>
                            <p class="mt-1">{{ $customer->contact_person ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Phone</label>
                            <p class="mt-1">{{ $customer->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email</label>
                            <p class="mt-1">{{ $customer->email ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Address</label>
                            <p class="mt-1">{{ $customer->address ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('customers.edit', $customer) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <a href="{{ route('customers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-scg-gray-dark mb-4">Shipments ({{ $customer->shipments->count() }})</h3>
                    @if($customer->shipments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-scg-gray-light">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Customer PO</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">ETD Port</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($customer->shipments as $shipment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shipment->customer_po }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs bg-{{ $shipment->status == 'Delivered' ? 'green' : ($shipment->status == 'Pending' ? 'yellow' : 'blue') }}-100">
                                                    {{ $shipment->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shipment->etd_port?->format('d M Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="text-scg-red hover:text-red-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No shipments yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
