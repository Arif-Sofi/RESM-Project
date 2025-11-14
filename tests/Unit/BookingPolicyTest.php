<?php

use App\Models\Booking;
use App\Models\Role;
use App\Models\User;
use App\Policies\BookingPolicy;

beforeEach(function () {
    // Ensure roles exist in the database
    Role::firstOrCreate(['id' => 1], ['name' => 'Admin']);
    Role::firstOrCreate(['id' => 2], ['name' => 'Regular User']);

    $this->admin = User::factory()->create(['role_id' => 1]);
    $this->regularUser = User::factory()->create(['role_id' => 2]);
    $this->anotherUser = User::factory()->create(['role_id' => 2]);
    $this->policy = new BookingPolicy();
});

describe('BookingPolicy viewAny Authorization', function () {
    test('admin can view any booking', function () {
        expect($this->policy->viewAny($this->admin))->toBeTrue();
    });

    test('regular user can view any booking', function () {
        expect($this->policy->viewAny($this->regularUser))->toBeTrue();
    });

    test('all authenticated users can view booking list', function () {
        $user1 = User::factory()->create(['role_id' => 2]);
        $user2 = User::factory()->create(['role_id' => 2]);

        expect($this->policy->viewAny($user1))->toBeTrue()
            ->and($this->policy->viewAny($user2))->toBeTrue();
    });
});

describe('BookingPolicy view Authorization', function () {
    test('admin can view any specific booking', function () {
        $booking = Booking::factory()->create();

        expect($this->policy->view($this->admin, $booking))->toBeTrue();
    });

    test('admin can view booking from another user', function () {
        $booking = Booking::factory()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->view($this->admin, $booking))->toBeTrue();
    });

    test('regular user can view their own booking', function () {
        $booking = Booking::factory()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->view($this->regularUser, $booking))->toBeTrue();
    });

    test('regular user can view their own approved booking', function () {
        $booking = Booking::factory()->approved()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->view($this->regularUser, $booking))->toBeTrue();
    });

    test('regular user can view their own rejected booking', function () {
        $booking = Booking::factory()->rejected()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->view($this->regularUser, $booking))->toBeTrue();
    });

    test('regular user cannot view another users booking', function () {
        $booking = Booking::factory()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->view($this->regularUser, $booking))->toBeFalse();
    });

    test('user cannot view booking that does not belong to them', function () {
        $booking1 = Booking::factory()->create(['user_id' => $this->regularUser->id]);
        $booking2 = Booking::factory()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->view($this->regularUser, $booking1))->toBeTrue()
            ->and($this->policy->view($this->regularUser, $booking2))->toBeFalse();
    });
});

describe('BookingPolicy create Authorization', function () {
    test('admin can create bookings', function () {
        expect($this->policy->create($this->admin))->toBeTrue();
    });

    test('regular user can create bookings', function () {
        expect($this->policy->create($this->regularUser))->toBeTrue();
    });

    test('any authenticated user can create bookings', function () {
        $user = User::factory()->create(['role_id' => 2]);

        expect($this->policy->create($user))->toBeTrue();
    });
});

describe('BookingPolicy update Authorization', function () {
    test('admin can update any pending booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->update($this->admin, $booking))->toBeTrue();
    });

    test('admin can update any approved booking', function () {
        $booking = Booking::factory()->approved()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->update($this->admin, $booking))->toBeTrue();
    });

    test('admin can update any rejected booking', function () {
        $booking = Booking::factory()->rejected()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->update($this->admin, $booking))->toBeTrue();
    });

    test('admin can update booking from another user', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->update($this->admin, $booking))->toBeTrue();
    });

    test('regular user can update their own pending booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->update($this->regularUser, $booking))->toBeTrue();
    });

    test('regular user cannot update their approved booking', function () {
        $booking = Booking::factory()->approved()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->update($this->regularUser, $booking))->toBeFalse();
    });

    test('regular user cannot update their rejected booking', function () {
        $booking = Booking::factory()->rejected()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->update($this->regularUser, $booking))->toBeFalse();
    });

    test('regular user cannot update another users booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->update($this->regularUser, $booking))->toBeFalse();
    });

    test('user cannot update booking that does not belong to them even if pending', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->admin->id]);

        expect($this->policy->update($this->regularUser, $booking))->toBeFalse();
    });
});

describe('BookingPolicy delete Authorization', function () {
    test('admin can delete any booking', function () {
        $booking = Booking::factory()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->delete($this->admin, $booking))->toBeTrue();
    });

    test('admin can delete approved booking', function () {
        $booking = Booking::factory()->approved()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->delete($this->admin, $booking))->toBeTrue();
    });

    test('admin can delete rejected booking', function () {
        $booking = Booking::factory()->rejected()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->delete($this->admin, $booking))->toBeTrue();
    });

    test('admin can delete pending booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->delete($this->admin, $booking))->toBeTrue();
    });

    test('regular user can delete their own pending booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->delete($this->regularUser, $booking))->toBeTrue();
    });

    test('regular user can delete their own approved booking', function () {
        $booking = Booking::factory()->approved()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->delete($this->regularUser, $booking))->toBeTrue();
    });

    test('regular user can delete their own rejected booking', function () {
        $booking = Booking::factory()->rejected()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->delete($this->regularUser, $booking))->toBeTrue();
    });

    test('regular user cannot delete another users booking', function () {
        $booking = Booking::factory()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->delete($this->regularUser, $booking))->toBeFalse();
    });
});

describe('BookingPolicy approve Authorization', function () {
    test('admin can approve pending booking', function () {
        $booking = Booking::factory()->pending()->create();

        expect($this->policy->approve($this->admin, $booking))->toBeTrue();
    });

    test('admin can approve booking from any user', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->approve($this->admin, $booking))->toBeTrue();
    });

    test('regular user cannot approve bookings', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->approve($this->regularUser, $booking))->toBeFalse();
    });

    test('regular user cannot approve their own booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->approve($this->regularUser, $booking))->toBeFalse();
    });

    test('regular user cannot approve another users booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->approve($this->regularUser, $booking))->toBeFalse();
    });

    test('admin cannot approve already approved booking', function () {
        $booking = Booking::factory()->approved()->create();

        expect($this->policy->approve($this->admin, $booking))->toBeFalse();
    });

    test('admin cannot approve rejected booking', function () {
        $booking = Booking::factory()->rejected()->create();

        expect($this->policy->approve($this->admin, $booking))->toBeFalse();
    });
});

describe('BookingPolicy reject Authorization', function () {
    test('admin can reject pending booking', function () {
        $booking = Booking::factory()->pending()->create();

        expect($this->policy->reject($this->admin, $booking))->toBeTrue();
    });

    test('admin can reject booking from any user', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->reject($this->admin, $booking))->toBeTrue();
    });

    test('regular user cannot reject bookings', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->reject($this->regularUser, $booking))->toBeFalse();
    });

    test('regular user cannot reject their own booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

        expect($this->policy->reject($this->regularUser, $booking))->toBeFalse();
    });

    test('regular user cannot reject another users booking', function () {
        $booking = Booking::factory()->pending()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->reject($this->regularUser, $booking))->toBeFalse();
    });

    test('admin cannot reject already approved booking', function () {
        $booking = Booking::factory()->approved()->create();

        expect($this->policy->reject($this->admin, $booking))->toBeFalse();
    });

    test('admin cannot reject already rejected booking', function () {
        $booking = Booking::factory()->rejected()->create();

        expect($this->policy->reject($this->admin, $booking))->toBeFalse();
    });
});

describe('BookingPolicy restore and forceDelete Authorization', function () {
    test('restore is not allowed for any user', function () {
        $booking = Booking::factory()->create();

        expect($this->policy->restore($this->admin, $booking))->toBeFalse()
            ->and($this->policy->restore($this->regularUser, $booking))->toBeFalse();
    });

    test('force delete is not allowed for any user', function () {
        $booking = Booking::factory()->create();

        expect($this->policy->forceDelete($this->admin, $booking))->toBeFalse()
            ->and($this->policy->forceDelete($this->regularUser, $booking))->toBeFalse();
    });
});

describe('BookingPolicy Edge Cases', function () {
    test('policy correctly identifies admin vs regular user', function () {
        expect($this->admin->isAdmin())->toBeTrue()
            ->and($this->regularUser->isAdmin())->toBeFalse()
            ->and($this->anotherUser->isAdmin())->toBeFalse();
    });

    test('policy handles null status correctly for updates', function () {
        $pendingBooking = Booking::factory()->create(['user_id' => $this->regularUser->id, 'status' => null]);
        $approvedBooking = Booking::factory()->create(['user_id' => $this->regularUser->id, 'status' => true]);
        $rejectedBooking = Booking::factory()->create(['user_id' => $this->regularUser->id, 'status' => false]);

        expect($this->policy->update($this->regularUser, $pendingBooking))->toBeTrue()
            ->and($this->policy->update($this->regularUser, $approvedBooking))->toBeFalse()
            ->and($this->policy->update($this->regularUser, $rejectedBooking))->toBeFalse();
    });

    test('multiple users cannot interfere with each others bookings', function () {
        $booking1 = Booking::factory()->create(['user_id' => $this->regularUser->id]);
        $booking2 = Booking::factory()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->view($this->regularUser, $booking1))->toBeTrue()
            ->and($this->policy->view($this->regularUser, $booking2))->toBeFalse()
            ->and($this->policy->update($this->regularUser, $booking2))->toBeFalse()
            ->and($this->policy->delete($this->regularUser, $booking2))->toBeFalse();
    });

    test('admin has full access to all bookings regardless of owner', function () {
        $userBooking = Booking::factory()->create(['user_id' => $this->regularUser->id]);
        $anotherBooking = Booking::factory()->create(['user_id' => $this->anotherUser->id]);

        expect($this->policy->view($this->admin, $userBooking))->toBeTrue()
            ->and($this->policy->view($this->admin, $anotherBooking))->toBeTrue()
            ->and($this->policy->update($this->admin, $userBooking))->toBeTrue()
            ->and($this->policy->update($this->admin, $anotherBooking))->toBeTrue()
            ->and($this->policy->delete($this->admin, $userBooking))->toBeTrue()
            ->and($this->policy->delete($this->admin, $anotherBooking))->toBeTrue();
    });
});
