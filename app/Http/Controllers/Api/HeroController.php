<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeroResource;
use App\Models\Hero;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HeroController extends Controller
{
    /**
     * @OA\Get(
     *     path="/heroes",
     *     tags={"Heroes"},
     *     summary="Get all heroes",
     *     description="List of all available heroes",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *
     *         @OA\Schema(type="boolean")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of heroes",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="rarity", type="string"),
     *                 @OA\Property(property="unlock_level", type="integer"),
     *                 @OA\Property(property="is_premium", type="boolean")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Hero::query();

        if (request()->has('is_active')) {
            $query->where('is_active', request()->boolean('is_active'));
        }

        $heroes = $query->orderBy('unlock_level')->get();

        return HeroResource::collection($heroes);
    }

    /**
     * @OA\Get(
     *     path="/heroes/{id}",
     *     tags={"Heroes"},
     *     summary="Get hero details",
     *     description="Hero details",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Hero details")
     * )
     */
    public function show(Hero $hero): HeroResource
    {
        return new HeroResource($hero);
    }
}
