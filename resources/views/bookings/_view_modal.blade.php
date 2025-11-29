<div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showViewModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            @click="closeViewModal()"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="showViewModal"
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
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Booking Details') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="viewBookingData?.room?.name"></p>
                    </div>
                    <button @click="closeViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Status Badge -->
                <div class="mb-6">
                    <span x-show="viewBookingData?.status === null" class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                        {{ __('Pending') }}
                    </span>
                    <span x-show="viewBookingData?.status === true" class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                        {{ __('Approved') }}
                    </span>
                    <span x-show="viewBookingData?.status === false" class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                        {{ __('Rejected') }}
                    </span>
                </div>

                <!-- Booking Info -->
                <div class="space-y-4">
                    <!-- Room & Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Room') }}</div>
                            <div class="text-lg font-medium text-gray-900 dark:text-gray-100" x-text="viewBookingData?.room?.name"></div>
                            <div class="text-sm text-gray-600 dark:text-gray-400" x-text="viewBookingData?.room?.location_details"></div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Date & Time') }}</div>
                            <div class="text-lg font-medium text-gray-900 dark:text-gray-100" x-text="formatDate(viewBookingData?.start_time)"></div>
                            <div class="text-sm text-gray-600 dark:text-gray-400" x-text="`${formatTime(viewBookingData?.start_time)} - ${formatTime(viewBookingData?.end_time)}`"></div>
                        </div>
                    </div>

                    <!-- Students & Equipment -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Number of Students') }}</div>
                            <div class="text-lg font-medium text-gray-900 dark:text-gray-100" x-text="viewBookingData?.number_of_student"></div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Equipment Needed') }}</div>
                            <div class="text-lg font-medium text-gray-900 dark:text-gray-100" x-text="viewBookingData?.equipment_needed || 'None'"></div>
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Purpose') }}</div>
                        <div class="text-gray-900 dark:text-gray-100 mt-1" x-text="viewBookingData?.purpose"></div>
                    </div>

                    <!-- Rejection Reason (if rejected) -->
                    <div x-show="viewBookingData?.status === false && viewBookingData?.rejection_reason" class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg p-4">
                        <div class="text-sm text-red-600 dark:text-red-400 font-medium">{{ __('Rejection Reason') }}</div>
                        <div class="text-red-700 dark:text-red-300 mt-1" x-text="viewBookingData?.rejection_reason"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <!-- Cancel Booking Button (only for own pending bookings) -->
                    <template x-if="viewBookingData?.user_id === {{ Auth::id() }} && viewBookingData?.status === null">
                        <button type="button" @click="cancelBooking(viewBookingData.id)"
                            class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('Cancel Booking') }}
                        </button>
                    </template>

                    <!-- Empty div to push Close button to right when Cancel is not shown -->
                    <template x-if="!(viewBookingData?.user_id === {{ Auth::id() }} && viewBookingData?.status === null)">
                        <div></div>
                    </template>

                    <!-- Close Button -->
                    <button type="button" @click="closeViewModal()"
                        class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
