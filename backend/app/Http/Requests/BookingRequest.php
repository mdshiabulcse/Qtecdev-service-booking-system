<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'service_id' => 'required|exists:services,id',
            'booking_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
        ];
    }

    public function messages()
    {
        return [
            'booking_date.after_or_equal' => 'Booking date must be today or in the future',
        ];
    }
}
