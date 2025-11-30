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
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-lg md:max-w-2xl w-full">

            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('messages.edit_event') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.edit_event_description') }}</p>
                    </div>
                    <button @click="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="updateEvent()" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="edit_title" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('messages.event_title') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit_title" x-model="editEventData.title" required
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                        <p x-show="editErrors.title" x-text="editErrors.title" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                    </div>

                    <!-- Date (full width) -->
                    <div>
                        <label for="edit_event_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('messages.date') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="edit_event_date" x-model="editEventData.date" required
                            min="{{ date('Y-m-d') }}"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                        <p x-show="editErrors.start_at" x-text="editErrors.start_at" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                    </div>

                    <!-- Start Time and End Time (side by side) -->
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Start Time -->
                        <div>
                            <label for="edit_event_start_time" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('messages.start_time') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="edit_event_start_time" x-model="editEventData.start_time" required
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="edit_event_end_time" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('messages.end_time') }}
                            </label>
                            <input type="time" id="edit_event_end_time" x-model="editEventData.end_time"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                            <p x-show="editErrors.end_at" x-text="editErrors.end_at" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                        </div>
                    </div>

                    <!-- Staff Selection (full width) -->
                    <div class="relative">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('messages.assign_staff') }}
                        </label>
                        <button type="button" @click="showEditStaffDropdown = !showEditStaffDropdown"
                            class="w-full px-3 py-2 text-left border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary">
                            <span x-text="editEventData.staff.length > 0 ? getEditSelectedStaffNames() : '{{ __('messages.select_staff') }}'"></span>
                            <svg class="absolute right-3 top-9 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="showEditStaffDropdown" @click.away="showEditStaffDropdown = false"
                            class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg max-h-48 overflow-auto">
                            <template x-for="user in availableStaff" :key="user.id">
                                <label class="flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                    <input type="checkbox" :checked="isEditStaffSelected(user.id)" @change="toggleEditStaff(user.id)"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300" x-text="user.name"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="edit_description" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('messages.description') }}
                        </label>
                        <textarea id="edit_description" x-model="editEventData.description" rows="3"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full"></textarea>
                        <p x-show="editErrors.description" x-text="editErrors.description" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
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
                        <button type="button" @click="openDeleteModal()"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('messages.cancel_event') }}
                        </button>

                        <div class="flex gap-3">
                            <button type="button" @click="closeEditModal()"
                                class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                {{ __('messages.cancel') }}
                            </button>
                            <button type="submit" :disabled="isSubmitting"
                                class="inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-md font-semibold text-sm text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmitting ? '{{ __('messages.saving') }}' : '{{ __('messages.save_changes') }}'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
