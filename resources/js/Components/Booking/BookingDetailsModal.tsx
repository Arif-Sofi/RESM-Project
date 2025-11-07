import React from 'react';
import { Dialog } from '@headlessui/react';
import { Booking } from '@/Types/models';
import { format, parseISO } from 'date-fns';
import Button from '@/Components/Common/Button';
import { router } from '@inertiajs/react';

interface Props {
  open: boolean;
  onClose: () => void;
  booking: Booking;
  canApprove?: boolean; // Whether the current user can approve/reject bookings
}

export default function BookingDetailsModal({ open, onClose, booking, canApprove = false }: Props) {
  const [processing, setProcessing] = React.useState(false);

  const startTime = parseISO(booking.start_time);
  const endTime = parseISO(booking.end_time);

  const handleApprove = () => {
    if (confirm('Are you sure you want to approve this booking?')) {
      setProcessing(true);
      router.patch(
        `/bookings/${booking.id}/approve`,
        {},
        {
          onSuccess: () => {
            onClose();
          },
          onFinish: () => {
            setProcessing(false);
          },
          preserveScroll: true,
        }
      );
    }
  };

  const handleReject = () => {
    if (confirm('Are you sure you want to reject this booking?')) {
      setProcessing(true);
      router.patch(
        `/bookings/${booking.id}/reject`,
        {},
        {
          onSuccess: () => {
            onClose();
          },
          onFinish: () => {
            setProcessing(false);
          },
          preserveScroll: true,
        }
      );
    }
  };

  const getStatusBadge = () => {
    const status = booking.status || 'pending';
    const styles = {
      pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
      approved: 'bg-green-100 text-green-800 border-green-200',
      rejected: 'bg-red-100 text-red-800 border-red-200',
    };

    const labels = {
      pending: 'Pending Approval',
      approved: 'Approved',
      rejected: 'Rejected',
    };

    return (
      <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border ${styles[status]}`}>
        {labels[status]}
      </span>
    );
  };

  return (
    <Dialog open={open} onClose={onClose} className="relative z-50">
      {/* Backdrop */}
      <div className="fixed inset-0 bg-black/30" aria-hidden="true" />

      {/* Modal */}
      <div className="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div className="flex min-h-full items-center justify-center">
          <Dialog.Panel className="mx-auto w-full sm:max-w-2xl rounded-lg bg-white shadow-xl my-8 max-h-[90vh] flex flex-col">
            {/* Header - Fixed */}
            <div className="flex items-start justify-between p-6 pb-4 border-b border-gray-200">
              <Dialog.Title className="text-xl font-semibold text-gray-900">
                Booking Details
              </Dialog.Title>
              {getStatusBadge()}
            </div>

            {/* Content - Scrollable */}
            <div className="overflow-y-auto px-6 py-4 flex-1">
              <div className="space-y-6">
            {/* Date/Time Section */}
            <div className="bg-indigo-50 border border-indigo-100 rounded-lg p-4">
              <h3 className="text-sm font-medium text-indigo-900 mb-3">Date & Time</h3>
              <div className="space-y-2">
                <div className="flex items-center text-sm text-indigo-900">
                  <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span className="font-medium">{format(startTime, 'EEEE, MMMM d, yyyy')}</span>
                </div>
                <div className="flex items-center text-sm text-indigo-900">
                  <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span className="font-medium">
                    {format(startTime, 'h:mm a')} - {format(endTime, 'h:mm a')}
                  </span>
                </div>
              </div>
            </div>

            {/* Room Information */}
            <div className="border border-gray-200 rounded-lg p-4">
              <h3 className="text-sm font-medium text-gray-900 mb-3">Room</h3>
              <div className="space-y-2">
                <div className="font-medium text-gray-900">
                  {booking.room?.name || `Room #${booking.room_id}`}
                </div>
                {booking.room && (
                  <div className="flex items-center gap-4 text-sm text-gray-600">
                    <div className="flex items-center">
                      <svg className="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                      Capacity: {booking.room.capacity}
                    </div>
                    {booking.room.has_projector && (
                      <div className="flex items-center text-green-600">
                        <svg className="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                        </svg>
                        Has Projector
                      </div>
                    )}
                    {booking.room.has_whiteboard && (
                      <div className="flex items-center text-green-600">
                        <svg className="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                        </svg>
                        Has Whiteboard
                      </div>
                    )}
                  </div>
                )}
              </div>
            </div>

            {/* Booking Information */}
            <div className="border border-gray-200 rounded-lg p-4">
              <h3 className="text-sm font-medium text-gray-900 mb-3">Booking Information</h3>
              <div className="space-y-3">
                <div>
                  <div className="text-xs text-gray-500 mb-1">Booked By</div>
                  <div className="text-sm text-gray-900">
                    {booking.user?.name || 'Unknown User'}
                    {booking.user?.email && (
                      <span className="text-gray-500 ml-2">({booking.user.email})</span>
                    )}
                  </div>
                </div>
                <div>
                  <div className="text-xs text-gray-500 mb-1">Number of Students</div>
                  <div className="text-sm text-gray-900">{booking.number_of_students}</div>
                </div>
                {booking.equipment_needed && (
                  <div>
                    <div className="text-xs text-gray-500 mb-1">Equipment Needed</div>
                    <div className="text-sm text-gray-900">{booking.equipment_needed}</div>
                  </div>
                )}
                <div>
                  <div className="text-xs text-gray-500 mb-1">Purpose</div>
                  <div className="text-sm text-gray-900 whitespace-pre-wrap">{booking.purpose}</div>
                </div>
                <div>
                  <div className="text-xs text-gray-500 mb-1">Created At</div>
                  <div className="text-sm text-gray-900">
                    {format(parseISO(booking.created_at), 'MMM d, yyyy h:mm a')}
                  </div>
                </div>
              </div>
            </div>
              </div>
            </div>

            {/* Actions - Fixed Footer */}
            <div className="flex justify-between items-center px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
              <Button
                type="button"
                onClick={onClose}
                variant="secondary"
                disabled={processing}
              >
                Close
              </Button>

              {canApprove && (booking.status === 'pending' || booking.status === null) && (
                <div className="flex space-x-3">
                  <Button
                    type="button"
                    onClick={handleReject}
                    variant="secondary"
                    disabled={processing}
                    className="bg-red-50 hover:bg-red-100 text-red-700 border-red-200"
                  >
                    Reject
                  </Button>
                  <Button
                    type="button"
                    onClick={handleApprove}
                    variant="primary"
                    isLoading={processing}
                    className="bg-green-600 hover:bg-green-700"
                  >
                    {processing ? 'Approving...' : 'Approve'}
                  </Button>
                </div>
              )}
            </div>
          </Dialog.Panel>
        </div>
      </div>
    </Dialog>
  );
}
