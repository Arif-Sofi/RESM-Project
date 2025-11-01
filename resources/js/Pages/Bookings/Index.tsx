import React, { useState, useMemo } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Components/Layout/AppLayout';
import BookingTimeline from '@/Components/Booking/Timeline';
import RoomSidebar from '@/Components/Booking/RoomSidebar';
import SearchBar from '@/Components/Booking/SearchBar';
import { Room, Booking, PageProps } from '@/Types';

interface Props extends PageProps {
  rooms: Room[];
  bookings: Booking[];
  initialDateRange: {
    start: string;
    end: string;
  };
}

export default function Index({ rooms, bookings, initialDateRange }: Props) {
  const [selectedRoomIds, setSelectedRoomIds] = useState<number[]>(rooms.map(r => r.id));
  const [searchQuery, setSearchQuery] = useState('');
  const [filters, setFilters] = useState({
    hasProjector: false,
    minCapacity: null as number | null,
  });

  // Filter rooms based on search and filters
  const filteredRooms = useMemo(() => {
    return rooms.filter((room) => {
      // Search query filter
      if (searchQuery && !room.name.toLowerCase().includes(searchQuery.toLowerCase())) {
        return false;
      }

      // Projector filter
      if (filters.hasProjector && !room.has_projector) {
        return false;
      }

      // Capacity filter
      if (filters.minCapacity && room.capacity < filters.minCapacity) {
        return false;
      }

      return true;
    });
  }, [rooms, searchQuery, filters]);

  // Only show rooms that are both filtered AND selected
  const displayRooms = useMemo(() => {
    if (selectedRoomIds.length === 0) {
      // If no rooms selected, show all filtered rooms
      return filteredRooms;
    }
    return filteredRooms.filter(room => selectedRoomIds.includes(room.id));
  }, [filteredRooms, selectedRoomIds]);

  // Update selected rooms when filters change
  React.useEffect(() => {
    // Auto-select all filtered rooms when filters change
    setSelectedRoomIds(filteredRooms.map(r => r.id));
  }, [searchQuery, filters.hasProjector, filters.minCapacity]);

  return (
    <AppLayout>
      <Head title="Book a Room" />

      <div className="min-h-screen bg-gray-50 pb-12">
        {/* Header */}
        <div className="bg-white shadow-sm border-b border-gray-200">
          <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 py-8">
            <div className="md:flex md:items-center md:justify-between mb-6">
              <div className="flex-1 min-w-0">
                <h1 className="text-3xl font-bold text-gray-900">
                  Room Booking
                </h1>
                <p className="mt-2 text-base text-gray-600">
                  Select an available time slot to book a room
                </p>
              </div>
            </div>

            <SearchBar
              value={searchQuery}
              onChange={setSearchQuery}
              filters={filters}
              onFiltersChange={setFilters}
            />
          </div>
        </div>

        {/* Main Content */}
        <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 py-8">
          <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {/* Sidebar */}
            <div className="lg:col-span-1 order-2 lg:order-1">
              <RoomSidebar
                rooms={filteredRooms}
                selectedRoomIds={selectedRoomIds}
                onSelectionChange={setSelectedRoomIds}
              />
            </div>

            {/* Timeline */}
            <div className="lg:col-span-3 order-1 lg:order-2">
              {displayRooms.length === 0 ? (
                <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-16 text-center">
                  <svg className="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <h3 className="mt-4 text-base font-semibold text-gray-900">No rooms found</h3>
                  <p className="mt-2 text-sm text-gray-500">
                    Try adjusting your search or filters
                  </p>
                </div>
              ) : (
                <BookingTimeline
                  rooms={displayRooms}
                  bookings={bookings}
                  initialDateRange={initialDateRange}
                />
              )}
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
