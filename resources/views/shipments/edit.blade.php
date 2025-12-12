<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('shipments.update', $shipment) }}" method="POST" id="shipmentForm">
                        @csrf
                        @method('PUT')

                        {{-- Customer & Supplier Information --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Customer <span class="text-red-500">*</span>
                                </label>
                                <select name="customer_id" id="customer_id" required
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $shipment->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <select name="supplier_id" id="supplier_id" required
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $shipment->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Document Numbers --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="customer_po" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Customer PO
                                </label>
                                <input type="text" name="customer_po" id="customer_po" value="{{ old('customer_po', $shipment->customer_po) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <p id="customer_po_warning" class="text-yellow-600 text-sm mt-1 hidden">⚠️ This PO number may already exist</p>
                            </div>

                            <div>
                                <label for="scg_po" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    SCG PO
                                </label>
                                <input type="text" name="scg_po" id="scg_po" value="{{ old('scg_po', $shipment->scg_po) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <p id="scg_po_warning" class="text-yellow-600 text-sm mt-1 hidden">⚠️ This PO number may already exist</p>
                            </div>

                            <div>
                                <label for="booking_number" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Booking Number
                                </label>
                                <input type="text" name="booking_number" id="booking_number" value="{{ old('booking_number', $shipment->booking_number) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>
                        </div>

                        {{-- Critical Dates for OTD --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-semibold text-scg-gray-dark mb-4">Critical Dates for OTD Tracking</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" required
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                        <option value="Pending" {{ old('status', $shipment->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="In Transit" {{ old('status', $shipment->status) == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="Delivered" {{ old('status', $shipment->status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="Cancelled" {{ old('status', $shipment->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="etd_port" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        ETD Port (Estimated Time Departure) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="etd_port" id="etd_port" 
                                        value="{{ old('etd_port', $shipment->etd_port ? $shipment->etd_port->format('Y-m-d') : '') }}" required
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>

                                <div>
                                    <label for="eta_port" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        ETA Port (Estimated Time Arrival)
                                    </label>
                                    <input type="date" name="eta_port" id="eta_port" 
                                        value="{{ old('eta_port', $shipment->eta_port ? $shipment->eta_port->format('Y-m-d') : '') }}"
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <p id="eta_error" class="text-red-500 text-sm mt-1 hidden">❌ ETA must be after or equal to ETD</p>
                                </div>

                                <div>
                                    <label for="ata_port" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        ATA Port (Actual Time Arrival)
                                    </label>
                                    <input type="date" name="ata_port" id="ata_port" 
                                        value="{{ old('ata_port', $shipment->ata_port ? $shipment->ata_port->format('Y-m-d') : '') }}"
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>

                                <div>
                                    <label for="customer_receiving_schedule" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        Customer Receiving Schedule <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="customer_receiving_schedule" id="customer_receiving_schedule" 
                                        value="{{ old('customer_receiving_schedule', $shipment->customer_receiving_schedule ? $shipment->customer_receiving_schedule->format('Y-m-d') : '') }}" required
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <p id="schedule_error" class="text-red-500 text-sm mt-1 hidden">❌ Schedule must be after or equal to ETA</p>
                                </div>

                                <div>
                                    <label for="ata_customer" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        ATA Customer (Actual Time Arrival at Customer)
                                    </label>
                                    <input type="date" name="ata_customer" id="ata_customer" 
                                        value="{{ old('ata_customer', $shipment->ata_customer ? $shipment->ata_customer->format('Y-m-d') : '') }}"
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>
                            </div>
                        </div>

                        {{-- Additional Document Numbers --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="delivery_note_number" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Delivery Note Number
                                </label>
                                <input type="text" name="delivery_note_number" id="delivery_note_number" 
                                    value="{{ old('delivery_note_number', $shipment->delivery_note_number) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="supplier_invoice" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Supplier Invoice
                                </label>
                                <input type="text" name="supplier_invoice" id="supplier_invoice" 
                                    value="{{ old('supplier_invoice', $shipment->supplier_invoice) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>
                        </div>

                        {{-- Cost Structure --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="shipping_cost" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Shipping Cost
                                </label>
                                <input type="number" step="0.01" name="shipping_cost" id="shipping_cost" 
                                    value="{{ old('shipping_cost', $shipment->shipping_cost) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="customs_cost" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Customs Cost
                                </label>
                                <input type="number" step="0.01" name="customs_cost" id="customs_cost" 
                                    value="{{ old('customs_cost', $shipment->customs_cost) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="other_costs" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Other Costs
                                </label>
                                <input type="number" step="0.01" name="other_costs" id="other_costs" 
                                    value="{{ old('other_costs', $shipment->other_costs) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">{{ old('notes', $shipment->notes) }}</textarea>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex justify-between items-center">
                            <div>
                                <a href="{{ route('shipments.show', $shipment) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    ← Back to Shipment Details
                                </a>
                            </div>
                            <div class="space-x-4">
                                <a href="{{ route('shipments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded transition">
                                    Cancel
                                </a>
                                <button type="submit" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-6 rounded transition">
                                    Update Shipment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Date validation
        const etdInput = document.getElementById('etd_port');
        const etaInput = document.getElementById('eta_port');
        const scheduleInput = document.getElementById('customer_receiving_schedule');
        const ataPortInput = document.getElementById('ata_port');
        const ataCustomerInput = document.getElementById('ata_customer');
        const etaError = document.getElementById('eta_error');
        const scheduleError = document.getElementById('schedule_error');

        function validateDates() {
            const etd = new Date(etdInput.value);
            const eta = etaInput.value ? new Date(etaInput.value) : null;
            const schedule = scheduleInput.value ? new Date(scheduleInput.value) : null;
            const ataPort = ataPortInput.value ? new Date(ataPortInput.value) : null;
            const ataCustomer = ataCustomerInput.value ? new Date(ataCustomerInput.value) : null;

            let isValid = true;

            // ETA must be >= ETD if both are provided
            if (eta && etdInput.value && eta < etd) {
                etaError.classList.remove('hidden');
                etaInput.classList.add('border-red-500');
                isValid = false;
            } else {
                etaError.classList.add('hidden');
                etaInput.classList.remove('border-red-500');
            }

            // Schedule must be >= ETA if both are provided
            if (schedule && eta && schedule < eta) {
                scheduleError.classList.remove('hidden');
                scheduleInput.classList.add('border-red-500');
                isValid = false;
            } else {
                scheduleError.classList.add('hidden');
                scheduleInput.classList.remove('border-red-500');
            }

            // ATA Port should be after ETD if both are provided
            if (ataPort && etdInput.value && ataPort < etd) {
                // Show error or warning if needed
                ataPortInput.classList.add('border-yellow-500');
            } else {
                ataPortInput.classList.remove('border-yellow-500');
            }

            // ATA Customer should be after ATA Port if both are provided
            if (ataCustomer && ataPort && ataCustomer < ataPort) {
                // Show error or warning if needed
                ataCustomerInput.classList.add('border-yellow-500');
            } else {
                ataCustomerInput.classList.remove('border-yellow-500');
            }

            return isValid;
        }

        // Add event listeners for date validation
        [etdInput, etaInput, scheduleInput, ataPortInput, ataCustomerInput].forEach(input => {
            if (input) {
                input.addEventListener('change', validateDates);
            }
        });

        // Form submission validation
        document.getElementById('shipmentForm').addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
                alert('Please fix the date validation errors before submitting.');
                return false;
            }
            return true;
        });
    </script>
    @endpush
</x-app-layout>
