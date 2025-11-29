<div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showEditModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            @click="closeEditModal()"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="showEditModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-lg md:max-w-2xl lg:max-w-4xl w-full">

            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Edit Booking') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Update the booking details below') }}</p>
                    </div>
                    <button @click="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="updateBooking()" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Room Selection -->
                        <div>
                            <label for="edit_room_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Room') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="edit_room_id" x-model="editBookingData.room_id" @change="checkEditAvailability()" required
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                                <option value="">{{ __('Select a room') }}</option>
                                <template x-for="room in rooms" :key="room.id">
                                    <option :value="room.id" x-text="`${room.name} - ${room.location_details || ''}`"></option>
                                </template>
                            </select>
                            <p x-show="editErrors.room_id" x-text="editErrors.room_id" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        </div>

                        <!-- Date Selection -->
                        <div>
                            <label for="edit_booking_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Date') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="edit_booking_date" x-model="editBookingData.date" @change="updateEditTimes(); checkEditAvailability()" required
                                min="{{ date('Y-m-d') }}"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                            <p x-show="editErrors.start_time" x-text="editErrors.start_time" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label for="edit_start_time" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Start Time') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="edit_start_time" x-model="editBookingData.start_time" @change="updateEditTimes(); checkEditAvailability()" required
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                            <p x-show="editErrors.start_time" x-text="editErrors.start_time" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="edit_end_time" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('End Time') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="edit_end_time" x-model="editBookingData.end_time" @change="updateEditTimes(); checkEditAvailability()" required
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                            <p x-show="editErrors.end_time" x-text="editErrors.end_time" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        </div>

                        <!-- Number of Students -->
                        <div>
                            <label for="edit_number_of_student" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Number of Students') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="edit_number_of_student" x-model="editBookingData.number_of_student" min="1" max="100" required
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                            <p x-show="editErrors.number_of_student" x-text="editErrors.number_of_student" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        </div>

                        <!-- Equipment Needed -->
                        <div>
                            <label for="edit_equipment_needed" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Equipment Needed') }}
                            </label>
                            <input type="text" id="edit_equipment_needed" x-model="editBookingData.equipment_needed" placeholder="{{ __('e.g., Projector, Whiteboard, Computer') }}"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                            <p x-show="editErrors.equipment_needed" x-text="editErrors.equipment_needed" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        </div>
                    </div>

                    <!-- Purpose (Full Width) -->
                    <div>
                        <label for="edit_purpose" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Purpose') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="edit_purpose" x-model="editBookingData.purpose" rows="3" required placeholder="{{ __('Describe the purpose of this booking...') }}"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full"></textarea>
                        <p x-show="editErrors.purpose" x-text="editErrors.purpose" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                    </div>

                    <!-- Clash Error Display -->
                    <div x-show="editClashError" x-transition class="p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-red-700 dark:text-red-300" x-text="editClashError"></p>
                        </div>
                    </div>

                    <!-- General Error Display -->
                    <div x-show="editGeneralError" x-transition class="p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-red-700 dark:text-red-300" x-text="editGeneralError"></p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <!-- Delete Button (Left) -->
                        <button type="button" @click="deleteBooking()"
                            class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('Cancel Booking') }}
                        </button>

                        <!-- Cancel/Update Buttons (Right) -->
                        <div class="flex gap-3">
                            <button type="button" @click="closeEditModal()"
                                class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" :disabled="!!editClashError || isSubmitting"
                                class="inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-md font-semibold text-sm text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmitting ? '{{ __('Updating...') }}' : '{{ __('Update Booking') }}'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
