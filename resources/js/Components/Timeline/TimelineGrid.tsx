import React, { useEffect, useState } from 'react';
import { Room, Booking } from '@/Types/models';
import TimelineRow from './TimelineRow';
import {
  generateTimeSlots,
  BUSINESS_HOURS,
  calculateEventPosition,
} from '@/Utils/timelineUtils';
import { format, setHours, setMinutes } from 'date-fns';

interface TimelineGridProps {
  rooms: Room[];
  bookings: Booking[];
  dayStart: Date;
  onEventClick: (booking: Booking) => void;
  onSlotClick?: (room: Room, date: Date) => void;
}

export default function TimelineGrid({
  rooms,
  bookings,
  dayStart,
  onEventClick,
  onSlotClick,
}: TimelineGridProps) {
  const timeSlots = generateTimeSlots(dayStart);
  const [currentTimePosition, setCurrentTimePosition] = useState<number | null>(null);

  // Update current time indicator every minute
  useEffect(() => {
    const updateCurrentTime = () => {
      const now = new Date();
      const todayStart = setHours(setMinutes(dayStart, 0), BUSINESS_HOURS.startHour);
      const todayEnd = setHours(setMinutes(dayStart, 0), BUSINESS_HOURS.endHour);

      // Check if current time is on the displayed day and within business hours
      if (
        format(now, 'yyyy-MM-dd') === format(dayStart, 'yyyy-MM-dd') &&
        now >= todayStart &&
        now <= todayEnd
      ) {
        const position = calculateEventPosition(now, now, dayStart, 'day');
        setCurrentTimePosition(position.left);
      } else {
        setCurrentTimePosition(null);
      }
    };

    updateCurrentTime();
    const interval = setInterval(updateCurrentTime, 60000); // Update every minute

    return () => clearInterval(interval);
  }, [dayStart]);

  return (
    <div className="border rounded-lg shadow-sm bg-white overflow-hidden">
      {/* Time Axis Header */}
      <div className="flex border-b border-gray-300 bg-gray-100 sticky top-0 z-10">
        {/* Empty corner for room names column */}
        <div className="w-52 flex-shrink-0 border-r border-gray-300 bg-gray-100 px-4 py-3">
          <div className="text-sm font-semibold text-gray-700">Room</div>
        </div>

        {/* Time slots */}
        <div className="flex-1 relative overflow-x-auto">
          <div className="flex h-14">
            {timeSlots.map((slot, index) => (
              <div
                key={slot.hour}
                className="flex-1 min-w-[80px] border-r border-gray-200 last:border-r-0 px-3 py-3 text-center"
              >
                <div className="text-sm font-medium text-gray-700 whitespace-nowrap">{slot.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Room Rows */}
      <div className="relative">
        {rooms.length === 0 ? (
          <div className="p-8 text-center text-gray-500">
            <svg className="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p className="text-sm">No rooms available</p>
            <p className="text-xs mt-1">Try adjusting your filters</p>
          </div>
        ) : (
          <>
            {rooms.map(room => (
              <TimelineRow
                key={room.id}
                room={room}
                bookings={bookings}
                dayStart={dayStart}
                onEventClick={onEventClick}
                onSlotClick={onSlotClick}
              />
            ))}

            {/* Current Time Indicator */}
            {currentTimePosition !== null && (
              <div
                className="absolute top-0 bottom-0 w-0.5 bg-red-500 z-20 pointer-events-none"
                style={{ left: `calc(13rem + ${currentTimePosition}%)` }}
              >
                <div className="absolute -top-2 -left-2 w-4 h-4 bg-red-500 rounded-full"></div>
              </div>
            )}

            {/* Vertical grid lines for time slots */}
            <div className="absolute top-0 bottom-0 left-52 right-0 pointer-events-none">
              {timeSlots.map((slot, index) => {
                if (index === 0) return null; // Skip first line
                const position = ((index) / timeSlots.length) * 100;
                return (
                  <div
                    key={`grid-${slot.hour}`}
                    className="absolute top-0 bottom-0 w-px bg-gray-200"
                    style={{ left: `${position}%` }}
                  />
                );
              })}
            </div>
          </>
        )}
      </div>
    </div>
  );
}
