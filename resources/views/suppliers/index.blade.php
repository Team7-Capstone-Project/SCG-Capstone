<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-scg-gray-dark dark:text-gray-200 leading-tight">
                {{ __('Suppliers Management') }}
            </h2>
            @can('create', App\Models\Supplier::class)
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 dark:from-red-600 dark:to-red-700 hover:from-red-700 hover:to-red-800 dark:hover:from-red-500 dark:hover:to-red-600 text-white font-bold py-2.5 px-5 rounded-xl transition-all duration-300 shadow-md shadow-red-600/10 hover:shadow-lg hover:shadow-red-600/20 transform hover:-translate-y-0.5 text-sm">
                    + {{ __('Create Supplier') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($suppliers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-scg-gray-light dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">{{ __('Supplier') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">{{ __('Name') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">{{ __('Phone') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">{{ __('Email') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">{{ __('Country') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-scg-gray-dark dark:text-gray-200 uppercase">{{ __('Address') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($suppliers as $supplier)
                                        <tr onclick="window.location='{{ route('suppliers.show', $supplier) }}'" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 cursor-pointer">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $supplier->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $supplier->contact_person ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $supplier->phone ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $supplier->email ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $supplier->country ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $supplier->address ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $suppliers->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('No suppliers found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
