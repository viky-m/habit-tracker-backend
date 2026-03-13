<?php

use App\Models\Habit;
use App\Services\XpCalculator;

test('xp calculator returns base XP for habit with no streak', function () {
    $habit = Habit::factory()->make(['streak' => 0]);
    $calculator = new XpCalculator;

    $xp = $calculator->calculate($habit);

    expect($xp)->toBe(10); // BASE_XP
});

test('xp calculator adds bonus for streak over 3 days', function () {
    $habit = Habit::factory()->make(['streak' => 6]);
    $calculator = new XpCalculator;

    $xp = $calculator->calculate($habit);

    // BASE_XP (10) + floor(6/3) * (10 * 0.2) = 10 + 2 * 2 = 14
    expect($xp)->toBe(14);
});

test('xp calculator adds correct bonus for long streak', function () {
    $habit = Habit::factory()->make(['streak' => 12]);
    $calculator = new XpCalculator;

    $xp = $calculator->calculate($habit);

    // BASE_XP (10) + floor(12/3) * (10 * 0.2) = 10 + 4 * 2 = 18
    expect($xp)->toBe(18);
});

test('xp calculator returns correct xp for level 2', function () {
    $calculator = new XpCalculator;

    $xp = $calculator->getXpForLevel(2);

    // 100 * 2 + (2^1.5 * 20) = 200 + 56.56 = ~256
    expect($xp)->toBe(256);
});

test('xp calculator returns correct xp for level 5', function () {
    $calculator = new XpCalculator;

    $xp = $calculator->getXpForLevel(5);

    // Exponential curve
    expect($xp)->toBe(723);
});

test('xp calculator curve is exponential', function () {
    $calculator = new XpCalculator;

    $level1 = $calculator->getXpForLevel(1);
    $level2 = $calculator->getXpForLevel(2);
    $level3 = $calculator->getXpForLevel(3);

    expect($level2)->toBeGreaterThan($level1);
    expect($level3 - $level2)->toBeGreaterThan($level2 - $level1);
});
