<?php

namespace Tests\Feature\Feature;

use App\Exports\EventsExport;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class EventExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_export_events()
    {
        Excel::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        Event::factory()->create(['status' => 'COMPLETED']);
        Event::factory()->create(['status' => 'NOT-COMPLETED']);

        $response = $this->get(route('events.export', [
            'status' => 'COMPLETED',
            'start_date' => now()->subYear()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
        ]));

        $response->assertSuccessful();

        Excel::assertDownloaded('events.xlsx', function (EventsExport $export) {
            // Assert that the export contains 1 row (plus header)
            return $export->collection()->count() === 1;
        });
    }

    /** @test */
    public function export_is_filtered_by_status()
    {
        Excel::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        Event::factory()->create(['status' => 'COMPLETED']);
        Event::factory()->create(['status' => 'NOT-COMPLETED']);
        Event::factory()->create(['status' => 'NOT-COMPLETED']);

        $this->get(route('events.export', [
            'status' => 'NOT-COMPLETED',
            'start_date' => now()->subYear()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
        ]));

        Excel::assertDownloaded('events.xlsx', function (EventsExport $export) {
            return $export->collection()->count() === 2;
        });
    }

    /** @test */
    public function export_is_filtered_by_date_range()
    {
        Excel::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        Event::factory()->create(['start_at' => now()->subMonth()]);
        Event::factory()->create(['start_at' => now()]);
        Event::factory()->create(['start_at' => now()->addMonth()]);

        $startDate = now()->subDays(1)->toDateTimeString();
        $endDate = now()->addDays(1)->toDateTimeString();

        $this->get(route('events.export', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => '',
        ]));

        Excel::assertDownloaded('events.xlsx', function (EventsExport $export) {
            return $export->collection()->count() === 1;
        });
    }
}
