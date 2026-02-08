<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserHeroResource;
use App\Models\Hero;
use App\Models\UserHero;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserHeroController extends Controller
{
    use AuthorizesRequests;

    /**
     * @OA\Get(
     *     path="/user/heroes",
     *     tags={"User Heroes"},
     *     summary="Get user's heroes",
     *     description="Герої користувача",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response=200, description="Список героїв користувача")
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $userHeroes = Auth::user()->userHeroes()->with('hero')->get();

        return UserHeroResource::collection($userHeroes);
    }

    /**
     * @OA\Get(
     *     path="/user/heroes/active",
     *     tags={"User Heroes"},
     *     summary="Get active hero",
     *     description="Поточний активний герой",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response=200, description="Активний герой")
     * )
     */
    public function active(): UserHeroResource|JsonResponse
    {
        $activeHero = Auth::user()->activeHero()->with('hero')->first();

        if (! $activeHero) {
            return response()->json(['message' => 'No active hero'], 404);
        }

        return new UserHeroResource($activeHero);
    }

    /**
     * @OA\Post(
     *     path="/user/heroes/{hero}/unlock",
     *     tags={"User Heroes"},
     *     summary="Unlock hero",
     *     description="Розблокувати героя",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="hero", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=201, description="Герой розблоковано")
     * )
     */
    public function unlock(Hero $hero): UserHeroResource
    {
        $userHero = Auth::user()->userHeroes()->firstOrCreate(
            ['hero_id' => $hero->id],
            [
                'level' => 1,
                'experience' => 0,
                'is_unlocked' => true,
                'stats' => $hero->stats,
            ]
        );

        $userHero->load('hero');

        return new UserHeroResource($userHero);
    }

    /**
     * @OA\Post(
     *     path="/user/heroes/{userHero}/activate",
     *     tags={"User Heroes"},
     *     summary="Activate hero",
     *     description="Активувати героя",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="userHero", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Герой активовано")
     * )
     */
    public function activate(UserHero $userHero): UserHeroResource
    {
        $this->authorize('update', $userHero);

        // Deactivate all other heroes
        Auth::user()->userHeroes()->update(['is_active' => false]);

        // Activate this hero
        $userHero->update([
            'is_active' => true,
            'last_active_at' => now(),
        ]);

        $userHero->load('hero');

        return new UserHeroResource($userHero);
    }
}
