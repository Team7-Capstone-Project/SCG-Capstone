<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('products.index') }}" 
               class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-scg-red hover:text-white transition-all duration-300 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
                    {{ __('Add New Product') }}
                </h2>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('Fill in the details to add a new product to your catalog') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-5 py-4 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <strong class="font-semibold">{{ __('Validation Errors') }}</strong>
                    </div>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-7">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Left Column: Image Upload --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-scg-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('Product Image') }}
                                </h3>
                            </div>
                            <div class="p-5">
                                <div id="image-upload-area" 
                                     class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-scg-red dark:hover:border-red-500 transition-colors duration-300 cursor-pointer overflow-hidden aspect-square">
                                    
                                    {{-- Preview --}}
                                    <div id="image-preview" class="hidden absolute inset-0">
                                        <img id="preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <span class="text-white text-sm font-medium bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">{{ __('Click to change') }}</span>
                                        </div>
                                    </div>
                                    
                                    {{-- Upload Placeholder --}}
                                    <div id="upload-placeholder" class="flex flex-col items-center justify-center h-full p-6 text-center">
                                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Drop image here or click to upload') }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">PNG, JPG, GIF, WebP (max. 2MB)</p>
                                    </div>
                                    
                                    <input type="file" name="image" id="image-input" accept="image/*" 
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Product Details --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-scg-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ __('Product Information') }}
                                </h3>
                            </div>
                            <div class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="sku" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            SKU <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-scg-red focus:ring focus:ring-scg-red/20 transition-all duration-200"
                                            placeholder="e.g., CHM-001">
                                    </div>

                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('Product Name') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-scg-red focus:ring focus:ring-scg-red/20 transition-all duration-200"
                                            placeholder="{{ __('Enter product name') }}">
                                    </div>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Description') }}
                                    </label>
                                    <textarea name="description" id="description" rows="4"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-scg-red focus:ring focus:ring-scg-red/20 transition-all duration-200 resize-none"
                                        placeholder="{{ __('Describe your product...') }}">{{ old('description') }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="unit_price" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('Unit Price (Rp)') }} <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 font-medium text-sm">Rp</span>
                                            <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" required
                                                class="w-full pl-12 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-scg-red focus:ring focus:ring-scg-red/20 transition-all duration-200"
                                                placeholder="0.00">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="supplier_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('Supplier') }}
                                        </label>
                                        <select name="supplier_id" id="supplier_id"
                                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-scg-red focus:ring focus:ring-scg-red/20 transition-all duration-200">
                                            <option value="">{{ __('Select Supplier') }}</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-3 mt-6">
                            <a href="{{ route('products.index') }}" 
                               class="px-6 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-scg-red to-red-700 hover:from-red-700 hover:to-red-900 text-white font-semibold shadow-lg shadow-red-500/25 hover:shadow-red-500/40 transition-all duration-300 hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('Create Product') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image-input');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const uploadPlaceholder = document.getElementById('upload-placeholder');
            const uploadArea = document.getElementById('image-upload-area');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        uploadPlaceholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Drag and drop styling
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-scg-red', 'bg-red-50');
                this.classList.add('dark:bg-red-900/10');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-scg-red', 'bg-red-50');
                this.classList.remove('dark:bg-red-900/10');
            });

            uploadArea.addEventListener('drop', function(e) {
                this.classList.remove('border-scg-red', 'bg-red-50');
                this.classList.remove('dark:bg-red-900/10');
            });
        });
    </script>
    @endpush
</x-app-layout>
