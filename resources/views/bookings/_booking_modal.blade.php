<!-- このモーダルは、通常 app.blade.php の下部や、予約ボタンがあるページに配置されます -->
<x-modal name="room-booking-flow-modal" focusable>
    <div x-data="roomBookingFlow()">
        <!-- Step 1: 部屋選択 -->
        <div x-show="currentStep === 1" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Select a Room') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Choose a room from the list below.') }}
            </p>

            <div class="mt-6">
                @foreach ($rooms as $room)
                    <label class="block mb-2 cursor-pointer">
                        {{-- x-model で選択された部屋IDを selectedRoomId にバインド --}}
                        <input type="radio" name="selected_room" value="{{ $room->id }}" x-model="selectedRoomId" class="mr-2">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $room->name }}</span>
                        <span class="text-gray-500 text-sm block pl-6">{{ $room->description }}</span>
                        <span class="text-gray-500 text-sm block pl-6">{{ $room->location_details }}</span>
                        <hr class="my-2 border-gray-200 dark:border-gray-700">
                    </label>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('messages.cancel') }}
                </x-secondary-button>
                <x-primary-button class="ms-3" x-on:click="
                    if (selectedRoomId) {
                        currentStep = 2; // 部屋が選択されていれば次のステップへ
                    } else {
                        alert('部屋を選択してください。');
                    }
                ">
                    {{ __('Next') }}
                </x-primary-button>
            </div>
        </div>

        <!-- Step 2 & 3 は一つのフォーム内に含める -->
        <form method="POST" action="{{ route('bookings.store') }}" class="p-6" x-show="currentStep === 2 || currentStep === 3">
            @csrf
            {{-- フォーム送信用の隠しフィールド --}}
            <input type="hidden" name="room_id" x-model="selectedRoomId">
            {{-- start_time と end_time は DateオブジェクトからISO形式で文字列化 --}}
            <input type="hidden" name="start_time" :value="selectedDate && selectedTime ? new Date(selectedDate + 'T' + selectedTime + ':00').toISOString().slice(0, 19).replace('T', ' ') : ''">
            {{-- 終了時刻は開始時刻から1時間後と仮定（必要に応じて時間入力フィールドを追加） --}}
            <input type="hidden" name="end_time" :value="selectedDate && selectedTime ? new Date(new Date(selectedDate + 'T' + selectedTime + ':00').getTime() + 60 * 60 * 1000).toISOString().slice(0, 19).replace('T', ' ') : ''">

            <!-- Step 2: 日付と時間選択 -->
            <div x-show="currentStep === 2">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Select Date and Time') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Previous bookings for this room:') }}
                </p>

                {{-- 既存予約の表示 --}}
                <div class="mt-4 max-h-40 overflow-y-auto border p-2 rounded">
                    <template x-if="previousBookings.length > 0">
                        <ul>
                            <template x-for="booking in previousBookings" :key="booking.id">
                                <li class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                    <span x-text="new Date(booking.start_time).toLocaleString()"></span> - <span x-text="new Date(booking.end_time).toLocaleString()"></span>
                                    (<span x-text="booking.purpose"></span>) <br>booking made by {{ auth()->user()->name }}
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="previousBookings.length === 0">
                        <p class="text-sm text-gray-500">{{ __('No previous bookings found for this room.') }}</p>
                    </template>
                </div>

                <div class="mt-6">
                    <div class="mb-4">
                        <x-input-label for="booking_date" :value="__('Date')" />
                        <x-text-input id="booking_date" name="booking_date" type="date" class="mt-1 block w-full" x-model="selectedDate" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="booking_time" :value="__('Time')" />
                        <x-text-input id="booking_time" name="booking_time" type="time" class="mt-1 block w-full" x-model="selectedTime" />
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
                    {{ __('Booking Details') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Please provide additional details for your booking.') }}
                </p>

                <div class="mt-6">
                    <div class="mb-4">
                        <x-input-label for="number_of_students" :value="__('Number of Students')" />
                        <x-text-input id="number_of_students" name="number_of_student" type="number" class="mt-1 block w-full" x-model="numberOfStudents" required />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="equipment_needed" :value="__('Equipment Needed (Optional)')" />
                        <textarea id="equipment_needed" name="equipment_needed" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" x-model="equipmentNeeded"></textarea>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="purpose" :value="__('Purpose')" />
                        <textarea id="purpose" name="purpose" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" x-model="purpose" required></textarea>
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
</x-modal>
