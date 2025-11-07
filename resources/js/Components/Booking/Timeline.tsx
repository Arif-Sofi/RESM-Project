import React, { useState } from 'react';
import { Room, Booking } from '@/Types/models';
import { usePage } from '@inertiajs/react';
import { PageProps } from '@/Types';
import BookingModal from './BookingModal';
import BookingDetailsModal from './BookingDetailsModal';
import CustomTimeline from '@/Components/Timeline/CustomTimeline';

interface Props {
  rooms: Room[];
  bookings: Booking[];
  initialDateRange: { start: string; end: string };
}

export default function Timeline({ rooms, bookings, initialDateRange }: Props) {
  const { auth } = usePage<PageProps>().props;
  const [modalOpen, setModalOpen] = useState(false);
  const [detailsModalOpen, setDetailsModalOpen] = useState(false);
  const [selectedSlot, setSelectedSlot] = useState<{
    room: Room;
    start: Date;
    end: Date;
  } | null>(null);
  const [selectedBooking, setSelectedBooking] = useState<Booking | null>(null);

  // Check if user can approve bookings (admin or teacher)
  const canApprove = auth.user && ('role' in auth.user) &&
    ((auth.user as any).role === 'admin' || (auth.user as any).role === 'teacher');

  const handleSlotSelect = (room: Room, start: Date, end: Date) => {
    // Check if slot is in the past
    if (start < new Date()) {
      alert('Cannot book time slots in the past');
      return;
    }

    setSelectedSlot({
      room,
      start,
      end,
    });
    setSelectedBooking(null);
    setModalOpen(true);
  };

  const handleEventClick = (booking: Booking) => {
    setSelectedBooking(booking);
    setSelectedSlot(null);
    setDetailsModalOpen(true);
  };

  return (
    <>
      <CustomTimeline
        rooms={rooms}
        bookings={bookings}
        initialDateRange={initialDateRange}
        onEventClick={handleEventClick}
        onSlotSelect={handleSlotSelect}
      />

      {selectedSlot && (
        <BookingModal
          open={modalOpen}
          onClose={() => {
            setModalOpen(false);
            setSelectedSlot(null);
          }}
          room={selectedSlot.room}
          startTime={selectedSlot.start}
          endTime={selectedSlot.end}
        />
      )}

      {selectedBooking && (
        <BookingDetailsModal
          open={detailsModalOpen}
          onClose={() => {
            setDetailsModalOpen(false);
            setSelectedBooking(null);
          }}
          booking={selectedBooking}
          canApprove={canApprove}
        />
      )}
    </>
  );
}
