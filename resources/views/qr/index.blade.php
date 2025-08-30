<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('navigation.qr') }}
        </h2>
    </x-slot>
    <div class="w-full py-12">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="items-center flex flex-col md:flex-row">
                    <div class="basis-1/2">
                        <form action="{{ route('qr.generate') }}" method="POST" class="mb-6">
                            @csrf
                            <div class="mb-4">
                                <label for="url"
                                    class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Enter
                                    URL:</label>
                                <input type="text" name="url" id="url"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                            </div>
                            <x-primary-button type="submit" class="h-12 w-28 justify-center">
                                Generate QR Code
                            </x-primary-button>
                        </form>
                    </div>
                    <div class="basis-1/2 pl-12">
                        @if (isset($qrCode))
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Generated QR
                                    Code:</h3>
                                <div class="p-4 bg-gray-100 dark:bg-gray-700 inline-block">
                                    {!! $qrCode !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
