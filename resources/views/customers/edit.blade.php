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
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Customer Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Address
                                </label>
                                <textarea name="address" id="address" rows="3"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">{{ old('address', $customer->address) }}</textarea>
                            </div>

                            <div>
                                <label for="contact_person" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Contact Person
                                </label>
                                <input type="text" name="contact_person" id="contact_person" 
                                    value="{{ old('contact_person', $customer->contact_person) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Phone
                                </label>
                                <input type="text" name="phone" id="phone" 
                                    value="{{ old('phone', $customer->phone) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    Email
                                </label>
                                <input type="email" name="email" id="email" 
                                    value="{{ old('email', $customer->email) }}"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="pic_user_id" class="block text-sm font-medium text-scg-gray-dark mb-2">
                                    PIC (Sales Person)
                                </label>
                                <select name="pic_user_id" id="pic_user_id"
                                    class="w-full rounded-md border-gray-300 focus:border-scg-red focus:ring focus:ring-scg-red focus:ring-opacity-50">
                                    <option value="">Select PIC</option>
                                    @foreach($picUsers as $user)
                                        <option value="{{ $user->id }}" {{ (old('pic_user_id', $customer->pic_user_id) == $user->id) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <div>
                                <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    ← Back to Customer Details
                                </a>
                            </div>
                            <div class="space-x-4">
                                <a href="{{ route('customers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded transition">
                                    Cancel
                                </a>
                                <button type="submit" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-6 rounded transition">
                                    Update Customer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
