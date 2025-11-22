<div class="p-6">
    <!-- Filters -->
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Search') }}</label>
            <input type="text" x-model="searchQuery" placeholder="{{ __('Search by room, user, or purpose...') }}"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
        </div>
        <div class="w-full md:w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
            <select x-model="statusFilter"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
                <option value="all">{{ __('All Statuses') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="approved">{{ __('Approved') }}</option>
                <option value="rejected">{{ __('Rejected') }}</option>
            </select>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Sort By') }}</label>
            <select x-model="sortBy"
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
        <span x-text="`Showing ${filteredBookings.length} of ${bookings.length} bookings`"></span>
    </div>

    <!-- Bookings Table - Desktop -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Room') }}</th>
                    @can('viewAny', App\Models\Booking::class)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('User') }}</th>
                    @endcan
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date & Time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Purpose') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="booking in filteredBookings" :key="booking.id">
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
                            <button @click="toggleDetails(booking.id)" class="text-primary hover:text-primary-dark dark:hover:text-primary-light mr-3">
                                {{ __('View') }}
                            </button>
                            <template x-if="booking.user_id === {{ Auth::id() }} && booking.status === null">
                                <button @click="openEditModal(booking)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    {{ __('Edit') }}
                                </button>
                            </template>
                        </td>
                    </tr>
                    <!-- Expandable Details Row -->
                    <tr x-show="expandedBooking === booking.id" x-transition class="bg-gray-50 dark:bg-gray-900">
                        <td colspan="{{ auth()->user()->can('viewAny', App\Models\Booking::class) ? '6' : '5' }}" class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Booking Details') }}</h4>
                                    <dl class="space-y-2 text-sm">
                                        <div>
                                            <dt class="text-gray-500 dark:text-gray-400 inline">{{ __('Students:') }}</dt>
                                            <dd class="text-gray-900 dark:text-gray-100 inline ml-2" x-text="booking.number_of_student"></dd>
                                        </div>
                                        <div>
                                            <dt class="text-gray-500 dark:text-gray-400 inline">{{ __('Equipment:') }}</dt>
                                            <dd class="text-gray-900 dark:text-gray-100 inline ml-2" x-text="booking.equipment_needed || 'None'"></dd>
                                        </div>
                                        <div>
                                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Purpose:') }}</dt>
                                            <dd class="text-gray-900 dark:text-gray-100 mt-1" x-text="booking.purpose"></dd>
                                        </div>
                                        <div x-show="booking.status === false && booking.rejection_reason">
                                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Rejection Reason:') }}</dt>
                                            <dd class="text-red-600 dark:text-red-400 mt-1" x-text="booking.rejection_reason"></dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <template x-if="booking.status === null && {{ auth()->user()->isAdmin() ? 'true' : 'false' }}">
                                        <div class="space-y-2">
                                            <form :action="`/bookings/${booking.id}/approve`" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm font-medium">
                                                    {{ __('Approve') }}
                                                </button>
                                            </form>
                                            <button @click="showRejectModal(booking)" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm font-medium">
                                                {{ __('Reject') }}
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="booking.user_id === {{ Auth::id() }}">
                                        <form :action="`/bookings/${booking.id}`" method="POST" @submit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition text-sm font-medium">
                                                {{ __('Cancel Booking') }}
                                            </button>
                                        </form>
                                    </template>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="filteredBookings.length === 0">
                    <td colspan="{{ auth()->user()->can('viewAny', App\Models\Booking::class) ? '6' : '5' }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        {{ __('No bookings found') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Bookings Cards - Mobile -->
    <div class="md:hidden space-y-4">
        <template x-for="booking in filteredBookings" :key="booking.id">
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
                    <button @click="toggleDetails(booking.id)" class="flex-1 px-3 py-2 text-sm bg-primary text-white rounded-md hover:bg-opacity-90 transition">
                        <span x-text="expandedBooking === booking.id ? '{{ __('Hide Details') }}' : '{{ __('View Details') }}'"></span>
                    </button>
                </div>
                <!-- Expandable Details -->
                <div x-show="expandedBooking === booking.id" x-transition class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <div class="text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Students:') }}</span>
                        <span class="text-gray-900 dark:text-gray-100 ml-2" x-text="booking.number_of_student"></span>
                    </div>
                    <div class="text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Equipment:') }}</span>
                        <span class="text-gray-900 dark:text-gray-100 ml-2" x-text="booking.equipment_needed || 'None'"></span>
                    </div>
                    <template x-if="booking.status === null && {{ auth()->user()->isAdmin() ? 'true' : 'false' }}">
                        <div class="flex gap-2 pt-2">
                            <form :action="`/bookings/${booking.id}/approve`" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-green-600 text-white rounded-md text-sm">{{ __('Approve') }}</button>
                            </form>
                            <button @click="showRejectModal(booking)" class="flex-1 px-3 py-2 bg-red-600 text-white rounded-md text-sm">{{ __('Reject') }}</button>
                        </div>
                    </template>
                </div>
            </div>
        </template>
        <div x-show="filteredBookings.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
            {{ __('No bookings found') }}
        </div>
    </div>
</div>
