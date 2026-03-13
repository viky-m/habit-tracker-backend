<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HealthResource;

class HealthController extends Controller
{
    /**
     * Health check endpoint.
     *
     * Returns the current API status, version, and a server timestamp.
     * Does not require authentication.
     */
    public function index(): HealthResource
    {
        return new HealthResource([]);
    }
}
