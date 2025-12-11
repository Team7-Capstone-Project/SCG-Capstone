<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-scg-gray-dark leading-tight">
                Customers Management
            </h2>
            <a href="{{ route('customers.create') }}" class="bg-scg-red hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition">
                + Add Customer
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($customers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-scg-gray-light">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Contact Person</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Phone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">PIC</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($customers as $customer)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $customer->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $customer->contact_person ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $customer->phone ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $customer->email ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $customer->pic->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('customers.show', $customer) }}" class="text-scg-red hover:text-red-900">View</a>
                                                <a href="{{ route('customers.edit', $customer) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $customers->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No customers found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
