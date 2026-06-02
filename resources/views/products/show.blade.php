<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}"
                class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-scg-red hover:text-white transition-all duration-300 shadow-sm">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-lg text-scg-gray-dark dark:text-gray-200 leading-tight">
                    {{ __('Product Details') }}
                </h2>
                <p class="mt-0.5 text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                    {{ $product->sku }} — {{ $product->name }}</p>
            </div>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Compact Product Header Card --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="flex items-start p-4 gap-5">
                    {{-- Tiny Image --}}
                    <div class="flex-shrink-0">
                        <div
                            class="w-32 h-32 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">
                        </div>
                    </div>

                    {{-- Compact Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-1">
                                    {{ $product->name }}
                                </h1>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        {{ $product->sku }}
                                    </span>
                                    @if($product->supplier)
                                        <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400">
                                            {{ $product->supplier->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="flex gap-2">
                                @can('update', $product)
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="p-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-500 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endcan
                            </div>
                        </div>

                        {{-- Price - Simple --}}
                        <div class="mb-3">
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">{{ __('Unit Price') }}:</span>
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400 ml-1">
                                Rp {{ number_format($product->unit_price, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Description - Compact --}}
                        @if($product->description)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            </div>
                        @endif

                        {{-- Back Button - Tiny --}}
                        <div class="flex items-center gap-3">
                            <a href="{{ route('products.index') }}"
                                class="text-xs font-bold text-gray-500 hover:text-scg-red transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                {{ __('Back to List') }}
                            </a>

                            @can('delete', $product)
                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this product? This is a fatal action and will affect all associated shipments.') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            >            {{-- Shipment History --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <div
                                class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                            </div>
                            {{ __('Shipment History') }}
                        </h3>
                        
                        <div class="flex flex-wrap items-end gap-3 bg-gray-50 dark:bg-gray-900/40 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
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

                             <form method="GET" action="{{ route('products.show', $product) }}" class="flex flex-wrap items-end gap-3">
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
                                            class="block border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm py-1 px-2 text-xs focus:outline-none focus:ring-[#A6192E] focus:border-[#A6192E]">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('End Date') }}</label>
                                        <input type="date" id="end_date" name="end_date" value="{{ $endDateVal }}"
                                            class="block border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm py-1 px-2 text-xs focus:outline-none focus:ring-[#A6192E] focus:border-[#A6192E]">
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-md text-white bg-[#A6192E] hover:bg-[#8a1426] focus:outline-none">
                                            {{ __('Filter') }}
                                        </button>
                                        @if(request('start_date') || request('end_date') || request('quick_filter'))
                                            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-bold rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                {{ __('Reset') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-150 dark:bg-gray-700 text-gray-700 dark:text-gray-300 h-8 self-end">
                                {{ count($shipments) }} {{ __('shipments') }}
                            </span>
                        </div>
                    </div>
                </div>

                @if(count($shipments) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Customer') }}</th>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Supplier') }}</th>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Delivery Note') }}</th>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Qty') }}</th>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Status') }}</th>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($shipments as $shipment)
                                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <div class="flex items-center gap-3">
                                                                        <div
                                                                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold shadow">
                                                                            {{ strtoupper(substr($shipment->customer->name, 0, 2)) }}
                                                                        </div>
                                                                        <span
                                                                            class="text-sm font-medium text-gray-900 dark:text-white">{{ $shipment->customer->name }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                                    {{ $shipment->supplier->name }}</td>
                                                                <td
                                                                    class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">
                                                                    {{ $shipment->delivery_note_number ?? '—' }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-bold">
                                                                        {{ $shipment->pivot->quantity }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    @php
                                                                        $statusStyles = [
                                                                            'Delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
                                                                            'Pending' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                                                            'In Transit' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                                                        ];
                                                                        $style = $statusStyles[$shipment->status] ?? 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                                                                    @endphp
                                     <span
                                                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $style }}">
                                                                        {{ $shipment->status }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <a href="{{ route('shipments.show', $shipment) }}"
                                                                        class="inline-flex items-center gap-1 text-scg-red dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 text-sm font-medium transition-colors duration-200">
                                                                        {{ __('View') }}
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M9 5l7 7-7 7" />
                                                                        </svg>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">
                            {{ __("No shipments found for the selected period.") }}</p>
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