import React, { FormEvent, useEffect, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Dialog } from '@headlessui/react';
import { Room, BookingFormData, Booking } from '@/Types/models';
import { format, getHours, getMinutes, setHours, setMinutes, isBefore, isAfter, parseISO } from 'date-fns';
import Button from '@/Components/Common/Button';
import Input from '@/Components/Common/Input';
import TextArea from '@/Components/Common/TextArea';

interface Props {
  open: boolean;
  onClose: () => void;
  room?: Room;
  startTime?: Date;
  endTime?: Date;
  editBooking?: Booking;
}

export default function BookingModal({ open, onClose, room, startTime, endTime, editBooking }: Props) {
  const isEditMode = !!editBooking;

  // Determine initial values based on mode
  const initialRoom = isEditMode ? editBooking.room : room;
  const initialStartTime = isEditMode ? parseISO(editBooking.start_time) : startTime!;
  const initialEndTime = isEditMode ? parseISO(editBooking.end_time) : endTime!;

  const { data, setData, post, put, processing, errors, reset, clearErrors } = useForm<BookingFormData>({
    room_id: isEditMode ? editBooking.room_id : room!.id,
    start_time: initialStartTime.toISOString(),
    end_time: initialEndTime.toISOString(),
    number_of_students: isEditMode ? editBooking.number_of_student : 1,
    equipment_needed: isEditMode ? (editBooking.equipment_needed || '') : '',
    purpose: isEditMode ? editBooking.purpose : '',
  });

  // Local state for time validation error
  const [timeError, setTimeError] = useState<string>('');

  // Update form data when props change
  useEffect(() => {
    if (isEditMode && editBooking) {
      setData({
        room_id: editBooking.room_id,
        start_time: parseISO(editBooking.start_time).toISOString(),
        end_time: parseISO(editBooking.end_time).toISOString(),
        number_of_students: editBooking.number_of_student,
        equipment_needed: editBooking.equipment_needed || '',
        purpose: editBooking.purpose,
      });
    } else if (room && startTime && endTime) {
      setData({
        room_id: room.id,
        start_time: startTime.toISOString(),
        end_time: endTime.toISOString(),
        number_of_students: 1,
        equipment_needed: '',
        purpose: '',
      });
    }
    clearErrors();
    setTimeError('');
  }, [isEditMode, editBooking?.id, room?.id, startTime, endTime]);

  // Parse current times from form data
  const currentStartTime = new Date(data.start_time);
  const currentEndTime = new Date(data.end_time);

  // Generate hour options (0-23)
  const hourOptions = Array.from({ length: 24 }, (_, i) => i);

  // Generate minute options (0, 15, 30, 45)
  const minuteOptions = [0, 15, 30, 45];

  // Handle time changes
  const handleStartHourChange = (hour: number) => {
    const newStartTime = setHours(currentStartTime, hour);
    if (isAfter(newStartTime, currentEndTime) || newStartTime.getTime() === currentEndTime.getTime()) {
      setTimeError('Start time must be before end time');
    } else {
      setTimeError('');
    }
    setData('start_time', newStartTime.toISOString());
  };

  const handleStartMinuteChange = (minute: number) => {
    const newStartTime = setMinutes(currentStartTime, minute);
    if (isAfter(newStartTime, currentEndTime) || newStartTime.getTime() === currentEndTime.getTime()) {
      setTimeError('Start time must be before end time');
    } else {
      setTimeError('');
    }
    setData('start_time', newStartTime.toISOString());
  };

  const handleEndHourChange = (hour: number) => {
    const newEndTime = setHours(currentEndTime, hour);
    if (isBefore(newEndTime, currentStartTime) || newEndTime.getTime() === currentStartTime.getTime()) {
      setTimeError('End time must be after start time');
    } else {
      setTimeError('');
    }
    setData('end_time', newEndTime.toISOString());
  };

  const handleEndMinuteChange = (minute: number) => {
    const newEndTime = setMinutes(currentEndTime, minute);
    if (isBefore(newEndTime, currentStartTime) || newEndTime.getTime() === currentStartTime.getTime()) {
      setTimeError('End time must be after start time');
    } else {
      setTimeError('');
    }
    setData('end_time', newEndTime.toISOString());
  };

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();

    // Prevent submission if there's a time validation error
    if (timeError) {
      return;
    }

    // For edit mode, we create a new booking (resubmit)
    // The old booking will remain in history
    post('/bookings', {
      onSuccess: () => {
        // If we're editing, optionally delete the old booking
        // But for now, let's keep it as history
        reset();
        onClose();
      },
      preserveScroll: true,
    });
  };

  const handleClose = () => {
    reset();
    clearErrors();
    onClose();
  };

  return (
    <Dialog open={open} onClose={handleClose} className="relative z-50">
      {/* Backdrop */}
      <div className="fixed inset-0 bg-black/30" aria-hidden="true" />

      {/* Modal */}
      <div className="fixed inset-0 flex items-center justify-center p-4">
        <Dialog.Panel className="mx-auto max-w-lg w-full rounded-lg bg-white p-6 shadow-xl">
          <Dialog.Title className="text-lg font-semibold text-gray-900 mb-4">
            {isEditMode ? (editBooking.status === false ? 'Resubmit Booking' : 'Edit Booking') : `Book ${initialRoom?.name || 'Room'}`}
          </Dialog.Title>

          <form onSubmit={handleSubmit} className="space-y-4">
            {/* Date Display */}
            <div className="bg-indigo-50 border border-indigo-100 rounded-lg p-4">
              <div className="flex items-center text-sm text-indigo-900 mb-3">
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span className="font-medium">{format(currentStartTime, 'EEEE, MMMM d, yyyy')}</span>
              </div>

              {/* Editable Time Inputs */}
              <div className="space-y-3">
                {/* Start Time */}
                <div>
                  <label className="block text-xs font-medium text-indigo-900 mb-1">Start Time</label>
                  <div className="flex items-center space-x-2">
                    <select
                      value={getHours(currentStartTime)}
                      onChange={(e) => handleStartHourChange(parseInt(e.target.value))}
                      className="flex-1 rounded-md border-indigo-200 text-sm text-indigo-900 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                      {hourOptions.map(hour => (
                        <option key={hour} value={hour}>
                          {hour.toString().padStart(2, '0')}
                        </option>
                      ))}
                    </select>
                    <span className="text-indigo-700">:</span>
                    <select
                      value={getMinutes(currentStartTime)}
                      onChange={(e) => handleStartMinuteChange(parseInt(e.target.value))}
                      className="flex-1 rounded-md border-indigo-200 text-sm text-indigo-900 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                      {minuteOptions.map(minute => (
                        <option key={minute} value={minute}>
                          {minute.toString().padStart(2, '0')}
                        </option>
                      ))}
                    </select>
                  </div>
                </div>

                {/* End Time */}
                <div>
                  <label className="block text-xs font-medium text-indigo-900 mb-1">End Time</label>
                  <div className="flex items-center space-x-2">
                    <select
                      value={getHours(currentEndTime)}
                      onChange={(e) => handleEndHourChange(parseInt(e.target.value))}
                      className="flex-1 rounded-md border-indigo-200 text-sm text-indigo-900 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                      {hourOptions.map(hour => (
                        <option key={hour} value={hour}>
                          {hour.toString().padStart(2, '0')}
                        </option>
                      ))}
                    </select>
                    <span className="text-indigo-700">:</span>
                    <select
                      value={getMinutes(currentEndTime)}
                      onChange={(e) => handleEndMinuteChange(parseInt(e.target.value))}
                      className="flex-1 rounded-md border-indigo-200 text-sm text-indigo-900 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                      {minuteOptions.map(minute => (
                        <option key={minute} value={minute}>
                          {minute.toString().padStart(2, '0')}
                        </option>
                      ))}
                    </select>
                  </div>
                </div>
              </div>

              {/* Time validation error */}
              {timeError && (
                <div className="mt-3 text-xs text-red-600 flex items-center">
                  <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                  </svg>
                  {timeError}
                </div>
              )}
            </div>

            {/* Room Info */}
            {initialRoom && (
              <div className="text-sm text-gray-600 bg-gray-50 rounded p-3">
                <div className="font-medium text-gray-900 mb-1">{initialRoom.name}</div>
                <div className="space-y-1">
                  <div className="flex items-center">
                    <svg className="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Capacity: {initialRoom.capacity} students
                  </div>
                  {initialRoom.has_projector && (
                    <div className="flex items-center text-green-600">
                      <svg className="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                      </svg>
                      Has Projector
                    </div>
                  )}
                  {initialRoom.has_whiteboard && (
                    <div className="flex items-center text-green-600">
                      <svg className="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                      </svg>
                      Has Whiteboard
                    </div>
                  )}
                </div>
              </div>
            )}

            {/* Number of Students */}
            <Input
              type="number"
              label="Number of Students"
              min="1"
              max={initialRoom?.capacity || 100}
              value={data.number_of_students}
              onChange={(e) => setData('number_of_students', parseInt(e.target.value))}
              error={errors.number_of_students}
              hint={initialRoom ? `Maximum capacity: ${initialRoom.capacity} students` : undefined}
              required
              autoFocus
            />

            {/* Equipment */}
            <Input
              type="text"
              label="Equipment Needed"
              value={data.equipment_needed}
              onChange={(e) => setData('equipment_needed', e.target.value)}
              error={errors.equipment_needed}
              placeholder="e.g., Projector, whiteboard, markers"
            />

            {/* Purpose */}
            <TextArea
              label="Purpose"
              rows={3}
              value={data.purpose}
              onChange={(e) => setData('purpose', e.target.value)}
              error={errors.purpose}
              placeholder="What is this booking for?"
              required
            />

            {/* General error */}
            {errors.start_time && (
              <div className="rounded-md bg-red-50 border border-red-200 p-3">
                <div className="flex">
                  <svg className="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                  </svg>
                  <p className="ml-2 text-sm text-red-700">{errors.start_time}</p>
                </div>
              </div>
            )}

            {/* Actions */}
            <div className="flex justify-end space-x-3 pt-4 border-t">
              <Button
                type="button"
                onClick={handleClose}
                variant="secondary"
                disabled={processing}
              >
                Cancel
              </Button>
              <Button
                type="submit"
                variant="primary"
                isLoading={processing}
              >
                {processing
                  ? (isEditMode ? 'Submitting...' : 'Booking...')
                  : (isEditMode ? (editBooking.status === false ? 'Resubmit Booking' : 'Update Booking') : 'Book Now')
                }
              </Button>
            </div>
          </form>
        </Dialog.Panel>
      </div>
    </Dialog>
  );
}
