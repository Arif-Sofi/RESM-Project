import React from 'react';
import { Room, Booking } from '@/Types/models';
import TimelineEvent from './TimelineEvent';
import { isSameDay, parseISOSafe } from '@/Utils/timelineUtils';

interface TimelineRowProps {
  room: Room;
  bookings: Booking[];
  dayStart: Date;
  onEventClick: (booking: Booking) => void;
  onSlotClick?: (room: Room, date: Date) => void;
}

export default function TimelineRow({
  room,
  bookings,
  dayStart,
  onEventClick,
  onSlotClick,
}: TimelineRowProps) {
  // Filter bookings for this room and day
  const dayBookings = bookings.filter(booking => {
    if (booking.room_id !== room.id) return false;
    const bookingStart = parseISOSafe(booking.start_time);
    return isSameDay(bookingStart, dayStart);
  });

  const handleRowClick = (e: React.MouseEvent<HTMLDivElement>) => {
    // Only trigger if clicking on the row itself, not on a booking
    if (e.target === e.currentTarget && onSlotClick) {
      onSlotClick(room, dayStart);
    }
  };

  return (
    <div className="flex border-b border-gray-200 hover:bg-gray-50 min-h-[96px]">
      {/* Room Name Column */}
      <div className="w-52 flex-shrink-0 px-4 py-4 border-r border-gray-200 bg-gray-50 flex flex-col justify-center">
        <div className="font-medium text-sm text-gray-900 truncate mb-2">{room.name}</div>
        <div className="text-xs text-gray-500 flex items-center gap-3">
          <span title="Capacity" className="flex items-center">
            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {room.capacity}
          </span>
          {room.has_projector && (
            <span title="Has Projector" className="text-indigo-600 flex items-center">
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </span>
          )}
        </div>
      </div>

      {/* Timeline Events Column */}
      <div
        className="flex-1 relative cursor-pointer"
        onClick={handleRowClick}
      >
        {/* Render bookings */}
        {dayBookings.map(booking => (
          <TimelineEvent
            key={booking.id}
            booking={booking}
            dayStart={dayStart}
            onClick={onEventClick}
          />
        ))}
      </div>
    </div>
  );
}
