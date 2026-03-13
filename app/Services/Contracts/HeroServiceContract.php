<?php

namespace App\Services\Contracts;

use App\Models\Hero;
use Illuminate\Database\Eloquent\Collection;

interface HeroServiceContract
{
    public function getHeroes(array $filters = []): Collection;

    public function getHeroDetails(int $id): ?Hero;
}
