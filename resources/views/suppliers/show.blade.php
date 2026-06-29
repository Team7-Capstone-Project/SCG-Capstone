<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('suppliers.index') }}" class="group inline-flex items-center justify-center p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/60 text-slate-500 hover:text-scg-red dark:hover:text-red-400 hover:shadow-md transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <span class="text-xs font-bold text-scg-red uppercase tracking-wider">{{ __('Supplier Details') }}</span>
                    <h2 class="font-bold text-2xl text-scg-gray-dark dark:text-gray-100 leading-tight">
                        {{ $supplier->name }}
                    </h2>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 hover:from-blue-700 hover:to-indigo-700 dark:hover:from-blue-600 dark:hover:to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    {{ __('Edit') }}
                </a>
                @can('delete', $supplier)
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this supplier? This is a fatal action and will delete all associated shipments.') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 dark:from-red-500 dark:to-rose-500 hover:from-red-700 hover:to-rose-700 dark:hover:from-red-600 dark:hover:to-rose-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Main Info Card -->
                <div class="lg:col-span-2 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/50 dark:border-slate-800/50 shadow-sm rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-scg-red mr-2"></span>
                        {{ __('General Information') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-start space-x-3">
                            <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Contact Person') }}</label>
                                <p class="mt-1 text-base font-semibold text-slate-800 dark:text-slate-100">{{ $supplier->contact_person ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2m-4-3h9m-1 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Country') }}</label>
                                <p class="mt-1 text-base font-semibold text-slate-800 dark:text-slate-100">{{ $supplier->country ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-800/80 my-2"></div>

                        <div class="flex items-start space-x-3 md:col-span-2">
                            <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Address') }}</label>
                                <p class="mt-1 text-base text-slate-700 dark:text-slate-300 leading-relaxed">{{ $supplier->address ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Card -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 dark:from-slate-900/60 dark:to-slate-950/60 border border-slate-800/80 shadow-lg rounded-2xl p-6 text-white flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-2"></span>
                            {{ __('Direct Contact') }}
                        </h3>
                        
                        <div class="space-y-5">
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 rounded-xl bg-white/10 text-slate-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ __('Phone') }}</span>
                                    <a href="tel:{{ $supplier->phone }}" class="text-sm font-semibold text-white hover:text-blue-400 transition-colors">{{ $supplier->phone ?? '-' }}</a>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 rounded-xl bg-white/10 text-slate-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ __('Email') }}</span>
                                    <a href="mailto:{{ $supplier->email }}" class="text-sm font-semibold text-white hover:text-blue-400 transition-colors break-all">{{ $supplier->email ?? '-' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-4 border-t border-white/10 text-center">
                        <span class="text-xs text-slate-500 uppercase tracking-wider block mb-1">{{ __('Total Associated Shipments') }}</span>
                        <span class="text-3xl font-extrabold text-white bg-gradient-to-r from-scg-red to-rose-500 bg-clip-text text-transparent">{{ count($supplier->shipments) }}</span>
                    </div>
                </div>
            </div>

            <!-- Related Shipments Card -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/50 dark:border-slate-800/50 shadow-sm rounded-2xl p-6 transition-all duration-300">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-2"></span>
                            {{ __('Shipment History') }} ({{ count($shipments) }})
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('List of shipments processed for this supplier') }}</p>
                    </div>
                    
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

                    <form method="GET" action="{{ route('suppliers.show', $supplier) }}" class="flex flex-wrap items-end gap-3 bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200/60 dark:border-slate-800/60">
                        <div>
                            <label for="quick_filter" class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">{{ __('Filter Range') }}</label>
                            <select id="quick_filter" name="quick_filter" onchange="handleQuickFilter(this)" class="block border border-slate-200 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 rounded-lg shadow-sm py-1 px-3 text-xs focus:outline-none focus:ring-[#A6192E] focus:border-[#A6192E] transition-all">
                                <option value="this_month" {{ $defaultQuickFilter == 'this_month' ? 'selected' : '' }}>{{ __('This Month') }}</option>
                                <option value="prev_month" {{ $defaultQuickFilter == 'prev_month' ? 'selected' : '' }}>{{ __('Previous Month') }}</option>
                                <option value="all" {{ $defaultQuickFilter == 'all' ? 'selected' : '' }}>{{ __('All Time') }}</option>
                                <option value="custom" {{ $defaultQuickFilter == 'custom' ? 'selected' : '' }}>{{ __('Custom Range') }}</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end gap-3">
                            <div>
                                <label for="start_date" class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">{{ __('Start Date') }}</label>
                                <input type="date" id="start_date" name="start_date" value="{{ $startDateVal }}"
                                    class="block border border-slate-200 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 rounded-lg shadow-sm py-1 px-3 text-xs focus:outline-none focus:ring-[#A6192E] focus:border-[#A6192E] transition-all">
                            </div>
                            <div>
                                <label for="end_date" class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">{{ __('End Date') }}</label>
                                <input type="date" id="end_date" name="end_date" value="{{ $endDateVal }}"
                                    class="block border border-slate-200 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 rounded-lg shadow-sm py-1 px-3 text-xs focus:outline-none focus:ring-[#A6192E] focus:border-[#A6192E] transition-all">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-gradient-to-r from-scg-red to-red-700 dark:from-red-600 dark:to-red-700 hover:from-red-700 hover:to-red-800 dark:hover:from-red-500 dark:hover:to-red-600 focus:outline-none shadow-md transition-all duration-200">
                                    {{ __('Filter') }}
                                </button>
                                @if(request('start_date') || request('end_date') || request('quick_filter'))
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="inline-flex items-center px-4 py-1.5 border border-slate-200 dark:border-slate-800 text-xs font-bold rounded-lg text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-sm transition-all duration-200">
                                        {{ __('Reset') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                @if(count($shipments) > 0)
                    <div class="overflow-x-auto rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                            <thead class="bg-slate-50 dark:bg-slate-900/60">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('No.') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Customer PO') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Customer') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('ETD Port') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Customer Receiving Schedule') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('OTD') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($shipments as $shipment)
                                    <tr onclick="window.location='{{ route('shipments.show', $shipment) }}'" class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 cursor-pointer transition duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-slate-100 font-medium">
                                            {{ $shipment->customer_po ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                            {{ $shipment->customer->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            {{ $shipment->etd_port?->format('d M Y') ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400 font-medium text-blue-600 dark:text-blue-400">
                                            {{ $shipment->customer_receiving_schedule?->format('d M Y') ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($shipment->isDelivered())
                                                @php
                                                    $daysDiff = $shipment->getDaysDifference();
                                                    $daysText = $shipment->getDaysDifferenceText();
                                                @endphp

                                                @if($shipment->isOnTime())
                                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                                                        ✓ {{ __('Ideal') }}
                                                    </span>
                                                @elseif($shipment->isEarly())
                                                    <div class="flex flex-col">
                                                        <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200 w-fit">
                                                            ✓ {{ __('Early') }}
                                                        </span>
                                                        @if($daysDiff !== null)
                                                            <span class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5 ml-1">
                                                                 {{ abs($daysDiff) }} {{ __('days early') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @elseif($shipment->isLate())
                                                    <div class="flex flex-col">
                                                        <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200 w-fit">
                                                            ✗ {{ __('Late') }}
                                                        </span>
                                                        @if($daysDiff !== null)
                                                            <span class="text-[10px] text-red-600 dark:text-red-400 mt-0.5 ml-1 font-medium">
                                                                {{ abs($daysDiff) }} {{ __('days late') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 text-xs">-</span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 text-xs">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
                                                    'In Transit' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                                                    'Delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                                                    'Cancelled' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
                                                ];
                                            @endphp
                                            <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusColors[$shipment->status] ?? 'bg-slate-100 text-slate-800' }}">
                                                {{ __($shipment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-10 px-4 text-center border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __('No shipments found for the selected period.') }}</p>
                    </div>
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
