<div x-show="showDateBookingFlow" x-transition class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
    <!-- Step 1: 日付選択 -->
    <div x-show="currentStep === 1">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            {{ __('messages.date_select') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ __('messages.date_choose') }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <x-input-label for="booking_date" :value="__('Date')" />
                <x-text-input id="booking_date" name="booking_date" type="date" class="mt-1 block w-full"
                    x-model="selectedDate" min="{{ now()->toDateString() }}"/>
            </div>
            <div>
                <x-input-label for="start_time" :value="__('Start time')" />
                <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full"
                    x-model="selectedStartTime" />
            </div>
            <div>
                <x-input-label for="end_time" :value="__('End time')" />
                <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full"
                    x-model="selectedEndTime" />
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="hideDateBooking()">
                {{ __('messages.cancel') }}
            </x-secondary-button>
            <x-primary-button class="ms-3"
                x-on:click="
                if (selectedDate!='' && selectedStartTime!='' && selectedEndTime!='') {
                    currentStep = 2;
                } else {
                    alert('{{ __('messages.date_select') }}');
                }
            ">
                {{ __('messages.next') }}
            </x-primary-button>
        </div>
    </div>

    <!-- Step 2 & 3 -->
    <form method="POST" action="{{ route('bookings.store') }}" x-show="currentStep === 2 || currentStep === 3">
        @csrf
        {{-- フォーム送信用の隠しフィールド --}}
        <input type="hidden" name="room_id" x-model="selectedRoomId">
        {{-- start_time と end_time は DateオブジェクトからISO形式で文字列化 --}}
        <input type="hidden" name="start_time"
            :value="selectedDate && selectedStartTime ? new Date(selectedDate + 'T' + selectedStartTime + ':00').toISOString()
                .slice(0, 19).replace('T', ' ') : ''">
        {{-- 終了時刻は開始時刻から1時間後と仮定（必要に応じて時間入力フィールドを追加） --}}
        <input type="hidden" name="end_time"
            :value="selectedDate && selectedEndTime ? new Date(selectedDate + 'T' + selectedEndTime + ':00').toISOString()
                .slice(0, 19).replace('T', ' ') : ''">

        <!-- Step 2: 日付と時間選択 -->
        <div x-show="currentStep === 2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('messages.room_select') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('messages.booking_previous') }} {{ __(' ') }} <span x-text="selectedDate"></span>
                {{ __(' from ') }} <span x-text="selectedStartTime"></span>{{ __(' - ') }}
                <span x-text="selectedEndTime"></span> {{ __(':') }}
            </p>

            {{-- 既存予約の表示 --}}
            <div
                class="mb-6 max-h-40 overflow-y-auto border border-gray-200 dark:border-gray-700 p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                <template x-if="previousBookings.length > 0">
                    <ul class="space-y-2">
                        <template x-for="booking in previousBookings" :key="booking.id">
                            <li
                                class="text-sm text-gray-700 dark:text-gray-300 p-2 bg-white dark:bg-gray-800 rounded border">
                                <div class="font-medium">
                                    <span
                                        x-text="new Date(booking.start_time).toLocaleString([], { dateStyle: 'short', timeStyle: 'short' })"></span>
                                    -
                                    <span
                                        x-text="new Date(booking.end_time).toLocaleTimeString([], { timeStyle: 'short' })"></span>
                                </div>
                                <div class="text-gray-600 dark:text-gray-400">
                                    Purpose: <span x-text="booking.purpose"></span>
                                </div>
                                <div class="text-gray-500 dark:text-gray-500 text-xs">
                                    Booked by: <span x-text="booking.user ? booking.user.name : 'Unknown User'"></span>
                                </div>
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="previousBookings.length === 0">
                    <p class="text-sm text-gray-500 text-center py-4">
                        {{ __('Select a Room to see previous bookings.') }}</p>
                </template>
            </div>

            <div class="mt-6">
                <div class="space-y-4">
                    <div class="grid 2xl:grid-cols-6 xl:grid-cols-5 lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 gap-4">
                        @foreach ($rooms as $room)
                            <label
                                class="block cursor-pointer p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center">
                                    <input type="radio" name="selected_room" value="{{ $room->id }}"
                                        x-model="selectedRoomId" class="mr-4">
                                    <div>
                                        <span
                                            class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $room->name }}</span>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                            {{ $room->description }}</p>
                                        <p class="text-gray-500 dark:text-gray-500 text-sm">
                                            {{ $room->location_details }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <p x-show="clashDetected" class="text-sm text-red-600 dark:text-red-400 mt-2" x-text="clashMessage"></p>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click.prevent="currentStep = 1">
                    {{ __('messages.previous') }}
                </x-secondary-button>
                <x-primary-button class="ms-3" x-on:click.prevent="checkClash()">
                    {{ __('messages.next') }}
                </x-primary-button>
            </div>
        </div>

        <!-- Step 3: 予約詳細入力 -->
        <div x-show="currentStep === 3">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('messages.booking_details') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('messages.booking_additional_detail') }}
            </p>

            <div class="mt-6">
                <div class="mb-4">
                    <x-input-label for="number_of_students" :value="__('Number of Students')" />
                    <x-text-input id="number_of_students" name="number_of_student" type="number"
                        class="mt-1 block w-full" x-model="numberOfStudents" min="0" required
                        @input="if ($event.target.value < 0) $event.target.value = 0;" />
                </div>
                <div class="mb-4">
                    <x-input-label for="equipment_needed" :value="__('Equipment Needed (Optional)')" />
                    <textarea id="equipment_needed" name="equipment_needed"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        x-model="equipmentNeeded"></textarea>
                </div>
                <div class="mb-4">
                    <x-input-label for="purpose" :value="__('Purpose')" />
                    <textarea id="purpose" name="purpose"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        x-model="purpose" required></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click.prevent="currentStep = 2">
                    {{ __('messages.previous') }}
                </x-secondary-button>
                <x-primary-button class="ms-3" type="submit">
                    {{ __('messages.save') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</div>
