# 🎯 Habit Tracker Backend API

Gamified Habit Tracker with 3D Heroes - Backend API

---

## 🚀 Quick Start

### Run the project:

```bash
# 1. Start Docker
docker-compose up -d

# 2. Install dependencies (if first time)
docker exec habittracker_php composer install

# 3. Setup database
docker exec habittracker_php php artisan migrate
docker exec habittracker_php php artisan db:seed

# 4. Generate documentation
docker exec habittracker_php php artisan scribe:generate

# 5. Open documentation
# http://localhost:8081/docs
```

---

## 📚 API Documentation

- **🎨 Scribe Documentation:** http://localhost:8081/docs ← **MAIN!**
- **🧪 API Playground:** http://localhost:8081/playground
- **❤️ Health Check:** http://localhost:8081/api/health

---

## 🎮 Implemented Features

### Authentication:
- ✅ Email/Password registration and login
- ✅ Apple Sign-In
- ✅ Google Sign-In
- ✅ Account Linking (link providers to one email)
- ✅ Laravel Sanctum tokens
- ✅ Automatic onboarding with first hero

### Habits:
- ✅ CRUD operations (Create, Read, Update, Delete)
- ✅ Categorization by frequency (daily, weekly, monthly, custom)
- ✅ Custom days for execution
- ✅ Target counts
- ✅ Icons and colors
- ✅ Soft deletes

### Habit Logging:
- ✅ Log habit completion
- ✅ Notes for execution
- ✅ Completion count
- ✅ Execution history
- ✅ Habit statistics (completion rate, streaks)

### Gamification:
- ✅ **XP System** - earn for completing habits
- ✅ **Streak System** - consecutive days series
- ✅ **Level Up** - automatic hero level increase
- ✅ **4 Starter Heroes** (Warrior, Sage, Guardian, Phoenix)
- ✅ **Hero Progression** - experience, level, stats
- ✅ **Onboarding** - automatic creation of first hero
- ✅ **User Statistics** - general progress stats
- ✅ **Reminders** - habit reminders (P0!)
- ✅ **Achievements** - achievement system (13 achievements!)

### Multi-language:
- ✅ English (en)
- ✅ Ukrainian (uk)

---

## 📊 API Endpoints (32)

### Authentication (5):
```
POST /api/auth/register         - Register
POST /api/auth/login            - Login
POST /api/auth/social-login     - Apple/Google Login
POST /api/auth/logout           - Logout
GET  /api/auth/me               - Current User
```

### Habits (5):
```
GET    /api/habits              - List habits
POST   /api/habits              - Create habit
GET    /api/habits/{id}         - Habit details
PUT    /api/habits/{id}         - Update habit
DELETE /api/habits/{id}         - Delete habit
```

### Habit Logs (3):
```
POST /api/habits/{id}/log       - Log completion (+ XP & Streak!)
GET  /api/habits/{id}/logs      - Execution history
GET  /api/habits/{id}/stats     - Habit statistics
```

### User Statistics (1):
```
GET /api/user/stats             - General user statistics
```

### Reminders (4):
```
GET    /api/reminders           - List reminders
POST   /api/reminders           - Create reminder
PUT    /api/reminders/{id}      - Update reminder
DELETE /api/reminders/{id}      - Delete reminder
```

### Achievements (3):
```
GET  /api/achievements          - All achievements
GET  /api/achievements/user     - Unlocked achievements
POST /api/achievements/check    - Check for new
```

### Heroes (2):
```
GET /api/heroes                 - List available heroes
GET /api/heroes/{id}            - Hero details
```

### User Heroes (4):
```
GET  /api/user/heroes           - My heroes
GET  /api/user/heroes/active    - Active hero
POST /api/user/heroes/{id}/unlock   - Unlock hero
POST /api/user/heroes/{id}/activate - Activate hero
```

### System (1):
```
GET /api/health                 - Health check
```

---

## 🛠️ Development Commands

### Docker:
```bash
# Start all containers
docker-compose up -d

# Stop
docker-compose down

# Rebuild after Dockerfile changes
docker-compose up -d --build

# Logs
docker-compose logs -f php
```

### Laravel:
```bash
# Migrations
docker exec habittracker_php php artisan migrate
docker exec habittracker_php php artisan migrate:fresh --seed

# Seed heroes
docker exec habittracker_php php artisan db:seed --class=HeroSeeder

# Cache clear
docker exec habittracker_php php artisan cache:clear
docker exec habittracker_php php artisan config:clear

# Routes
docker exec habittracker_php php artisan route:list
```

### Testing:
```bash
# All tests (90 passed!)
docker exec habittracker_php php artisan test

# Specific group
docker exec habittracker_php php artisan test --filter=AuthTest
docker exec habittracker_php php artisan test --filter=GamificationTest
docker exec habittracker_php php artisan test --filter=AchievementTest
docker exec habittracker_php php artisan test --filter=ReminderTest

# With coverage
docker exec habittracker_php php artisan test --coverage

# With detailed output
docker exec habittracker_php php artisan test --verbose
```

### Code Quality:
```bash
# Laravel Pint (formatter)
docker exec habittracker_php vendor/bin/pint

# Check without changes
docker exec habittracker_php vendor/bin/pint --test

# Larastan (static analysis)
docker exec habittracker_php vendor/bin/phpstan analyse
```

### Documentation:
```bash
# Generate Scribe documentation
docker exec habittracker_php php artisan scribe:generate

# Clear Scribe cache
docker exec habittracker_php php artisan scribe:clear
```

### Database:
```bash
# Open MySQL shell
docker exec -it habittracker_mysql mysql -u habittracker -phabittracker habittracker

# Backup database
docker exec habittracker_mysql mysqldump -u habittracker -phabittracker habittracker > backup.sql

# Restore database
docker exec -i habittracker_mysql mysql -u habittracker -phabittracker habittracker < backup.sql
```

### PHP Container:
```bash
# Enter PHP container
docker exec -it habittracker_php bash

# Composer
docker exec habittracker_php composer install
docker exec habittracker_php composer update
```

---

## Frontend Integration

For the API to work correctly with a frontend application (CORS), you must configure the `FRONTEND_URL` in your `.env` file:

```env
FRONTEND_URL=http://localhost:5173
```

This URL should match the URL where your frontend application is running.

---

## 📋 Development Rules

### Code Quality Standards:

#### 1. **SOLID Principles** (mandatory!)
- **S**ingle Responsibility
- **O**pen/Closed
- **L**iskov Substitution
- **I**nterface Segregation
- **D**ependency Inversion

#### 2. **Use Interfaces:**
```php
// ✅ Good
public function __construct(
    private GamificationServiceContract $gamification
) {}

// ❌ Bad
public function __construct(
    private GamificationService $gamification
) {}
```

#### 3. **Service Layer for Business Logic:**
```php
// ✅ Good - Thin Controller
class HabitController {
    public function store(HabitService $service) {
        return $service->createHabit($request->validated());
    }
}

// ❌ Bad - Fat Controller
class HabitController {
    public function store() {
        // 50 lines of business logic here
    }
}
```

#### 4. **Type Hints Everywhere:**
```php
// ✅ Good
public function calculate(Habit $habit): int

// ❌ Bad
public function calculate($habit)
```

#### 5. **No Magic Numbers:**
```php
// ✅ Good
private const BASE_XP = 10;
$xp = self::BASE_XP;

// ❌ Bad
$xp = 10;
```

#### 6. **Small Methods (< 20 lines):**
```php
// ✅ Good
public function processPayment() {
    $this->validate();
    $this->charge();
    $this->sendReceipt();
}

// ❌ Bad - 50+ lines method
```

#### 7. **Dependency Injection via Constructor:**
```php
// ✅ Good - Constructor Injection
class UserService {
    public function __construct(
        private UserRepository $repository
    ) {}
}
```

### Testing Requirements:

#### 1. **Mandatory Tests for:**
- ✅ All API endpoints (feature tests)
- ✅ Business logic in services (unit tests)
- ✅ Validation rules (feature tests)
- ✅ Authorization policies (feature tests)

#### 2. **Minimum Coverage 70%:**
```bash
php artisan test --coverage --min=70
```

#### 3. **Naming Convention:**
```php
// Feature tests
test_user_can_register_with_valid_data()
test_registration_fails_with_invalid_email()

// Unit tests  
test_xp_calculator_returns_correct_base_xp()
test_level_up_increases_hero_level()
```

#### 4. **Use Pest Assertions:**
```php
// ✅ Good
$response->assertSuccessful();
$response->assertCreated();
$response->assertUnauthorized();

// ❌ Bad
$response->assertStatus(200);
$response->assertStatus(401);
```

### Code Style:

#### 1. **Laravel Pint before every commit:**
```bash
vendor/bin/pint
```

#### 2. **PHPDoc for public methods:**
```php
/**
 * Calculate XP for completing a habit
 *
 * @param Habit $habit Habit that was completed
 * @return int XP amount awarded
 */
public function calculate(Habit $habit): int
```

#### 3. **Use strict types:**
```php
<?php

declare(strict_types=1);

namespace App\Services;
```

---

## 🗄️ Tech Stack

- **PHP:** 8.4
- **Laravel:** 12.x
- **MySQL:** 8.0
- **Redis:** 7.0
- **Nginx:** Latest
- **Docker:** 20.10+
- **Composer:** 2.x

### Packages:
- `laravel/sanctum` - API authentication
- `knuckleswtf/scribe` - API documentation
- `pestphp/pest` - Testing framework
- `larastan/larastan` - Static analysis
- `laravel/pint` - Code formatter

---

## 🎯 For Frontend Developer

### Documentation:
1. **Scribe Docs:** http://localhost:8081/docs
2. **Integration Guide:** `FRONTEND_INTEGRATION_GUIDE.md`
3. **Quick Start:** `FOR_FRONTEND_DEVELOPER.md`

### Important:
- Most endpoints require `Authorization: Bearer {token}`
- Token is obtained via `/auth/login` or `/auth/register`
- All responses are in JSON format
- Use `Accept: application/json` header

---

## 📞 Support

- **Documentation:** See `.md` files in project root
- **API Docs:** http://localhost:8081/docs
- **Issues:** GitHub Issues

---

**🎊 Backend ready for production!** 🚀
