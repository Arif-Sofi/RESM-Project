import React, { PropsWithChildren } from 'react';
import { Link } from '@inertiajs/react';

interface GuestLayoutProps extends PropsWithChildren {
  title?: string;
}

export default function GuestLayout({ title, children }: GuestLayoutProps) {
  return (
    <div
      className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100"
      style={{
        backgroundImage: "url('/images/background2024.webp')",
        backgroundSize: 'cover',
        backgroundPosition: 'center',
      }}
    >
      <div>
        <Link href="/">
          <img
            src="/images/SKSU-logo.png"
            alt="SKSU Logo"
            className="w-30 h-30 object-contain"
          />
        </Link>
      </div>

      <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {title && (
          <h2 className="text-2xl font-bold text-gray-900 mb-6">{title}</h2>
        )}
        {children}
      </div>
    </div>
  );
}
