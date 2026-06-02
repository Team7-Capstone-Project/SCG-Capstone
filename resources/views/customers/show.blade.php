<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
            Customer Details
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Customer</label>
                            <p class="mt-1 text-lg font-semibold text-scg-gray-dark">{{ $customer->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Country</label>
                            <p class="mt-1 text-lg">{{ $customer->country ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Name</label>
                            <p class="mt-1 text-lg font-semibold text-scg-gray-dark">{{ $customer->contact_person ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Phone</label>
                            <p class="mt-1">{{ $customer->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email</label>
                            <p class="mt-1">{{ $customer->email ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Address</label>
                            <p class="mt-1">{{ $customer->address ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('customers.edit', $customer) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Edit') }}
                        </a>
                        @can('delete', $customer)
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this customer? This is a fatal action and will delete all associated shipments.') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        @endcan
                        <a href="{{ route('customers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        <h3 class="text-lg font-semibold text-scg-gray-dark">Shipments ({{ count($shipments) }})</h3>
                        
                        @php
                            $defaultQuickFilter = request('quick_filter', 'all');
                            $startDateVal = request('start_date');
                            $endDateVal = request('end_date');
                            
                            if (!$startDateVal && !$endDateVal) {
                                if ($defaultQuickFilter == 'this_month') {
                                    $startDateVal = now()->startOfMonth()->toDateString();
                                    $endDateVal = now()->endOfMonth()->toDateString();
                                } elseif ($defaultQuickFilter == 'prev_month') {
                                    $startDateVal = now()->subMonth()->startOfMonth()->toDateString();
                                    $endDateVal = now()->subMonth()->endOfMonth()->toDateString();
                                }
                            }
                        @endphp

                        <form method="GET" action="{{ route('customers.show', $customer) }}" class="flex flex-wrap items-end gap-3 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border border-gray-150">
                            <div>
                                <label for="quick_filter" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('Filter Range') }}</label>
                                <select id="quick_filter" name="quick_filter" onchange="handleQuickFilter(this)" class="block border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm py-1 px-2 text-xs focus:outline-none focus:ring-[#A6192E] focus:border-[#A6192E]">
                                    <option value="this_month" {{ $defaultQuickFilter == 'this_month' ? 'selected' : '' }}>{{ __('This Month') }}</option>
                                    <option value="prev_month" {{ $defaultQuickFilter == 'prev_month' ? 'selected' : '' }}>{{ __('Previous Month') }}</option>
                                    <option value="all" {{ $defaultQuickFilter == 'all' ? 'selected' : '' }}>{{ __('All Time') }}</option>
                                    <option value="custom" {{ $defaultQuickFilter == 'custom' ? 'selected' : '' }}>{{ __('Custom Range') }}</option>
                                </select>
                            </div>
                            
                            <div class="flex items-end gap-3">
                                <div>
                                    <label for="start_date" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('Start Date') }}</label>
                                    <input type="date" id="start_date" name="start_date" value="{{ $startDateVal }}"
                                        class="block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm py-1 px-2 text-xs focus:outline-none focus:ring-[#A6192E] focus:border-[#A6192E]">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('End Date') }}</label>
                                    <input type="date" id="end_date" name="end_date" value="{{ $endDateVal }}"
                                        class="block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm py-1 px-2 text-xs focus:outline-none focus:ring-[#A6192E] focus:border-[#A6192E]">
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-md text-white bg-[#A6192E] hover:bg-[#8a1426] focus:outline-none">
                                        {{ __('Filter') }}
                                    </button>
                                    @if(request('start_date') || request('end_date') || request('quick_filter'))
                                        <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-bold rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            {{ __('Reset') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    @if(count($shipments) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-scg-gray-light">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Customer PO</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">ETD Port</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($shipments as $shipment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shipment->customer_po ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shipment->supplier->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs bg-{{ $shipment->status == 'Delivered' ? 'green' : ($shipment->status == 'Pending' ? 'yellow' : 'blue') }}-100">
                                                    {{ $shipment->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shipment->etd_port?->format('d M Y') ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="text-scg-red hover:text-red-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No shipments found for the selected period.</p>
                    @endif
            </div>
        </div>
    </div>
    
    <script>
        function handleQuickFilter(select) {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const val = select.value;
            
            if (val === 'this_month') {
                const now = new Date();
                const start = new Date(now.getFullYear(), now.getMonth(), 1);
                const end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                startInput.value = formatDate(start);
                endInput.value = formatDate(end);
                select.form.submit();
            } else if (val === 'prev_month') {
                const now = new Date();
                const start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                const end = new Date(now.getFullYear(), now.getMonth(), 0);
                startInput.value = formatDate(start);
                endInput.value = formatDate(end);
                select.form.submit();
            } else if (val === 'all') {
                startInput.value = '';
                endInput.value = '';
                select.form.submit();
            }
        }
        
        function formatDate(date) {
            const d = new Date(date);
            let month = '' + (d.getMonth() + 1);
            let day = '' + d.getDate();
            const year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const select = document.getElementById('quick_filter');
            
            const markCustom = () => {
                select.value = 'custom';
            };
            
            if (startInput) startInput.addEventListener('change', markCustom);
            if (endInput) endInput.addEventListener('change', markCustom);
        });
    </script>
</x-app-layout>
