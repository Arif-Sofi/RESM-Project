import React, { PropsWithChildren, useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { PageProps } from '@/Types';
import Navbar from './Navbar';

export default function AppLayout({ children }: PropsWithChildren) {
  const { flash } = usePage<PageProps>().props;

  // Show flash messages
  useEffect(() => {
    if (flash.success) {
      // You can replace this with a toast library later
      console.log('Success:', flash.success);
    }
    if (flash.error) {
      console.error('Error:', flash.error);
    }
  }, [flash]);

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <main>{children}</main>
    </div>
  );
}
