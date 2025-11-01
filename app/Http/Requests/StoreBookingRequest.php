<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_id' => ['required', 'exists:rooms,id'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'number_of_students' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'number_of_student' => ['sometimes', 'integer', 'min:1', 'max:100'], // Support legacy field name
            'equipment_needed' => ['nullable', 'string', 'max:500'],
            'purpose' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'room_id.required' => 'Please select a room.',
            'room_id.exists' => 'The selected room does not exist.',
            'start_time.required' => 'Please select a start time.',
            'start_time.after' => 'Booking must be in the future.',
            'end_time.required' => 'Please select an end time.',
            'end_time.after' => 'End time must be after start time.',
            'number_of_students.min' => 'At least 1 student is required.',
            'number_of_students.max' => 'Number of students cannot exceed 100.',
            'purpose.required' => 'Please provide a purpose for the booking.',
            'purpose.min' => 'Purpose must be at least 5 characters.',
            'purpose.max' => 'Purpose cannot exceed 500 characters.',
        ];
    }
}
