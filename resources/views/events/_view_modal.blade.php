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
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full">

            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('messages.event_details') }}</h3>
                    <button @click="closeViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <template x-if="viewEventData">
                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.event_title') }}</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="viewEventData.title"></p>
                        </div>

                        <!-- Location -->
                        <div x-show="viewEventData.location">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Location') }}</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewEventData.location"></p>
                        </div>

                        <!-- Date & Time -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.date') }}</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100" x-text="formatDate(viewEventData.start_at)"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.time') }}</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    <span x-text="formatTime(viewEventData.start_at)"></span>
                                    <span x-show="viewEventData.end_at"> - <span x-text="formatTime(viewEventData.end_at)"></span></span>
                                </p>
                            </div>
                        </div>

                        <!-- Creator -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.created_by') }}</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewEventData.creator?.name || 'Unknown'"></p>
                        </div>

                        <!-- Staff -->
                        <div x-show="viewEventData.staff && viewEventData.staff.length > 0">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.assigned_staff') }}</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                <template x-for="staff in viewEventData.staff" :key="staff.id">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200" x-text="staff.name"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Other Staff / Team -->
                        <div x-show="viewEventData.other_staff">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.assigned_staff') }} (Other)</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewEventData.other_staff"></p>
                        </div>

                        <!-- Description -->
                        <div x-show="viewEventData.description">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.description') }}</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap" x-text="viewEventData.description"></p>
                        </div>

                        <div x-show="viewEventData.status">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.status') }}</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100 capitalize" x-text="viewEventData.status"></p>
                        </div>
                    </div>
                </template>

                <!-- Close Button -->
                <div class="flex justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="closeViewModal()"
                        class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        {{ __('messages.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
