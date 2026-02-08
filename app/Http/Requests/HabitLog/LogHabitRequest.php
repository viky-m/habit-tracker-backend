<?php

namespace App\Http\Requests\HabitLog;

use Illuminate\Foundation\Http\FormRequest;

class LogHabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'completed_at' => ['sometimes', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
            'count' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
