<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
            Update Shipment Monitoring #{{ $shipment->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Info Alert --}}
            <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Admin SCM Mode:</strong>
                <span class="block sm:inline">You can only update monitoring fields (Status, ATA, Costs, Invoice, Delivery Note).</span>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Validation Errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-300">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Shipment Details
                        </a>
                    </div>

                    {{-- Read-Only Shipment Info --}}
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-scg-gray-dark dark:text-gray-200 mb-3">Shipment Information (Read-Only)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $shipment->customer->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Supplier:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $shipment->supplier->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Type:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $shipment->type }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Customer PO:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $shipment->customer_po ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">SCG PO:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $shipment->scg_po ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Booking Number:</span>
                                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $shipment->booking_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('shipments.update-status', $shipment) }}" method="POST" id="monitoringForm">
                        @csrf
                        @method('POST')

                        {{-- Status Update --}}
                        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-semibold text-scg-gray-dark dark:text-gray-200 mb-4">Status & Tracking</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        Status
                                    </label>
                                    <select name="status" id="status"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                        <option value="Pending" {{ old('status', $shipment->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="In Transit" {{ old('status', $shipment->status) == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="Delivered" {{ old('status', $shipment->status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="Cancelled" {{ old('status', $shipment->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="ata_port" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        ATA Port (Actual Time Arrival at Port)
                                    </label>
                                    <input type="date" name="ata_port" id="ata_port"
                                        value="{{ old('ata_port', $shipment->ata_port ? $shipment->ata_port->format('Y-m-d') : '') }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>

                                <div>
                                    <label for="ata_customer" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        ATA Customer (Actual Time Arrival at Customer)
                                    </label>
                                    <input type="date" name="ata_customer" id="ata_customer"
                                        value="{{ old('ata_customer', $shipment->ata_customer ? $shipment->ata_customer->format('Y-m-d') : '') }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Auto-sets status to "Delivered" when filled</p>
                                </div>
                            </div>
                        </div>

                        {{-- Document Numbers --}}
                        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-semibold text-scg-gray-dark dark:text-gray-200 mb-4">Document Numbers</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="delivery_note_number" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        Delivery Note Number
                                    </label>
                                    <input type="text" name="delivery_note_number" id="delivery_note_number"
                                        value="{{ old('delivery_note_number', $shipment->delivery_note_number) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>

                                <div>
                                    <label for="supplier_invoice" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        Supplier Invoice
                                    </label>
                                    <input type="text" name="supplier_invoice" id="supplier_invoice"
                                        value="{{ old('supplier_invoice', $shipment->supplier_invoice) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>
                            </div>
                        </div>

                        {{-- Cost Structure --}}
                        <div class="bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-semibold text-scg-gray-dark dark:text-gray-200 mb-4">Cost Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="shipping_cost" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        Shipping Cost
                                    </label>
                                    <input type="number" step="0.01" name="shipping_cost" id="shipping_cost"
                                        value="{{ old('shipping_cost', $shipment->shipping_cost) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>

                                <div>
                                    <label for="customs_cost" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        Customs Cost
                                    </label>
                                    <input type="number" step="0.01" name="customs_cost" id="customs_cost"
                                        value="{{ old('customs_cost', $shipment->customs_cost) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>

                                <div>
                                    <label for="other_costs" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        Other Costs
                                    </label>
                                    <input type="number" step="0.01" name="other_costs" id="other_costs"
                                        value="{{ old('other_costs', $shipment->other_costs) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex justify-end space-x-4 pt-4">
                            <a href="{{ route('shipments.show', $shipment) }}" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-6 rounded transition">
                                Cancel
                            </a>
                            <button type="submit" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-6 rounded transition">
                                Update Monitoring Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
