<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
            Edit Shipment #{{ $shipment->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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
                            {{ __('Back to Shipment Details') }}
                        </a>
                    </div>

                    <form action="{{ route('shipments.update', $shipment) }}" method="POST" id="shipmentForm">
                        @csrf
                        @method('PUT')

                        {{-- Customer & Supplier Information --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Type <span class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('type') border-red-500 @enderror">
                                    <option value="Import" {{ old('type', $shipment->type) == 'Import' ? 'selected' : '' }}>Import</option>
                                    <option value="Export" {{ old('type', $shipment->type) == 'Export' ? 'selected' : '' }}>Export</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Customer <span class="text-red-500">*</span>
                                </label>
                                <select name="customer_id" id="customer_id" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('customer_id') border-red-500 @enderror">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $shipment->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <select name="supplier_id" id="supplier_id" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('supplier_id') border-red-500 @enderror">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $shipment->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Document Numbers --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="customer_po" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Customer PO
                                </label>
                                <input type="text" name="customer_po" id="customer_po" value="{{ old('customer_po', $shipment->customer_po) }}"
                                    minlength="10" maxlength="15"
                                    pattern="^[a-zA-Z0-9\/\-_\.\s]+$"
                                    title="Only alphanumeric, space, and symbols - / _ . are allowed (10-15 characters)."
                                    placeholder="e.g. PO-12345/ABC"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('customer_po') border-red-500 @enderror">
                                @error('customer_po')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                                <p id="customer_po_warning" class="text-yellow-600 text-sm mt-1 hidden">⚠️ This PO number may already exist</p>
                            </div>

                            <div>
                                <label for="scg_po" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    SCG PO
                                </label>
                                <input type="text" name="scg_po" id="scg_po" value="{{ old('scg_po', $shipment->scg_po) }}"
                                    minlength="10" maxlength="15"
                                    pattern="^[a-zA-Z0-9\/\-_\.\s]+$"
                                    title="Only alphanumeric, space, and symbols - / _ . are allowed (10-15 characters)."
                                    placeholder="e.g. SCGPO-98765"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('scg_po') border-red-500 @enderror">
                                @error('scg_po')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                                <p id="scg_po_warning" class="text-yellow-600 text-sm mt-1 hidden">⚠️ This PO number may already exist</p>
                            </div>

                            <div>
                                <label for="booking_number" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Booking Number
                                </label>
                                <input type="text" name="booking_number" id="booking_number" value="{{ old('booking_number', $shipment->booking_number) }}"
                                    minlength="10" maxlength="15"
                                    pattern="^[a-zA-Z0-9\/\-_\.\s]+$"
                                    title="Only alphanumeric, space, and symbols - / _ . are allowed (10-15 characters)."
                                    placeholder="e.g. BKG-1010-01"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('booking_number') border-red-500 @enderror">
                                @error('booking_number')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Critical Dates for OTD --}}
                        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-semibold text-scg-gray-dark dark:text-gray-200 mb-4">Critical Dates for OTD Tracking</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('status') border-red-500 @enderror">
                                        <option value="Pending" {{ old('status', $shipment->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="In Transit" {{ old('status', $shipment->status) == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="Delivered" {{ old('status', $shipment->status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="Cancelled" {{ old('status', $shipment->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="etd_port" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        ETD Port (Estimated Time Departure) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="etd_port" id="etd_port"
                                        value="{{ old('etd_port', $shipment->etd_port ? $shipment->etd_port->format('Y-m-d') : '') }}" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('etd_port') border-red-500 @enderror">
                                    @error('etd_port')
                                        <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="eta_port" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        ETA Port (Estimated Time Arrival)
                                    </label>
                                    <input type="date" name="eta_port" id="eta_port"
                                        value="{{ old('eta_port', $shipment->eta_port ? $shipment->eta_port->format('Y-m-d') : '') }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('eta_port') border-red-500 @enderror">
                                    <p id="eta_error" class="text-red-500 text-sm mt-1 hidden">❌ ETA must be after or equal to ETD</p>
                                    @error('eta_port')
                                        <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ata_port" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        ATA Port (Actual Time Arrival)
                                    </label>
                                    <input type="date" name="ata_port" id="ata_port"
                                        value="{{ old('ata_port', $shipment->ata_port ? $shipment->ata_port->format('Y-m-d') : '') }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('ata_port') border-red-500 @enderror">
                                    @error('ata_port')
                                        <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="customer_receiving_schedule" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        Customer Receiving Schedule <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="customer_receiving_schedule" id="customer_receiving_schedule"
                                        value="{{ old('customer_receiving_schedule', $shipment->customer_receiving_schedule ? $shipment->customer_receiving_schedule->format('Y-m-d') : '') }}" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('customer_receiving_schedule') border-red-500 @enderror">
                                    <p id="schedule_error" class="text-red-500 text-sm mt-1 hidden">❌ Schedule must be after or equal to ETA</p>
                                    @error('customer_receiving_schedule')
                                        <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ata_customer" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                        ATA Customer (Actual Time Arrival at Customer)
                                    </label>
                                    <input type="date" name="ata_customer" id="ata_customer"
                                        value="{{ old('ata_customer', $shipment->ata_customer ? $shipment->ata_customer->format('Y-m-d') : '') }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('ata_customer') border-red-500 @enderror">
                                    @error('ata_customer')
                                        <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Additional Document Numbers --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="scg_so" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    SCG SO
                                </label>
                                <input type="text" name="scg_so" id="scg_so" value="{{ old('scg_so', $shipment->scg_so) }}"
                                    minlength="10" maxlength="15"
                                    pattern="^[a-zA-Z0-9\/\-_\.\s]+$"
                                    title="Only alphanumeric, space, and symbols - / _ . are allowed (10-15 characters)."
                                    placeholder="e.g. SO-112233-01"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('scg_so') border-red-500 @enderror">
                                @error('scg_so')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="supplier_invoice" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Supplier Invoice
                                </label>
                                <input type="text" name="supplier_invoice" id="supplier_invoice"
                                    value="{{ old('supplier_invoice', $shipment->supplier_invoice) }}"
                                    minlength="10" maxlength="15"
                                    pattern="^[a-zA-Z0-9\/\-_\.\s]+$"
                                    title="Only alphanumeric, space, and symbols - / _ . are allowed (10-15 characters)."
                                    placeholder="e.g. INV-99999-01"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('supplier_invoice') border-red-500 @enderror">
                                @error('supplier_invoice')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="delivery_note_number" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Delivery Note Number
                                </label>
                                <input type="text" name="delivery_note_number" id="delivery_note_number"
                                    value="{{ old('delivery_note_number', $shipment->delivery_note_number) }}"
                                    minlength="10" maxlength="15"
                                    pattern="^[a-zA-Z0-9\/\-_\.\s]+$"
                                    title="Only alphanumeric, space, and symbols - / _ . are allowed (10-15 characters)."
                                    placeholder="e.g. DN-88888-001"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('delivery_note_number') border-red-500 @enderror">
                                @error('delivery_note_number')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Cost Structure --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="shipping_cost" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Shipping Cost
                                </label>
                                <input type="text" name="shipping_cost" id="shipping_cost"
                                    value="{{ old('shipping_cost', $shipment->shipping_cost) }}"
                                    data-max="999999999.99"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 price-input">
                            </div>

                            <div>
                                <label for="customs_cost" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Customs Cost
                                </label>
                                <input type="text" name="customs_cost" id="customs_cost"
                                    value="{{ old('customs_cost', $shipment->customs_cost) }}"
                                    data-max="999999999.99"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 price-input">
                            </div>

                            <div>
                                <label for="other_costs" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                    Other Costs
                                </label>
                                <input type="text" name="other_costs" id="other_costs"
                                    value="{{ old('other_costs', $shipment->other_costs) }}"
                                    data-max="999999999.99"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 price-input">
                            </div>
                        </div>

                        {{-- Products Section --}}
                        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-semibold text-scg-gray-dark dark:text-gray-200">Products</h3>
                                <button type="button" onclick="addProductRow()" class="inline-flex items-center justify-center gap-1.5 bg-gradient-to-r from-red-600 to-red-700 dark:from-red-600 dark:to-red-700 hover:from-red-700 hover:to-red-800 dark:hover:from-red-500 dark:hover:to-red-600 text-white font-bold py-1.5 px-4 rounded-xl transition-all duration-300 shadow-md shadow-red-600/10 hover:shadow-lg hover:shadow-red-600/20 transform hover:-translate-y-0.5 text-sm">
                                    + Add Product
                                </button>
                            </div>

                            <div id="productsContainer">
                                {{-- Product rows will be added here dynamically --}}
                                @foreach($shipment->products as $index => $product)
                                <div class="grid grid-cols-12 gap-4 mb-3 product-row p-3 bg-white dark:bg-gray-700/50 rounded shadow-sm border border-gray-100 dark:border-gray-600">
                                    <div class="col-span-12 md:col-span-5">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Select Product</label>
                                        <select name="products[{{ $index }}][product_id]" required onchange="onProductChange(this, {{ $index }})" 
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                            <option value="">Select Product...</option>
                                            @foreach($products as $p)
                                                @if($p->supplier_id == $shipment->supplier_id)
                                                <option value="{{ $p->id }}" data-price="{{ $p->unit_price }}" {{ $p->id == $product->id ? 'selected' : '' }}>
                                                    {{ $p->sku ? "[$p->sku] " : '' }}{{ $p->name }}
                                                </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-5 md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Quantity</label>
                                        <input type="number" name="products[{{ $index }}][quantity]" value="{{ $product->pivot->quantity }}" placeholder="Qty" min="1" required
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 product-quantity">
                                    </div>
                                    <div class="col-span-5 md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Unit Price (IDR)</label>
                                        <input type="text" name="products[{{ $index }}][unit_price]" id="price_{{ $index }}" value="{{ $product->pivot->unit_price }}" placeholder="Price" required
                                            data-max="999999999999.99"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 product-price price-input">
                                    </div>
                                    <div class="col-span-2 md:col-span-1 flex items-end pb-3">
                                        <button type="button" onclick="removeProductRow(this)" title="Remove" class="remove-product text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400 font-bold p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">{{ old('notes', $shipment->notes) }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4">
                            <a href="{{ route('shipments.index') }}" class="inline-flex items-center justify-center gap-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-6 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 text-sm">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 dark:from-red-600 dark:to-red-700 hover:from-red-700 hover:to-red-800 dark:hover:from-red-500 dark:hover:to-red-600 text-white font-bold py-2.5 px-6 rounded-xl transition-all duration-300 shadow-md shadow-red-600/10 hover:shadow-lg hover:shadow-red-600/20 transform hover:-translate-y-0.5 text-sm">
                                {{ __('Update Shipment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Product data config
        let productIndex = {{ $shipment->products->count() }};
        const products = @json($products);
        const supplierSelect = document.getElementById('supplier_id');

        // Add product row
        function addProductRow() {
            const container = document.getElementById('productsContainer');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-4 mb-3 product-row p-3 bg-white dark:bg-gray-700/50 rounded shadow-sm border border-gray-100 dark:border-gray-600 animate-fade-in-down';
            
            const selectedSupplierId = supplierSelect.value;
            const filteredProducts = selectedSupplierId 
                ? products.filter(p => p.supplier_id == selectedSupplierId) 
                : products;

            row.innerHTML = `
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Select Product</label>
                    <select name="products[${productIndex}][product_id]" required onchange="onProductChange(this, ${productIndex})" 
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                        <option value="">Select Product...</option>
                        ${filteredProducts.map(p => `
                            <option value="${p.id}" data-price="${p.unit_price}">
                                ${p.sku ? `[${p.sku}] ` : ''}${p.name}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-span-5 md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Quantity</label>
                    <input type="number" name="products[${productIndex}][quantity]" placeholder="Qty" min="1" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 product-quantity">
                </div>
                <div class="col-span-5 md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Unit Price (IDR)</label>
                    <input type="text" name="products[${productIndex}][unit_price]" id="price_${productIndex}" placeholder="Price" required
                        data-max="999999999999.99"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 product-price price-input">
                </div>
                <div class="col-span-2 md:col-span-1 flex items-end pb-3">
                    <button type="button" onclick="removeProductRow(this)" title="Remove" class="remove-product text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400 font-bold p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            `;
            container.appendChild(row);
            productIndex++;
            validateProducts();
        }

        // Handle product selection change
        function onProductChange(select, index) {
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const priceInput = document.getElementById('price_' + index);
            if (price && priceInput) {
                priceInput.value = price;
                priceInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }

        // Remove product row
        function removeProductRow(button) {
            const row = button.closest('.product-row');
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
                validateProducts();
            } else {
                alert('At least one product is required.');
            }
        }

        // Supplier filter logic
        supplierSelect.addEventListener('change', function() {
            const container = document.getElementById('productsContainer');
            if (container.querySelectorAll('.product-row').length > 0) {
                if (confirm('Changing supplier will reset your current product selection. Continue?')) {
                    container.innerHTML = '';
                    productIndex = 0;
                    addProductRow();
                } else {
                    // This would ideally revert the supplier change, but simple for now
                }
            } else {
                addProductRow();
            }
        });

        // Date validation logic
        const etdInput = document.getElementById('etd_port');
        const etaInput = document.getElementById('eta_port');
        const scheduleInput = document.getElementById('customer_receiving_schedule');
        const ataPortInput = document.getElementById('ata_port');
        const ataCustomerInput = document.getElementById('ata_customer');
        const etaError = document.getElementById('eta_error');
        const scheduleError = document.getElementById('schedule_error');

        function validateDates() {
            const etd = etdInput.value ? new Date(etdInput.value) : null;
            const eta = etaInput.value ? new Date(etaInput.value) : null;
            const schedule = scheduleInput.value ? new Date(scheduleInput.value) : null;

            let isValid = true;

            if (eta && etd && eta < etd) {
                etaError.classList.remove('hidden');
                etaInput.classList.add('border-red-500');
                isValid = false;
            } else {
                etaError.classList.add('hidden');
                etaInput.classList.remove('border-red-500');
            }

            if (schedule && eta && schedule < eta) {
                scheduleError.classList.remove('hidden');
                scheduleInput.classList.add('border-red-500');
                isValid = false;
            } else {
                scheduleError.classList.add('hidden');
                scheduleInput.classList.remove('border-red-500');
            }

            return isValid;
        }

        [etdInput, etaInput, scheduleInput].forEach(inp => inp.addEventListener('change', validateDates));

        // Product quantity validation
        function validateProducts() {
            const productRows = document.querySelectorAll('.product-row');
            let allValid = true;
            productRows.forEach(row => {
                const quantity = row.querySelector('.product-quantity');
                if (quantity && (quantity.value === '' || parseInt(quantity.value) < 1)) {
                    quantity.classList.add('border-red-500');
                    allValid = false;
                } else if (quantity) {
                    quantity.classList.remove('border-red-500');
                }
            });
            return allValid;
        }

        // Form submission
        document.getElementById('shipmentForm').addEventListener('submit', function(e) {
            const datesValid = validateDates();
            const productsValid = validateProducts();
            
            if (!datesValid || !productsValid) {
                e.preventDefault();
                alert('Please fix the validation errors before submitting.');
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
