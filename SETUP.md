# Habit Tracker Backend Setup

## ✅ What's already done:

1. **Docker configuration:**
   - ✅ docker-compose.yaml
   - ✅ Nginx configuration (with CORS for API)
   - ✅ PHP 8.4 Dockerfile
   - ✅ MySQL (MariaDB 10.7)
   - ✅ Redis
   - ✅ Mailhog for email

2. **Configuration files:**
   - ✅ .env.example
   - ✅ .env
   - ✅ .gitignore
   - ✅ Makefile with useful commands
   - ✅ README.md

## 🚀 Next steps:

### 1. Create Laravel project (in Docker container)

After starting Docker containers, you need to install Laravel:

```bash
# Start containers
docker-compose up -d

# Enter PHP container
docker exec -it habittracker_php bash

# Install Laravel (inside container)
composer create-project laravel/laravel .

# OR use existing Laravel from blue-venture as template
# Copy necessary files from blue-venture
```

### 2. Configure Laravel for API

**2.1. Install required packages:**

```bash
# Sanctum for API authentication
composer require laravel/sanctum

# Swagger for API documentation
composer require darkaonline/l5-swagger

# PEST for testing
composer require pestphp/pest --dev
composer require pestphp/pest-plugin-laravel --dev

# Laravel Pint (already in Laravel 12)
# Larastan for static analysis
composer require larastan/larastan --dev
```

**2.2. Publish configurations:**

```bash
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Create basic structure

**3.1. Migrations:**

```bash
# Create migrations for main tables
php artisan make:migration create_habits_table
php artisan make:migration create_habit_logs_table
php artisan make:migration create_user_progress_table
php artisan make:migration add_locale_to_users_table
```

**3.2. Models:**

```bash
php artisan make:model Habit -a  # -a creates controller, migration, factory, seeder
php artisan make:model HabitLog
php artisan make:model UserProgress
```

**3.3. API Controllers:**

```bash
php artisan make:controller Api/AuthController
php artisan make:controller Api/HabitController --api
php artisan make:controller Api/StatsController
php artisan make:controller Api/HeroController
php artisan make:controller Api/UserController
```

**3.4. Middleware for localization:**

```bash
php artisan make:middleware SetLocale
```

**3.5. Form Requests:**

```bash
php artisan make:request StoreHabitRequest
php artisan make:request UpdateHabitRequest
```

**3.6. Resources:**

```bash
php artisan make:resource HabitResource
php artisan make:resource UserResource
php artisan make:resource HeroResource
```

### 4. Configure multi-language support

**4.1. Create language structure:**

```bash
mkdir -p lang/en
mkdir -p lang/uk
mkdir -p lang/de
mkdir -p lang/fr
mkdir -p lang/es
```

**4.2. Create translation files:**

```
lang/en/habits.php
lang/en/auth.php
lang/en/validation.php
lang/uk/habits.php
lang/uk/auth.php
lang/uk/validation.php
...
```

### 5. Configure API routes

Edit `routes/api.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HabitController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\HeroController;
use App\Http\Controllers\Api\UserController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user/language', [UserController::class, 'updateLanguage']);
    
    // Habits
    Route::apiResource('habits', HabitController::class);
    Route::post('/habits/{habit}/complete', [HabitController::class, 'complete']);
    
    // Statistics
    Route::prefix('stats')->group(function () {
        Route::get('/daily', [StatsController::class, 'daily']);
        Route::get('/weekly', [StatsController::class, 'weekly']);
        Route::get('/monthly', [StatsController::class, 'monthly']);
    });
    
    // Hero
    Route::prefix('hero')->group(function () {
        Route::get('/', [HeroController::class, 'show']);
        Route::get('/stats', [HeroController::class, 'stats']);
    });
});
```

### 6. Configure Swagger

Edit `config/l5-swagger.php`:

```php
'documentations' => [
    'default' => [
        'api' => [
            'title' => 'Habit Tracker API',
            'version' => '1.0.0',
        ],
        'routes' => [
            'api' => 'api/documentation',
        ],
    ],
],
```

### 7. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

### 8. Generate Swagger documentation

```bash
php artisan l5-swagger:generate
```

## 🔧 Useful commands

**Development:**
```bash
make up           # Start containers
make shell        # Enter PHP container
make logs         # Show logs
make restart      # Restart containers
```

**Laravel:**
```bash
make migrate      # Run migrations
make fresh        # Fresh DB with seeds
make test         # Run tests
make pint         # Code formatting
```

**Database:**
```bash
make mysql        # Connect to MySQL
```

## 📝 Database structure (preliminary)

**users:**
- id
- name
- email
- password
- locale (en, uk, de, fr, es)
- xp (experience)
- level
- timestamps

**habits:**
- id
- user_id
- name
- description
- category (health, fitness, learning, work, personal)
- frequency (daily, weekly, custom)
- frequency_config (JSON)
- difficulty (easy, medium, hard, epic)
- xp_value
- icon
- timestamps

**habit_logs:**
- id
- habit_id
- user_id
- completed_at
- xp_earned
- note (optional)
- timestamps

**user_progress:**
- id
- user_id
- date
- total_xp
- habits_completed
- habits_total
- completion_rate
- streak_current
- streak_best
- timestamps

## 🎯 MVP Features to implement:

1. ✅ Authentication (register, login, logout)
2. ✅ CRUD for habits
3. ✅ Habit completion logging
4. ✅ XP and level system
5. ✅ Basic statistics
6. ✅ Multi-language support
7. ✅ API documentation (Swagger)

## 📚 Documentation and links:

- Laravel 12: https://laravel.com/docs/12.x
- Laravel Sanctum: https://laravel.com/docs/12.x/sanctum
- L5 Swagger: https://github.com/DarkaOnLine/L5-Swagger
- PEST: https://pestphp.com/
- Larastan: https://github.com/nunomaduro/larastan

## ⚠️ Important notes:

1. **Don't use Node.js** - this is pure API without frontend
2. **API-only** - remove all view-related files
3. **Sanctum** - for API tokens (not session-based auth)
4. **Locale detection** - through Accept-Language header
5. **API Resources** - for response formatting
6. **Form Requests** - for validation

---

**Ready for development!** 🚀

Next step: start Docker and install Laravel inside container.