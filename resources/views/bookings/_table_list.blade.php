<div class="p-6">
    <!-- Filters -->
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Search') }}</label>
            <input type="text" x-model="searchQuery" @input="resetPagination()" placeholder="{{ __('Search by room, user, or purpose...') }}"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
        </div>
        <div class="w-full md:w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
            <select x-model="statusFilter" @change="resetPagination()"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
                <option value="all">{{ __('All Statuses') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="approved">{{ __('Approved') }}</option>
                <option value="rejected">{{ __('Rejected') }}</option>
            </select>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Sort By') }}</label>
            <select x-model="sortBy" @change="resetPagination()"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
                <option value="date_desc">{{ __('Date (Newest)') }}</option>
                <option value="date_asc">{{ __('Date (Oldest)') }}</option>
                <option value="room">{{ __('Room') }}</option>
                <option value="status">{{ __('Status') }}</option>
            </select>
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        <span x-text="`Showing ${paginatedBookings.length} of ${filteredBookings.length} bookings`"></span>
        <span x-show="filteredBookings.length !== bookings.length" x-text="` (${bookings.length} total)`" class="text-gray-400"></span>
    </div>

    <!-- Bookings Table - Desktop -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th @click="toggleSort('room')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 select-none">
                        <div class="flex items-center gap-1">
                            {{ __('Room') }}
                            <span x-show="sortBy === 'room_asc'" class="text-primary">↑</span>
                            <span x-show="sortBy === 'room_desc'" class="text-primary">↓</span>
                        </div>
                    </th>
                    @can('viewAny', App\Models\Booking::class)
                    <th @click="toggleSort('user')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 select-none">
                        <div class="flex items-center gap-1">
                            {{ __('User') }}
                            <span x-show="sortBy === 'user_asc'" class="text-primary">↑</span>
                            <span x-show="sortBy === 'user_desc'" class="text-primary">↓</span>
                        </div>
                    </th>
                    @endcan
                    <th @click="toggleSort('date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 select-none">
                        <div class="flex items-center gap-1">
                            {{ __('Date & Time') }}
                            <span x-show="sortBy === 'date_asc'" class="text-primary">↑</span>
                            <span x-show="sortBy === 'date_desc'" class="text-primary">↓</span>
                        </div>
                    </th>
                    <th @click="toggleSort('purpose')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 select-none">
                        <div class="flex items-center gap-1">
                            {{ __('Purpose') }}
                            <span x-show="sortBy === 'purpose_asc'" class="text-primary">↑</span>
                            <span x-show="sortBy === 'purpose_desc'" class="text-primary">↓</span>
                        </div>
                    </th>
                    <th @click="toggleSort('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 select-none">
                        <div class="flex items-center gap-1">
                            {{ __('Status') }}
                            <span x-show="sortBy === 'status_asc'" class="text-primary">↑</span>
                            <span x-show="sortBy === 'status_desc'" class="text-primary">↓</span>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <template x-for="booking in paginatedBookings" :key="booking.id">
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="booking.room?.name || 'Unknown Room'"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="booking.room?.location_details || ''"></div>
                        </td>
                        @can('viewAny', App\Models\Booking::class)
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100" x-text="booking.user?.name"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="booking.user?.email"></div>
                        </td>
                        @endcan
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100" x-text="formatDate(booking.start_time)"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="`${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}`"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-100 line-clamp-2" x-text="booking.purpose"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span x-show="booking.status === null" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                {{ __('Pending') }}
                            </span>
                            <span x-show="booking.status === true" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                {{ __('Approved') }}
                            </span>
                            <span x-show="booking.status === false" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                {{ __('Rejected') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openViewModal(booking)" class="text-primary hover:text-primary-dark dark:hover:text-primary-light mr-3">
                                {{ __('View') }}
                            </button>
                            <template x-if="booking.user_id === {{ Auth::id() }} && booking.status === null">
                                <button @click="openEditModal(booking)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    {{ __('Edit') }}
                                </button>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </template>
            <tbody x-show="filteredBookings.length === 0">
                <tr>
                    <td colspan="{{ auth()->user()->can('viewAny', App\Models\Booking::class) ? '6' : '5' }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        {{ __('No bookings found') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Bookings Cards - Mobile -->
    <div class="md:hidden space-y-4">
        <template x-for="booking in paginatedBookings" :key="booking.id">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100" x-text="booking.room?.name || 'Unknown Room'"></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="booking.room?.location_details || ''"></p>
                    </div>
                    <span x-show="booking.status === null" class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        {{ __('Pending') }}
                    </span>
                    <span x-show="booking.status === true" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        {{ __('Approved') }}
                    </span>
                    <span x-show="booking.status === false" class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                        {{ __('Rejected') }}
                    </span>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <div x-text="formatDate(booking.start_time)"></div>
                    <div x-text="`${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}`"></div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-3 line-clamp-2" x-text="booking.purpose"></p>
                <div class="flex gap-2">
                    <button @click="openViewModal(booking)" class="flex-1 px-3 py-2 text-sm bg-primary text-white rounded-md hover:bg-opacity-90 transition">
                        {{ __('View Details') }}
                    </button>
                    <template x-if="booking.user_id === {{ Auth::id() }} && booking.status === null">
                        <button @click="openEditModal(booking)" class="flex-1 px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-opacity-90 transition">
                            {{ __('Edit') }}
                        </button>
                    </template>
                </div>
            </div>
        </template>
        <div x-show="paginatedBookings.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
            {{ __('No bookings found') }}
        </div>
    </div>

    <!-- Pagination Controls -->
    <div x-show="totalPages > 1" class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center gap-2">
            <button @click="prevPage()" :disabled="currentPage === 1"
                class="px-3 py-2 text-sm font-medium rounded-md border transition"
                :class="currentPage === 1 ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                {{ __('Previous') }}
            </button>
            <button @click="nextPage()" :disabled="currentPage === totalPages"
                class="px-3 py-2 text-sm font-medium rounded-md border transition"
                :class="currentPage === totalPages ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                {{ __('Next') }}
            </button>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <span x-text="`Page ${currentPage} of ${totalPages}`"></span>
        </div>
    </div>
</div>
