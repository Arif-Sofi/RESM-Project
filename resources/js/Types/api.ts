import { Booking, Room } from './models';

export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
}

export interface AvailabilitySlot {
  start: string; // HH:mm format
  end: string;
  available: boolean;
}

export interface RoomAvailability {
  room_id: number;
  date: string; // YYYY-MM-DD
  slots: AvailabilitySlot[];
}

export interface AvailabilityResponse {
  availability: RoomAvailability[];
  date_range: {
    start: string;
    end: string;
  };
}

export interface BookingResponse {
  booking: Booking;
  message: string;
}

export interface ValidationError {
  message: string;
  errors: Record<string, string[]>;
}
