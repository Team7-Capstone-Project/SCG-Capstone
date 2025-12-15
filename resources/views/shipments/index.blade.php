<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
                Shipments Management
            </h2>
            @can('create', App\Models\Shipment::class)
                <div class="flex space-x-2">
                    <a id="exportButton" href="{{ route('shipments.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition shadow-md flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </a>
                    <a href="{{ route('shipments.create') }}" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition shadow-md">
                        + Create Shipment
                    </a>
                </div>
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 transition-colors duration-300">
                <div class="p-6">
                    <form id="filterForm" method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">Search</label>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                placeholder="PO, Booking Number..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">Status</label>
                            <select name="status" id="statusFilter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <option value="">All Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">Type</label>
                            <select name="type" id="typeFilter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <option value="">All Types</option>
                                <option value="Import" {{ request('type') == 'Import' ? 'selected' : '' }}>Import</option>
                                <option value="Export" {{ request('type') == 'Export' ? 'selected' : '' }}>Export</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">Sort By</label>
                            <select name="sort" id="sortFilter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
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
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Loading shipments...</span>
                    </div>
                </div>
            </div>

            {{-- Shipments Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-300">
                <div class="p-6">
                    @if($shipments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-scg-gray-light dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">Customer PO</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">ETD Port</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">OTD</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($shipments as $shipment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200 animate-fade-in-up" style="animation-delay: {{ $loop->index * 50 }}ms">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                #{{ $shipment->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
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
                                                    {{ $shipment->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $shipment->etd_port?->format('d M Y') ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($shipment->isDelivered())
                                                    @if($shipment->isOnTime())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            ✓ On-Time
                                                        </span>
                                                    @elseif($shipment->isLate())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
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
                                                <a href="{{ route('shipments.show', $shipment) }}" class="text-scg-red dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">View</a>
                                                @can('update', $shipment)
                                                    <a href="{{ route('shipments.edit', $shipment) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">Edit</a>
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
            const typeFilter = document.getElementById('typeFilter');
            const sortFilter = document.getElementById('sortFilter');
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

                    // Update Export Button URL
                    const exportButton = document.getElementById('exportButton');
                    if (exportButton) {
                        try {
                            const currentExportUrl = new URL(exportButton.href);
                            currentExportUrl.search = params.toString();
                            exportButton.href = currentExportUrl.toString();
                        } catch (e) {
                            console.error('Error updating export URL', e);
                        }
                    }

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
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        loadingIndicator.classList.add('hidden');
                        isSubmitting = false;
                        history.pushState({}, '', url);
                    });
                }, 300);
            }

            // Debounce the search input
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(submitForm, debounceDelay);
            });

            // Filter changes
            statusFilter.addEventListener('change', submitForm);
            if(typeFilter) typeFilter.addEventListener('change', submitForm);
            sortFilter.addEventListener('change', submitForm);

            // Reset filters button
            document.getElementById('resetFilters').addEventListener('click', function(e) {
                e.preventDefault();
                // Reset all form fields
                searchInput.value = '';
                statusFilter.selectedIndex = 0;
                if(typeFilter) typeFilter.selectedIndex = 0;
                sortFilter.selectedIndex = 0;

                // Submit the form
                const formAction = form.getAttribute('action').split('?')[0];
                window.location.href = formAction;
            });
        });
    </script>
    @endpush
</x-app-layout>
