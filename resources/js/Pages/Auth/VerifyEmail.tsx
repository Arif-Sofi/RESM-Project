import React, { FormEventHandler, useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import GuestLayout from '@/Components/Layout/GuestLayout';
import PrimaryButton from '@/Components/Common/PrimaryButton';
import LinkButton from '@/Components/Common/LinkButton';
import Alert from '@/Components/Common/Alert';

interface VerifyEmailProps {
  status?: string;
}

export default function VerifyEmail({ status }: VerifyEmailProps) {
  const { post, processing } = useForm({});

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('verification.send'));
  };

  const handleLogout: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('logout'));
  };

  return (
    <GuestLayout title="Email Verification">
      <Head title="Email Verification" />

      <div className="mb-4 text-sm text-gray-600">
        Thanks for signing up! Before getting started, could you verify your email address by clicking
        on the link we just emailed to you? If you didn't receive the email, we will gladly send you
        another.
      </div>

      {status === 'verification-link-sent' && (
        <div className="mb-4">
          <Alert type="success">
            A new verification link has been sent to the email address you provided during registration.
          </Alert>
        </div>
      )}

      <div className="mt-4 flex items-center justify-between">
        <form onSubmit={submit}>
          <PrimaryButton type="submit" disabled={processing} isLoading={processing}>
            Resend Verification Email
          </PrimaryButton>
        </form>

        <form onSubmit={handleLogout}>
          <button
            type="submit"
            className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Log Out
          </button>
        </form>
      </div>
    </GuestLayout>
  );
}
