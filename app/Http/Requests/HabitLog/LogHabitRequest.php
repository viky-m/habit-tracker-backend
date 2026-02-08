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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $habit = $this->route('habit');
            if ($habit && $habit->logs()->whereDate('completed_at', $this->input('completed_at', today()))->exists()) {
                $validator->errors()->add('completed_at', 'Habit already logged for this date');
            }
        });
    }
}
