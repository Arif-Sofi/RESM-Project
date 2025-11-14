<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view booking list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        // Admins can view any booking, users can view their own
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // dd($user);
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        if ($user->isAdmin() OR ($user->id === $booking->user_id && $booking->status === null)) {
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        if ($user->isAdmin() OR $user->id === $booking->user_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can approve the booking.
     */
    public function approve(User $user, Booking $booking): bool
    {
        // Only admins can approve, and only if booking is pending
        return $user->isAdmin() && $booking->status === null;
    }

    /**
     * Determine whether the user can reject the booking.
     */
    public function reject(User $user, Booking $booking): bool
    {
        // Only admins can reject, and only if booking is pending
        return $user->isAdmin() && $booking->status === null;
    }
}
