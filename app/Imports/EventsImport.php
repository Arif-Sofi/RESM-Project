<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EventsImport implements SkipsOnFailure, ToCollection, WithHeadingRow, WithMapping, WithValidation
{
    use SkipsFailures;

    private int $importedCount = 0;

    private array $errorMessages = [];

    public function map($row): array
    {
        // Convert date format from YYYY/MM/DD HH:MM (CSV) or Excel's numeric format (XLSX)
        if (! empty($row['start_at'])) {
            try {
                if (is_numeric($row['start_at'])) {
                    // Handle Excel's numeric date format from .xlsx files
                    $row['start_at'] = Carbon::instance(Date::excelToDateTimeObject($row['start_at']))->toDateTimeString();
                } else {
                    // Handle string date format from .csv files
                    $row['start_at'] = Carbon::createFromFormat('Y/m/d H:i', (string) $row['start_at'])->toDateTimeString();
                }
            } catch (\Exception $e) {
                // If parsing fails, leave the original value for the validator to catch the error.
            }
        }

        if (! empty($row['end_at'])) {
            try {
                if (is_numeric($row['end_at'])) {
                    $row['end_at'] = Carbon::instance(Date::excelToDateTimeObject($row['end_at']))->toDateTimeString();
                } else {
                    $row['end_at'] = Carbon::createFromFormat('Y/m/d H:i', (string) $row['end_at'])->toDateTimeString();
                }
            } catch (\Exception $e) {
                // If parsing fails, leave the original value for the validator to catch the error.
            }
        }

        return $row;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            // The row number in the spreadsheet is $rowIndex + 2 (1 for header, 1 for 0-based index)
            $rowNumber = $rowIndex + 2;

            try {
                // 1. Validate 'created_by' user existence
                $user = User::where('name', $row['created_by'])->first();
                if (! $user) {
                    $this->addError($rowNumber, 'created_by', "User '{$row['created_by']}' not found.");

                    continue; // Skip this row
                }

                // 2. Validate date parsing
                $start_at = Carbon::parse($row['start_at']);
                $end_at = $row['end_at'] ? Carbon::parse($row['end_at']) : null;

                if ($end_at && $end_at->isBefore($start_at)) {
                    $this->addError($rowNumber, 'end_at', 'End date cannot be before the start date.');

                    continue; // Skip this row
                }

                Event::create([
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'start_at' => $start_at,
                    'end_at' => $end_at,
                    'user_id' => $user->id,
                ]);

                $this->importedCount++;

            } catch (\Exception $e) {
                // Catch Carbon parsing errors or other unexpected issues
                $this->addError($rowNumber, 'general', 'An unexpected error occurred: '.$e->getMessage());
            }
        }
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date_format:Y-m-d H:i:s', // Enforce a specific format
            'end_at' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:start_at',
            'created_by' => 'required|string',
        ];
    }

    /**
     * @param  Failure[]  $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->addError($failure->row(), $failure->attribute(), $failure->errors()[0]);
        }
    }

    private function addError(int $rowNumber, string $attribute, string $message): void
    {
        $this->errorMessages[] = "Row {$rowNumber} ({$attribute}): {$message}";
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrorMessages(): array
    {
        // Remove duplicates and return
        return array_unique($this->errorMessages);
    }
}
