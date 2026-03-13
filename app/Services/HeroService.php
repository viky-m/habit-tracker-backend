<?php

namespace App\Services;

use App\Models\Hero;
use App\Repositories\Contracts\HeroRepositoryContract;
use App\Services\Contracts\HeroServiceContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class HeroService
 */
class HeroService implements HeroServiceContract
{
    /**
     * HeroService constructor.
     */
    public function __construct(
        protected HeroRepositoryContract $heroRepository
    ) {}

    /**
     * Get all available heroes.
     */
    public function getHeroes(array $filters = []): Collection
    {
        return $this->heroRepository->getAll($filters);
    }

    /**
     * Get details for a specific hero.
     */
    public function getHeroDetails(int $id): ?Hero
    {
        return $this->heroRepository->findById($id);
    }
}
