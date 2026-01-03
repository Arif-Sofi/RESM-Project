<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EventsImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow, WithValidation
{
    private int $importedCount = 0;

    private array $errorMessages = [];

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row = $row->toArray();

        // 1. Find Creator
        $user = User::where('name', $row['created_by'])->first();
        if (! $user) {
            return;
        }

        // 2. Parse Dates (Matching your new CSV headers)
        $startAt = $this->transformDate($row['start_date']);
        $endAt = $this->transformDate($row['end_date']);

        // 3. Create Event
        $event = Event::create([
            'title' => $row['title'],
            'description' => $row['description'] ?? null,
            'location' => $row['location'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'user_id' => $user->id,
            'status' => $row['status'] ?? 'NOT-COMPLETED',
        ]);

        // 4. Handle Staff Assignment
        if (! empty($row['staff'])) {
            if (strcasecmp(trim($row['staff']), 'All Staff') === 0) {
                // If "All Staff", assign all users except the creator
                $staffIds = User::where('id', '!=', $user->id)->pluck('id')->toArray();
            } else {
                // Assume staff are provided as comma-separated names e.g. "John Doe, Jane Smith"
                $staffNames = explode(',', $row['staff']);
                $staffNames = array_map('trim', $staffNames);
                $staffIds = User::whereIn('name', $staffNames)->pluck('id')->toArray();
            }

            if (! empty($staffIds)) {
                $event->staff()->sync($staffIds);
            }
        }

        $this->importedCount++;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required',
            'created_by' => 'required|exists:users,name',
            'location' => 'nullable|string',
            'staff' => 'nullable|string',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * Transform Excel date or string to Carbon instance
     */
    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // 1. Get the raw date object (unaware of timezone)
            if (is_numeric($value)) {
                $date = Carbon::instance(Date::excelToDateTimeObject($value));
            } else {
                $date = Carbon::parse($value);
            }

            // 2. Interpret this "raw time" as being in Kuala Lumpur
            // shiftTimezone just tells Carbon "This existing time is actually in KL" without moving the hour hand
            $date->shiftTimezone('Asia/Kuala_Lumpur');

            // 3. Convert it to the app's storage timezone (UTC) so it saves correctly
            $date->setTimezone(config('app.timezone'));

            return $date;

        } catch (\Exception $e) {
            // Fallback: Try specific formats
            try {
                $date = Carbon::createFromFormat('d/m/Y H:i', $value);
                $date->shiftTimezone('Asia/Kuala_Lumpur');
                $date->setTimezone(config('app.timezone'));

                return $date;
            } catch (\Exception $e2) {
                try {
                    $date = Carbon::createFromFormat('m/d/Y H:i', $value);
                    $date->shiftTimezone('Asia/Kuala_Lumpur');
                    $date->setTimezone(config('app.timezone'));

                    return $date;
                } catch (\Exception $e3) {
                    return null;
                }
            }
        }
    }
}
