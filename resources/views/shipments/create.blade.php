<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
            Create New Shipment
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
                    <form action="{{ route('shipments.store') }}" method="POST" id="shipmentForm">
                        @csrf

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
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                <input type="text" name="customer_po" id="customer_po" value="{{ old('customer_po') }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <p id="customer_po_warning" class="text-yellow-600 text-sm mt-1 hidden">⚠️ This PO number may already exist</p>
                            </div>

                            <div>
                                <label for="scg_po" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    SCG PO
                                </label>
                                <input type="text" name="scg_po" id="scg_po" value="{{ old('scg_po') }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <p id="scg_po_warning" class="text-yellow-600 text-sm mt-1 hidden">⚠️ This PO number may already exist</p>
                            </div>

                            <div>
                                <label for="booking_number" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Booking Number
                                </label>
                                <input type="text" name="booking_number" id="booking_number" value="{{ old('booking_number') }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>
                        </div>

                        {{-- Critical Dates for OTD --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-semibold text-scg-gray-dark mb-4">Critical Dates for OTD Tracking</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="etd_port" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        ETD Port (Estimated Time Departure) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="etd_port" id="etd_port" value="{{ old('etd_port') }}" required
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                </div>

                                <div>
                                    <label for="eta_port" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        ETA Port (Estimated Time Arrival)
                                    </label>
                                    <input type="date" name="eta_port" id="eta_port" value="{{ old('eta_port') }}"
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <p id="eta_error" class="text-red-500 text-sm mt-1 hidden">❌ ETA must be after or equal to ETD</p>
                                </div>

                                <div>
                                    <label for="customer_receiving_schedule" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                        Customer Receiving Schedule <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="customer_receiving_schedule" id="customer_receiving_schedule" value="{{ old('customer_receiving_schedule') }}" required
                                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <p id="schedule_error" class="text-red-500 text-sm mt-1 hidden">❌ Schedule must be after or equal to ETA</p>
                                </div>
                            </div>
                        </div>

                        {{-- Cost Structure --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="shipping_cost" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Shipping Cost
                                </label>
                                <input type="number" step="0.01" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', 0) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="customs_cost" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Customs Cost
                                </label>
                                <input type="number" step="0.01" name="customs_cost" id="customs_cost" value="{{ old('customs_cost', 0) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="other_costs" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Other Costs
                                </label>
                                <input type="number" step="0.01" name="other_costs" id="other_costs" value="{{ old('other_costs', 0) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>
                        </div>

                        {{-- Products Section --}}
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-semibold text-scg-gray-dark">Products <span class="text-red-500">*</span></h3>
                                <button type="button" id="addProduct" class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded transition">
                                    + Add Product
                                </button>
                            </div>

                            <div id="productsContainer">
                                {{-- Product rows will be added here dynamically --}}
                            </div>
                            <p id="products_error" class="text-red-500 text-sm mt-2 hidden">❌ At least one product is required</p>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('shipments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded transition">
                                Cancel
                            </a>
                            <button type="submit" id="submitBtn" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-6 rounded transition">
                                Create Shipment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Product template
        let productIndex = 0;
        const products = @json($products);

        // Add first product row on page load
        document.addEventListener('DOMContentLoaded', function() {
            addProductRow();
        });

        // Add product row
        document.getElementById('addProduct').addEventListener('click', function() {
            addProductRow();
        });

        function addProductRow() {
            const container = document.getElementById('productsContainer');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-4 mb-3 product-row';
            row.innerHTML = `
                <div class="col-span-5">
                    <select name="products[${productIndex}][product_id]" required class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                        <option value="">Select Product</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.unit_price}">${p.name} (${p.sku})</option>`).join('')}
                    </select>
                </div>
                <div class="col-span-3">
                    <input type="number" name="products[${productIndex}][quantity]" placeholder="Quantity" min="1" required
                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 product-quantity">
                </div>
                <div class="col-span-3">
                    <input type="number" step="0.01" name="products[${productIndex}][unit_price]" placeholder="Unit Price" min="0" required
                        class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 product-price">
                </div>
                <div class="col-span-1 flex items-center">
                    <button type="button" class="remove-product text-red-600 hover:text-red-800 font-bold">✕</button>
                </div>
            `;
            container.appendChild(row);

            // Auto-fill price when product selected
            row.querySelector('select').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                if (price) {
                    row.querySelector('.product-price').value = price;
                }
            });

            // Remove product row
            row.querySelector('.remove-product').addEventListener('click', function() {
                if (document.querySelectorAll('.product-row').length > 1) {
                    row.remove();
                    validateProducts();
                }
            });

            productIndex++;
            validateProducts();
        }

        // VALIDATION 1: Date Logic Validation
        const etdInput = document.getElementById('etd_port');
        const etaInput = document.getElementById('eta_port');
        const scheduleInput = document.getElementById('customer_receiving_schedule');
        const etaError = document.getElementById('eta_error');
        const scheduleError = document.getElementById('schedule_error');

        function validateDates() {
            const etd = new Date(etdInput.value);
            const eta = new Date(etaInput.value);
            const schedule = new Date(scheduleInput.value);

            let isValid = true;

            // ETA must be >= ETD
            if (etaInput.value && etdInput.value && eta < etd) {
                etaError.classList.remove('hidden');
                etaInput.classList.add('border-red-500');
                isValid = false;
            } else {
                etaError.classList.add('hidden');
                etaInput.classList.remove('border-red-500');
            }

            // Schedule must be >= ETA
            if (scheduleInput.value && etaInput.value && schedule < eta) {
                scheduleError.classList.remove('hidden');
                scheduleInput.classList.add('border-red-500');
                isValid = false;
            } else {
                scheduleError.classList.add('hidden');
                scheduleInput.classList.remove('border-red-500');
            }

            return isValid;
        }

        etdInput.addEventListener('change', validateDates);
        etaInput.addEventListener('change', validateDates);
        scheduleInput.addEventListener('change', validateDates);

        // VALIDATION 2: Duplicate PO Number Check (AJAX)
        const customerPoInput = document.getElementById('customer_po');
        const scgPoInput = document.getElementById('scg_po');
        let poCheckTimeout;

        customerPoInput.addEventListener('input', function() {
            clearTimeout(poCheckTimeout);
            if (this.value.length > 0) {
                poCheckTimeout = setTimeout(() => checkDuplicatePO('customer_po', this.value), 500);
            } else {
                document.getElementById('customer_po_warning').classList.add('hidden');
            }
        });

        scgPoInput.addEventListener('input', function() {
            clearTimeout(poCheckTimeout);
            if (this.value.length > 0) {
                poCheckTimeout = setTimeout(() => checkDuplicatePO('scg_po', this.value), 500);
            } else {
                document.getElementById('scg_po_warning').classList.add('hidden');
            }
        });

        function checkDuplicatePO(field, value) {
            // Simulated AJAX check - in production, this would call a backend endpoint
            // For now, we'll just show the warning as a demonstration
            const warningEl = document.getElementById(field + '_warning');
            
            // Example: You would implement actual AJAX call here
            // fetch(`/api/check-po?field=${field}&value=${value}`)
            //     .then(response => response.json())
            //     .then(data => {
            //         if (data.exists) {
            //             warningEl.classList.remove('hidden');
            //         } else {
            //             warningEl.classList.add('hidden');
            //         }
            //     });
        }

        // VALIDATION 3: Product Quantity Validation
        function validateProducts() {
            const productRows = document.querySelectorAll('.product-row');
            const productsError = document.getElementById('products_error');
            
            if (productRows.length === 0) {
                productsError.classList.remove('hidden');
                return false;
            } else {
                productsError.classList.add('hidden');
            }

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

        // Form submission validation
        document.getElementById('shipmentForm').addEventListener('submit', function(e) {
            const datesValid = validateDates();
            const productsValid = validateProducts();

            if (!datesValid || !productsValid) {
                e.preventDefault();
                alert('Please fix the validation errors before submitting.');
                return false;
            }
        });

        // Real-time product validation
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('product-quantity')) {
                validateProducts();
            }
        });
    </script>
    @endpush
</x-app-layout>
