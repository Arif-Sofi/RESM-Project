<?php

namespace Tests\Feature\Feature;

use App\Exports\BookingsExport;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class BookingExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_export_bookings()
    {
        Excel::fake();

        $user = User::factory()->create();
        $room = Room::factory()->create();
        $this->actingAs($user);

        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => 1]); // Approved
        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => null]); // Pending

        $response = $this->get(route('bookings.export', [
            'status' => 'all',
            'start_date' => now()->subYear()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
        ]));

        $response->assertSuccessful();

        Excel::assertDownloaded('bookings.xlsx', function (BookingsExport $export) {
            // Assert that the export contains 2 bookings (plus header)
            return $export->collection()->count() === 2;
        });
    }

    /** @test */
    public function export_is_filtered_by_status_approved()
    {
        Excel::fake();

        $user = User::factory()->create();
        $room = Room::factory()->create();
        $this->actingAs($user);

        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => 1]); // Approved
        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => null]); // Pending
        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => 0]); // Rejected

        $this->get(route('bookings.export', [
            'status' => '1',
            'start_date' => now()->subYear()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
        ]));

        Excel::assertDownloaded('bookings.xlsx', function (BookingsExport $export) {
            return $export->collection()->count() === 1;
        });
    }

    /** @test */
    public function export_is_filtered_by_status_pending()
    {
        Excel::fake();

        $user = User::factory()->create();
        $room = Room::factory()->create();
        $this->actingAs($user);

        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => 1]); // Approved
        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => null]); // Pending
        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => null]); // Pending
        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => 0]); // Rejected

        $this->get(route('bookings.export', [
            'status' => 'pending',
            'start_date' => now()->subYear()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
        ]));

        Excel::assertDownloaded('bookings.xlsx', function (BookingsExport $export) {
            return $export->collection()->count() === 2;
        });
    }

    /** @test */
    public function export_is_filtered_by_date_range()
    {
        Excel::fake();

        $user = User::factory()->create();
        $room = Room::factory()->create();
        $this->actingAs($user);

        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'start_time' => now()->subMonth()]);
        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'start_time' => now()]);
        Booking::factory()->create(['user_id' => $user->id, 'room_id' => $room->id, 'start_time' => now()->addMonth()]);

        $startDate = now()->subDays(1)->toDateTimeString();
        $endDate = now()->addDays(1)->toDateTimeString();

        $this->get(route('bookings.export', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'all',
        ]));

        Excel::assertDownloaded('bookings.xlsx', function (BookingsExport $export) {
            return $export->collection()->count() === 1;
        });
    }
}
