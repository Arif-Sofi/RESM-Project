import React, { FormEventHandler, useState, useEffect } from 'react';
import { useForm, Link } from '@inertiajs/react';
import Input from '@/Components/Common/Input';
import PrimaryButton from '@/Components/Common/PrimaryButton';
import { User } from '@/Types';

interface UpdateProfileInformationFormProps {
  mustVerifyEmail: boolean;
  status?: string;
  user: User;
}

export default function UpdateProfileInformationForm({
  mustVerifyEmail,
  status,
  user,
}: UpdateProfileInformationFormProps) {
  const { data, setData, patch, errors, processing, recentlySuccessful } = useForm({
    name: user.name,
    email: user.email,
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
    patch(route('profile.update'));
  };

  const sendVerification: FormEventHandler = (e) => {
    e.preventDefault();
    // Use a form post to send verification
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route('verification.send');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = csrfToken;
      form.appendChild(csrfInput);
    }

    document.body.appendChild(form);
    form.submit();
  };

  return (
    <section>
      <header>
        <h2 className="text-lg font-medium text-gray-900">Profile Information</h2>
        <p className="mt-1 text-sm text-gray-600">
          Update your account's profile information and email address.
        </p>
      </header>

      <form onSubmit={submit} className="mt-6 space-y-6">
        <Input
          label="Name"
          type="text"
          name="name"
          value={data.name}
          onChange={(e) => setData('name', e.target.value)}
          error={errors.name}
          required
          autoFocus
          autoComplete="name"
        />

        <div>
          <Input
            label="Email"
            type="email"
            name="email"
            value={data.email}
            onChange={(e) => setData('email', e.target.value)}
            error={errors.email}
            required
            autoComplete="username"
          />

          {mustVerifyEmail && user.email_verified_at === null && (
            <div className="mt-2">
              <p className="text-sm text-gray-800">
                Your email address is unverified.{' '}
                <button
                  onClick={sendVerification}
                  className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Click here to re-send the verification email.
                </button>
              </p>

              {status === 'verification-link-sent' && (
                <p className="mt-2 font-medium text-sm text-green-600">
                  A new verification link has been sent to your email address.
                </p>
              )}
            </div>
          )}
        </div>

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
