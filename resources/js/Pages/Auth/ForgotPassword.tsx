import React, { FormEventHandler } from 'react';
import { Head, useForm } from '@inertiajs/react';
import GuestLayout from '@/Components/Layout/GuestLayout';
import Input from '@/Components/Common/Input';
import PrimaryButton from '@/Components/Common/PrimaryButton';
import Alert from '@/Components/Common/Alert';

interface ForgotPasswordProps {
  status?: string;
}

export default function ForgotPassword({ status }: ForgotPasswordProps) {
  const { data, setData, post, processing, errors } = useForm({
    email: '',
  });

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('password.email'));
  };

  return (
    <GuestLayout>
      <Head title="Forgot Password" />

      <div className="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Just let us know your email address and we will email you a
        password reset link that will allow you to choose a new one.
      </div>

      {status && (
        <div className="mb-4">
          <Alert type="success">{status}</Alert>
        </div>
      )}

      <form onSubmit={submit} className="space-y-6">
        <Input
          label="Email"
          type="email"
          name="email"
          value={data.email}
          onChange={(e) => setData('email', e.target.value)}
          error={errors.email}
          required
          autoFocus
          autoComplete="username"
        />

        <div className="flex items-center justify-end">
          <PrimaryButton type="submit" disabled={processing} isLoading={processing}>
            Email Password Reset Link
          </PrimaryButton>
        </div>
      </form>
    </GuestLayout>
  );
}
