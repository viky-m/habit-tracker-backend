<?php

namespace App\Repositories\Eloquent;

use App\Models\Hero;
use App\Repositories\Contracts\HeroRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class HeroRepository
 */
class HeroRepository implements HeroRepositoryContract
{
    /**
     * {@inheritDoc}
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Hero::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->orderBy('unlock_level')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): ?Hero
    {
        return Hero::find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findStarterHero(): ?Hero
    {
        return Hero::where('unlock_level', 0)
            ->orWhere('unlock_level', 1)
            ->orderBy('unlock_level')
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Hero
    {
        return Hero::create($data);
    }
}
