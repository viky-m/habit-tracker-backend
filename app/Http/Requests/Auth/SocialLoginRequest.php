<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
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
            'provider' => ['required', 'string', 'in:apple,google'],
            'provider_id' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['sometimes', 'string', 'url'],
            'locale' => ['sometimes', 'string', 'in:en,uk'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'provider.required' => 'Provider is required',
            'provider.in' => 'Provider must be either apple or google',
            'provider_id.required' => 'Provider ID is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'name.required' => 'Name is required',
            'avatar.url' => 'Avatar must be a valid URL',
            'locale.in' => 'Locale must be one of: en, uk',
        ];
    }
}
