<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
                    PT SCG International Indonesia
                </h2>
                <p class="text-sm text-gray-600 mt-1">Supply Chain Management Dashboard</p>
            </div>
            @can('create', App\Models\Shipment::class)
                <a href="{{ route('shipments.create') }}" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-150">
                    + Create Shipment
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Metrics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                {{-- Total Shipments --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Shipments</p>
                                <p class="text-3xl font-bold text-scg-gray-dark mt-2">{{ $totalShipments }}</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivered Shipments --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Delivered Shipments</p>
                                <p class="text-3xl font-bold text-scg-gray-dark mt-2">{{ $deliveredShipments }}</p>
                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Late Shipments --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-scg-red">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Late Shipments</p>
                                <p class="text-3xl font-bold text-scg-red mt-2">{{ $lateShipments }}</p>
                            </div>
                            <div class="bg-red-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-scg-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- OTD Rate --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 {{ $otdRate >= 80 ? 'border-green-500' : ($otdRate >= 50 ? 'border-yellow-500' : 'border-scg-red') }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">On-Time Delivery Rate</p>
                                <p class="text-3xl font-bold {{ $otdRate >= 80 ? 'text-green-600' : ($otdRate >= 50 ? 'text-yellow-600' : 'text-scg-red') }} mt-2">
                                    {{ number_format($otdRate, 1) }}%
                                </p>
                            </div>
                            <div class="bg-{{ $otdRate >= 80 ? 'green' : ($otdRate >= 50 ? 'yellow' : 'red') }}-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-{{ $otdRate >= 80 ? 'green' : ($otdRate >= 50 ? 'yellow' : 'red') }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Recent Shipments Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-scg-gray-dark">Recent Shipments</h3>
                        <a href="{{ route('shipments.index') }}" class="text-scg-red hover:text-red-800 font-medium">
                            View All →
                        </a>
                    </div>

                    @if($recentShipments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-scg-gray-light">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Customer PO</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">ETD Port</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentShipments as $shipment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $shipment->customer_po ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $shipment->customer->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $shipment->supplier->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                                        'In Transit' => 'bg-blue-100 text-blue-800',
                                                        'Delivered' => 'bg-green-100 text-green-800',
                                                        'Cancelled' => 'bg-gray-100 text-gray-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$shipment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $shipment->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $shipment->etd_port?->format('d M Y') ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="text-scg-red hover:text-red-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No shipments found. Create your first shipment to get started.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
