# 🛠️ Development Guide

Development rules and code quality standards for Habit Tracker Backend

---

## 📋 Code Quality Standards

### SOLID Principles (mandatory!)

#### **S - Single Responsibility Principle**
One class = one responsibility

```php
// ✅ Good
class XpCalculator {
    public function calculate(Habit $habit): int { }
}

class LevelUpService {
    public function processLevelUp(UserHero $hero): bool { }
}

// ❌ Bad
class GamificationService {
    public function calculateXp() { }
    public function levelUp() { }
    public function updateStreak() { }
    public function sendNotifications() { }  // Too many responsibilities!
}
```

#### **O - Open/Closed Principle**
Open for extension, closed for modification

```php
// ✅ Good - use interfaces
interface XpCalculatorContract {
    public function calculate(Habit $habit): int;
}

// Can add a new implementation without changing existing code
class PremiumXpCalculator implements XpCalculatorContract { }
```

#### **L - Liskov Substitution Principle**
Any implementation of an interface should work the same way

```php
// ✅ Good
function awardXp(XpCalculatorContract $calculator) {
    $xp = $calculator->calculate($habit);
    // Works with any implementation!
}
```

#### **I - Interface Segregation Principle**
Small specific interfaces instead of large ones

```php
// ✅ Good
interface XpCalculatorContract {
    public function calculate(Habit $habit): int;
}

interface LevelUpServiceContract {
    public function processLevelUp(UserHero $hero): bool;
}

// ❌ Bad - too big
interface GamificationServiceContract {
    public function calculate(...): int;
    public function levelUp(...): bool;
    public function updateStreak(...): array;
    public function sendEmail(...): void;
}
```

#### **D - Dependency Inversion Principle**
Depend on abstractions, not on concrete classes

```php
// ✅ Good - dependency on interface
class GamificationService {
    public function __construct(
        private XpCalculatorContract $xpCalculator
    ) {}
}

// ❌ Bad - dependency on concrete class
class GamificationService {
    private XpCalculator $xpCalculator;
    
    public function __construct() {
        $this->xpCalculator = new XpCalculator();
    }
}
```

---

## 🎯 Clean Code Practices

### 1. **No Magic Numbers**
```php
// ✅ Good
private const BASE_XP = 10;
private const STREAK_BONUS_THRESHOLD = 3;

$xp = self::BASE_XP;

// ❌ Bad
$xp = 10;
if ($streak > 3) { }
```

### 2. **Descriptive Names**
```php
// ✅ Good
$completionRateLastSevenDays
$isHabitCompletedToday
$calculateStreakBonus()

// ❌ Bad
$rate
$check
$calc()
```

### 3. **Small Methods (< 20 lines)**
```php
// ✅ Good
public function awardXp() {
    $xp = $this->calculate();
    $leveledUp = $this->checkLevelUp();
    return $this->buildResponse();
}

private function calculate(): int { }
private function checkLevelUp(): bool { }
private function buildResponse(): array { }
```

### 4. **Early Returns**
```php
// ✅ Good
public function process() {
    if (!$user) {
        return null;
    }
    
    if (!$hero) {
        return [];
    }
    
    return $this->doWork();
}

// ❌ Bad
public function process() {
    if ($user) {
        if ($hero) {
            return $this->doWork();
        } else {
            return [];
        }
    } else {
        return null;
    }
}
```

### 5. **Type Hints Everywhere**
```php
// ✅ Good
public function calculate(Habit $habit): int
public function getUsers(): Collection
public function isActive(User $user): bool

// ❌ Bad
public function calculate($habit)
public function getUsers()
```

---

## 🧪 Testing Requirements

### Mandatory Tests:

#### 1. **Feature Tests for API endpoints:**
```php
test_user_can_register_with_valid_data()
test_user_can_login_with_correct_credentials()
test_habit_log_awards_xp_to_active_hero()
```

#### 2. **Unit Tests for business logic:**
```php
test_xp_calculator_returns_base_xp_for_first_completion()
test_xp_calculator_adds_streak_bonus_after_three_days()
test_level_up_service_increases_level_when_enough_xp()
```

#### 3. **Minimum Coverage 70%:**
```bash
php artisan test --coverage --min=70
```

### Pest Assertions (use them):
```php
// HTTP Status
$response->assertSuccessful();      // 200-299
$response->assertCreated();         // 201
$response->assertUnauthorized();    // 401
$response->assertForbidden();       // 403
$response->assertNotFound();        // 404
$response->assertUnprocessable();   // 422

// JSON Structure
$response->assertJsonStructure(['data', 'meta']);
$response->assertJson(['status' => 'ok']);
$response->assertJsonFragment(['email' => 'test@example.com']);

// Database
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertDatabaseMissing('users', ['email' => 'deleted@example.com']);
```

---

## 🎨 Code Style

### 1. **Laravel Pint (mandatory before commit!):**
```bash
vendor/bin/pint
```

### 2. **PHPDoc Comments:**
```php
/**
 * Calculate XP for habit completion
 *
 * @param Habit $habit Completed habit
 * @return int XP amount awarded
 */
public function calculate(Habit $habit): int
{
    // ...
}
```

### 3. **Curly Braces Always:**
```php
// ✅ Good
if ($condition) {
    doSomething();
}

// ❌ Bad
if ($condition) doSomething();
```

### 4. **Constructor Property Promotion (PHP 8):**
```php
// ✅ Good
public function __construct(
    private UserRepository $repository,
    private XpCalculatorContract $calculator
) {}

// ❌ Bad
private $repository;
private $calculator;

public function __construct(UserRepository $repository, XpCalculator $calculator) {
    $this->repository = $repository;
    $this->calculator = $calculator;
}
```

---

## 🏗️ Architecture Patterns

### 1. **Service Layer Pattern**
Business logic in services:
```
app/Services/
├── Contracts/
│   └── UserServiceContract.php
└── UserService.php
```

### 2. **Repository Pattern (via Eloquent)**
```php
// Use Eloquent as Repository
User::where('email', $email)->first();
Habit::with('logs')->get();
```

### 3. **Resource Pattern for API responses:**
```php
// ✅ Good
return new UserResource($user);
return HabitResource::collection($habits);

// ❌ Bad
return $user->toArray();
```

### 4. **Form Request Pattern for validation:**
```php
// ✅ Good
public function store(StoreHabitRequest $request)

// ❌ Bad
public function store(Request $request) {
    $validated = $request->validate([...]);
}
```

---

## ⚠️ N+1 Query Prevention (CRITICAL!)

### **Always use Eager Loading:**

```php
// ✅ Good
$habits = Habit::with('logs', 'user')->get();
foreach ($habits as $habit) {
    echo $habit->user->name;  // No additional query
}

// ❌ Bad - N+1 Problem!
$habits = Habit::all();
foreach ($habits as $habit) {
    echo $habit->user->name;  // Query for EACH habit!
}
```

### **Use withCount() for counting:**
```php
// ✅ Good
$users = User::withCount('habits')->get();
$count = $user->habits_count;

// ❌ Bad
$users = User::all();
$count = $user->habits()->count();  // N+1!
```

---

## 📁 File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/              ← API Controllers (thin!)
│   ├── Requests/             ← Form Requests (validation)
│   ├── Resources/            ← API Resources (responses)
│   └── Middleware/
├── Services/
│   ├── Contracts/            ← Interfaces
│   └── *.php                 ← Service implementations
├── Models/                   ← Eloquent Models
├── Policies/                 ← Authorization
└── Providers/                ← Service Providers

database/
├── migrations/               ← Database schema
├── seeders/                  ← Database seeders
├── factories/                ← Model factories

tests/
├── Feature/                  ← API/Integration tests
└── Unit/                     ← Business logic tests
```

---

## 🔄 Development Workflow

### 1. **Create feature:**
```bash
# 1. Create migration
php artisan make:migration create_categories_table

# 2. Create model with factory
php artisan make:model Category -f

# 3. Create service with contract
# (manually create interface + implementation)

# 4. Create controller
php artisan make:controller Api/CategoryController --api

# 5. Create request
php artisan make:request Category/StoreCategoryRequest

# 6. Create resource
php artisan make:resource CategoryResource

# 7. Create policy
php artisan make:policy CategoryPolicy

# 8. Create tests
php artisan make:test Feature/CategoryTest --pest
```

### 2. **Check code:**
```bash
# Format code
vendor/bin/pint

# Run tests
php artisan test

# Static analysis (optional)
vendor/bin/phpstan analyse
```

### 3. **Update documentation:**
```bash
php artisan scribe:generate
```

### 4. **Commit:**
```bash
git add .
git commit -m "feat(TASK-123): add category management

Implements CRUD operations for habit categories"
```

---

## 🚨 Common Mistakes to Avoid

### ❌ **1. Fat Controllers**
```php
// ❌ Bad - business logic in controller
class HabitController {
    public function store(Request $request) {
        // 50 lines of validation, calculation, database operations
    }
}
```

### ❌ **2. No Type Hints**
```php
// ❌ Bad
public function calculate($habit) { }
```

### ❌ **3. Direct Model Access in Controllers**
```php
// ❌ Bad
User::where('email', $email)->update([...]);

// ✅ Good - use service
$this->userService->updateByEmail($email, $data);
```

### ❌ **4. No Interfaces**
```php
// ❌ Bad - depends on concrete class
public function __construct(XpCalculator $calculator) { }

// ✅ Good - depends on interface
public function __construct(XpCalculatorContract $calculator) { }
```

### ❌ **5. N+1 Queries**
```php
// ❌ Bad
$users = User::all();
foreach ($users as $user) {
    $user->habits->count();  // N+1!
}

// ✅ Good
$users = User::withCount('habits')->get();
foreach ($users as $user) {
    $user->habits_count;
}
```

---

## ✅ Checklist Before Commit

- [ ] `vendor/bin/pint` run
- [ ] `php artisan test` - all tests passed
- [ ] PHPDoc added to public methods
- [ ] Type hints for all parameters and return types
- [ ] No magic numbers (used constants)
- [ ] Eager loading for relationships
- [ ] SOLID principles followed
- [ ] `php artisan scribe:generate` - documentation updated

---

## 📊 Quality Metrics

### Target Metrics:
- **Test Coverage:** ≥ 70%
- **Lines per Method:** ≤ 20
- **Lines per Class:** ≤ 300
- **Cyclomatic Complexity:** ≤ 5
- **SOLID Compliance:** 100%

### Checking:
```bash
# Test coverage
php artisan test --coverage

# Code style
vendor/bin/pint --test

# Static analysis
vendor/bin/phpstan analyse
```

---

## 🎯 Best Practices

### Service Layer:
- ✅ Business logic in services only
- ✅ Controllers only coordinate
- ✅ Use dependency injection

### Testing:
- ✅ Feature tests for endpoints
- ✅ Unit tests for services
- ✅ Mock external dependencies
- ✅ Use factories for test data

### Database:
- ✅ Eager loading for relationships
- ✅ Indexes for frequently used fields
- ✅ Soft deletes for important data
- ✅ Transactions for multiple operations

### API:
- ✅ API Resources for responses
- ✅ Form Requests for validation
- ✅ Policies for authorization
- ✅ Rate limiting for production

---

## 📚 Resources

- **Laravel Docs:** https://laravel.com/docs/12.x
- **SOLID Principles:** https://en.wikipedia.org/wiki/SOLID
- **Clean Code:** https://github.com/ryanmcdermott/clean-code-javascript
- **Pest Testing:** https://pestphp.com/docs/
- **Scribe Docs:** https://scribe.knuckles.wtf/laravel

---

**Follow these rules for high-quality, maintainable code!** 🚀
