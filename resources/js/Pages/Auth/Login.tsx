import React, { useEffect, FormEventHandler } from 'react';
import { Head, useForm } from '@inertiajs/react';
import GuestLayout from '@/Components/Layout/GuestLayout';
import Input from '@/Components/Common/Input';
import Checkbox from '@/Components/Common/Checkbox';
import PrimaryButton from '@/Components/Common/PrimaryButton';
import LinkButton from '@/Components/Common/LinkButton';
import Alert from '@/Components/Common/Alert';

interface LoginProps {
  status?: string;
  canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  useEffect(() => {
    return () => {
      reset('password');
    };
  }, []);

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('login'));
  };

  return (
    <GuestLayout>
      <Head title="Log in" />

      {status && (
        <div className="mb-4">
          <Alert type="success">{status}</Alert>
        </div>
      )}

      <div className="mb-6">
        <h2 className="text-2xl font-semibold text-gray-800">Log in</h2>
      </div>

      <form onSubmit={submit} className="space-y-6">
        <Input
          label="Email address"
          type="email"
          name="email"
          value={data.email}
          onChange={(e) => setData('email', e.target.value)}
          error={errors.email}
          placeholder="abcd@schoolemail.com"
          required
          autoFocus
          autoComplete="username"
        />

        <Input
          label="Password"
          type="password"
          name="password"
          value={data.password}
          onChange={(e) => setData('password', e.target.value)}
          error={errors.password}
          required
          autoComplete="current-password"
        />

        <Checkbox
          label="Remember me"
          checked={data.remember}
          onChange={(e) => setData('remember', e.target.checked)}
        />

        <PrimaryButton
          type="submit"
          disabled={processing}
          isLoading={processing}
          className="w-full justify-center"
          style={{ backgroundColor: '#9333ea', borderRadius: '20px' }}
        >
          Log in
        </PrimaryButton>

        {canResetPassword && (
          <div className="text-center">
            <LinkButton href={route('password.request')} variant="link">
              <u>Forgot your password?</u>
            </LinkButton>
          </div>
        )}
      </form>
    </GuestLayout>
  );
}
