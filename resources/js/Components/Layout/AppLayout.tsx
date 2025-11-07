import React, { PropsWithChildren, useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import { PageProps } from '@/Types';
import Navbar from './Navbar';
import Toast from '@/Components/Common/Toast';

interface ToastData {
  message: string;
  type: 'success' | 'error' | 'info';
  id: number;
}

export default function AppLayout({ children }: PropsWithChildren) {
  const { flash } = usePage<PageProps>().props;
  const [toasts, setToasts] = useState<ToastData[]>([]);
  const [toastId, setToastId] = useState(0);

  // Show flash messages as toasts
  useEffect(() => {
    if (flash.success) {
      const id = toastId;
      setToasts(prev => [...prev, { message: flash.success!, type: 'success', id }]);
      setToastId(prev => prev + 1);
    }
    if (flash.error) {
      const id = toastId;
      setToasts(prev => [...prev, { message: flash.error!, type: 'error', id }]);
      setToastId(prev => prev + 1);
    }
  }, [flash]);

  const removeToast = (id: number) => {
    setToasts(prev => prev.filter(toast => toast.id !== id));
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <main>{children}</main>

      {/* Toast notifications */}
      <div className="fixed top-4 right-4 z-50 space-y-2">
        {toasts.map((toast, index) => (
          <div key={toast.id} style={{ transform: `translateY(${index * 8}px)` }}>
            <Toast
              message={toast.message}
              type={toast.type}
              onClose={() => removeToast(toast.id)}
            />
          </div>
        ))}
      </div>
    </div>
  );
}
