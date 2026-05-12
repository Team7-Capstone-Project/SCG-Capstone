<x-app-layout>
    <div class="min-h-screen bg-[#F8F9FB] dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('products.index') }}" 
                        class="group flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 hover:border-scg-red transition-all">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-scg-red transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Product</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Fill in the details to add a new product to inventory</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    {{-- Left Side: Image Upload (4/12 = 1/3) --}}
                    <div class="lg:col-span-4 space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 uppercase tracking-wider">
                                    <svg class="w-4 h-4 text-scg-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Product Image
                                </h3>
                            </div>
                            <div class="p-6">
                                <div id="drop-area" class="relative group aspect-square rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 hover:border-scg-red dark:hover:border-scg-red bg-gray-50 dark:bg-gray-900/50 transition-all flex flex-col items-center justify-center text-center cursor-pointer overflow-hidden">
                                    
                                    {{-- Preview --}}
                                    <div id="preview-container" class="hidden absolute inset-0 z-10">
                                        <img id="image-preview" src="" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-lg text-white text-xs font-bold uppercase tracking-widest border border-white/30">Replace Image</span>
                                        </div>
                                    </div>

                                    {{-- Placeholder --}}
                                    <div id="upload-placeholder" class="space-y-4 p-4">
                                        <div class="w-12 h-12 mx-auto rounded-xl bg-scg-red/10 flex items-center justify-center text-scg-red group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">Click to Upload</p>
                                            <p class="text-[11px] text-gray-500 mt-1 uppercase tracking-tighter font-medium">PNG, JPG, WEBP (MAX. 2MB)</p>
                                        </div>
                                    </div>

                                    <input type="file" name="image" id="image-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                </div>

                                <div class="mt-6 flex items-start gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-[11px] leading-relaxed">
                                        <strong>Quality tip:</strong> High-quality product images can increase conversion rates by up to 30%. Use well-lit, square images.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Form (8/12 = 2/3) --}}
                    <div class="lg:col-span-8 space-y-8">
                        
                        {{-- General Info Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 uppercase tracking-wider">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    General Information
                                </h3>
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- SKU --}}
                                    <div class="space-y-2">
                                        <label for="sku" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Product SKU <span class="text-red-500">*</span></label>
                                        <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-900 focus-within:ring-2 focus-within:ring-scg-red/20 focus-within:border-scg-red transition-all">
                                            <div class="pl-4 text-gray-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                            </div>
                                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                                class="w-full border-none focus:ring-0 py-3 px-4 text-gray-900 dark:text-white placeholder:text-gray-400"
                                                placeholder="e.g. PRD-001">
                                        </div>
                                    </div>
                                    {{-- Name --}}
                                    <div class="space-y-2">
                                        <label for="name" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Product Name <span class="text-red-500">*</span></label>
                                        <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-900 focus-within:ring-2 focus-within:ring-scg-red/20 focus-within:border-scg-red transition-all">
                                            <div class="pl-4 text-gray-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                                class="w-full border-none focus:ring-0 py-3 px-4 text-gray-900 dark:text-white placeholder:text-gray-400"
                                                placeholder="Enter product name">
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="space-y-2">
                                    <label for="description" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Detailed Description</label>
                                    <textarea name="description" id="description" rows="5"
                                        class="w-full border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 focus:ring-2 focus:ring-scg-red/20 focus:border-scg-red transition-all p-4 text-gray-900 dark:text-white placeholder:text-gray-400 resize-none"
                                        placeholder="Tell customers about the product features, specifications, and benefits...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Pricing Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 uppercase tracking-wider">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Pricing & Supplier
                                </h3>
                            </div>
                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Price --}}
                                    <div class="space-y-2">
                                        <label for="unit_price" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Base Price <span class="text-red-500">*</span></label>
                                        <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-900 focus-within:ring-2 focus-within:ring-scg-red/20 focus-within:border-scg-red transition-all">
                                            <div class="px-4 bg-gray-50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 font-bold text-scg-red">
                                                Rp
                                            </div>
                                            <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" required
                                                class="w-full border-none focus:ring-0 py-3 px-4 text-gray-900 dark:text-white font-bold"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                    {{-- Supplier --}}
                                    <div class="space-y-2">
                                        <label for="supplier_id" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Supplier</label>
                                        <div class="relative">
                                            <select name="supplier_id" id="supplier_id"
                                                class="w-full pl-4 pr-10 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-scg-red/20 focus:border-scg-red transition-all appearance-none">
                                                <option value="">No Supplier (Internal)</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                            <a href="{{ route('products.index') }}" 
                                class="px-8 py-3 rounded-xl text-gray-500 dark:text-gray-400 font-bold hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                Discard
                            </a>
                            <button type="submit" 
                                class="px-10 py-3 bg-scg-red text-white font-bold rounded-xl shadow-lg shadow-red-500/30 hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                                Save Product
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('image-input');
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('preview-container');
            const placeholder = document.getElementById('upload-placeholder');

            input.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        container.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>