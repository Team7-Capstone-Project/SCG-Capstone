<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
                    PT SCG International Indonesia
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Supply Chain Management Dashboard') }}</p>
            </div>
            @can('create', App\Models\Shipment::class)
                <a href="{{ route('shipments.create') }}" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-150">
                    + {{ __('Create Shipment') }}
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
                <a href="{{ route('shipments.index') }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border-l-4 border-blue-500 hover:bg-blue-50 dark:hover:bg-gray-700 transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl animate-fade-in-up delay-100">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Shipments') }}</p>
                                <p class="text-4xl font-extrabold text-gray-800 dark:text-white mt-2">{{ $totalShipments }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 rounded-2xl p-4 shadow-sm">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Delivered Shipments --}}
                <a href="{{ route('shipments.index', ['status' => 'Delivered']) }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border-l-4 border-green-500 hover:bg-green-50 dark:hover:bg-gray-700 transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl animate-fade-in-up delay-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Delivered Shipments') }}</p>
                                <p class="text-4xl font-extrabold text-gray-800 dark:text-white mt-2">{{ $deliveredShipments }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900 dark:to-green-800 rounded-2xl p-4 shadow-sm">
                                <svg class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Late Shipments --}}
                <a href="{{ route('shipments.index', ['late' => 1]) }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border-l-4 border-scg-red hover:bg-red-50 dark:hover:bg-gray-700 transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl animate-fade-in-up delay-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Late Shipments') }}</p>
                                <p class="text-4xl font-extrabold text-scg-red dark:text-red-400 mt-2">{{ $lateShipments }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900 dark:to-red-800 rounded-2xl p-4 shadow-sm">
                                <svg class="w-8 h-8 text-scg-red dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- OTD Rate --}}
                <a href="{{ route('shipments.index', ['on_time' => 1]) }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border-l-4 {{ $otdRate >= 80 ? 'border-green-500' : ($otdRate >= 50 ? 'border-yellow-500' : 'border-scg-red') }} hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl animate-fade-in-up delay-400">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('On-Time Delivery Rate') }}</p>
                                <p class="text-4xl font-extrabold {{ $otdRate >= 80 ? 'text-green-600 dark:text-green-400' : ($otdRate >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-scg-red dark:text-red-400') }} mt-2">
                                    {{ number_format($otdRate, 1) }}%
                                </p>
                            </div>
                            <div class="bg-gradient-to-br from-{{ $otdRate >= 80 ? 'green' : ($otdRate >= 50 ? 'yellow' : 'red') }}-100 to-{{ $otdRate >= 80 ? 'green' : ($otdRate >= 50 ? 'yellow' : 'red') }}-200 dark:from-{{ $otdRate >= 80 ? 'green' : ($otdRate >= 50 ? 'yellow' : 'red') }}-900 dark:to-{{ $otdRate >= 80 ? 'green' : ($otdRate >= 50 ? 'yellow' : 'red') }}-800 rounded-2xl p-4 shadow-sm">
                                <svg class="w-8 h-8 text-{{ $otdRate >= 80 ? 'green' : ($otdRate >= 50 ? 'yellow' : 'red') }}-600 dark:text-{{ $otdRate >= 80 ? 'green' : ($otdRate >= 50 ? 'yellow' : 'red') }}-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

            </div>

            {{-- Recent Shipments Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-scg-gray-dark dark:text-gray-200">{{ __('Recent Shipments') }}</h3>
                        <a href="{{ route('shipments.index') }}" class="text-scg-red dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">
                            {{ __('View All') }} →
                        </a>
                    </div>

                    @if($recentShipments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-scg-gray-light dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Customer PO') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Customer') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Supplier') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('ETD Port') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentShipments as $shipment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $shipment->customer_po ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $shipment->customer->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $shipment->supplier->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                        'In Transit' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                        'Delivered' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                        'Cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$shipment->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                    {{ __($shipment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $shipment->etd_port?->format('d M Y') ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="text-scg-red dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">{{ __('View') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('No shipments found. Create your first shipment to get started.') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
