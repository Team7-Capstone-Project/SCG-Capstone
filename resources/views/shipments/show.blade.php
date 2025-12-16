<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
            Shipment Details - {{ $shipment->customer_po ?? 'N/A' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Shipment Information --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-scg-gray-dark mb-4">Shipment Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Customer</label>
                            <p class="mt-1 text-lg font-semibold">{{ $shipment->customer->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Supplier</label>
                            <p class="mt-1 text-lg font-semibold">{{ $shipment->supplier->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Status</label>
                            <p class="mt-1">
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'In Transit' => 'bg-blue-100 text-blue-800',
                                        'Delivered' => 'bg-green-100 text-green-800',
                                        'Cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $statusColors[$shipment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $shipment->status }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Customer PO</label>
                            <p class="mt-1">{{ $shipment->customer_po ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">SCG PO</label>
                            <p class="mt-1">{{ $shipment->scg_po ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Booking Number</label>
                            <p class="mt-1">{{ $shipment->booking_number ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dates & OTD --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-scg-gray-dark mb-4">Dates & OTD Tracking</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ETD Port</label>
                            <p class="mt-1">{{ $shipment->etd_port?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ETA Port</label>
                            <p class="mt-1">{{ $shipment->eta_port?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ATA Port</label>
                            <p class="mt-1">{{ $shipment->ata_port?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Customer Receiving Schedule</label>
                            <p class="mt-1 font-semibold text-blue-600">{{ $shipment->customer_receiving_schedule?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ATA Customer (Actual Delivery)</label>
                            <p class="mt-1 font-semibold {{ $shipment->ata_customer ? 'text-green-600' : '' }}">
                                {{ $shipment->ata_customer?->format('d M Y') ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">OTD Status</label>
                            <div class="mt-1">
                                @if($shipment->isDelivered())
                                    @php
                                        $daysDiff = $shipment->getDaysDifference();
                                        $daysText = $shipment->getDaysDifferenceText();
                                    @endphp
                                    
                                    @if($shipment->isOnTime())
                                        <div class="flex flex-col space-y-1">
                                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800 w-fit">
                                                ✓ On-Time
                                            </span>
                                            @if($daysDiff !== null && $daysDiff < 0)
                                                <span class="text-sm text-green-600 font-medium">
                                                    {{ $daysText }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif($shipment->isLate())
                                        <div class="flex flex-col space-y-1">
                                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800 w-fit">
                                                ✗ Late
                                            </span>
                                            @if($daysText)
                                                <span class="text-sm text-red-600 font-bold">
                                                    {{ $daysText }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">Pending Delivery</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-scg-gray-dark mb-4">Products</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-scg-gray-light">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Product Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Unit Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($shipment->products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->sku }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->pivot->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">Rp {{ number_format($product->pivot->unit_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                            Rp {{ number_format($product->pivot->quantity * $product->pivot->unit_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Cost Structure --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-scg-gray-dark mb-4">Cost Structure</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Shipping Cost</label>
                            <p class="mt-1 text-lg">Rp {{ number_format($shipment->shipping_cost, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Customs Cost</label>
                            <p class="mt-1 text-lg">Rp {{ number_format($shipment->customs_cost, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Other Costs</label>
                            <p class="mt-1 text-lg">Rp {{ number_format($shipment->other_costs, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Total Cost</label>
                            <p class="mt-1 text-xl font-bold text-scg-red">Rp {{ number_format($shipment->total_cost, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Update Status Form (Only for Staf SCM) --}}
            @can('updateStatus', $shipment)
                <div class="bg-blue-50 border border-blue-200 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-scg-gray-dark mb-4">Update Status (Staf SCM Only)</h3>
                        
                        <form action="{{ route('shipments.update-status', $shipment) }}" method="POST">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-scg-gray-dark mb-2">Status</label>
                                    <select name="status" id="status" class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                        <option value="">Keep Current</option>
                                        <option value="Pending" {{ $shipment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="In Transit" {{ $shipment->status == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="Delivered" {{ $shipment->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="Cancelled" {{ $shipment->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="ata_port" class="block text-sm font-medium text-scg-gray-dark mb-2">ATA Port</label>
                                    <input type="date" name="ata_port" id="ata_port" value="{{ $shipment->ata_port?->format('Y-m-d') }}"
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>
                                
                                <div>
                                    <label for="ata_customer" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        ATA Customer <span class="text-xs text-gray-500">(Auto-sets to Delivered)</span>
                                    </label>
                                    <input type="date" name="ata_customer" id="ata_customer" value="{{ $shipment->ata_customer?->format('Y-m-d') }}"
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>
                            </div>
                            
                            <button type="submit" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-6 rounded transition">
                                Update Status
                            </button>
                        </form>
                    </div>
                </div>
            @endcan

            {{-- Activity Log --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-scg-gray-dark mb-4">Activity Log</h3>
                    
                    @if($shipment->activityLogs->count() > 0)
                        <div class="space-y-4">
                            @foreach($shipment->activityLogs as $log)
                                <div class="border-l-4 border-scg-red pl-4 py-2">
                                    <p class="text-sm font-semibold text-scg-gray-dark">{{ $log->action }}</p>
                                    <p class="text-sm text-gray-600">{{ $log->description }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        By {{ $log->user->name }} • {{ $log->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No activity logs yet.</p>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-between">
                <a href="{{ route('shipments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded transition">
                    Back to List
                </a>
                
                <div class="space-x-4">
                    @can('update', $shipment)
                        <a href="{{ route('shipments.edit', $shipment) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                            Edit Shipment
                        </a>
                    @endcan
                    
                    @can('delete', $shipment)
                        <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this shipment?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition">
                                Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
