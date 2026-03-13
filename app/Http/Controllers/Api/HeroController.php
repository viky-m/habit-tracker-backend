<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeroResource;
use App\Services\Contracts\HeroServiceContract;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Heroes
 *
 * APIs for viewing available heroes.
 */
class HeroController extends Controller
{
    /**
     * HeroController constructor.
     */
    public function __construct(
        protected HeroServiceContract $heroService
    ) {}

    /**
     * Get all heroes
     *
     * Returns a list of all available heroes in the game.
     *
     * @authenticated
     *
     * @queryParam is_active boolean Filter by active status. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Warrior",
     *       "description": "Strong and brave",
     *       "rarity": "common",
     *       "unlock_level": 1,
     *       "is_premium": false
     *     }
     *   ]
     * }
     */
    public function index(): AnonymousResourceCollection
    {
        $filters = request()->only(['is_active']);
        $heroes = $this->heroService->getHeroes($filters);

        return HeroResource::collection($heroes);
    }

    /**
     * Get hero details
     *
     * Returns detailed information about a specific hero.
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the hero. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Warrior",
     *     "description": "Strong and brave",
     *     "rarity": "common",
     *     "unlock_level": 1,
     *     "is_premium": false
     *   }
     * }
     */
    public function show(int $id): HeroResource
    {
        $hero = $this->heroService->getHeroDetails($id);

        if (! $hero) {
            abort(404, 'Hero not found');
        }

        return new HeroResource($hero);
    }
}
