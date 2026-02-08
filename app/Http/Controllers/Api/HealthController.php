<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class HealthController extends Controller
{
    #[OA\Get(
        path: '/health',
        summary: 'Health check endpoint',
        description: 'Перевіряє статус API. Не вимагає авторизації.',
        tags: ['System'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'API працює нормально',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                        new OA\Property(property: 'timestamp', type: 'string', format: 'date-time', example: '2025-10-29T10:30:00.000000Z'),
                        new OA\Property(property: 'version', type: 'string', example: '1.0.0'),
                        new OA\Property(property: 'message', type: 'string', example: 'Habit Tracker API is running'),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'message' => 'Habit Tracker API is running',
        ]);
    }
}
