<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
                    {{ __('Products Management') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Manage your product catalog and inventory') }}</p>
            </div>
            @can('create', App\Models\Product::class)
                <a href="{{ route('products.create') }}" 
                   class="inline-flex items-center gap-2 bg-[#A6192E] hover:bg-[#8B1527] text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-all duration-200">
                    <span class="text-xl leading-none">+</span>
                    <span class="text-base">{{ __('Add Product') }}</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 px-5 py-4 rounded-xl flex items-center gap-3 animate-fade-in">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif


            {{-- Product Grid - Compact Cards --}}
            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach($products as $product)
                        <a href="{{ route('products.show', $product) }}" 
                           class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:shadow-gray-200/50 dark:hover:shadow-black/20 transition-all duration-300 hover:-translate-y-1">
                            
                            {{-- Product Image - Compact --}}
                            <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                                <img src="{{ $product->image_url }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                     loading="lazy">
                                
                                {{-- SKU Badge --}}
                                <div class="absolute top-2 left-2">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-white/90 dark:bg-gray-900/90 text-gray-600 dark:text-gray-300 backdrop-blur-sm shadow-sm">
                                        {{ $product->sku }}
                                    </span>
                                </div>

                                {{-- Hover Action Icons --}}
                                <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                    @can('update', $product)
                                        <span onclick="event.preventDefault(); window.location='{{ route('products.edit', $product) }}';" 
                                              class="w-7 h-7 rounded-md bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm flex items-center justify-center text-gray-500 hover:bg-blue-600 hover:text-white transition-colors duration-150 shadow-sm cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </span>
                                    @endcan
                                </div>

                                {{-- Supplier Badge (bottom) --}}
                                @if($product->supplier)
                                    <div class="absolute bottom-2 left-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-500/90 text-white backdrop-blur-sm shadow-sm">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ Str::limit($product->supplier->name, 12) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Product Info - Compact --}}
                            <div class="p-3">
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-white leading-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-200 line-clamp-2 mb-1.5">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($product->unit_price, 0, ',', '.') }}
                                </p>
                                @if($product->description)
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 line-clamp-1">
                                        {{ $product->description }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('No products found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Get started by creating your first product.') }}</p>
                    @can('create', App\Models\Product::class)
                        <a href="{{ route('products.create') }}" 
                           class="inline-flex items-center gap-2 bg-[#A6192E] hover:bg-[#8B1527] text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-all duration-200">
                            <span class="text-xl leading-none">+</span>
                            <span class="text-base">{{ __('Create First Product') }}</span>
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @endpush
</x-app-layout>
