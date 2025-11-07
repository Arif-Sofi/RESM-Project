import React, { FormEventHandler, useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import DangerButton from '@/Components/Common/DangerButton';
import SecondaryButton from '@/Components/Common/SecondaryButton';
import Modal from '@/Components/Common/Modal';
import Input from '@/Components/Common/Input';

export default function DeleteUserForm() {
  const [confirmingUserDeletion, setConfirmingUserDeletion] = useState(false);
  const {
    data,
    setData,
    delete: destroy,
    processing,
    reset,
    errors,
  } = useForm({
    password: '',
  });

  const confirmUserDeletion = () => {
    setConfirmingUserDeletion(true);
  };

  const closeModal = () => {
    setConfirmingUserDeletion(false);
    reset();
  };

  const deleteUser: FormEventHandler = (e) => {
    e.preventDefault();

    destroy(route('profile.destroy'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      onError: () => {
        // Keep modal open if there are errors
      },
      onFinish: () => {
        // Reset password field
        reset('password');
      },
    });
  };

  // Open modal if there are errors (from server validation)
  useEffect(() => {
    if (errors.password) {
      setConfirmingUserDeletion(true);
    }
  }, [errors.password]);

  return (
    <section className="space-y-6">
      <header>
        <h2 className="text-lg font-medium text-gray-900">Delete Account</h2>
        <p className="mt-1 text-sm text-gray-600">
          Once your account is deleted, all of its resources and data will be permanently deleted.
          Before deleting your account, please download any data or information that you wish to
          retain.
        </p>
      </header>

      <DangerButton onClick={confirmUserDeletion}>Delete Account</DangerButton>

      <Modal show={confirmingUserDeletion} onClose={closeModal} maxWidth="md">
        <form onSubmit={deleteUser} className="p-6">
          <h2 className="text-lg font-medium text-gray-900">
            Are you sure you want to delete your account?
          </h2>

          <p className="mt-1 text-sm text-gray-600">
            Once your account is deleted, all of its resources and data will be permanently deleted.
            Please enter your password to confirm you would like to permanently delete your account.
          </p>

          <div className="mt-6">
            <Input
              type="password"
              name="password"
              value={data.password}
              onChange={(e) => setData('password', e.target.value)}
              error={errors.password}
              placeholder="Password"
              autoFocus
            />
          </div>

          <div className="mt-6 flex justify-end space-x-3">
            <SecondaryButton type="button" onClick={closeModal}>
              Cancel
            </SecondaryButton>

            <DangerButton type="submit" disabled={processing} isLoading={processing}>
              Delete Account
            </DangerButton>
          </div>
        </form>
      </Modal>
    </section>
  );
}
