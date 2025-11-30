<div class="p-6">
    <!-- Filters -->
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.search') }}</label>
            <input type="text" x-model="searchQuery" placeholder="{{ __('messages.search_events_placeholder') }}"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
        </div>
        <div class="w-full md:w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.sort_by') }}</label>
            <select x-model="sortBy"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
                <option value="date_desc">{{ __('messages.date_newest') }}</option>
                <option value="date_asc">{{ __('messages.date_oldest') }}</option>
                <option value="title">{{ __('messages.title') }}</option>
            </select>
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        <span x-text="`{{ __('messages.showing') }} ${filteredEventsList.length} {{ __('messages.events_lowercase') }}`"></span>
    </div>

    <!-- Events Table - Desktop -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('messages.event_title') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('messages.date') }} & {{ __('messages.time') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('messages.assigned_staff') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('messages.created_by') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('messages.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="event in filteredEventsList" :key="event.id">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="event.title"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1" x-text="event.description || ''"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100" x-text="formatDate(event.start_at)"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="`${formatTime(event.start_at)}${event.end_at ? ' - ' + formatTime(event.end_at) : ''}`"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <template x-for="staff in (event.staff || []).slice(0, 3)" :key="staff.id">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200" x-text="staff.name"></span>
                                </template>
                                <span x-show="(event.staff || []).length > 3" class="text-xs text-gray-500 dark:text-gray-400" x-text="`+${event.staff.length - 3} more`"></span>
                                <span x-show="!event.staff || event.staff.length === 0" class="text-xs text-gray-400 dark:text-gray-500">{{ __('messages.no_staff') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <template x-if="event.user_id === authUserId">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">{{ __('messages.you') }}</span>
                                </template>
                                <template x-if="event.user_id !== authUserId">
                                    <span class="text-sm text-gray-900 dark:text-gray-100" x-text="event.creator?.name || 'Unknown'"></span>
                                </template>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <template x-if="event.user_id === authUserId">
                                <button @click="openEditModal(event)" class="text-primary hover:text-primary/80 mr-3">
                                    {{ __('messages.edit') }}
                                </button>
                            </template>
                            <button @click="openViewModal(event)" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                {{ __('messages.view') }}
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Empty State -->
        <div x-show="filteredEventsList.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('messages.no_events_found') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.click_calendar_to_create') }}</p>
        </div>
    </div>

    <!-- Events Cards - Mobile -->
    <div class="md:hidden space-y-4">
        <template x-for="event in filteredEventsList" :key="event.id">
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100" x-text="event.title"></h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2" x-text="event.description || ''"></p>
                    </div>
                    <template x-if="event.user_id === authUserId">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">{{ __('messages.you') }}</span>
                    </template>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span x-text="formatDate(event.start_at)"></span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="`${formatTime(event.start_at)}${event.end_at ? ' - ' + formatTime(event.end_at) : ''}`"></span>
                    </div>
                    <div class="flex flex-wrap gap-1 mt-2">
                        <template x-for="staff in (event.staff || []).slice(0, 2)" :key="staff.id">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200" x-text="staff.name"></span>
                        </template>
                        <span x-show="(event.staff || []).length > 2" class="text-xs text-gray-500 dark:text-gray-400" x-text="`+${event.staff.length - 2} more`"></span>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 flex justify-end gap-3">
                    <template x-if="event.user_id === authUserId">
                        <button @click="openEditModal(event)" class="text-sm text-primary hover:text-primary/80">
                            {{ __('messages.edit') }}
                        </button>
                    </template>
                    <button @click="openViewModal(event)" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        {{ __('messages.view') }}
                    </button>
                </div>
            </div>
        </template>

        <!-- Empty State Mobile -->
        <div x-show="filteredEventsList.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('messages.no_events_found') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.click_calendar_to_create') }}</p>
        </div>
    </div>
</div>
