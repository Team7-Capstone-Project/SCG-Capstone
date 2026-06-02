<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
                {{ __('Shipments Management') }}
            </h2>
            <div class="flex space-x-2">
                {{-- Export Excel: Available for all roles who can view shipments --}}
                @can('viewAny', App\Models\Shipment::class)
                    <a id="exportButton" href="{{ route('shipments.export', request()->query()) }}" download class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition shadow-md flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('Export Excel') }}
                    </a>
                @endcan
                
                {{-- Create Shipment: Only for PIC Sales --}}
                @can('create', App\Models\Shipment::class)
                    <a href="{{ route('shipments.create') }}" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition shadow-md">
                        + {{ __('Create Shipment') }}
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 transition-colors duration-300">
                <div class="p-6">
                    <form id="filterForm" method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">{{ __('Search') }}</label>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                placeholder="PO, Booking Number..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                            <select name="status" id="statusFilter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>{{ __('In Transit') }}</option>
                                <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">{{ __('Type') }}</label>
                            <select name="type" id="typeFilter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <option value="">{{ __('All Types') }}</option>
                                <option value="Import" {{ request('type') == 'Import' ? 'selected' : '' }}>{{ __('Import') }}</option>
                                <option value="Export" {{ request('type') == 'Export' ? 'selected' : '' }}>{{ __('Export') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-scg-gray-dark dark:text-gray-300 mb-2">{{ __('Sort By') }}</label>
                            <select name="sort" id="sortFilter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('Date Created (Newest)') }}</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Date Created (Oldest)') }}</option>
                                <option value="month_asc" {{ request('sort') == 'month_asc' ? 'selected' : '' }}>{{ __('Departure/ETD (Earliest)') }}</option>
                                <option value="month_desc" {{ request('sort') == 'month_desc' ? 'selected' : '' }}>{{ __('Departure/ETD (Latest)') }}</option>
                                <option value="deadline_asc" {{ request('sort') == 'deadline_asc' ? 'selected' : '' }}>{{ __('Receiving Schedule (Earliest)') }}</option>
                                <option value="deadline_desc" {{ request('sort') == 'deadline_desc' ? 'selected' : '' }}>{{ __('Receiving Schedule (Latest)') }}</option>
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="button" id="resetFilters" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded transition">
                                {{ __('Reset') }}
                            </button>
                            <button type="submit" class="w-1/2 bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition">
                                {{ __('Apply') }}
                            </button>
                        </div>
                    </form>
                    <div id="loadingIndicator" class="hidden mt-4 text-center">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-scg-red"></div>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Loading shipments...') }}</span>
                    </div>
                </div>
            </div>

            {{-- Shipments Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-300">
                <div class="p-6" id="shipmentsTableContainer">
                    @if($shipments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-scg-gray-light dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('No.') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Customer PO') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Customer') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('ETD Port') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('ETA Port') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Customer Receiving Schedule') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('OTD') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase tracking-wider">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($shipments as $shipment)
                                        <tr onclick="window.location='{{ route('shipments.show', $shipment) }}'" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200 animate-fade-in-up cursor-pointer" style="animation-delay: {{ $loop->index * 50 }}ms">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ ($shipments->currentPage() - 1) * $shipments->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $shipment->customer_po ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $shipment->customer->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $shipment->etd_port?->format('d M Y') ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $shipment->eta_port?->format('d M Y') ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $shipment->customer_receiving_schedule?->format('d M Y') ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($shipment->isDelivered())
                                                    @php
                                                        $daysDiff = $shipment->getDaysDifference();
                                                        $daysText = $shipment->getDaysDifferenceText();
                                                    @endphp
 
                                                    @if($shipment->isOnTime())
                                                        <div class="flex flex-col">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                ✓ {{ __('Ideal') }}
                                                            </span>
                                                        </div>
                                                    @elseif($shipment->isEarly())
                                                        <div class="flex flex-col">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200">
                                                                ✓ {{ __('Early') }}
                                                            </span>
                                                            @if($daysDiff !== null)
                                                                <span class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                                                     {{ abs($daysDiff) }} {{ __('days early') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @elseif($shipment->isLate())
                                                        <div class="flex flex-col">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                ✗ {{ __('Late') }}
                                                            </span>
                                                            @if($daysDiff !== null)
                                                                <span class="text-xs text-red-600 dark:text-red-400 mt-1 font-medium">
                                                                    {{ abs($daysDiff) }} {{ __('days late') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">{{ __('Pending') }}</span>
                                                @endif
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
                                                @can('updateStatus', $shipment)
                                                    <div class="flex flex-col gap-1.5" onclick="event.stopPropagation()">
                                                        <form action="{{ route('shipments.update-status', $shipment) }}" method="POST" class="inline">
                                                            @csrf
                                                            <select name="status" onchange="this.form.submit()" class="rounded-full text-xs font-semibold py-0.5 pl-2.5 pr-8 border {{ $statusColors[$shipment->status] ?? 'bg-gray-100 text-gray-800' }} focus:outline-none focus:ring-1 focus:ring-scg-red cursor-pointer">
                                                                <option value="Pending" {{ $shipment->status == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                                <option value="In Transit" {{ $shipment->status == 'In Transit' ? 'selected' : '' }}>{{ __('In Transit') }}</option>
                                                                <option value="Delivered" {{ $shipment->status == 'Delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                                                <option value="Cancelled" {{ $shipment->status == 'Cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                                            </select>
                                                        </form>
                                                        
                                                        <form action="{{ route('shipments.update-status', $shipment) }}" method="POST" class="inline flex items-center">
                                                            @csrf
                                                            <input type="text" name="notes" value="{{ $shipment->notes }}" placeholder="{{ __('Location/Note...') }}" onkeydown="if(event.key === 'Enter') { this.form.submit(); }" class="text-[10px] px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-scg-red w-32 shadow-sm">
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$shipment->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                        {{ __($shipment->status) }}
                                                    </span>
                                                    @if($shipment->notes)
                                                        <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 max-w-[150px] truncate" title="{{ $shipment->notes }}">
                                                            📝 {{ $shipment->notes }}
                                                        </div>
                                                    @endif
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
                        <div class="flex flex-col items-center justify-center py-12 px-4 text-center animate-fade-in">
                            <!-- Beautiful Animated SVG Icon with gradient -->
                            <div class="relative mb-6">
                                <div class="absolute inset-0 bg-gradient-to-tr from-scg-red/20 to-red-500/10 rounded-full blur-xl opacity-60 dark:opacity-40 animate-pulse"></div>
                                <div class="relative bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-700 flex items-center justify-center transition-colors duration-300">
                                    <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Title -->
                            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">
                                {{ __('No Matching Shipments') }}
                            </h3>
                            
                            <!-- Description -->
                            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mb-6">
                                {{ __('We couldn\'t find any shipments matching your search, filter, or sorting criteria. Try resetting them to see all shipments.') }}
                            </p>
                            
                            <!-- Action Button -->
                            <button type="button" onclick="document.getElementById('resetFilters').click()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-scg-red to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.2"></path>
                                </svg>
                                {{ __('Reset Filters') }}
                            </button>
                        </div>
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
            const shipmentsTableContainer = document.getElementById('shipmentsTableContainer');

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
                        // Parse the response and update the container
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('shipmentsTableContainer');

                        if (shipmentsTableContainer && newContent) {
                            shipmentsTableContainer.innerHTML = newContent.innerHTML;
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
