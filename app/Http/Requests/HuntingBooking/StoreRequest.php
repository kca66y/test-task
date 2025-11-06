<?php

namespace App\Http\Requests\HuntingBooking;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_name' => ['required', 'string', 'max:255'],
            'hunter_name' => ['required', 'string', 'max:255'],
            'guide_id' => ['required', 'integer'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'participants_count' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.after_or_equal' => 'Дата тура должна быть не раньше сегодняшнего дня.',
            'participants_count.min' => 'Количество участников должно быть от 1 до 10.',
            'participants_count.max' => 'Количество участников должно быть от 1 до 10.',
        ];
    }
}
