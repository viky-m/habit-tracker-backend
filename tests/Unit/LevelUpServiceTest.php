<?php

use App\Models\UserHero;
use App\Services\LevelUpService;
use App\Services\XpCalculator;

test('level up service increases level when enough XP', function () {
    $hero = UserHero::factory()->make([
        'level' => 1,
        'experience' => 150, // More than needed for level 2 (100)
    ]);

    $xpCalculator = new XpCalculator();
    $service = new LevelUpService($xpCalculator);

    $leveledUp = $service->processLevelUp($hero);

    expect($leveledUp)->toBeTrue();
    expect($hero->level)->toBe(2);
});

test('level up service does not increase level when not enough XP', function () {
    $hero = UserHero::factory()->make([
        'level' => 1,
        'experience' => 50, // Not enough for level 2 (needs 100)
    ]);

    $xpCalculator = new XpCalculator();
    $service = new LevelUpService($xpCalculator);

    $leveledUp = $service->processLevelUp($hero);

    expect($leveledUp)->toBeFalse();
    expect($hero->level)->toBe(1);
});

test('level up service can level up multiple times', function () {
    $hero = UserHero::factory()->make([
        'level' => 1,
        'experience' => 1000, // Enough for multiple levels
    ]);

    $xpCalculator = new XpCalculator();
    $service = new LevelUpService($xpCalculator);

    $leveledUp = $service->processLevelUp($hero);

    expect($leveledUp)->toBeTrue();
    expect($hero->level)->toBeGreaterThan(3);
});

test('level up service stops at correct level', function () {
    $xpCalculator = new XpCalculator();

    // XP for level 3 is 360
    $hero = UserHero::factory()->make([
        'level' => 1,
        'experience' => 350, // Just under level 3
    ]);

    $service = new LevelUpService($xpCalculator);
    $service->processLevelUp($hero);

    expect($hero->level)->toBe(2); // Should stop at 2, not reach 3
});

