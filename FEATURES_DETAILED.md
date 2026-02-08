# 🎯 Детальний опис фіч та як вони працюють

**Habit Tracker Backend API - Повний опис функціоналу**

---

## 📋 Зміст

1. [Authentication & Authorization](#1-authentication--authorization)
2. [Habit Management](#2-habit-management)
3. [Habit Logging](#3-habit-logging)
4. [Gamification System](#4-gamification-system)
5. [Heroes System](#5-heroes-system)
6. [Reminders System](#6-reminders-system)
7. [Achievements System](#7-achievements-system)
8. [User Statistics](#8-user-statistics)
9. [Onboarding](#9-onboarding)

---

## 1. Authentication & Authorization

### **1.1. Email/Password Authentication**

#### **Реєстрація (`POST /api/auth/register`)**

**Як працює:**
1. Користувач надсилає: `name`, `email`, `password`, `password_confirmation`, `locale` (опціонально)
2. Backend валідує дані:
   - Email має бути унікальним
   - Password мінімум 8 символів
   - Passwords повинні співпадати
3. Створює користувача з хешованим паролем
4. **Автоматично створює першого героя** (Warrior) через `GamificationService::createFirstHero()`
5. Генерує Sanctum токен
6. Повертає: user data, token, first_hero info

**Приклад Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "locale": "en"
}
```

**Приклад Response:**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "locale": "en"
  },
  "token": "1|abc123xyz456...",
  "first_hero": {
    "id": 1,
    "name": "Warrior",
    "level": 1
  }
}
```

#### **Login (`POST /api/auth/login`)**

**Як працює:**
1. Користувач надсилає: `email`, `password`
2. Backend перевіряє credentials через Laravel Auth
3. Генерує новий Sanctum токен
4. Повертає user data та token

#### **Logout (`POST /api/auth/logout`)**

**Як працює:**
1. Видаляє поточний Sanctum токен
2. Користувач більше не може використовувати цей токен

---

### **1.2. Social Authentication**

#### **Apple/Google Sign-In (`POST /api/auth/social-login`)**

**Як працює:**
1. Frontend отримує token з Apple/Google
2. Frontend надсилає: `provider` (apple/google), `provider_id`, `name`, `email`, `avatar`
3. Backend перевіряє:
   - Якщо користувач вже існує по `provider_id` → login
   - Якщо користувач існує по `email` → link провайдера до існуючого аккаунта
   - Якщо користувача немає → створює нового
4. **Для нового користувача** автоматично створює першого героя
5. Генерує Sanctum токен
6. Повертає user data, token, is_new_user flag

**Приклад Request:**
```json
{
  "provider": "apple",
  "provider_id": "001234.abc123def456",
  "name": "John Doe",
  "email": "john@example.com",
  "avatar": "https://..."
}
```

---

## 2. Habit Management

### **2.1. Створення звички (`POST /api/habits`)**

**Як працює:**
1. Користувач надсилає дані звички
2. Backend валідує та створює запис в БД
3. Повертає створену звичку

**Поля звички:**
- `title` (required) - Назва звички
- `description` (optional) - Опис
- `icon` (optional) - Emoji або іконка
- `color` (optional) - Hex колір (default: #6366f1)
- `frequency` (required) - `daily`, `weekly`, `monthly`
- `frequency_days` (optional) - Масив днів тижня [1,2,3,4,5] для weekly
- `target_count` (optional) - Скільки разів за період (default: 1)
- `is_active` (optional) - Активна/неактивна (default: true)

**Початкові значення:**
- `streak` = 0
- `best_streak` = 0
- `total_completions` = 0
- `last_completed_at` = null

**Приклад Request:**
```json
{
  "title": "Ранкова зарядка",
  "description": "Робити зарядку кожного ранку",
  "icon": "💪",
  "color": "#6366f1",
  "frequency": "daily",
  "target_count": 1
}
```

---

### **2.2. Перегляд звичок (`GET /api/habits`)**

**Як працює:**
1. Отримує всі звички користувача
2. Опціональна фільтрація: `?is_active=true`
3. Сортування: по даті створення (desc)
4. Повертає список зі всією статистикою

**Response включає:**
- Базові дані (title, icon, color, frequency)
- Статистику (streak, best_streak, total_completions)
- Стан (is_active, is_completed_today, last_completed_at)

---

### **2.3. Оновлення звички (`PUT /api/habits/{id}`)**

**Як працює:**
1. Перевіряє authorization (тільки власник може оновлювати)
2. Валідує дані
3. Оновлює тільки надіслані поля
4. Повертає оновлену звичку

**Можна оновити:**
- title, description, icon, color
- frequency, frequency_days, target_count
- is_active (активувати/деактивувати)

---

### **2.4. Видалення звички (`DELETE /api/habits/{id}`)**

**Як працює:**
1. Перевіряє authorization
2. Виконує **soft delete** (не видаляє з БД, лише помічає як видалену)
3. Повертає 204 No Content

**Soft delete означає:**
- Запис залишається в БД
- `deleted_at` timestamp встановлюється
- Звичку не видно в списку
- Можна відновити (якщо додати restore endpoint)

---

## 3. Habit Logging

### **3.1. Логування виконання (`POST /api/habits/{id}/log`)**

**Це найважливіший endpoint - тут відбувається вся магія! 🎉**

**Як працює (покроково):**

#### **Крок 1: Створення логу**
```php
$log = $habit->logs()->updateOrCreate([
    'habit_id' => $habit->id,
    'user_id' => auth()->id(),
    'completed_at' => $request->input('completed_at', today()),
], [
    'note' => $request->input('note'),
    'count' => $request->input('count', 1),
]);
```
- Створює або оновлює лог виконання
- Якщо для цього дня вже є лог - оновлює його
- Підтримує кастомну дату (можна логувати за минулі дні)

#### **Крок 2: Оновлення streak**
```php
$streakInfo = $gamification->updateStreak($habit);
```

**Логіка streak:**
- **Перше виконання** (last_completed_at = null): `streak = 1`, статус `started`
- **Послідовний день** (вчора було виконано): `streak++`, статус `increased`
- **Пропущено день** (більше ніж 1 день): `streak = 1`, статус `broken`
- **Вже виконано сьогодні**: без змін, статус `maintained`
- **Milestone** (streak % 7 === 0): прапорець `is_milestone = true`

#### **Крок 3: Оновлення статистики звички**
```php
$habit->increment('total_completions');
$habit->last_completed_at = now();
$habit->save();
```

#### **Крок 4: Нарахування XP**
```php
$xpInfo = $gamification->awardXpForHabit(auth()->user(), $habit);
```

**Як рахується XP:**
1. Базова XP: **10 points**
2. Streak bonus: **+20% кожні 3 дні**
3. Формула: `BASE_XP + (floor(streak/3) * BASE_XP * 0.2)`

**Приклади:**
- Streak 0-2 дні: 10 XP
- Streak 3-5 днів: 12 XP (10 + 2)
- Streak 6-8 днів: 14 XP (10 + 4)
- Streak 9-11 днів: 16 XP (10 + 6)
- і так далі...

**XP додається до активного героя:**
- Якщо немає активного героя → XP не нараховується
- Якщо є активний герой → XP додається, перевіряється level up

#### **Крок 5: Перевірка achievements**
```php
$newAchievements = $achievements->checkAchievements(auth()->user());
```

**Як працює:**
- Перевіряє всі achievements, які ще не розблоковані
- Перевіряє чи користувач відповідає вимогам
- Якщо так - розблоковує achievement та нараховує XP reward

#### **Крок 6: Формування response**
```json
{
  "data": {
    "id": 1,
    "completed_at": "2025-01-15",
    "note": "Відчував себе чудово!",
    "count": 1
  },
  "gamification": {
    "xp": {
      "xp_awarded": 14,
      "total_xp": 523,
      "level": 5,
      "level_up": false,
      "next_level_xp": 620
    },
    "streak": {
      "streak": 8,
      "streak_status": "increased",
      "is_milestone": false
    }
  },
  "achievements": {
    "newly_unlocked": [
      {
        "id": 2,
        "title": "Week Warrior",
        "icon": "🏆",
        "xp_reward": 50
      }
    ],
    "count": 1
  }
}
```

**Приклад Request:**
```json
{
  "completed_at": "2025-01-15",
  "note": "Відчував себе чудово!",
  "count": 1
}
```

---

### **3.2. Історія виконань (`GET /api/habits/{id}/logs`)**

**Як працює:**
1. Отримує всі логи для звички
2. Сортування: по даті (desc - найновіші перші)
3. Повертає список логів з нотатками

---

### **3.3. Статистика звички (`GET /api/habits/{id}/stats`)**

**Як працює:**
1. Отримує статистику звички
2. Розраховує completion rate за 30 днів
3. Повертає детальну статистику

**Response:**
```json
{
  "total_completions": 45,
  "current_streak": 8,
  "best_streak": 12,
  "completion_rate_30_days": 75.5,
  "last_completed_at": "2025-01-15T10:30:00.000000Z",
  "is_completed_today": true
}
```

---

## 4. Gamification System

### **4.1. XP (Experience Points) System**

#### **Як нараховується XP:**

**Базова формула:**
```
BASE_XP = 10 points
Streak Bonus = floor(streak / 3) * (BASE_XP * 0.2)
Total XP = BASE_XP + Streak Bonus
```

**Реалізація в `XpCalculator`:**
```php
public function calculate(Habit $habit): int
{
    $xp = self::BASE_XP; // 10
    
    // Add streak bonus
    $xp += $this->calculateStreakBonus($habit->streak);
    
    return (int) $xp;
}

private function calculateStreakBonus(int $streak): float
{
    if ($streak <= 3) {
        return 0; // No bonus for streaks < 3
    }
    
    $bonusTiers = floor($streak / 3);
    return $bonusTiers * (self::BASE_XP * 0.2);
}
```

**Приклади розрахунку:**
- Streak 1-2: 10 XP
- Streak 3-5: 12 XP (10 + 2)
- Streak 6-8: 14 XP (10 + 4)
- Streak 9-11: 16 XP (10 + 6)
- Streak 30: 30 XP (10 + 20)

#### **Як XP додається до героя:**
```php
// В GamificationService::awardXpForHabit()
$xp = $this->xpCalculator->calculate($habit);
$activeHero->experience += $xp;

// Перевірка level up
$leveledUp = $this->levelUpService->processLevelUp($activeHero);
```

---

### **4.2. Level System**

#### **Формула XP для рівня:**
```
XP required = 100 * level + (level^1.5 * 20)
```

**Приклади:**
- Level 1 → 2: 100 + 20 = 120 XP
- Level 2 → 3: 200 + 56 = 256 XP
- Level 3 → 4: 300 + 103 = 403 XP
- Level 5 → 6: 500 + 223 = 723 XP
- Level 10 → 11: 1000 + 632 = 1632 XP

**Реалізація:**
```php
public function getXpForLevel(int $level): int
{
    return self::XP_PER_LEVEL * $level 
         + (int) (pow($level, self::LEVEL_EXPONENT) * self::LEVEL_MULTIPLIER);
}
```

#### **Автоматичний Level Up:**

**Як працює `LevelUpService::processLevelUp()`:**
1. Перевіряє чи `experience >= XP для наступного рівня`
2. Якщо так:
   - Збільшує `level++`
   - Віднімає використаний XP
   - Перевіряє чи можна підняти ще один рівень (loop)
3. Повертає `true` якщо був level up

**Приклад:**
- Hero має 1000 XP, Level 3
- XP для Level 4 = 403
- XP для Level 5 = 523
- Hero отримав +200 XP → тепер 1200 XP
- Level 3 → 4: 1200 - 403 = 797 XP, Level = 4
- Level 4 → 5: 797 - 523 = 274 XP, Level = 5
- Повертає `level_up = true`

---

### **4.3. Streak System**

#### **Як оновлюється streak:**

**Логіка в `GamificationService::updateStreak()`:**

```php
$today = today();
$lastCompleted = $habit->last_completed_at?->startOfDay();

// Перше виконання
if (!$lastCompleted || $habit->streak === 0) {
    $habit->streak = 1;
    return ['streak' => 1, 'streak_status' => 'started'];
}

$daysDiff = $lastCompleted->diffInDays($today);

// Вже виконано сьогодні
if ($daysDiff === 0) {
    return ['streak' => $habit->streak, 'streak_status' => 'maintained'];
}

// Послідовний день
if ($daysDiff === 1) {
    $habit->streak++;
    $habit->best_streak = max($habit->best_streak, $habit->streak);
    
    $isMilestone = ($habit->streak % 7 === 0);
    return [
        'streak' => $habit->streak,
        'streak_status' => 'increased',
        'is_milestone' => $isMilestone
    ];
}

// Streak broken (пропущено > 1 день)
$previousStreak = $habit->streak;
$habit->streak = 1;
return [
    'streak' => 1,
    'streak_status' => 'broken',
    'previous_streak' => $previousStreak
];
```

**Сценарії:**
- **День 1:** streak = 1, статус = `started`
- **День 2:** streak = 2, статус = `increased`
- **День 7:** streak = 7, статус = `increased`, is_milestone = true
- **Пропущено 1 день:** streak = 1, статус = `broken`, previous_streak = 6

---

## 5. Heroes System

### **5.1. Hero Templates**

**Структура Hero:**
- `name` - Назва (Warrior, Sage, Guardian, Phoenix)
- `description` - Опис
- `model_url` - URL до 3D моделі
- `thumbnail_url` - Превью зображення
- `rarity` - common, rare, epic, legendary
- `unlock_level` - Мінімальний рівень для розблокування
- `unlock_cost` - Вартість в поінтах (опціонально)
- `is_premium` - Чи потрібна преміум підписка
- `stats` - JSON з характеристиками (strength, agility, etc.)

### **5.2. User Hero Instance**

**Кожен користувач має власні екземпляри героїв:**

**Структура UserHero:**
- `level` - Рівень героя (починається з 1)
- `experience` - Поточний досвід
- `experience_to_next_level` - XP до наступного рівня
- `total_habits_completed` - Всього звичок виконано
- `current_streak` - Поточна серія (для героя)
- `best_streak` - Найкраща серія
- `is_active` - Чи обраний зараз
- `is_unlocked` - Чи розблокований
- `stats` - Поточні характеристики (з урахуванням рівня)
- `customization` - JSON з кастомізаціями
- `achievements` - JSON з досягненнями героя

### **5.3. Розблокування героя (`POST /api/user/heroes/{id}/unlock`)**

**Як працює:**
1. Перевіряє чи герой існує
2. Створює UserHero instance (якщо ще немає)
3. Встановлює `is_unlocked = true`
4. Копіює базові stats з Hero template
5. Повертає UserHero

**Примітка:** Наразі не перевіряє вимоги (unlock_level, unlock_cost) - можна додати валідацію.

### **5.4. Активування героя (`POST /api/user/heroes/{userHero}/activate`)**

**Як працює:**
1. Деактивує всі інші герої користувача
2. Активує обраний герой (`is_active = true`)
3. Встановлює `last_active_at = now()`
4. Повертає активованого героя

**Важливо:** XP нараховується тільки активному герою!

---

## 6. Reminders System

### **6.1. Створення нагадування (`POST /api/reminders`)**

**Як працює:**
1. Валідує дані
2. Перевіряє що звичка належить користувачу
3. Створює запис HabitReminder
4. Повертає створене нагадування

**Поля:**
- `habit_id` (required) - ID звички
- `time` (required) - Час у форматі HH:MM (наприклад, "09:00")
- `days` (optional) - Масив днів тижня [1,2,3,4,5] (1=Понеділок, 7=Неділя)
- `timezone` (optional) - Часовий пояс (default: UTC)
- `notification_type` (optional) - `push`, `email`, `both` (default: push)
- `message` (optional) - Кастомне повідомлення
- `is_enabled` (optional) - Чи активно (default: true)

**Приклад Request:**
```json
{
  "habit_id": 1,
  "time": "09:00",
  "days": [1, 2, 3, 4, 5],
  "timezone": "Europe/Kyiv",
  "notification_type": "push",
  "message": "Час для ранкової зарядки! 💪"
}
```

### **6.2. Логіка надсилання нагадувань**

**Метод `shouldSendToday()` в HabitReminder модель:**

```php
public function shouldSendToday(): bool
{
    if (!$this->is_enabled) {
        return false;
    }
    
    // Якщо немає конкретних днів - надсилати щодня
    if (empty($this->days)) {
        return true;
    }
    
    // Перевірка чи сьогодні потрібний день
    $today = now($this->timezone)->dayOfWeekIso; // 1-7
    return in_array($today, $this->days);
}
```

**Метод `getDueReminders()` в HabitReminderService:**
```php
public function getDueReminders(): Collection
{
    $currentTime = now()->format('H:i');
    
    return HabitReminder::where('is_enabled', true)
        ->whereTime('time', '<=', $currentTime)
        ->get()
        ->filter(function ($reminder) {
            // Перевірка чи вже надіслано сьогодні
            if ($reminder->last_sent_at?->isToday()) {
                return false;
            }
            
            // Перевірка чи сьогодні потрібний день
            return $reminder->shouldSendToday();
        });
}
```

**Як використовувати:**
1. Створити scheduled job (Laravel Task Scheduler)
2. Викликати `getDueReminders()` кожну хвилину
3. Для кожного нагадування:
   - Надіслати push notification (Firebase/APNs)
   - Надіслати email (якщо notification_type = email/both)
   - Викликати `markAsSent()` для оновлення `last_sent_at`

**Приклад Scheduled Job:**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $reminders = app(HabitReminderServiceContract::class)->getDueReminders();
        
        foreach ($reminders as $reminder) {
            // Send notification
            // ...
            
            app(HabitReminderServiceContract::class)->markAsSent($reminder);
        }
    })->everyMinute();
}
```

---

## 7. Achievements System

### **7.1. Структура Achievement**

**Поля:**
- `key` - Унікальний ключ (first_habit, week_warrior, etc.)
- `title` - Назва
- `description` - Опис
- `icon` - Emoji або іконка
- `category` - Категорія (habits, streaks, levels, etc.)
- `rarity` - Рідкість (common, rare, epic, legendary)
- `xp_reward` - XP винагорода при розблокуванні
- `requirements` - JSON з вимогами
- `is_secret` - Чи секретне (не показується до розблокування)
- `sort_order` - Порядок сортування

### **7.2. Requirements System**

**Підтримувані вимоги:**
```php
return match ($key) {
    'total_habits' => $user->habits()->count() >= $value,
    'total_completions' => $user->habitLogs()->count() >= $value,
    'current_streak' => $user->habits()->max('streak') >= $value,
    'hero_level' => $user->activeHero?->level >= $value,
    'days_registered' => $user->created_at->diffInDays(now()) >= $value,
    default => false,
};
```

**Приклад Achievement:**
```json
{
  "key": "week_warrior",
  "title": "Week Warrior",
  "description": "Complete 7 day streak",
  "icon": "🏆",
  "category": "streaks",
  "rarity": "rare",
  "xp_reward": 50,
  "requirements": {
    "current_streak": 7
  },
  "is_secret": false
}
```

### **7.3. Автоматична перевірка achievements**

**Як працює `AchievementService::checkAchievements()`:**

1. Отримує всі achievements, які ще не розблоковані користувачем
2. Для кожного achievement перевіряє вимоги через `meetsRequirements()`
3. Якщо всі вимоги виконані:
   - Викликає `unlockAchievement()`
   - Додає запис в `user_achievements` таблицю
   - Нараховує XP reward активному герою (якщо є)
4. Повертає колекцію нових achievements

**Інтеграція з логуванням:**
```php
// В HabitLogController::store()
$newAchievements = $achievements->checkAchievements(auth()->user());
```

**Важливо:** Перевірка відбувається автоматично при кожному логуванні звички!

### **7.4. Endpoints**

#### **GET /api/achievements**
Отримати всі доступні achievements (крім секретних).

#### **GET /api/achievements/user**
Отримати розблоковані achievements користувача з датою розблокування.

#### **POST /api/achievements/check**
Ручна перевірка нових achievements (зазвичай не потрібна, бо автоматична).

---

## 8. User Statistics

### **8.1. Загальна статистика (`GET /api/user/stats`)**

**Як працює:**

**Habits Stats:**
```php
'total' => $user->habits()->count(),
'active' => $user->habits()->where('is_active', true)->count(),
'completed_today' => $user->habits()->whereHas('logs', function ($query) {
    $query->whereDate('completed_at', today());
})->count(),
'completion_rate_7_days' => $this->getCompletionRate($user, 7),
'completion_rate_30_days' => $this->getCompletionRate($user, 30),
'total_completions' => $user->habitLogs()->count(),
```

**Completion Rate формула:**
```php
$activeHabits = $user->activeHabits()->count();
$expectedCompletions = $activeHabits * $days; // Наприклад, 5 habits * 7 days = 35
$actualCompletions = $user->habitLogs()
    ->where('completed_at', '>=', now()->subDays($days))
    ->count();

return ($actualCompletions / $expectedCompletions) * 100;
```

**Streaks Stats:**
```php
'longest_current' => $user->habits()->max('streak') ?? 0,
'longest_ever' => $user->habits()->max('best_streak') ?? 0,
'total_streak_days' => $user->habits()->sum('streak'),
```

**Hero Stats:**
```php
if ($activeHero) {
    $nextLevelXp = $this->getXpForLevel($activeHero->level + 1);
    $progress = ($activeHero->experience / $nextLevelXp) * 100;
    
    'name' => $activeHero->hero->name,
    'level' => $activeHero->level,
    'experience' => $activeHero->experience,
    'next_level_xp' => $nextLevelXp,
    'progress_percent' => round($progress, 1),
}
```

**Приклад Response:**
```json
{
  "habits": {
    "total": 5,
    "active": 4,
    "completed_today": 2,
    "completion_rate_7_days": 85.7,
    "completion_rate_30_days": 78.3,
    "total_completions": 156
  },
  "streaks": {
    "longest_current": 12,
    "longest_ever": 24,
    "total_streak_days": 156
  },
  "hero": {
    "name": "Warrior",
    "level": 5,
    "experience": 523,
    "next_level_xp": 620,
    "progress_percent": 84.3
  },
  "achievements": {
    "total_unlocked": 3,
    "recent": []
  }
}
```

---

## 9. Onboarding

### **9.1. Автоматичне створення першого героя**

**Як працює:**

**При реєстрації (`AuthController::register()`):**
```php
$user = User::create([...]);

// ⚡ ONBOARDING: Create first hero automatically
$firstHero = $gamification->createFirstHero($user);
```

**Логіка `GamificationService::createFirstHero()`:**

1. Шукає starter hero (де `unlock_level = 0` або `1`)
2. Якщо немає - створює дефолтного "Warrior"
3. Створює UserHero instance:
   - `level = 1`
   - `experience = 0`
   - `is_unlocked = true`
   - `is_active = true`
   - `stats` копіюються з Hero template

4. Повертає UserHero

**Response включає:**
```json
{
  "message": "User registered successfully",
  "user": {...},
  "token": "...",
  "first_hero": {
    "id": 1,
    "name": "Warrior",
    "level": 1
  }
}
```

**Важливо:** Користувач одразу має активного героя та може отримувати XP!

---

## 🔄 Повний Flow: Від реєстрації до виконання звички

### **Сценарій 1: Новий користувач**

1. **Реєстрація:**
   ```
   POST /api/auth/register
   → Створює User
   → Автоматично створює Warrior hero (Level 1, 0 XP)
   → Повертає token + first_hero info
   ```

2. **Створення звички:**
   ```
   POST /api/habits
   → Створює Habit (streak = 0, total_completions = 0)
   ```

3. **Створення нагадування:**
   ```
   POST /api/reminders
   → Створює HabitReminder (time = 09:00, days = [1,2,3,4,5])
   ```

4. **Виконання звички (День 1):**
   ```
   POST /api/habits/1/log
   → Створює HabitLog
   → Оновлює streak: 0 → 1 (status: started)
   → Нараховує XP: 10 (streak < 3, немає bonus)
   → Hero: 0 XP → 10 XP (Level 1, прогресує до Level 2)
   → Перевіряє achievements (може розблокувати "First Step")
   → Повертає всю інформацію
   ```

5. **Виконання звички (День 2):**
   ```
   POST /api/habits/1/log
   → Оновлює streak: 1 → 2 (status: increased)
   → Нараховує XP: 10
   → Hero: 10 XP → 20 XP
   ```

6. **Виконання звички (День 7):**
   ```
   POST /api/habits/1/log
   → Оновлює streak: 6 → 7 (status: increased, is_milestone: true)
   → Нараховує XP: 12 (streak bonus: +2)
   → Hero отримує XP та можливий level up
   → Може розблокувати "Week Warrior" achievement (+50 XP)
   ```

---

## 📊 Статистика та API Endpoints

### **Всі доступні endpoints:**

| Метод | Endpoint | Опис |
|-------|----------|------|
| **Authentication** | | |
| POST | `/api/auth/register` | Реєстрація |
| POST | `/api/auth/login` | Вхід |
| POST | `/api/auth/social-login` | Apple/Google вхід |
| POST | `/api/auth/logout` | Вихід |
| GET | `/api/auth/me` | Поточний користувач |
| **Habits** | | |
| GET | `/api/habits` | Список звичок |
| POST | `/api/habits` | Створити звичку |
| GET | `/api/habits/{id}` | Деталі звички |
| PUT | `/api/habits/{id}` | Оновити звичку |
| DELETE | `/api/habits/{id}` | Видалити звичку |
| **Habit Logs** | | |
| POST | `/api/habits/{id}/log` | Залогувати виконання |
| GET | `/api/habits/{id}/logs` | Історія виконань |
| GET | `/api/habits/{id}/stats` | Статистика звички |
| **Reminders** | | |
| GET | `/api/reminders` | Список нагадувань |
| POST | `/api/reminders` | Створити нагадування |
| PUT | `/api/reminders/{id}` | Оновити нагадування |
| DELETE | `/api/reminders/{id}` | Видалити нагадування |
| **Achievements** | | |
| GET | `/api/achievements` | Всі achievements |
| GET | `/api/achievements/user` | Розблоковані achievements |
| POST | `/api/achievements/check` | Перевірити нові |
| **User Stats** | | |
| GET | `/api/user/stats` | Загальна статистика |
| **Heroes** | | |
| GET | `/api/heroes` | Список героїв |
| GET | `/api/heroes/{id}` | Деталі героя |
| **User Heroes** | | |
| GET | `/api/user/heroes` | Мої герої |
| GET | `/api/user/heroes/active` | Активний герой |
| POST | `/api/user/heroes/{id}/unlock` | Розблокувати героя |
| POST | `/api/user/heroes/{userHero}/activate` | Активувати героя |
| **System** | | |
| GET | `/api/health` | Health check |

**Всього: 32+ endpoints**

---

## 🎯 Ключові особливості

### **1. Автоматизація**
- ✅ Автоматичне нарахування XP
- ✅ Автоматичне оновлення streak
- ✅ Автоматичний level up
- ✅ Автоматична перевірка achievements
- ✅ Автоматичне створення першого героя

### **2. Інтеграція систем**
- ✅ Логування звички → XP → Level Up → Achievements (всі разом!)
- ✅ Achievements нараховують bonus XP
- ✅ Streak впливає на XP bonus

### **3. Гнучкість**
- ✅ Підтримка кастомних дат виконання
- ✅ Кастомні дні для weekly habits
- ✅ Різні типи notifications для reminders
- ✅ Timezone support

### **4. Безпека**
- ✅ Authorization policies (кожен бачить тільки свої дані)
- ✅ Bearer token authentication
- ✅ Form Request validation

---

**Документ оновлено:** 2025-01-XX  
**Версія API:** 1.0

