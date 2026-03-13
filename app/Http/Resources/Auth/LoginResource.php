<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource transformer for authentication responses.
 *
 * Handles register, login, and social-login endpoints.
 * Conditionally includes token_type, is_new_user, and first_hero fields.
 */
class LoginResource extends JsonResource
{
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'message' => $this->resource['message'] ?? 'Login successful',
            'user' => new UserResource($this->resource['user']),
            'token' => $this->resource['token'] ?? null,
        ];

        if (isset($this->resource['token_type'])) {
            $response['token_type'] = $this->resource['token_type'];
        }

        if (array_key_exists('is_new_user', $this->resource)) {
            $response['is_new_user'] = $this->resource['is_new_user'];
        }

        if (isset($this->resource['first_hero'])) {
            $response['first_hero'] = $this->resource['first_hero'];
        }

        return $response;
    }
}
