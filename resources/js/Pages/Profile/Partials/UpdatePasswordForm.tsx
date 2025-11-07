import React, { FormEventHandler, useEffect, useState } from 'react';
import { useForm } from '@inertiajs/react';
import Input from '@/Components/Common/Input';
import PrimaryButton from '@/Components/Common/PrimaryButton';

export default function UpdatePasswordForm() {
  const { data, setData, put, errors, processing, recentlySuccessful, reset } = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
  });

  const [showSuccess, setShowSuccess] = useState(false);

  useEffect(() => {
    if (recentlySuccessful) {
      setShowSuccess(true);
      const timer = setTimeout(() => setShowSuccess(false), 2000);
      return () => clearTimeout(timer);
    }
  }, [recentlySuccessful]);

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    put(route('password.update'), {
      preserveScroll: true,
      onSuccess: () => reset(),
    });
  };

  return (
    <section>
      <header>
        <h2 className="text-lg font-medium text-gray-900">Update Password</h2>
        <p className="mt-1 text-sm text-gray-600">
          Ensure your account is using a long, random password to stay secure.
        </p>
      </header>

      <form onSubmit={submit} className="mt-6 space-y-6">
        <Input
          label="Current Password"
          type="password"
          name="current_password"
          value={data.current_password}
          onChange={(e) => setData('current_password', e.target.value)}
          error={errors.current_password}
          autoComplete="current-password"
        />

        <Input
          label="New Password"
          type="password"
          name="password"
          value={data.password}
          onChange={(e) => setData('password', e.target.value)}
          error={errors.password}
          autoComplete="new-password"
        />

        <Input
          label="Confirm Password"
          type="password"
          name="password_confirmation"
          value={data.password_confirmation}
          onChange={(e) => setData('password_confirmation', e.target.value)}
          error={errors.password_confirmation}
          autoComplete="new-password"
        />

        <div className="flex items-center gap-4">
          <PrimaryButton type="submit" disabled={processing} isLoading={processing}>
            Save
          </PrimaryButton>

          {showSuccess && (
            <p className="text-sm text-gray-600 transition-opacity duration-300">Saved.</p>
          )}
        </div>
      </form>
    </section>
  );
}
