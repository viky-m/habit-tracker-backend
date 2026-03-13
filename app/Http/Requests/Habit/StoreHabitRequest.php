<?php

namespace App\Http\Requests\Habit;

use Illuminate\Foundation\Http\FormRequest;

class StoreHabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:10'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'frequency' => ['required', 'in:daily,weekly,monthly'],
            'frequency_days' => ['nullable', 'array'],
            'frequency_days.*' => ['integer', 'between:0,6'],
            'target_count' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'frequency.required' => 'Frequency is required',
            'frequency.in' => 'Frequency must be daily, weekly, or monthly',
            'color.regex' => 'Color must be a valid hex color',
        ];
    }
}
