<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
                Shipments Management
            </h2>
            @can('create', App\Models\Shipment::class)
                <a href="{{ route('shipments.create') }}" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition">
                    + Create Shipment
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form id="filterForm" method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark mb-2">Search</label>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                                placeholder="PO, Booking Number..."
                                class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark mb-2">Status</label>
                            <select name="status" id="statusFilter" class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <option value="">All Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark mb-2">Customer</label>
                            <select name="customer_id" id="customerFilter" class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <option value="">All Customers</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="button" id="resetFilters" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded transition">
                                Reset
                            </button>
                            <button type="submit" class="w-1/2 bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition">
                                Apply
                            </button>
                        </div>
                    </form>
                    <div id="loadingIndicator" class="hidden mt-4 text-center">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-scg-red"></div>
                        <span class="ml-2 text-sm text-gray-600">Loading shipments...</span>
                    </div>
                </div>
            </div>

            {{-- Shipments Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($shipments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-scg-gray-light">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Customer PO</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">ETD Port</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">OTD</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($shipments as $shipment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ $shipment->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($shipment->isDelivered())
                                                    @if($shipment->isOnTime())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            ✓ On-Time
                                                        </span>
                                                    @elseif($shipment->isLate())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            ✗ Late
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="text-scg-red hover:text-red-900">View</a>
                                                @can('update', $shipment)
                                                    <a href="{{ route('shipments.edit', $shipment) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $shipments->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No shipments found. Try adjusting your filters or create a new shipment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const customerFilter = document.getElementById('customerFilter');
            const resetButton = document.getElementById('resetFilters');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const tableContainer = document.querySelector('.overflow-x-auto');
            const paginationContainer = document.querySelector('.mt-4');
            
            let debounceTimer;
            const debounceDelay = 500; // 500ms delay
            let isSubmitting = false;

            // Function to submit form with debounce
            function submitForm(e) {
                if (e && e.type === 'submit') {
                    e.preventDefault();
                }
                
                if (isSubmitting) return;
                
                clearTimeout(debounceTimer);
                loadingIndicator.classList.remove('hidden');
                isSubmitting = true;
                
                debounceTimer = setTimeout(() => {
                    // Update URL without page reload
                    const url = new URL(window.location.href);
                    const params = new URLSearchParams(new FormData(form));
                    url.search = params.toString();
                    
                    // Use fetch to get the HTML response
                    fetch(`${url.toString()}&ajax=1`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Parse the response and update the table
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTable = doc.querySelector('table');
                        const newPagination = doc.querySelector('.pagination');
                        const noResults = doc.querySelector('.text-gray-500');
                        
                        // Update the table content
                        if (tableContainer) {
                            if (newTable) {
                                tableContainer.innerHTML = '';
                                tableContainer.appendChild(newTable);
                            } else if (noResults) {
                                tableContainer.innerHTML = '';
                                tableContainer.appendChild(noResults);
                            }
                        }
                        
                        // Update pagination
                        if (paginationContainer) {
                            if (newPagination) {
                                paginationContainer.innerHTML = newPagination.innerHTML;
                            } else if (noResults) {
                                paginationContainer.innerHTML = '';
                                paginationContainer.parentNode.insertBefore(noResults, paginationContainer);
                            }
                        }
                        
                        // Update browser URL for SEO
                        window.history.pushState({}, '', url);
                        document.title = `Shipments - ${document.title.split(' - ').pop()}`;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Fallback to normal form submission if AJAX fails
                        form.submit();
                    })
                    .finally(() => {
                        loadingIndicator.classList.add('hidden');
                        isSubmitting = false;
                    });
                }, debounceDelay);
            }

            // Reset form
            function resetForm() {
                searchInput.value = '';
                statusFilter.value = '';
                customerFilter.value = '';
                submitForm();
            }

            // Event listeners
            form.addEventListener('submit', submitForm);
            resetButton.addEventListener('click', resetForm);
            
            // Auto-submit when filters change
            [statusFilter, customerFilter].forEach(select => {
                select.addEventListener('change', submitForm);
            });

            // Debounced search input
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(submitForm, debounceDelay);
            });

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                // Force a full page reload to handle back/forward navigation
                window.location.reload();
            });
        });
    </script>
    @endpush
</x-app-layout>
