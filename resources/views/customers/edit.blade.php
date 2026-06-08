<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
            Edit Customer: {{ $customer->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <strong>Validation Errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            {{ __('Back to Customer Details') }}
                        </a>
                    </div>
                    
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    {{ __('Customer') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required
                                    pattern="^[a-zA-Z0-9\s\.,&'\-\(\)]+$"
                                    title="Letters, numbers, spaces, and . , & ' - ( ) are allowed."
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    {{ __('Address') }}
                                </label>
                                <textarea name="address" id="address" rows="3"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('address') border-red-500 @enderror">{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact_person" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    {{ __('Contact Person') }}
                                </label>
                                <input type="text" name="contact_person" id="contact_person" 
                                    value="{{ old('contact_person', $customer->contact_person) }}"
                                    pattern="^[a-zA-Z\s\.'\-]+$"
                                    title="Only letters, spaces, dots, quotes, and dashes are allowed."
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('contact_person') border-red-500 @enderror">
                                @error('contact_person')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    {{ __('Phone') }}
                                </label>
                                <input type="text" name="phone" id="phone" 
                                    value="{{ old('phone', $customer->phone) }}"
                                    maxlength="15"
                                    pattern="^[0-9\s+\(\)]+$"
                                    title="Only numbers, spaces, plus signs, and parentheses are allowed."
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    {{ __('Email') }}
                                </label>
                                <input type="email" name="email" id="email" 
                                    value="{{ old('email', $customer->email) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="country" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    {{ __('Country') }}
                                </label>
                                <input type="text" name="country" id="country" 
                                    value="{{ old('country', $customer->country) }}"
                                    pattern="^[a-zA-Z\s\.\-\(\)]+$"
                                    title="Only letters, spaces, dots, dashes, and parentheses are allowed."
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50 @error('country') border-red-500 @enderror">
                                @error('country')
                                    <p class="text-red-500 text-sm mt-1">❌ {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4">
                            <a href="{{ route('customers.index') }}" class="inline-flex items-center justify-center gap-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-5 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 text-sm">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 dark:from-red-600 dark:to-red-700 hover:from-red-700 hover:to-red-800 dark:hover:from-red-500 dark:hover:to-red-600 text-white font-bold py-2.5 px-5 rounded-xl transition-all duration-300 shadow-md shadow-red-600/10 hover:shadow-lg hover:shadow-red-600/20 transform hover:-translate-y-0.5 text-sm">
                                {{ __('Update Customer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
