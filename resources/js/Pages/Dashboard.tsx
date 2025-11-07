import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Components/Layout/AppLayout';
import { PageProps, Booking } from '@/Types';
import Card from '@/Components/Common/Card';
import Alert from '@/Components/Common/Alert';
import LinkButton from '@/Components/Common/LinkButton';

interface DashboardProps extends PageProps {
  bookings: Booking[];
  appVersion: string;
}

export default function Dashboard({ auth, bookings, appVersion }: DashboardProps) {
  const [showBookings, setShowBookings] = useState(true);

  const getStatusBadge = (status: boolean | null) => {
    if (status === null) {
      return <span className="text-yellow-500">Pending</span>;
    } else if (status) {
      return <span className="text-green-500">Approved</span>;
    } else {
      return <span className="text-red-500">Disapproved</span>;
    }
  };

  const formatDateTime = (date: string) => {
    return new Date(date).toLocaleString('en-US', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  return (
    <AppLayout user={auth.user}>
      <Head title="Dashboard" />

      <div className="w-full py-12">
        <div className="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
          {/* Admin Indicator */}
          {auth.user.role?.name === 'Admin' && (
            <div className="mb-6">
              <Alert type="info" title="あなたが…">
                {auth.user.role.name}ですよ!
              </Alert>
            </div>
          )}

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* Welcome Card */}
            <Card className="col-span-1 md:col-span-2" padding="lg">
              <h3 className="font-semibold text-lg text-gray-800 mb-4">
                Welcome {auth.user.name}さん
              </h3>
              <p className="text-gray-600">
                Thank you for using the Room & Event System Management!
              </p>
            </Card>

            {/* Quick Links Card */}
            <Card title="Quick Links" padding="lg">
              <div className="space-y-3">
                <Link
                  href={route('bookings.index')}
                  className="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                >
                  <span className="text-gray-700">Bookings</span>
                </Link>
                <Link
                  href={route('events.index')}
                  className="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                >
                  <span className="text-gray-700">Event Calendar</span>
                </Link>
                <Link
                  href={route('profile.edit')}
                  className="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                >
                  <span className="text-gray-700">Profile Settings</span>
                </Link>
              </div>
            </Card>
          </div>

          {/* User's Booked Rooms Card */}
          <Card title="Booked Rooms" className="mt-6" padding="lg">
            {bookings.length === 0 ? (
              <div className="border border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center">
                <p className="text-gray-500 text-center">
                  No bookings set. Create a booking to see it here.
                </p>
                <LinkButton
                  href={route('bookings.index')}
                  variant="primary"
                  className="mt-3"
                >
                  Create Booking
                </LinkButton>
              </div>
            ) : (
              <div>
                <button
                  onClick={() => setShowBookings(!showBookings)}
                  className="mb-4 text-sm text-gray-600 hover:text-gray-900"
                >
                  {showBookings ? '▼ Hide' : '▶ Show'} ({bookings.length} bookings)
                </button>

                {showBookings && (
                  <div className="space-y-4">
                    {bookings.map((booking) => (
                      <div
                        key={booking.id}
                        className="p-4 bg-gray-50 rounded-lg shadow"
                      >
                        <div className="flex justify-between">
                          <div>
                            <p className="text-sm font-semibold text-gray-800">
                              {booking.room?.name || 'Unknown Room'}
                            </p>
                            <p className="text-xs text-gray-600">
                              {formatDateTime(booking.start_time)} -{' '}
                              {new Date(booking.end_time).toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                              })}
                            </p>
                            <p className="text-sm text-gray-700 mt-2">
                              Purpose: {booking.purpose || 'No purpose'}
                            </p>
                            <p className="text-sm text-gray-700 mt-1">
                              No of students: {booking.number_of_student || 'N/A'}
                            </p>
                            <p className="text-sm text-gray-700 mt-1">
                              Equipment needed:{' '}
                              {booking.equipment_needed || 'No equipment needed'}
                            </p>
                            <p className="text-sm text-gray-700 mt-1">
                              Status: {getStatusBadge(booking.status)}
                            </p>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            )}
          </Card>

          {/* System Status Card */}
          <Card title="System Info" className="mt-6" padding="lg">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="p-4 bg-gray-50 rounded-lg">
                <p className="text-sm text-gray-500">App Version</p>
                <p className="font-medium text-gray-800">{appVersion}</p>
              </div>
              <div className="p-4 bg-gray-50 rounded-lg">
                <p className="text-sm text-gray-500">User Status</p>
                <p className="font-medium text-gray-800">
                  {auth.user.role?.name || 'User'}
                </p>
              </div>
              <div className="p-4 bg-gray-50 rounded-lg">
                <p className="text-sm text-gray-500">Last Login</p>
                <p className="font-medium text-gray-800">
                  {new Date().toLocaleString()}
                </p>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </AppLayout>
  );
}
