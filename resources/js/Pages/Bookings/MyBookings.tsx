import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Components/Layout/AppLayout';
import BookingCard from '@/Components/Booking/BookingCard';
import BookingDetailsModal from '@/Components/Booking/BookingDetailsModal';
import BookingModal from '@/Components/Booking/BookingModal';
import { Booking, PageProps } from '@/Types';

interface Props extends PageProps {
  upcomingBookings: Booking[];
  pastBookings: Booking[];
}

export default function MyBookings({ upcomingBookings, pastBookings }: Props) {
  const [selectedBooking, setSelectedBooking] = useState<Booking | null>(null);
  const [isDetailsModalOpen, setIsDetailsModalOpen] = useState(false);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [editBooking, setEditBooking] = useState<Booking | null>(null);

  const handleViewDetails = (booking: Booking) => {
    setSelectedBooking(booking);
    setIsDetailsModalOpen(true);
  };

  const handleEdit = (booking: Booking) => {
    setEditBooking(booking);
    setIsEditModalOpen(true);
  };

  const handleResubmit = (booking: Booking) => {
    setEditBooking(booking);
    setIsEditModalOpen(true);
  };

  const handleCloseDetailsModal = () => {
    setIsDetailsModalOpen(false);
    setSelectedBooking(null);
  };

  const handleCloseEditModal = () => {
    setIsEditModalOpen(false);
    setEditBooking(null);
  };

  return (
    <AppLayout>
      <Head title="My Bookings" />

      {/* Page Header */}
      <div className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 py-8">
          <h1 className="text-3xl font-bold text-gray-900">My Bookings</h1>
          <p className="mt-2 text-base text-gray-600">
            View and manage your room reservations
          </p>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 py-8">
        {/* Upcoming Bookings Section */}
        <div className="mb-12">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-semibold text-gray-900">
              Upcoming Bookings
            </h2>
            <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
              {upcomingBookings.length} {upcomingBookings.length === 1 ? 'booking' : 'bookings'}
            </span>
          </div>

          {upcomingBookings.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {upcomingBookings.map((booking) => (
                <BookingCard
                  key={booking.id}
                  booking={booking}
                  isPast={false}
                  onViewDetails={handleViewDetails}
                  onEdit={handleEdit}
                  onResubmit={handleResubmit}
                />
              ))}
            </div>
          ) : (
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
              <svg
                className="mx-auto h-16 w-16 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={1.5}
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
              <h3 className="mt-4 text-base font-semibold text-gray-900">
                No upcoming bookings
              </h3>
              <p className="mt-2 text-sm text-gray-500">
                You don't have any upcoming room reservations.
              </p>
              <div className="mt-6">
                <a
                  href="/bookings"
                  className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Book a Room
                </a>
              </div>
            </div>
          )}
        </div>

        {/* Past Bookings Section */}
        <div>
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-semibold text-gray-900">
              Past Bookings
            </h2>
            <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
              {pastBookings.length} {pastBookings.length === 1 ? 'booking' : 'bookings'}
            </span>
          </div>

          {pastBookings.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {pastBookings.map((booking) => (
                <BookingCard
                  key={booking.id}
                  booking={booking}
                  isPast={true}
                  onViewDetails={handleViewDetails}
                />
              ))}
            </div>
          ) : (
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
              <svg
                className="mx-auto h-16 w-16 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={1.5}
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <h3 className="mt-4 text-base font-semibold text-gray-900">
                No past bookings
              </h3>
              <p className="mt-2 text-sm text-gray-500">
                Your booking history will appear here.
              </p>
            </div>
          )}
        </div>
      </div>

      {/* Modals */}
      {selectedBooking && (
        <BookingDetailsModal
          open={isDetailsModalOpen}
          onClose={handleCloseDetailsModal}
          booking={selectedBooking}
        />
      )}

      {editBooking && (
        <BookingModal
          open={isEditModalOpen}
          onClose={handleCloseEditModal}
          editBooking={editBooking}
        />
      )}
    </AppLayout>
  );
}
