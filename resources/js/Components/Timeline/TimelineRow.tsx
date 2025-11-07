import React, { useState, useRef, useEffect } from 'react';
import { Room, Booking } from '@/Types/models';
import TimelineEvent from './TimelineEvent';
import {
  isSameDay,
  parseISOSafe,
  setHours,
  setMinutes,
  isWithinInterval,
  getHoursConfig,
  generateTimeSlots
} from '@/Utils/timelineUtils';

interface TimelineRowProps {
  room: Room;
  bookings: Booking[];
  dayStart: Date;
  onEventClick: (booking: Booking) => void;
  onSlotClick?: (room: Room, startDate: Date, endDate: Date) => void;
  slotWidth: number; // Width in pixels for each hour slot
  scrollContainerRef: React.RefObject<HTMLDivElement>; // Ref to scroll container for auto-scroll
  show24Hours?: boolean; // Toggle between business hours and full 24-hour view
}

export default function TimelineRow({
  room,
  bookings,
  dayStart,
  onEventClick,
  onSlotClick,
  slotWidth,
  scrollContainerRef,
  show24Hours = false,
}: TimelineRowProps) {
  const [isDragging, setIsDragging] = useState(false);
  const [dragStart, setDragStart] = useState<number | null>(null);
  const [dragEnd, setDragEnd] = useState<number | null>(null);
  const [hoveredHour, setHoveredHour] = useState<number | null>(null);
  const containerRef = useRef<HTMLDivElement>(null);
  const autoScrollIntervalRef = useRef<number | null>(null);

  // Cleanup auto-scroll interval on unmount
  useEffect(() => {
    return () => {
      if (autoScrollIntervalRef.current !== null) {
        clearInterval(autoScrollIntervalRef.current);
      }
    };
  }, []);

  // Filter bookings for this room and day
  const dayBookings = bookings.filter(booking => {
    if (booking.room_id !== room.id) return false;
    const bookingStart = parseISOSafe(booking.start_time);
    return isSameDay(bookingStart, dayStart);
  });

  // Generate hour slots based on the hours config
  const hoursConfig = getHoursConfig(show24Hours);
  const { startHour, endHour } = hoursConfig;
  const hourSlots: number[] = [];
  for (let hour = startHour; hour < endHour; hour++) {
    hourSlots.push(hour);
  }

  // Check if a specific hour is occupied by any booking
  const isHourOccupied = (hour: number): boolean => {
    const slotStart = setHours(setMinutes(dayStart, 0), hour);
    const slotEnd = setHours(setMinutes(dayStart, 0), hour + 1);

    return dayBookings.some(booking => {
      const bookingStart = parseISOSafe(booking.start_time);
      const bookingEnd = parseISOSafe(booking.end_time);

      // Check if booking overlaps with this hour slot
      return (
        isWithinInterval(slotStart, { start: bookingStart, end: bookingEnd }) ||
        isWithinInterval(slotEnd, { start: bookingStart, end: bookingEnd }) ||
        (bookingStart <= slotStart && bookingEnd >= slotEnd)
      );
    });
  };

  const handleMouseDown = (hour: number) => {
    if (isHourOccupied(hour)) return;

    setIsDragging(true);
    setDragStart(hour);
    setDragEnd(hour);
  };

  const handleMouseEnter = (hour: number, e: React.MouseEvent) => {
    setHoveredHour(hour);

    if (isDragging && dragStart !== null) {
      setDragEnd(hour);

      // Auto-scroll logic when dragging near edges
      if (scrollContainerRef.current) {
        const scrollContainer = scrollContainerRef.current;
        const containerRect = scrollContainer.getBoundingClientRect();
        const mouseX = e.clientX;

        // Define edge zones (50px from each side)
        const edgeZone = 50;
        const leftEdge = containerRect.left + edgeZone;
        const rightEdge = containerRect.right - edgeZone;

        // Start auto-scrolling if near edges
        if (mouseX < leftEdge) {
          startAutoScroll('left');
        } else if (mouseX > rightEdge) {
          startAutoScroll('right');
        } else {
          stopAutoScroll();
        }
      }
    }
  };

  const startAutoScroll = (direction: 'left' | 'right') => {
    // Don't start if already scrolling
    if (autoScrollIntervalRef.current !== null) return;

    const scroll = () => {
      if (scrollContainerRef.current) {
        const scrollAmount = direction === 'left' ? -10 : 10;
        scrollContainerRef.current.scrollLeft += scrollAmount;
      }
    };

    // Scroll immediately, then every 50ms
    scroll();
    autoScrollIntervalRef.current = window.setInterval(scroll, 50);
  };

  const stopAutoScroll = () => {
    if (autoScrollIntervalRef.current !== null) {
      clearInterval(autoScrollIntervalRef.current);
      autoScrollIntervalRef.current = null;
    }
  };

  const handleMouseUp = () => {
    if (isDragging && dragStart !== null && dragEnd !== null && onSlotClick) {
      // Calculate actual start and end hours (handle reverse drag)
      const actualStart = Math.min(dragStart, dragEnd);
      const actualEnd = Math.max(dragStart, dragEnd) + 1; // +1 because end is exclusive

      // Check if any hour in the range is occupied
      const hasOccupiedSlot = hourSlots
        .filter(h => h >= actualStart && h < actualEnd)
        .some(h => isHourOccupied(h));

      if (!hasOccupiedSlot) {
        const startDate = setHours(setMinutes(dayStart, 0), actualStart);
        const endDate = setHours(setMinutes(dayStart, 0), actualEnd);
        onSlotClick(room, startDate, endDate);
      }
    }

    stopAutoScroll();
    setIsDragging(false);
    setDragStart(null);
    setDragEnd(null);
  };

  const handleMouseLeave = () => {
    setHoveredHour(null);
    stopAutoScroll();
  };

  // Determine if an hour should be highlighted during drag
  const isHourSelected = (hour: number): boolean => {
    if (!isDragging || dragStart === null || dragEnd === null) return false;
    const min = Math.min(dragStart, dragEnd);
    const max = Math.max(dragStart, dragEnd);
    return hour >= min && hour <= max;
  };

  return (
    <div className="flex border-b border-gray-200 min-h-[96px]">
      {/* Room Name Column - Sticky */}
      <div className="w-52 flex-shrink-0 px-4 py-4 border-r border-gray-200 bg-gray-50 flex flex-col justify-center sticky left-0 z-10">
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
        ref={containerRef}
        className="flex-1 relative select-none"
        onMouseLeave={handleMouseLeave}
        onMouseUp={handleMouseUp}
      >
        {/* Clickable hour slots - Fixed widths */}
        <div className="absolute inset-0 flex">
          {hourSlots.map((hour, index) => {
            const occupied = isHourOccupied(hour);
            const selected = isHourSelected(hour);
            const hovered = hoveredHour === hour && !isDragging;

            return (
              <div
                key={hour}
                className={`
                  border-r border-gray-100 last:border-r-0 transition-colors flex-shrink-0
                  ${occupied
                    ? 'cursor-not-allowed bg-gray-100 bg-stripes'
                    : 'cursor-pointer hover:bg-blue-50'
                  }
                  ${selected && !occupied ? 'bg-blue-200 hover:bg-blue-200' : ''}
                  ${hovered && !occupied ? 'bg-blue-100' : ''}
                `}
                style={{ width: `${slotWidth}px` }}
                onMouseDown={() => handleMouseDown(hour)}
                onMouseEnter={(e) => handleMouseEnter(hour, e)}
                title={
                  occupied
                    ? `${hour}:00 - Occupied`
                    : `${hour}:00 - Click or drag to book`
                }
              />
            );
          })}
        </div>

        {/* Render bookings on top */}
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
