<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-red-100 dark:bg-red-950/40 text-red-800 dark:text-red-400 tracking-wider uppercase">SCG SCM</span>
                    <span class="text-xs text-slate-300 dark:text-slate-700">•</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight mt-1">
                    PT SCG International Indonesia
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Supply Chain Management Dashboard') }}</p>
            </div>
            @can('create', App\Models\Shipment::class)
                <a href="{{ route('shipments.create') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 dark:from-red-600 dark:to-red-700 hover:from-red-700 hover:to-red-800 dark:hover:from-red-500 dark:hover:to-red-600 text-white font-bold py-2.5 px-5 rounded-xl transition-all duration-300 shadow-md shadow-red-600/10 hover:shadow-lg hover:shadow-red-600/20 transform hover:-translate-y-0.5 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>{{ __('Create Shipment') }}</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 text-emerald-800 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-up" role="alert">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Metrics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">

                {{-- Total Shipments --}}
                <a href="{{ route('shipments.index') }}" class="group relative flex flex-col justify-between bg-white/70 dark:bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-200/60 dark:border-slate-800/80 p-6 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 dark:hover:shadow-blue-500/10 hover:border-blue-500/30 dark:hover:border-blue-500/30 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-100">
                    <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Total Shipments') }}</p>
                                <p class="text-4xl font-extrabold text-slate-800 dark:text-white mt-3 tracking-tight">{{ $totalShipments }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-500/10 to-indigo-500/10 dark:from-blue-500/20 dark:to-indigo-500/20 text-blue-600 dark:text-blue-400 rounded-xl p-3.5 shadow-inner transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wide uppercase">{{ __('Logistics Volume') }}</span>
                    </div>
                </a>

                {{-- Delivered Shipments --}}
                <a href="{{ route('shipments.index', ['status' => 'Delivered']) }}" class="group relative flex flex-col justify-between bg-white/70 dark:bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-200/60 dark:border-slate-800/80 p-6 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 dark:hover:shadow-emerald-500/10 hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-200">
                    <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Delivered') }}</p>
                                <p class="text-4xl font-extrabold text-slate-800 dark:text-white mt-3 tracking-tight">{{ $deliveredShipments }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-emerald-500/10 to-teal-500/10 dark:from-emerald-500/20 dark:to-teal-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl p-3.5 shadow-inner transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-1.5">
                        <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 dark:text-slate-500">
                            <span class="tracking-wide uppercase">{{ __('Completion') }}</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $totalShipments > 0 ? round(($deliveredShipments / $totalShipments) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full" style="width: {{ $totalShipments > 0 ? ($deliveredShipments / $totalShipments) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </a>

                {{-- Late Shipments --}}
                <a href="{{ route('shipments.index', ['late' => 1]) }}" class="group relative flex flex-col justify-between bg-white/70 dark:bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-200/60 dark:border-slate-800/80 p-6 shadow-sm hover:shadow-xl hover:shadow-rose-500/5 dark:hover:shadow-rose-500/10 hover:border-rose-500/30 dark:hover:border-rose-500/30 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-300">
                    <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gradient-to-r from-rose-500 to-red-600 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Late Shipments') }}</p>
                                <p class="text-4xl font-extrabold text-rose-600 dark:text-rose-400 mt-3 tracking-tight">{{ $lateShipments }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-rose-500/10 to-red-500/10 dark:from-rose-500/20 dark:to-red-500/20 text-rose-600 dark:text-rose-400 rounded-xl p-3.5 shadow-inner transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-1.5">
                        <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 dark:text-slate-500">
                            <span class="tracking-wide uppercase">{{ __('Delay Ratio') }}</span>
                            <span class="{{ $lateShipments > 0 ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-slate-400' }}">{{ $totalShipments > 0 ? round(($lateShipments / $totalShipments) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-rose-500 to-red-500 h-full rounded-full" style="width: {{ $totalShipments > 0 ? ($lateShipments / $totalShipments) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </a>

                {{-- Early Shipments --}}
                <a href="{{ route('shipments.index', ['early' => 1]) }}" class="group relative flex flex-col justify-between bg-white/70 dark:bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-200/60 dark:border-slate-800/80 p-6 shadow-sm hover:shadow-xl hover:shadow-amber-500/5 dark:hover:shadow-amber-500/10 hover:border-amber-500/30 dark:hover:border-amber-500/30 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-350">
                    <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-500 to-orange-600 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Early Shipments') }}</p>
                                <p class="text-4xl font-extrabold text-amber-600 dark:text-amber-400 mt-3 tracking-tight">{{ $earlyShipments }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/10 dark:from-amber-500/20 dark:to-orange-500/20 text-amber-600 dark:text-amber-400 rounded-xl p-3.5 shadow-inner transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-1.5">
                        <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 dark:text-slate-500">
                            <span class="tracking-wide uppercase">{{ __('Early Share') }}</span>
                            <span class="text-amber-600 dark:text-amber-400 font-semibold">{{ $totalShipments > 0 ? round(($earlyShipments / $totalShipments) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-full rounded-full" style="width: {{ $totalShipments > 0 ? ($earlyShipments / $totalShipments) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </a>

                {{-- On-Time Shipments --}}
                <a href="{{ route('shipments.index', ['on_time' => 1]) }}" class="group relative flex flex-col justify-between bg-white/70 dark:bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-200/60 dark:border-slate-800/80 p-6 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 dark:hover:shadow-emerald-500/10 hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden animate-fade-in-up delay-400">
                    <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 opacity-50 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('On-Time Shipments') }}</p>
                                <p class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-3 tracking-tight">{{ $onTimeShipments }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-emerald-500/10 to-teal-500/10 dark:from-emerald-500/20 dark:to-teal-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl p-3.5 shadow-inner transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-1.5">
                        <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 dark:text-slate-500">
                            <span class="tracking-wide uppercase">{{ __('On-Time Share') }}</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $totalShipments > 0 ? round(($onTimeShipments / $totalShipments) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full" style="width: {{ $totalShipments > 0 ? ($onTimeShipments / $totalShipments) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </a>

            </div>

            {{-- Recent Shipments Table --}}
            <div class="bg-white/85 dark:bg-slate-900/70 backdrop-blur-md overflow-hidden rounded-2xl border border-slate-200/60 dark:border-slate-800/80 shadow-md">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Recent Shipments') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Real-time log of the latest cargo movements and statuses') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="relative hidden md:block">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </span>
                                <input type="text" id="dashboard-search" placeholder="{{ __('Search PO, Customer...') }}" class="w-64 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl py-1.5 pl-9 pr-4 text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:focus:ring-red-500 dark:focus:border-red-500 text-slate-800 dark:text-slate-100 transition-all duration-300">
                            </div>
                            <a href="{{ route('shipments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 bg-red-50 dark:bg-red-950/20 px-3.5 py-2 rounded-xl transition-all duration-200">
                                <span>{{ __('View All') }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    @if($recentShipments->count() > 0)
                        <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800/80">
                            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800/80">
                                <thead class="bg-slate-50/70 dark:bg-slate-800/40">
                                    <tr>
                                        <th class="px-6 py-3.5 text-left text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Customer PO') }}</th>
                                        <th class="px-6 py-3.5 text-left text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Customer') }}</th>
                                        <th class="px-6 py-3.5 text-left text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Supplier') }}</th>
                                        <th class="px-6 py-3.5 text-left text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Status') }}</th>
                                        <th class="px-6 py-3.5 text-left text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('ETD Port') }}</th>
                                        <th class="px-6 py-3.5 text-left text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('OTD') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="shipments-table-body" class="bg-transparent divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @foreach($recentShipments as $shipment)
                                        <tr onclick="window.location='{{ route('shipments.show', $shipment) }}'" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition duration-150 cursor-pointer">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                    {{ $shipment->customer_po ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-9 w-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                                        {{ count(explode(' ', $shipment->customer->name)) > 1 ? substr(explode(' ', $shipment->customer->name)[1], 0, 2) : substr($shipment->customer->name, 0, 2) }}
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $shipment->customer->name }}</div>
                                                        <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $shipment->customer->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-9 w-9 rounded-xl bg-violet-50 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                                        {{ substr($shipment->supplier->name, 0, 2) }}
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $shipment->supplier->name }}</div>
                                                        <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $shipment->supplier->country ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusClasses = [
                                                        'Pending' => 'bg-amber-50 text-amber-700 border-amber-200/50 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/40',
                                                        'In Transit' => 'bg-blue-50 text-blue-700 border-blue-200/50 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/40',
                                                        'Delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/40',
                                                        'Cancelled' => 'bg-slate-50 text-slate-600 border-slate-200/50 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700/50',
                                                    ];
                                                    $dotClasses = [
                                                        'Pending' => 'bg-amber-500',
                                                        'In Transit' => 'bg-blue-500',
                                                        'Delivered' => 'bg-emerald-500',
                                                        'Cancelled' => 'bg-slate-400',
                                                    ];
                                                @endphp
                                                <div class="flex flex-col gap-1">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses[$shipment->status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                                        <span class="h-1.5 w-1.5 rounded-full {{ $dotClasses[$shipment->status] ?? 'bg-slate-400' }}"></span>
                                                        {{ __($shipment->status) }}
                                                    </span>
                                                    @if($shipment->notes)
                                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 max-w-[150px] truncate" title="{{ $shipment->notes }}">
                                                            📝 {{ $shipment->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span class="text-xs font-semibold">{{ $shipment->etd_port?->format('d M Y') ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($shipment->isDelivered())
                                                    @php
                                                        $daysDiff = $shipment->getDaysDifference();
                                                        $daysText = $shipment->getDaysDifferenceText();
                                                    @endphp

                                                    @if($shipment->isOnTime())
                                                        <div class="flex flex-col">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 justify-center">
                                                                ✓ {{ __('Ideal') }}
                                                            </span>
                                                        </div>
                                                    @elseif($shipment->isEarly())
                                                        <div class="flex flex-col">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200 justify-center">
                                                                ✓ {{ __('Early') }}
                                                            </span>
                                                            @if($daysDiff !== null)
                                                                <span class="text-xs text-amber-600 dark:text-amber-400 mt-1 pl-1">
                                                                     {{ abs($daysDiff) }} {{ __('days early') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @elseif($shipment->isLate())
                                                        <div class="flex flex-col">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 justify-center">
                                                                ✗ {{ __('Late') }}
                                                            </span>
                                                            @if($daysDiff !== null)
                                                                <span class="text-xs text-red-600 dark:text-red-400 mt-1 font-medium pl-1">
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-semibold">{{ __('No shipments found. Create your first shipment to get started.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('dashboard-search')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#shipments-table-body tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
