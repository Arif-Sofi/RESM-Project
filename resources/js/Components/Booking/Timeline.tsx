import React, { useState } from 'react';
import { Room, Booking } from '@/Types/models';
import BookingModal from './BookingModal';
import CustomTimeline from '@/Components/Timeline/CustomTimeline';

interface Props {
  rooms: Room[];
  bookings: Booking[];
  initialDateRange: { start: string; end: string };
}

export default function Timeline({ rooms, bookings, initialDateRange }: Props) {
  const [modalOpen, setModalOpen] = useState(false);
  const [selectedSlot, setSelectedSlot] = useState<{
    room: Room;
    start: Date;
    end: Date;
  } | null>(null);
  const [selectedBooking, setSelectedBooking] = useState<Booking | null>(null);

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
    // TODO: Show booking details modal
    console.log('Clicked booking:', booking);
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
    </>
  );
}
