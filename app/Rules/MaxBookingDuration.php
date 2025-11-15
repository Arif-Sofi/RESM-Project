<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxBookingDuration implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Maximum duration in hours.
     */
    protected int $maxHours;

    /**
     * Create a new rule instance.
     */
    public function __construct(int $maxHours = 8)
    {
        $this->maxHours = $maxHours;
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! isset($this->data['start_time']) || ! $value) {
            return;
        }

        try {
            $startTime = Carbon::parse($this->data['start_time']);
            $endTime = Carbon::parse($value);

            $duration = $startTime->diffInHours($endTime, false);

            if ($duration > $this->maxHours) {
                $fail("Booking duration cannot exceed {$this->maxHours} hours.");
            }
        } catch (\Exception $e) {
            // Invalid date format, will be caught by other validation rules
        }
    }
}
