import React, { useEffect, FormEventHandler } from 'react';
import { Head, useForm } from '@inertiajs/react';
import GuestLayout from '@/Components/Layout/GuestLayout';
import Input from '@/Components/Common/Input';
import PrimaryButton from '@/Components/Common/PrimaryButton';

export default function ConfirmPassword() {
  const { data, setData, post, processing, errors, reset } = useForm({
    password: '',
  });

  useEffect(() => {
    return () => {
      reset('password');
    };
  }, []);

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('password.confirm'));
  };

  return (
    <GuestLayout title="Confirm Password">
      <Head title="Confirm Password" />

      <div className="mb-4 text-sm text-gray-600">
        This is a secure area of the application. Please confirm your password before continuing.
      </div>

      <form onSubmit={submit} className="space-y-6">
        <Input
          label="Password"
          type="password"
          name="password"
          value={data.password}
          onChange={(e) => setData('password', e.target.value)}
          error={errors.password}
          required
          autoFocus
          autoComplete="current-password"
        />

        <div className="flex justify-end">
          <PrimaryButton type="submit" disabled={processing} isLoading={processing}>
            Confirm
          </PrimaryButton>
        </div>
      </form>
    </GuestLayout>
  );
}
