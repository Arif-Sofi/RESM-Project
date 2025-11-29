<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => __('Rooms'), 'url' => route('rooms.index')],
            ['label' => __('Edit Room'), 'active' => true]
        ]" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('Edit Room') }}
        </h2>
    </x-slot>

    <div class="w-full py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <header class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Room Details') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Update the room information.') }}
                        </p>
                    </header>

                    <form method="POST" action="{{ route('rooms.update', $room) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Room Name -->
                        <div>
                            <x-input-label for="name" :value="__('Room Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $room->name)" required autofocus placeholder="{{ __('e.g., Conference Room A') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Location Details -->
                        <div>
                            <x-input-label for="location_details" :value="__('Location Details')" />
                            <x-text-input id="location_details" name="location_details" type="text" class="mt-1 block w-full" :value="old('location_details', $room->location_details)" placeholder="{{ __('e.g., Building A, 2nd Floor') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('location_details')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" placeholder="{{ __('Describe the room facilities, capacity, equipment available, etc.') }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm">{{ old('description', $room->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <!-- Delete Button -->
                            <button type="button" onclick="document.getElementById('delete-form').submit()"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                {{ __('Delete Room') }}
                            </button>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('rooms.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    {{ __('Cancel') }}
                                </a>
                                <x-primary-button>
                                    {{ __('Update Room') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    <!-- Hidden Delete Form -->
                    <form id="delete-form" method="POST" action="{{ route('rooms.destroy', $room) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('button[onclick*="delete-form"]').addEventListener('click', async function(e) {
            e.preventDefault();
            const confirmed = await window.confirmDelete('{{ __("this room") }}');
            if (confirmed) {
                document.getElementById('delete-form').submit();
            }
        });
    </script>
</x-app-layout>
