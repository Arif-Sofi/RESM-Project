import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Components/Layout/AppLayout';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import DeleteUserForm from './Partials/DeleteUserForm';
import { PageProps, User } from '@/Types';

interface ProfileEditProps extends PageProps {
  mustVerifyEmail: boolean;
  status?: string;
}

export default function Edit({ auth, mustVerifyEmail, status }: ProfileEditProps) {
  return (
    <AppLayout user={auth.user}>
      <Head title="Profile" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div className="max-w-xl">
              <UpdateProfileInformationForm
                mustVerifyEmail={mustVerifyEmail}
                status={status}
                user={auth.user}
              />
            </div>
          </div>

          <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div className="max-w-xl">
              <UpdatePasswordForm />
            </div>
          </div>

          <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div className="max-w-xl">
              <DeleteUserForm />
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
