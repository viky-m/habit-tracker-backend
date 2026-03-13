<?php

namespace App\Services\Contracts;

use App\Models\User;

interface UserStatsServiceContract
{
    public function getOverallStats(User $user): array;
}
