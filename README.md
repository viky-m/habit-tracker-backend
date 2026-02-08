# 🎯 Habit Tracker Backend API

Гейміфікований трекер звичок з 3D героями - Backend API

---

## 🚀 Швидкий старт

### Запуск проекту:

```bash
# 1. Запустити Docker
docker-compose up -d

# 2. Встановити залежності (якщо перший раз)
docker exec habittracker_php composer install

# 3. Налаштувати базу даних
docker exec habittracker_php php artisan migrate
docker exec habittracker_php php artisan db:seed --class=HeroSeeder

# 4. Згенерувати документацію
docker exec habittracker_php php artisan scribe:generate

# 5. Відкрити документацію
# http://localhost:8081/docs
```

---

## 📚 Документація API

- **🎨 Scribe Documentation:** http://localhost:8081/docs ← **ГОЛОВНЕ!**
- **🧪 API Playground:** http://localhost:8081/playground
- **❤️ Health Check:** http://localhost:8081/api/health

---

## 🎮 Реалізований функціонал

### Авторизація:
- ✅ Email/Password реєстрація та login
- ✅ Apple Sign-In
- ✅ Google Sign-In
- ✅ Account Linking (прив'язка провайдерів до одного email)
- ✅ Laravel Sanctum токени
- ✅ Автоматичний onboarding з першим героєм

### Звички (Habits):
- ✅ CRUD операції (Create, Read, Update, Delete)
- ✅ Категорізація за частотою (daily, weekly, monthly, custom)
- ✅ Кастомні дні для виконання
- ✅ Target counts
- ✅ Іконки та кольори
- ✅ Soft deletes

### Логування виконань:
- ✅ Логування виконання звички
- ✅ Нотатки до виконання
- ✅ Кількість виконань
- ✅ Історія всіх виконань
- ✅ Статистика по звичці (completion rate, streaks)

### Гейміфікація:
- ✅ **XP система** - нарахування за виконання звичок
- ✅ **Streak система** - серії послідовних днів
- ✅ **Level up** - автоматичне підвищення рівня героя
- ✅ **4 starter heroes** (Warrior, Sage, Guardian, Phoenix)
- ✅ **Hero progression** - досвід, рівень, статистика
- ✅ **Onboarding** - автоматичне створення першого героя
- ✅ **User statistics** - загальна статистика прогресу
- ✅ **Reminders** - нагадування про звички (P0!)
- ✅ **Achievements** - система досягнень (13 achievements!)

### Мультимовність:
- ✅ English (en)
- ✅ Українська (uk)

---

## 📊 API Endpoints (32)

### Authentication (5):
```
POST /api/auth/register         - Реєстрація
POST /api/auth/login            - Вхід
POST /api/auth/social-login     - Apple/Google вхід
POST /api/auth/logout           - Вихід
GET  /api/auth/me               - Поточний користувач
```

### Habits (5):
```
GET    /api/habits              - Список звичок
POST   /api/habits              - Створити звичку
GET    /api/habits/{id}         - Деталі звички
PUT    /api/habits/{id}         - Оновити звичку
DELETE /api/habits/{id}         - Видалити звичку
```

### Habit Logs (3):
```
POST /api/habits/{id}/log       - Залогувати виконання (+ XP & Streak!)
GET  /api/habits/{id}/logs      - Історія виконань
GET  /api/habits/{id}/stats     - Статистика звички
```

### User Statistics (1):
```
GET /api/user/stats             - Загальна статистика користувача
```

### Reminders (4):
```
GET    /api/reminders           - Список нагадувань
POST   /api/reminders           - Створити нагадування
PUT    /api/reminders/{id}      - Оновити нагадування
DELETE /api/reminders/{id}      - Видалити нагадування
```

### Achievements (3):
```
GET  /api/achievements          - Всі досягнення
GET  /api/achievements/user     - Розблоковані досягнення
POST /api/achievements/check    - Перевірити нові
```

### Heroes (2):
```
GET /api/heroes                 - Список доступних героїв
GET /api/heroes/{id}            - Деталі героя
```

### User Heroes (4):
```
GET  /api/user/heroes           - Мої герої
GET  /api/user/heroes/active    - Активний герой
POST /api/user/heroes/{id}/unlock   - Розблокувати героя
POST /api/user/heroes/{id}/activate - Активувати героя
```

### System (1):
```
GET /api/health                 - Health check
```

---

## 🛠️ Команди для розробки

### Docker:
```bash
# Запустити всі контейнери
docker-compose up -d

# Зупинити
docker-compose down

# Перебудувати після змін в Dockerfile
docker-compose up -d --build

# Логи
docker-compose logs -f php
```

### Laravel:
```bash
# Міграції
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

### Тестування:
```bash
# Всі тести (90 passed!)
docker exec habittracker_php php artisan test

# Конкретна група
docker exec habittracker_php php artisan test --filter=AuthTest
docker exec habittracker_php php artisan test --filter=GamificationTest
docker exec habittracker_php php artisan test --filter=AchievementTest
docker exec habittracker_php php artisan test --filter=ReminderTest

# З coverage
docker exec habittracker_php php artisan test --coverage

# З детальним виводом
docker exec habittracker_php php artisan test --verbose
```

### Code Quality:
```bash
# Laravel Pint (formatter)
docker exec habittracker_php vendor/bin/pint

# Перевірка без змін
docker exec habittracker_php vendor/bin/pint --test

# Larastan (static analysis)
docker exec habittracker_php vendor/bin/phpstan analyse
```

### Документація:
```bash
# Згенерувати Scribe документацію
docker exec habittracker_php php artisan scribe:generate

# Очистити кеш Scribe
docker exec habittracker_php php artisan scribe:clear
```

### Database:
```bash
# Відкрити MySQL shell
docker exec -it habittracker_mysql mysql -u habittracker -phabittracker habittracker

# Backup database
docker exec habittracker_mysql mysqldump -u habittracker -phabittracker habittracker > backup.sql

# Restore database
docker exec -i habittracker_mysql mysql -u habittracker -phabittracker habittracker < backup.sql
```

### PHP Container:
```bash
# Зайти в PHP контейнер
docker exec -it habittracker_php bash

# Composer
docker exec habittracker_php composer install
docker exec habittracker_php composer update
```

---

## 📋 Правила розробки

### Code Quality Standards:

#### 1. **SOLID Principles** (обов'язково!)
- **S**ingle Responsibility - один клас = одна відповідальність
- **O**pen/Closed - відкрито для розширення, закрито для модифікації
- **L**iskov Substitution - можливість заміни реалізацій
- **I**nterface Segregation - специфічні інтерфейси
- **D**ependency Inversion - залежність від абстракцій

#### 2. **Використовуйте Interfaces:**
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

#### 3. **Service Layer для бізнес-логіки:**
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

#### 4. **Type Hints скрізь:**
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

#### 7. **Dependency Injection через Constructor:**
```php
// ✅ Good - Constructor Injection
class UserService {
    public function __construct(
        private UserRepository $repository
    ) {}
}
```

#
## Frontend Integration

For the API to work correctly with a frontend application (CORS), you must configure the `FRONTEND_URL` in your `.env` file:

```env
FRONTEND_URL=http://localhost:5173
```

This URL should match the URL where your frontend application is running.

## Testing Requirements:

#### 1. **Обов'язкові тести для:**
- ✅ Всі API endpoints (feature tests)
- ✅ Бізнес-логіка в сервісах (unit tests)
- ✅ Validation rules (feature tests)
- ✅ Authorization policies (feature tests)

#### 2. **Coverage мінімум 70%:**
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

#### 1. **Laravel Pint перед кожним commit:**
```bash
vendor/bin/pint
```

#### 2. **PHPDoc для публічних методів:**
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

## 🎯 Для Frontend розробника

### Документація:
1. **Scribe Docs:** http://localhost:8081/docs
2. **Integration Guide:** `FRONTEND_INTEGRATION_GUIDE.md`
3. **Quick Start:** `FOR_FRONTEND_DEVELOPER.md`

### Важливо:
- Більшість endpoints вимагають `Authorization: Bearer {token}`
- Токен отримується при `/auth/login` або `/auth/register`
- Всі responses в JSON форматі
- Використовуйте `Accept: application/json` header

---

## 📞 Підтримка

- **Документація:** Дивіться `.md` файли в корені проекту
- **API Docs:** http://localhost:8081/docs
- **Issues:** GitHub Issues

---

**🎊 Backend ready for production!** 🚀
