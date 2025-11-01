import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { PageProps } from '@/Types';

export default function Navbar() {
  const { auth } = usePage<PageProps>().props;

  return (
    <nav className="bg-white shadow-sm border-b border-gray-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          <div className="flex">
            {/* Logo */}
            <div className="flex-shrink-0 flex items-center">
              <Link href="/bookings" className="text-xl font-bold text-indigo-600">
                RESM Booking
              </Link>
            </div>

            {/* Navigation Links */}
            <div className="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link
                href="/bookings"
                className="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              >
                Book a Room
              </Link>
              <Link
                href="/my-bookings"
                className="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              >
                My Bookings
              </Link>
            </div>
          </div>

          {/* User Menu */}
          <div className="flex items-center">
            {auth.user ? (
              <div className="flex items-center space-x-4">
                <span className="text-sm text-gray-700">{auth.user.name}</span>
                <Link
                  href="/logout"
                  method="post"
                  as="button"
                  className="text-sm text-gray-500 hover:text-gray-700"
                >
                  Logout
                </Link>
              </div>
            ) : (
              <Link
                href="/login"
                className="text-sm text-gray-500 hover:text-gray-700"
              >
                Login
              </Link>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
}
