import React, { useEffect, useState, useRef } from 'react';
import { Room, Booking } from '@/Types/models';
import TimelineRow from './TimelineRow';
import {
  generateTimeSlots,
  BUSINESS_HOURS,
  calculateEventPosition,
  getHoursConfig,
} from '@/Utils/timelineUtils';
import { format, setHours, setMinutes } from 'date-fns';

interface TimelineGridProps {
  rooms: Room[];
  bookings: Booking[];
  dayStart: Date;
  onEventClick: (booking: Booking) => void;
  onSlotClick?: (room: Room, date: Date) => void;
  slotWidth?: number; // Width in pixels for each hour slot
  show24Hours?: boolean; // Toggle between business hours and full 24-hour view
  onContainerMeasured?: (width: number) => void; // Callback to report measured container width
}

export default function TimelineGrid({
  rooms,
  bookings,
  dayStart,
  onEventClick,
  onSlotClick,
  slotWidth = 80, // Default to 80px per hour
  show24Hours = false, // Default to business hours
  onContainerMeasured,
}: TimelineGridProps) {
  // Get appropriate hours config based on toggle
  const hoursConfig = getHoursConfig(show24Hours);
  const timeSlots = generateTimeSlots(dayStart, hoursConfig);
  const [currentTimePosition, setCurrentTimePosition] = useState<number | null>(null);
  const scrollContainerRef = useRef<HTMLDivElement>(null);

  // Measure container width and report it
  useEffect(() => {
    if (scrollContainerRef.current && onContainerMeasured) {
      const measureContainer = () => {
        if (scrollContainerRef.current) {
          const width = scrollContainerRef.current.clientWidth;
          onContainerMeasured(width);
        }
      };

      // Measure immediately
      measureContainer();

      // Measure on window resize
      window.addEventListener('resize', measureContainer);
      return () => window.removeEventListener('resize', measureContainer);
    }
  }, [onContainerMeasured]);

  // Update current time indicator every minute
  useEffect(() => {
    const updateCurrentTime = () => {
      const now = new Date();
      const todayStart = setHours(setMinutes(dayStart, 0), hoursConfig.startHour);
      const todayEnd = setHours(setMinutes(dayStart, 0), hoursConfig.endHour);

      // Check if current time is on the displayed day and within business hours
      if (
        format(now, 'yyyy-MM-dd') === format(dayStart, 'yyyy-MM-dd') &&
        now >= todayStart &&
        now <= todayEnd
      ) {
        const position = calculateEventPosition(now, now, dayStart, 'day', hoursConfig);
        setCurrentTimePosition(position.left);
      } else {
        setCurrentTimePosition(null);
      }
    };

    updateCurrentTime();
    const interval = setInterval(updateCurrentTime, 60000); // Update every minute

    return () => clearInterval(interval);
  }, [dayStart, hoursConfig]);

  // Calculate total timeline width based on slot width
  const totalTimelineWidth = timeSlots.length * slotWidth;

  return (
    <div className="border rounded-lg shadow-sm bg-white overflow-hidden">
      {/* Scrollable container wrapping both header and rows */}
      <div className="overflow-x-auto" ref={scrollContainerRef}>
        <div style={{ minWidth: `calc(13rem + ${totalTimelineWidth}px)` }}>
          {/* Time Axis Header - Sticky */}
          <div className="flex border-b border-gray-300 bg-gray-100 sticky top-0 z-10">
            {/* Empty corner for room names column - Sticky horizontally */}
            <div className="w-52 flex-shrink-0 border-r border-gray-300 bg-gray-100 px-4 py-3 sticky left-0 z-20">
              <div className="text-sm font-semibold text-gray-700">Room</div>
            </div>

            {/* Time slots - Fixed widths */}
            <div className="flex h-14">
              {timeSlots.map((slot, index) => (
                <div
                  key={slot.hour}
                  className="border-r border-gray-200 last:border-r-0 px-3 py-3 text-center flex-shrink-0"
                  style={{ width: `${slotWidth}px` }}
                >
                  <div className="text-sm font-medium text-gray-700 whitespace-nowrap">{slot.label}</div>
                </div>
              ))}
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
                    slotWidth={slotWidth}
                    scrollContainerRef={scrollContainerRef}
                    show24Hours={show24Hours}
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

                {/* Vertical grid lines for time slots - Fixed pixel positions */}
                <div className="absolute top-0 bottom-0 left-52 pointer-events-none" style={{ width: `${totalTimelineWidth}px` }}>
                  {timeSlots.map((slot, index) => {
                    if (index === 0) return null; // Skip first line
                    const position = index * slotWidth;
                    return (
                      <div
                        key={`grid-${slot.hour}`}
                        className="absolute top-0 bottom-0 w-px bg-gray-200"
                        style={{ left: `${position}px` }}
                      />
                    );
                  })}
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
