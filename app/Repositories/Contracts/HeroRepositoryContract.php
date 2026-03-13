<?php

namespace App\Repositories\Contracts;

use App\Models\Hero;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract HeroRepositoryContract
 */
interface HeroRepositoryContract
{
    /**
     * Get all heroes with optional filtering.
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Find a hero by ID.
     */
    public function findById(int $id): ?Hero;

    /**
     * Find the starter hero.
     */
    public function findStarterHero(): ?Hero;

    /**
     * Create a new hero.
     */
    public function create(array $data): Hero;
}
