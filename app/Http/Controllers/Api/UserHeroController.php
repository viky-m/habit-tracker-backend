<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserHeroResource;
use App\Models\Hero;
use App\Models\UserHero;
use App\Services\Contracts\UserHeroServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @group User Heroes
 *
 * APIs for managing user-specific heroes, unlocking and activating them.
 */
class UserHeroController extends Controller
{
    /**
     * UserHeroController constructor.
     */
    public function __construct(
        protected UserHeroServiceContract $userHeroService
    ) {}

    /**
     * Get user's heroes
     *
     * Returns a list of all heroes owned or unlocked by the authenticated user.
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "level": 5,
     *       "experience": 450,
     *       "is_active": true,
     *       "hero": {
     *         "id": 1,
     *         "name": "Warrior"
     *       }
     *     }
     *   ]
     * }
     */
    public function index(): AnonymousResourceCollection
    {
        $userHeroes = $this->userHeroService->getUserHeroes(Auth::user());

        return UserHeroResource::collection($userHeroes);
    }

    /**
     * Get active hero
     *
     * Returns the user's currently active hero.
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "level": 5,
     *     "experience": 450,
     *     "is_active": true,
     *     "hero": {
     *       "id": 1,
     *       "name": "Warrior"
     *     }
     *   }
     * }
     * @response 404 {
     *   "message": "No active hero"
     * }
     */
    public function active(): UserHeroResource|JsonResponse
    {
        $activeHero = $this->userHeroService->getActiveHero(Auth::user());

        if (! $activeHero) {
            return (new MessageResource('No active hero'))
                ->response()
                ->setStatusCode(404);
        }

        return new UserHeroResource($activeHero);
    }

    /**
     * Unlock hero
     *
     * Unlocked a new hero for the user.
     *
     * @authenticated
     *
     * @urlParam hero integer required The ID of the hero to unlock. Example: 1
     *
     * @response 201 {
     *   "data": {
     *     "id": 2,
     *     "level": 1,
     *     "experience": 0,
     *     "is_unlocked": true,
     *     "hero": {
     *       "id": 2,
     *       "name": "Mage"
     *     }
     *   }
     * }
     */
    public function unlock(Hero $hero): UserHeroResource
    {
        $userHero = $this->userHeroService->unlockHero(Auth::user(), $hero);

        return new UserHeroResource($userHero);
    }

    /**
     * Activate hero
     *
     * Sets a specific hero as active for the authenticated user.
     *
     * @authenticated
     *
     * @urlParam userHero integer required The ID of the user hero record. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "is_active": true,
     *     "hero": {
     *       "id": 1,
     *       "name": "Warrior"
     *     }
     *   }
     * }
     */
    public function activate(UserHero $userHero): UserHeroResource
    {
        $updatedHero = $this->userHeroService->activateHero(Auth::user(), $userHero);

        return new UserHeroResource($updatedHero);
    }
}
