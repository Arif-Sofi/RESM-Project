export interface User {
  id: number;
  name: string;
  email: string;
  role?: 'student' | 'teacher' | 'admin';
}

export interface Room {
  id: number;
  name: string;
  capacity: number;
  building?: string;
  floor?: number;
  has_projector: boolean;
  has_whiteboard: boolean;
  equipment?: string;
  description?: string;
  image_url?: string;
}

export interface Booking {
  id: number;
  room_id: number;
  user_id: number;
  start_time: string; // ISO 8601
  end_time: string;
  status: 'pending' | 'approved' | 'rejected' | null;
  number_of_students: number;
  equipment_needed?: string;
  purpose: string;
  created_at: string;
  updated_at: string;

  // Relations (when included)
  room?: Room;
  user?: User;
}

export interface BookingFormData {
  room_id: number;
  start_time: string;
  end_time: string;
  number_of_students: number;
  equipment_needed?: string;
  purpose: string;
}

export type BookingStatus = Booking['status'];
