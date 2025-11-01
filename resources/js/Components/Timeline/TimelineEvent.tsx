import React from 'react';
import { Booking } from '@/Types/models';
import { calculateEventPosition, parseISOSafe, formatTime } from '@/Utils/timelineUtils';

interface TimelineEventProps {
  booking: Booking;
  dayStart: Date;
  onClick: (booking: Booking) => void;
}

const STATUS_COLORS = {
  approved: {
    bg: 'bg-green-500',
    border: 'border-green-600',
    text: 'text-white',
    hover: 'hover:bg-green-600',
  },
  pending: {
    bg: 'bg-yellow-500',
    border: 'border-yellow-600',
    text: 'text-gray-900',
    hover: 'hover:bg-yellow-600',
  },
  rejected: {
    bg: 'bg-red-500',
    border: 'border-red-600',
    text: 'text-white',
    hover: 'hover:bg-red-600',
  },
  default: {
    bg: 'bg-gray-500',
    border: 'border-gray-600',
    text: 'text-white',
    hover: 'hover:bg-gray-600',
  },
};

export default function TimelineEvent({ booking, dayStart, onClick }: TimelineEventProps) {
  const startTime = parseISOSafe(booking.start_time);
  const endTime = parseISOSafe(booking.end_time);

  const position = calculateEventPosition(startTime, endTime, dayStart, 'day');
  const status = (booking.status || 'default') as keyof typeof STATUS_COLORS;
  const colors = STATUS_COLORS[status] || STATUS_COLORS.default;

  // Format times for display
  const startLabel = formatTime(startTime);
  const endLabel = formatTime(endTime);

  return (
    <button
      onClick={() => onClick(booking)}
      className={`
        absolute top-1 bottom-1 rounded border-l-4
        ${colors.bg} ${colors.border} ${colors.text} ${colors.hover}
        transition-all duration-150
        cursor-pointer
        px-2 py-1
        text-left text-xs font-medium
        overflow-hidden
        shadow-sm hover:shadow-md
        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1
      `}
      style={{
        left: `${position.left}%`,
        width: `${position.width}%`,
        minWidth: '60px',
      }}
      title={`${booking.purpose}\n${startLabel} - ${endLabel}\nStatus: ${status}`}
    >
      <div className="flex flex-col h-full justify-center">
        <div className="font-semibold truncate">
          {booking.purpose}
        </div>
        <div className="text-[10px] opacity-90 truncate">
          {startLabel} - {endLabel}
        </div>
        {booking.number_of_students && (
          <div className="text-[10px] opacity-75 truncate">
            {booking.number_of_students} students
          </div>
        )}
      </div>
    </button>
  );
}
