<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->ensureTestDatabaseExists();
        parent::setUp();
    }

    protected function ensureTestDatabaseExists(): void
    {
        // Only run this logic if we are testing with MySQL
        // We manually load .env.test if it exists to get the DB credentials
        // because the App is not booted yet.
        $envFile = __DIR__.'/../.env.test';
        $dbConfig = [];

        if (file_exists($envFile)) {
            // We use Dotenv to parse the file but not populate the environment globally
            // to avoid side effects, although populate is probably fine here.
            // Actually, simply parsing it is safer.
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $dbConfig[trim($k)] = trim(trim($v), '"\'');
                }
            }
        }

        $connection = $dbConfig['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?? 'mysql';

        if ($connection !== 'mysql') {
            return;
        }

        $host = $dbConfig['DB_HOST'] ?? getenv('DB_HOST') ?? 'mysql';
        $port = $dbConfig['DB_PORT'] ?? getenv('DB_PORT') ?? 3306;
        $database = $dbConfig['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? 'habittracker_test';
        $username = $dbConfig['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? 'root';
        $password = $dbConfig['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? 'root';

        try {
            $pdo = new \PDO("mysql:host={$host};port={$port}", $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}`");
        } catch (\PDOException $e) {
            // Be silent if connection fails, maybe the test doesn't need DB or it will fail later
            // But for debugging, we might want to output
            // fwrite(STDERR, "DB Setup Warning: " . $e->getMessage() . "\n");
        }
    }
}
