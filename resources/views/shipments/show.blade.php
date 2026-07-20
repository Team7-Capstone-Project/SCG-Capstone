<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('shipments.index') }}" class="group inline-flex items-center justify-center p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/60 text-slate-500 hover:text-scg-red dark:hover:text-red-400 hover:shadow-md transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold text-scg-red uppercase tracking-wider">{{ __('Shipment Details') }}</span>
                        @if($shipment->type)
                            <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                {{ __($shipment->type) }}
                            </span>
                        @endif
                    </div>
                    <h2 class="font-bold text-2xl text-scg-gray-dark dark:text-gray-100 leading-tight">
                        PO: {{ $shipment->customer_po ?? 'N/A' }}
                    </h2>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                @can('update', $shipment)
                    <a href="{{ route('shipments.edit', $shipment) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 hover:from-blue-700 hover:to-indigo-700 dark:hover:from-blue-600 dark:hover:to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        {{ __('Edit Shipment') }}
                    </a>
                @endcan
                
                @can('delete', $shipment)
                    <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this shipment?') }}');">
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
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-900/60 text-green-800 dark:text-green-300 px-4 py-3 rounded-xl flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/60 text-red-800 dark:text-red-300 px-4 py-3 rounded-xl flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Progress Timeline --}}
            @php
                $statusSteps = ['Pending', 'In Transit', 'Delivered'];
                $currentStatus = $shipment->status;
                $isCancelled = $currentStatus === 'Cancelled';
                
                $activeIndex = array_search($currentStatus, $statusSteps);
                if ($activeIndex === false) {
                    $activeIndex = -1;
                }
            @endphp

            @if($isCancelled)
                <div class="mb-6 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/60 p-4 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="p-2 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-slate-100">{{ __('Shipment Cancelled') }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('This shipment has been cancelled and is no longer active.') }}</p>
                        </div>
                    </div>
                    @if($shipment->notes)
                        <div class="text-right">
                            <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Cancellation Reason/Note') }}</span>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">"{{ $shipment->notes }}"</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/50 dark:border-slate-800/50 shadow-sm rounded-2xl p-6 mb-6">
                    <div class="relative flex items-center justify-between w-full max-w-4xl mx-auto py-4">
                        
                        <!-- Connecting Line Background -->
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-100 dark:bg-slate-800 rounded-full z-0"></div>
                        
                        <!-- Connecting Line Active Progress -->
                        @php
                            $lineWidthPercent = 0;
                            if ($activeIndex === 1) {
                                $lineWidthPercent = 50;
                            } elseif ($activeIndex === 2) {
                                $lineWidthPercent = 100;
                            }
                        @endphp
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-gradient-to-r from-scg-red to-blue-500 rounded-full z-0 transition-all duration-500" style="width: {{ $lineWidthPercent }}%"></div>
                        
                        <!-- Step 1: Pending -->
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 {{ $activeIndex >= 0 ? ($activeIndex > 0 ? 'bg-scg-red border-scg-red text-white' : 'bg-white dark:bg-slate-900 border-scg-red text-scg-red ring-4 ring-red-100 dark:ring-red-950') : 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200">{{ __('Pending') }}</span>
                                @if($shipment->created_at)
                                    <span class="block text-[10px] text-slate-400 dark:text-slate-500">{{ $shipment->created_at->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Step 2: In Transit -->
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 {{ $activeIndex >= 1 ? ($activeIndex > 1 ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white dark:bg-slate-900 border-blue-500 text-blue-500 ring-4 ring-blue-100 dark:ring-blue-950') : 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 17h20l-2 3H4l-2-3zM5 17l1-5h12l1 5M9 12V8h6v4M12 8V4" />
                                </svg>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200">{{ __('In Transit') }}</span>
                                @if($shipment->etd_port)
                                    <span class="block text-[10px] text-slate-400 dark:text-slate-500">ETD: {{ $shipment->etd_port?->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Step 3: Delivered -->
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 {{ $activeIndex >= 2 ? 'bg-green-600 border-green-600 text-white ring-4 ring-green-100 dark:ring-green-950' : 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10h10zm0 0h6l2-3v-3a1 1 0 00-1-1h-7v7z" />
                                </svg>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200">{{ __('Delivered') }}</span>
                                @if($shipment->ata_customer)
                                    <span class="block text-[10px] text-green-600 dark:text-green-400 font-medium">ATA: {{ $shipment->ata_customer?->format('d M Y') }}</span>
                                @elseif($shipment->customer_receiving_schedule)
                                    <span class="block text-[10px] text-slate-400 dark:text-slate-500">Sch: {{ $shipment->customer_receiving_schedule?->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                        
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- General Information Card -->
                    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/50 dark:border-slate-800/50 shadow-sm rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-scg-red mr-2"></span>
                            {{ __('Shipment Information') }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Customer -->
                            <div class="flex items-start space-x-3">
                                <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Customer') }}</label>
                                    <a href="{{ route('customers.show', $shipment->customer) }}" class="mt-1 text-sm font-bold text-scg-red hover:underline block">{{ $shipment->customer->name }}</a>
                                </div>
                            </div>

                            <!-- Supplier -->
                            <div class="flex items-start space-x-3">
                                <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Supplier') }}</label>
                                    <a href="{{ route('suppliers.show', $shipment->supplier) }}" class="mt-1 text-sm font-bold text-blue-600 hover:underline block">{{ $shipment->supplier->name }}</a>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex items-start space-x-3">
                                <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">{{ __('Status') }}</label>
                                    @php
                                        $statusColors = [
                                            'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
                                            'In Transit' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                                            'Delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                                            'Cancelled' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full {{ $statusColors[$shipment->status] ?? 'bg-slate-100 text-slate-800' }}">
                                        {{ __($shipment->status) }}
                                    </span>
                                    @if($shipment->notes)
                                        <span class="block text-[10px] text-slate-500 dark:text-slate-400 mt-1">📝 {{ $shipment->notes }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-slate-100 dark:border-slate-800 my-6"></div>
                        
                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-4">{{ __('Documents & References') }}</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Customer PO -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Customer PO') }}</label>
                                <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->customer_po ?? '-' }}</p>
                            </div>
                            <!-- SCG PO -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('SCG PO') }}</label>
                                <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->scg_po ?? '-' }}</p>
                            </div>
                            <!-- Booking Number -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Booking Number') }}</label>
                                <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->booking_number ?? '-' }}</p>
                            </div>
                            <!-- SCG SO -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('SCG SO') }}</label>
                                <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->scg_so ?? '-' }}</p>
                            </div>
                            <!-- Delivery Note -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Delivery Note') }}</label>
                                <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->delivery_note_number ?? '-' }}</p>
                            </div>
                            <!-- Supplier Invoice -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Supplier Invoice') }}</label>
                                <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->supplier_invoice ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products Table Card -->
                    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/50 dark:border-slate-800/50 shadow-sm rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-2"></span>
                            {{ __('Products') }}
                        </h3>
                        
                        <div class="overflow-x-auto rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                                <thead class="bg-slate-50 dark:bg-slate-900/60">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('SKU') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Product Name') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Quantity') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Unit Price') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($shipment->products as $product)
                                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $product->sku }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $product->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 dark:text-slate-200 text-right font-medium">{{ $product->pivot->quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 text-right">Rp {{ number_format($product->pivot->unit_price, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-950 dark:text-white text-right">
                                                Rp {{ number_format($product->pivot->quantity * $product->pivot->unit_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-slate-50 dark:bg-slate-900/80 divide-y divide-slate-200 dark:divide-slate-800 border-t-2 border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <td colspan="4" class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Total Product Value') }}</td>
                                        <td class="px-6 py-3 text-right text-sm font-bold text-slate-800 dark:text-slate-200">
                                            Rp {{ number_format($shipment->total_products_value, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Total Cost Structure') }}</td>
                                        <td class="px-6 py-3 text-right text-sm font-semibold text-slate-600 dark:text-slate-300">
                                            + Rp {{ number_format($shipment->total_cost, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr class="bg-scg-red/5 dark:bg-scg-red/10 border-t-2 border-scg-red/30">
                                        <td colspan="4" class="px-6 py-4 text-right text-xs font-extrabold text-scg-red dark:text-red-400 uppercase tracking-wider">{{ __('Grand Total (Products + Costs)') }}</td>
                                        <td class="px-6 py-4 text-right text-base font-extrabold text-scg-red dark:text-red-400">
                                            Rp {{ number_format($shipment->grand_total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Cost Structure Card -->
                    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/50 dark:border-slate-800/50 shadow-sm rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 mr-2"></span>
                            {{ __('Cost Structure') }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm">
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Shipping Cost') }}</label>
                                <p class="mt-2 text-base font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($shipment->shipping_cost, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm">
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Customs Cost') }}</label>
                                <p class="mt-2 text-base font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($shipment->customs_cost, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200/60 dark:border-slate-800/60 shadow-sm">
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Other Costs') }}</label>
                                <p class="mt-2 text-base font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($shipment->other_costs, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl border border-slate-300/60 dark:border-slate-700/60 shadow-sm">
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Total Cost') }}</label>
                                <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Rp {{ number_format($shipment->total_cost, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-scg-red/5 to-red-500/10 dark:from-scg-red/10 dark:to-transparent p-4 rounded-xl border border-scg-red/30 shadow-sm">
                                <label class="block text-xs font-bold text-scg-red dark:text-red-400 uppercase tracking-wider">{{ __('Grand Total') }}</label>
                                <p class="mt-2 text-lg font-extrabold text-scg-red dark:text-red-400">Rp {{ number_format($shipment->grand_total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Right Column -->
                <div class="space-y-6">
                    
                    <!-- Dates & OTD Card -->
                    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/50 dark:border-slate-800/50 shadow-sm rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 mr-2"></span>
                            {{ __('Dates & OTD Tracking') }}
                        </h3>
                        
                        <div class="space-y-5">
                            <div class="flex items-start space-x-3">
                                <div class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('ETD Port') }}</span>
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->etd_port?->format('d M Y') ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('ETA Port') }}</span>
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->eta_port?->format('d M Y') ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('ATA Port') }}</span>
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $shipment->ata_port?->format('d M Y') ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 dark:border-slate-800/80 my-3"></div>

                            <div class="flex items-start space-x-3">
                                <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-blue-500 dark:text-blue-400 uppercase tracking-wider">{{ __('Customer Receiving Schedule') }}</span>
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $shipment->customer_receiving_schedule?->format('d M Y') ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="p-2 rounded-lg bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-green-500 dark:text-green-400 uppercase tracking-wider">{{ __('ATA Customer (Actual Delivery)') }}</span>
                                    <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ $shipment->ata_customer?->format('d M Y') ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 dark:border-slate-800/80 my-3"></div>

                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">{{ __('OTD Status') }}</span>
                                <div>
                                    @if($shipment->isDelivered())
                                        @php
                                            $daysDiff = $shipment->getDaysDifference();
                                            $daysText = $shipment->getDaysDifferenceText();
                                        @endphp
                                        
                                        @if($shipment->isOnTime())
                                            <div class="flex flex-col space-y-1">
                                                <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-xl bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200 w-fit">
                                                    ✓ {{ __('Ideal') }}
                                                </span>
                                            </div>
                                        @elseif($shipment->isEarly())
                                            <div class="flex flex-col space-y-1">
                                                <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-xl bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200 w-fit">
                                                    ✓ {{ __('Early') }}
                                                </span>
                                                @if($daysText)
                                                    <span class="text-xs text-amber-600 dark:text-amber-400 font-bold mt-1 ml-1">
                                                        {{ $daysText }}
                                                    </span>
                                                @endif
                                            </div>
                                        @elseif($shipment->isLate())
                                            <div class="flex flex-col space-y-1">
                                                <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-xl bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-200 w-fit">
                                                    ✗ {{ __('Late') }}
                                                </span>
                                                @if($daysText)
                                                    <span class="text-xs text-red-600 dark:text-red-400 font-extrabold mt-1 ml-1">
                                                        {{ $daysText }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block bg-slate-50 dark:bg-slate-950 py-2 px-3 rounded-lg border border-slate-200/50 dark:border-slate-800/50 w-fit">{{ __('Pending Delivery') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Update Status Form -->
                    @can('updateStatus', $shipment)
                        <div class="bg-gradient-to-br from-blue-50/60 to-indigo-50/40 dark:from-slate-900/60 dark:to-indigo-950/20 border border-blue-100 dark:border-slate-800 shadow-sm rounded-2xl p-6">
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center border-b border-blue-100/60 dark:border-slate-800 pb-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                                {{ __('Update Shipment Status') }}
                            </h3>
                            
                            <form action="{{ route('shipments.update-status', $shipment) }}" method="POST" class="space-y-4">
                                @csrf
                                
                                <div>
                                    <label for="status" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Status</label>
                                    <select name="status" id="status" class="w-full rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 text-sm">
                                        <option value="">Keep Current</option>
                                        <option value="Pending" {{ $shipment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="In Transit" {{ $shipment->status == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="Delivered" {{ $shipment->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="Cancelled" {{ $shipment->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="ata_port" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">ATA Port</label>
                                    <input type="date" name="ata_port" id="ata_port" value="{{ $shipment->ata_port?->format('Y-m-d') }}"
                                        class="w-full rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 text-sm">
                                </div>
                                
                                <div>
                                    <label for="ata_customer" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                                        ATA Customer
                                    </label>
                                    <span class="block text-[10px] text-slate-400 mb-2">(Setting this automatically updates status to Delivered)</span>
                                    <input type="date" name="ata_customer" id="ata_customer" value="{{ $shipment->ata_customer?->format('Y-m-d') }}"
                                        class="w-full rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 text-sm">
                                </div>
                                
                                <div>
                                    <label for="notes" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                        Location / Note
                                    </label>
                                    <input type="text" name="notes" id="notes" value="{{ $shipment->notes }}" placeholder="e.g. Karawang"
                                        class="w-full rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 text-sm">
                                </div>
                                
                                <button type="submit" class="w-full bg-gradient-to-r from-scg-red to-red-700 dark:from-red-600 dark:to-red-700 hover:from-red-700 hover:to-red-800 dark:hover:from-red-500 dark:hover:to-red-600 text-white font-bold py-2.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-sm">
                                    Update Status
                                </button>
                            </form>
                        </div>
                    @endcan
                    
                    <!-- Activity Log Card -->
                    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/50 dark:border-slate-800/50 shadow-sm rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-500 mr-2"></span>
                            Activity Log
                        </h3>
                        
                        @if($shipment->activityLogs->count() > 0)
                            <div class="relative pl-6 border-l border-slate-200 dark:border-slate-800 space-y-6">
                                @foreach($shipment->activityLogs as $log)
                                    <div class="relative">
                                        <span class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full bg-white dark:bg-slate-900 border-2 border-scg-red"></span>
                                        
                                        <div>
                                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $log->action }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">{{ $log->description }}</p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-medium">
                                                By {{ $log->user->name }} • {{ $log->created_at->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">{{ __('No activity logs yet.') }}</p>
                            </div>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
