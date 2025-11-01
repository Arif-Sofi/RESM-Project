import { EventInput } from '@fullcalendar/core';
import { Booking, Room } from './models';

export interface TimelineResource {
  id: string;
  title: string;
  capacity?: number;
  building?: string;
  extendedProps?: {
    room: Room;
  };
}

export interface TimelineEvent extends EventInput {
  id: string;
  resourceId: string;
  start: string;
  end: string;
  title: string;
  backgroundColor?: string;
  borderColor?: string;
  extendedProps?: {
    booking: Booking;
  };
}

export function bookingToEvent(booking: Booking): TimelineEvent {
  const statusColors = {
    approved: { bg: '#10B981', border: '#059669' },
    rejected: { bg: '#EF4444', border: '#DC2626' },
    pending: { bg: '#F59E0B', border: '#D97706' },
  };

  const status = booking.status || 'pending';
  const color = statusColors[status];

  return {
    id: booking.id.toString(),
    resourceId: booking.room_id.toString(),
    start: booking.start_time,
    end: booking.end_time,
    title: booking.purpose,
    backgroundColor: color.bg,
    borderColor: color.border,
    extendedProps: { booking },
  };
}

export function roomToResource(room: Room): TimelineResource {
  return {
    id: room.id.toString(),
    title: room.name,
    capacity: room.capacity,
    building: room.building,
    extendedProps: { room },
  };
}
