<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($customers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-scg-gray-light dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">Contact Person</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">Phone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">PIC</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($customers as $customer)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $customer->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $customer->contact_person ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $customer->phone ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $customer->email ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $customer->pic->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('customers.show', $customer) }}" class="text-scg-red dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">View</a>
                                                <a href="{{ route('customers.edit', $customer) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">Edit</a>
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
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No customers found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
