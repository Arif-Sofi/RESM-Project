<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => [
                'required',
                'date',
                'after:start_time',
                new \App\Rules\MaxBookingDuration(8),
            ],
            'number_of_student' => ['required', 'integer', 'min:1'],
            'purpose' => ['required', 'string', 'min:3', 'max:500'],
            'equipment_needed' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'room_id.required' => 'Please select a room.',
            'room_id.exists' => 'The selected room does not exist.',
            'start_time.required' => 'Start time is required.',
            'start_time.after' => 'Booking must be scheduled for a future date.',
            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time must be after start time.',
            'number_of_student.required' => 'Number of students is required.',
            'number_of_student.min' => 'Number of students must be at least 1.',
            'purpose.required' => 'Purpose is required.',
            'purpose.min' => 'Purpose must be at least 3 characters.',
            'purpose.max' => 'Purpose cannot exceed 500 characters.',
        ];
    }
}
