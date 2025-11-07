import React from 'react';
import { Booking } from '@/Types/models';
import { format, parseISO } from 'date-fns';
import { router } from '@inertiajs/react';

interface Props {
  booking: Booking;
  isPast: boolean;
  onViewDetails: (booking: Booking) => void;
  onEdit?: (booking: Booking) => void;
  onResubmit?: (booking: Booking) => void;
}

export default function BookingCard({ booking, isPast, onViewDetails, onEdit, onResubmit }: Props) {
  const [isDeleting, setIsDeleting] = React.useState(false);

  const startTime = parseISO(booking.start_time);
  const endTime = parseISO(booking.end_time);

  const getStatus = (): 'pending' | 'approved' | 'rejected' => {
    if (booking.status === null || booking.status === undefined) return 'pending';
    return booking.status ? 'approved' : 'rejected';
  };

  const status = getStatus();

  const statusStyles = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    approved: 'bg-green-100 text-green-800 border-green-200',
    rejected: 'bg-red-100 text-red-800 border-red-200',
  };

  const statusLabels = {
    pending: 'Pending',
    approved: 'Approved',
    rejected: 'Rejected',
  };

  const handleDelete = () => {
    if (confirm('Are you sure you want to cancel this booking?')) {
      setIsDeleting(true);
      router.delete(`/bookings/${booking.id}`, {
        onFinish: () => setIsDeleting(false),
        preserveScroll: true,
      });
    }
  };

  const canEdit = !isPast && status === 'pending';
  const canDelete = !isPast;
  const canResubmit = !isPast && status === 'rejected';

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 h-full flex flex-col">
      <div className="p-5 flex-1 flex flex-col">
        {/* Header with Status Badge */}
        <div className="flex items-start justify-between mb-4">
          <div className="flex-1 min-w-0">
            <h3 className="text-lg font-semibold text-gray-900 truncate">
              {booking.room?.name || `Room #${booking.room_id}`}
            </h3>
            {booking.room && (
              <p className="text-sm text-gray-600 mt-1">
                {booking.room.building && `${booking.room.building}`}
                {booking.room.floor && `, Floor ${booking.room.floor}`}
                {' • '}
                Capacity: {booking.room.capacity}
              </p>
            )}
          </div>
          <span className={`ml-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border whitespace-nowrap ${statusStyles[status]}`}>
            {statusLabels[status]}
          </span>
        </div>

        {/* Date & Time */}
        <div className="space-y-2 mb-4">
          <div className="flex items-center text-sm text-gray-700">
            <svg className="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span className="font-medium">{format(startTime, 'EEEE, MMM d, yyyy')}</span>
          </div>
          <div className="flex items-center text-sm text-gray-700">
            <svg className="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{format(startTime, 'h:mm a')} - {format(endTime, 'h:mm a')}</span>
          </div>
        </div>

        {/* Purpose */}
        <div className="mb-4">
          <div className="text-xs text-gray-500 mb-1">Purpose</div>
          <p className="text-sm text-gray-900 line-clamp-2">{booking.purpose}</p>
        </div>

        {/* Equipment Badge */}
        <div className="min-h-[32px] mb-4">
          {booking.equipment_needed && (
            <span className="inline-flex items-center px-2 py-1 rounded-md text-xs bg-gray-100 text-gray-700">
              Equipment: {booking.equipment_needed}
            </span>
          )}
        </div>

        {/* Spacer to push actions to bottom */}
        <div className="flex-1"></div>

        {/* Actions */}
        <div className="flex items-center gap-2 pt-4 border-t border-gray-100 mt-auto">
          <button
            onClick={() => onViewDetails(booking)}
            className="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
          >
            View Details
          </button>

          {canEdit && onEdit && (
            <button
              onClick={() => onEdit(booking)}
              className="flex-1 px-3 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
            >
              Edit
            </button>
          )}

          {canResubmit && onResubmit && (
            <button
              onClick={() => onResubmit(booking)}
              className="flex-1 px-3 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
            >
              Resubmit
            </button>
          )}

          {canDelete && (
            <button
              onClick={handleDelete}
              disabled={isDeleting}
              className="px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {isDeleting ? 'Canceling...' : 'Cancel'}
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
