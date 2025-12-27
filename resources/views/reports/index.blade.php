<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => __('Reports'), 'active' => true]
        ]" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('Generate Reports') }}
        </h2>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Events Report --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Events Report') }}</h3>
                        <form action="{{ route('events.export') }}" method="GET" class="flex flex-col sm:flex-row gap-2 items-center">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <label class=" ">Start Date:</label>
                                    <input type="date" name="start_date" required class="form-input rounded-md shadow-sm text-sm dark:bg-gray-700 dark:text-gray-200 @error('start_date', 'events_export') border-red-500 @enderror">
                                    <label class=" ">End Date:</label>
                                    <input type="date" name="end_date" required class="form-input rounded-md shadow-sm text-sm dark:bg-gray-700 dark:text-gray-200 @error('end_date', 'events_export') border-red-500 @enderror">
                                    <label class=" ">Status:</label>
                                    <select name="status" class="form-select rounded-md shadow-sm text-sm dark:bg-gray-700 dark:text-gray-200">
                                        <option value="">All Statuses</option>
                                        <option value="COMPLETED">Completed</option>
                                        <option value="NOT-COMPLETED">Not Completed</option>
                                    </select>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Export Events') }}
                                    </button>
                                </div>
                                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </form>
                    </div>

                    {{-- Bookings Report --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Bookings Report') }}</h3>
                        <form action="{{ route('bookings.export') }}" method="GET" class="flex flex-col sm:flex-row gap-2 items-center">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <label> Start Date:</label>
                                    <input type="date" name="start_date" required class="form-input rounded-md shadow-sm text-sm dark:bg-gray-700 dark:text-gray-200 @error('start_date') border-red-500 @enderror">
                                    <label> End Date:</label>
                                    <input type="date" name="end_date" required class="form-input rounded-md shadow-sm text-sm dark:bg-gray-700 dark:text-gray-200 @error('end_date') border-red-500 @enderror">
                                    <label> Status:</label>
                                    <select name="status" class="form-select rounded-md shadow-sm text-sm dark:bg-gray-700 dark:text-gray-200">
                                        <option value="all">All Statuses</option>
                                        <option value="1">Approved</option>
                                        <option value="0">Rejected</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Export Bookings') }}
                                    </button>
                                </div>
                                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
