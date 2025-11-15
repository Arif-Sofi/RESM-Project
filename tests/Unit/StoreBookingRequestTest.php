<?php

use App\Http\Requests\StoreBookingRequest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->room = Room::factory()->create();
    $this->actingAs($this->user);
});

describe('StoreBookingRequest Authorization', function () {
    test('authenticated user is authorized to create booking', function () {
        $request = new StoreBookingRequest;

        expect($request->authorize())->toBeTrue();
    });

    test('guest user is not authorized to create booking', function () {
        auth()->logout();
        $request = new StoreBookingRequest;

        expect($request->authorize())->toBeFalse();
    });
});

describe('StoreBookingRequest Required Fields', function () {
    test('room_id is required', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('room_id'))->toBeTrue();
    });

    test('start_time is required', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            ['room_id' => $this->room->id],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('start_time'))->toBeTrue();
    });

    test('end_time is required', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('end_time'))->toBeTrue();
    });

    test('purpose is required', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('purpose'))->toBeTrue();
    });

    test('number_of_student is required', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('number_of_student'))->toBeTrue();
    });
});

describe('StoreBookingRequest Field Types', function () {
    test('room_id must be an integer', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => 'not-an-integer',
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('room_id'))->toBeTrue();
    });

    test('room_id must exist in rooms table', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => 99999, // Non-existent room
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('room_id'))->toBeTrue();
    });

    test('start_time must be a valid date', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => 'not-a-date',
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('start_time'))->toBeTrue();
    });

    test('end_time must be a valid date', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => 'not-a-date',
                'number_of_student' => 10,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('end_time'))->toBeTrue();
    });

    test('number_of_student must be an integer', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 'ten',
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('number_of_student'))->toBeTrue();
    });

    test('number_of_student must be positive', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => -5,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('number_of_student'))->toBeTrue();
    });

    test('number_of_student cannot be zero', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 0,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('number_of_student'))->toBeTrue();
    });

    test('purpose must be a string', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 12345,
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('purpose'))->toBeTrue();
    });
});

describe('StoreBookingRequest Business Logic Validation', function () {
    test('end_time must be after start_time', function () {
        $request = new StoreBookingRequest;
        $startTime = now()->addDay()->setHour(14);
        $endTime = now()->addDay()->setHour(12); // Before start

        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'number_of_student' => 10,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('end_time'))->toBeTrue();
    });

    test('start_time cannot equal end_time', function () {
        $request = new StoreBookingRequest;
        $time = now()->addDay()->setHour(14);

        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => $time,
                'end_time' => $time,
                'number_of_student' => 10,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('end_time'))->toBeTrue();
    });

    test('start_time must be in the future', function () {
        $request = new StoreBookingRequest;
        $pastTime = now()->subDay();

        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => $pastTime,
                'end_time' => $pastTime->copy()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('start_time'))->toBeTrue();
    });

    test('booking duration must be reasonable (max 8 hours)', function () {
        $request = new StoreBookingRequest;
        $startTime = now()->addDay();
        $endTime = now()->addDay()->addHours(10); // 10 hours

        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'number_of_student' => 10,
                'purpose' => 'Meeting',
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue();
    });
});

describe('StoreBookingRequest Optional Fields', function () {
    test('equipment_needed is optional', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 'Meeting',
                // equipment_needed not provided
            ],
            $request->rules()
        );

        expect($validator->passes())->toBeTrue();
    });

    test('equipment_needed must be string when provided', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 'Meeting',
                'equipment_needed' => 12345, // Not a string
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('equipment_needed'))->toBeTrue();
    });
});

describe('StoreBookingRequest Valid Data', function () {
    test('passes with all required fields', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 'Team meeting',
            ],
            $request->rules()
        );

        expect($validator->passes())->toBeTrue();
    });

    test('passes with all fields including optional ones', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 25,
                'purpose' => 'Team meeting and presentation',
                'equipment_needed' => 'Projector, Whiteboard, Microphone',
            ],
            $request->rules()
        );

        expect($validator->passes())->toBeTrue();
    });

    test('accepts booking at exactly 1 hour duration', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay()->setHour(10),
                'end_time' => now()->addDay()->setHour(11),
                'number_of_student' => 10,
                'purpose' => 'Quick meeting',
            ],
            $request->rules()
        );

        expect($validator->passes())->toBeTrue();
    });

    test('accepts booking with large number of students', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 100,
                'purpose' => 'Large conference',
            ],
            $request->rules()
        );

        expect($validator->passes())->toBeTrue();
    });
});

describe('StoreBookingRequest String Length Validation', function () {
    test('purpose must have minimum length', function () {
        $request = new StoreBookingRequest;
        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => 'AB', // Too short
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('purpose'))->toBeTrue();
    });

    test('purpose can have reasonable maximum length', function () {
        $request = new StoreBookingRequest;
        $longPurpose = str_repeat('A', 501); // Over 500 characters

        $validator = Validator::make(
            [
                'room_id' => $this->room->id,
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'number_of_student' => 10,
                'purpose' => $longPurpose,
            ],
            $request->rules()
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('purpose'))->toBeTrue();
    });
});
