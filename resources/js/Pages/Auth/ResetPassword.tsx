import React, { useEffect, FormEventHandler } from 'react';
import { Head, useForm } from '@inertiajs/react';
import GuestLayout from '@/Components/Layout/GuestLayout';
import Input from '@/Components/Common/Input';
import PrimaryButton from '@/Components/Common/PrimaryButton';

interface ResetPasswordProps {
  token: string;
  email: string;
}

export default function ResetPassword({ token, email }: ResetPasswordProps) {
  const { data, setData, post, processing, errors, reset } = useForm({
    token: token,
    email: email,
    password: '',
    password_confirmation: '',
  });

  useEffect(() => {
    return () => {
      reset('password', 'password_confirmation');
    };
  }, []);

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('password.store'));
  };

  return (
    <GuestLayout title="Reset Password">
      <Head title="Reset Password" />

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

        <Input
          label="New Password"
          type="password"
          name="password"
          value={data.password}
          onChange={(e) => setData('password', e.target.value)}
          error={errors.password}
          required
          autoComplete="new-password"
        />

        <Input
          label="Confirm New Password"
          type="password"
          name="password_confirmation"
          value={data.password_confirmation}
          onChange={(e) => setData('password_confirmation', e.target.value)}
          error={errors.password_confirmation}
          required
          autoComplete="new-password"
        />

        <div className="flex items-center justify-end">
          <PrimaryButton type="submit" disabled={processing} isLoading={processing}>
            Reset Password
          </PrimaryButton>
        </div>
      </form>
    </GuestLayout>
  );
}
