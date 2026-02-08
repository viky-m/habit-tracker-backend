# 📱 Frontend Integration Guide - Детальна інструкція

## 🎯 **Для Frontend розробника**

---

## 🔗 **Base URL:**
```
http://localhost:8081/api
```

**Production:** Буде змінено на ваш домен

---

## 🔑 **Авторизація:**

### **Формат:**
```javascript
headers: {
  'Authorization': 'Bearer YOUR_TOKEN_HERE',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

**⚠️ Важливо:** 
- Всі endpoints (крім `/health`, `/auth/register`, `/auth/login`, `/auth/social-login`) вимагають Bearer token
- Token отримується при login/register
- Зберігайте token в AsyncStorage (React Native) або SecureStore (Expo)

---

## 📚 **1. AUTHENTICATION**

### **1.1. Реєстрація (Email/Password)**

**Endpoint:** `POST /auth/register`

**Request Body:**
```json
{
  "name": "John Doe",                    // required, string, max:255
  "email": "john@example.com",           // required, email, unique
  "password": "password123",             // required, min:8
  "password_confirmation": "password123", // required, must match password
  "locale": "en"                         // optional, enum: en|uk|ru, default: en
}
```

**Success Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "avatar": null,
    "provider": "email",
    "locale": "en",
    "created_at": "2025-10-29T10:00:00.000000Z"
  },
  "token": "1|abc123xyz456..."
}
```

**Error Response (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

**Frontend код (React Native):**
```javascript
const register = async (name, email, password) => {
  try {
    const response = await fetch('http://localhost:8081/api/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name,
        email,
        password,
        password_confirmation: password,
        locale: 'en'  // or get from device settings
      })
    });
    
    const data = await response.json();
    
    if (response.ok) {
      // Зберегти token
      await AsyncStorage.setItem('token', data.token);
      await AsyncStorage.setItem('user', JSON.stringify(data.user));
      return data;
    } else {
      // Показати помилки валідації
      throw new Error(data.errors);
    }
  } catch (error) {
    console.error('Registration error:', error);
    throw error;
  }
};
```

---

### **1.2. Вхід (Email/Password)**

**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
  "email": "john@example.com",    // required, email
  "password": "password123"       // required
}
```

**Success Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "avatar": null,
    "provider": "email",
    "locale": "en",
    "created_at": "2025-10-29T10:00:00.000000Z"
  },
  "token": "2|def456uvw789..."
}
```

**Error Response (401):**
```json
{
  "message": "Invalid credentials"
}
```

---

### **1.3. Соціальний вхід (Apple/Google)**

**Endpoint:** `POST /auth/social-login`

**Request Body (Apple):**
```json
{
  "provider": "apple",                              // required, enum: apple|google
  "provider_id": "001234.abc123def456.1234",        // required, unique ID from provider
  "email": "john@privaterelay.appleid.com",         // required
  "name": "John Doe",                               // required
  "avatar": "https://example.com/avatar.jpg",       // optional
  "locale": "en"                                    // optional, default: en
}
```

**Request Body (Google):**
```json
{
  "provider": "google",
  "provider_id": "123456789012345678901",
  "email": "john@gmail.com",
  "name": "John Doe",
  "avatar": "https://lh3.googleusercontent.com/a/...",
  "locale": "uk"
}
```

**Success Response (200 або 201):**
```json
{
  "message": "Login successful",          // або "User created successfully" для нових
  "user": {
    "id": 2,
    "name": "John Doe",
    "email": "john@privaterelay.appleid.com",
    "avatar": "https://...",
    "provider": "apple",
    "locale": "en",
    "created_at": "2025-10-29T10:00:00.000000Z"
  },
  "token": "3|ghi789rst012...",
  "is_new_user": false                    // true якщо новий користувач
}
```

**Frontend (iOS - Swift):**
```swift
// Після успішного Apple Sign-In
func socialLogin(appleIDCredential: ASAuthorizationAppleIDCredential) async {
    let body: [String: Any] = [
        "provider": "apple",
        "provider_id": appleIDCredential.user,
        "email": appleIDCredential.email ?? "",
        "name": "\(appleIDCredential.fullName?.givenName ?? "") \(appleIDCredential.fullName?.familyName ?? "")",
        "locale": Locale.current.languageCode ?? "en"
    ]
    
    // POST to /api/auth/social-login
}
```

---

### **1.4. Вихід**

**Endpoint:** `POST /auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Request Body:** Порожній `{}`

**Success Response (200):**
```json
{
  "message": "Logout successful"
}
```

---

### **1.5. Поточний користувач**

**Endpoint:** `GET /auth/me`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "avatar": null,
    "provider": "email",
    "locale": "en",
    "created_at": "2025-10-29T10:00:00.000000Z"
  }
}
```

---

## ✅ **2. HABITS (Звички)**

### **2.1. Список звичок**

**Endpoint:** `GET /habits`

**Query Parameters:**
- `is_active` (optional, boolean) - фільтр по активності

**Examples:**
```
GET /habits                    // всі звички
GET /habits?is_active=true     // тільки активні
GET /habits?is_active=false    // неактивні
```

**Success Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Ранкова зарядка",
      "description": "Робити зарядку кожного ранку",
      "icon": "💪",
      "color": "#6366f1",
      "frequency": "daily",
      "frequency_days": null,
      "target_count": 1,
      "streak": 5,
      "best_streak": 10,
      "total_completions": 50,
      "is_active": true,
      "is_completed_today": true,
      "last_completed_at": "2025-10-29T10:00:00.000000Z",
      "created_at": "2025-10-28T10:00:00.000000Z",
      "updated_at": "2025-10-29T10:00:00.000000Z"
    }
  ]
}
```

**Frontend (React Native):**
```javascript
const getHabits = async (token, isActive = null) => {
  let url = 'http://localhost:8081/api/habits';
  if (isActive !== null) {
    url += `?is_active=${isActive}`;
  }
  
  const response = await fetch(url, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  
  const { data: habits } = await response.json();
  return habits;
};
```

---

### **2.2. Створити звичку**

**Endpoint:** `POST /habits`

**Request Body:**
```json
{
  "title": "Ранкова зарядка",        // required, string, max:255
  "description": "Робити зарядку...", // optional, string
  "icon": "💪",                       // optional, string (emoji), max:10
  "color": "#6366f1",                 // optional, hex color, default: #6366f1
  "frequency": "daily",               // required, enum: daily|weekly|monthly
  "frequency_days": [1, 3, 5],        // optional, array of integers 0-6 (only for weekly)
                                      // 0=Sunday, 1=Monday, 2=Tuesday ... 6=Saturday
  "target_count": 1                   // optional, integer, min:1, default: 1
}
```

**Приклади:**

**Daily habit:**
```json
{
  "title": "Випити 2л води",
  "icon": "💧",
  "color": "#3b82f6",
  "frequency": "daily",
  "target_count": 1
}
```

**Weekly habit (Пн, Ср, Пт):**
```json
{
  "title": "Тренування в залі",
  "icon": "🏋️",
  "frequency": "weekly",
  "frequency_days": [1, 3, 5],
  "target_count": 1
}
```

**Monthly habit:**
```json
{
  "title": "Прочитати книгу",
  "icon": "📚",
  "frequency": "monthly",
  "target_count": 1
}
```

**Success Response (200):**
```json
{
  "data": {
    "id": 2,
    "title": "Випити 2л води",
    "description": null,
    "icon": "💧",
    "color": "#3b82f6",
    "frequency": "daily",
    "frequency_days": null,
    "target_count": 1,
    "streak": 0,
    "best_streak": 0,
    "total_completions": 0,
    "is_active": true,
    "is_completed_today": false,
    "last_completed_at": null,
    "created_at": "2025-10-29T11:00:00.000000Z",
    "updated_at": "2025-10-29T11:00:00.000000Z"
  }
}
```

---

### **2.3. Деталі звички**

**Endpoint:** `GET /habits/{id}`

**Success Response (200):**
```json
{
  "data": {
    "id": 1,
    "title": "Ранкова зарядка",
    "description": "Робити зарядку кожного ранку",
    "icon": "💪",
    "color": "#6366f1",
    "frequency": "daily",
    "streak": 5,
    "is_completed_today": true,
    ...
  }
}
```

---

### **2.4. Оновити звичку**

**Endpoint:** `PUT /habits/{id}` або `PATCH /habits/{id}`

**Request Body (всі поля optional):**
```json
{
  "title": "Нова назва",
  "description": "Новий опис",
  "is_active": false,              // деактивувати звичку
  "color": "#ef4444"
}
```

**Success Response (200):**
```json
{
  "data": {
    "id": 1,
    "title": "Нова назва",
    ...
  }
}
```

---

### **2.5. Видалити звичку**

**Endpoint:** `DELETE /habits/{id}`

**Success Response (204):** Порожня відповідь

**Frontend:**
```javascript
const deleteHabit = async (token, habitId) => {
  const response = await fetch(`http://localhost:8081/api/habits/${habitId}`, {
    method: 'DELETE',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  
  if (response.status === 204) {
    console.log('Habit deleted successfully');
  }
};
```

---

## 📊 **3. HABIT LOGS (Логування)**

### **3.1. Відмітити виконання**

**Endpoint:** `POST /habits/{id}/log`

**Request Body:**
```json
{
  "completed_at": "2025-10-29",          // optional, date (YYYY-MM-DD), default: today
  "note": "Відчував себе чудово!",       // optional, string, max:500
  "count": 1                             // optional, integer, min:1, default: 1
}
```

**Приклади:**

**Відмітити сьогодні:**
```json
{
  "count": 1
}
```

**З ноткою:**
```json
{
  "note": "Важко було, але справився! 💪",
  "count": 1
}
```

**За минулу дату:**
```json
{
  "completed_at": "2025-10-28",
  "note": "Пропустив логування вчора",
  "count": 1
}
```

**Success Response (201):**
```json
{
  "data": {
    "id": 1,
    "habit_id": 1,
    "completed_at": "2025-10-29",
    "note": "Відчував себе чудово!",
    "count": 1,
    "created_at": "2025-10-29T11:00:00.000000Z"
  }
}
```

**Frontend (React Native):**
```javascript
const logHabit = async (token, habitId, note = null) => {
  const response = await fetch(`http://localhost:8081/api/habits/${habitId}/log`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      count: 1,
      note: note
    })
  });
  
  return await response.json();
};
```

---

### **3.2. Історія виконань**

**Endpoint:** `GET /habits/{id}/logs`

**Success Response (200):**
```json
{
  "data": [
    {
      "id": 5,
      "habit_id": 1,
      "completed_at": "2025-10-29",
      "note": "Відчував себе чудово!",
      "count": 1,
      "created_at": "2025-10-29T11:00:00.000000Z"
    },
    {
      "id": 4,
      "habit_id": 1,
      "completed_at": "2025-10-28",
      "note": null,
      "count": 1,
      "created_at": "2025-10-28T11:00:00.000000Z"
    }
  ]
}
```

---

### **3.3. Статистика звички**

**Endpoint:** `GET /habits/{id}/stats`

**Success Response (200):**
```json
{
  "total_completions": 50,              // скільки разів всього виконано
  "current_streak": 10,                  // поточна серія днів підряд
  "best_streak": 15,                     // найкраща серія
  "completion_rate_30_days": 83.33,      // % виконання за останні 30 днів
  "last_completed_at": "2025-10-29T11:00:00.000000Z",
  "is_completed_today": true             // чи виконано сьогодні
}
```

**Використання у UI:**
```javascript
// Показати прогрес бар
<ProgressBar value={stats.completion_rate_30_days} />

// Показати streak з fire emoji
{stats.current_streak > 0 && (
  <Text>🔥 {stats.current_streak} days streak!</Text>
)}

// Disable кнопку якщо вже виконано сьогодні
<Button 
  disabled={stats.is_completed_today}
  onPress={logHabit}
>
  {stats.is_completed_today ? '✅ Виконано' : 'Відмітити'}
</Button>
```

---

## 🦸 **4. HEROES (Герої)**

### **4.1. Список всіх героїв**

**Endpoint:** `GET /heroes`

**Success Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Warrior",
      "description": "Могутній воїн з великим мечем",
      "model_url": "https://cdn.habittracker.com/heroes/warrior.glb",
      "thumbnail_url": "https://cdn.habittracker.com/heroes/warrior-thumb.jpg",
      "rarity": "legendary",
      "unlock_level": 1,
      "unlock_cost": 0,
      "is_premium": false,
      "stats": {
        "strength": 20,
        "agility": 10,
        "intelligence": 5
      },
      "customization_options": {
        "colors": ["red", "blue", "gold"],
        "armor": ["light", "heavy"]
      }
    }
  ]
}
```

**Frontend (3D Model):**
```javascript
import ModelViewer from 'react-native-3d-model-view';

<ModelViewer
  source={{ uri: hero.model_url }}
  style={{ width: 300, height: 300 }}
/>
```

---

### **4.2. Мої герої**

**Endpoint:** `GET /user/heroes`

**Success Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "hero": {
        "id": 1,
        "name": "Warrior",
        "model_url": "https://...",
        "rarity": "legendary"
      },
      "level": 5,
      "experience": 450,
      "experience_to_next_level": 1118,
      "level_progress": 40.25,
      "total_habits_completed": 50,
      "current_streak": 10,
      "best_streak": 15,
      "is_active": true,
      "is_unlocked": true,
      "stats": {
        "strength": 30,      // зростає з рівнем (+10% per level)
        "agility": 15,
        "intelligence": 7
      }
    }
  ]
}
```

**Frontend (Показати прогрес):**
```javascript
<View>
  <Text>Level {userHero.level}</Text>
  <ProgressBar value={userHero.level_progress} max={100} />
  <Text>{userHero.experience} / {userHero.experience_to_next_level} XP</Text>
  
  <ModelViewer source={{ uri: userHero.hero.model_url }} />
  
  <Text>🔥 Streak: {userHero.current_streak} days</Text>
</View>
```

---

### **4.3. Активний герой**

**Endpoint:** `GET /user/heroes/active`

**Success Response (200):**
```json
{
  "data": {
    "id": 1,
    "hero": { ... },
    "level": 5,
    "experience": 450,
    "is_active": true,
    ...
  }
}
```

**Error (404):**
```json
{
  "message": "No active hero"
}
```

---

### **4.4. Розблокувати героя**

**Endpoint:** `POST /user/heroes/{hero_id}/unlock`

**Request Body:** Порожній `{}`

**Success Response (201):**
```json
{
  "data": {
    "id": 2,
    "hero": { ... },
    "level": 1,
    "experience": 0,
    "is_unlocked": true,
    ...
  }
}
```

---

### **4.5. Активувати героя**

**Endpoint:** `POST /user/heroes/{user_hero_id}/activate`

**Request Body:** Порожній `{}`

**Success Response (200):**
```json
{
  "data": {
    "id": 2,
    "is_active": true,
    ...
  }
}
```

**Примітка:** Автоматично деактивує інших героїв.

---

## 🎮 **5. ГЕЙМИФІКАЦІЯ - Як це працює**

### **Flow виконання звички:**

1. **Користувач відмічає звичку:**
```javascript
POST /habits/1/log
{ "count": 1 }
```

2. **Backend автоматично:**
- ✅ Створює лог
- ✅ Збільшує `total_completions`
- ✅ Оновлює `last_completed_at`
- ✅ (TODO) Нараховує XP героєві
- ✅ (TODO) Перевіряє level up

3. **Frontend отримує:**
```javascript
GET /habits/1/stats
// Оновлена статистика

GET /user/heroes/active
// Оновлений рівень героя (якщо був level up)
```

4. **Показати анімацію:**
```javascript
if (newLevel > oldLevel) {
  showLevelUpAnimation();
  playSound('levelup.mp3');
}
```

---

## 📱 **6. ТИПОВІ СЦЕНАРІЇ**

### **Сценарій 1: Перший запуск застосунку**

```javascript
// 1. Реєстрація
const { token, user } = await register(name, email, password);

// 2. Зберегти token
await AsyncStorage.setItem('token', token);

// 3. Отримати список героїв
const { data: heroes } = await getHeroes(token);

// 4. Розблокувати першого героя (автоматично)
await unlockHero(token, heroes[0].id);

// 5. Показати onboarding
showOnboarding();
```

---

### **Сценарій 2: Щоденне використання**

```javascript
// 1. Отримати токен
const token = await AsyncStorage.getItem('token');

// 2. Отримати звички
const habits = await getHabits(token, true); // тільки активні

// 3. Показати список
habits.forEach(habit => {
  renderHabitCard(habit);
});

// 4. Користувач клікає "Виконано"
await logHabit(token, habitId);

// 5. Оновити UI
await refreshHabits();
await refreshActiveHero();
```

---

### **Сценарій 3: Перегляд статистики**

```javascript
// 1. Отримати stats
const stats = await getHabitStats(token, habitId);

// 2. Показати у UI
<Card>
  <Text>Total: {stats.total_completions}</Text>
  <Text>Streak: 🔥 {stats.current_streak} days</Text>
  <Text>Best: 🏆 {stats.best_streak} days</Text>
  <ProgressBar value={stats.completion_rate_30_days} />
</Card>
```

---

## 🚨 **7. ОБРОБКА ПОМИЛОК**

### **401 - Unauthorized:**
```json
{ "message": "Unauthenticated" }
```
**Дія:** Редірект на login screen, очистити token

### **403 - Forbidden:**
```json
{ "message": "This action is unauthorized." }
```
**Дія:** Показати повідомлення "Немає доступу"

### **404 - Not Found:**
```json
{ "message": "Habit not found" }
```
**Дія:** Показати "Звичка не знайдена"

### **422 - Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "email": ["The email has already been taken."]
  }
}
```
**Дія:** Показати помилки біля полів форми

### **500 - Server Error:**
**Дія:** Показати "Щось пішло не так, спробуйте пізніше"

---

## 📖 **8. ДЕ ДИВИТИСЯ ДОКУМЕНТАЦІЮ**

### **Swagger UI:**
**URL:** http://localhost:8081/api/documentation

**Що там:**
- ✅ Всі endpoints
- ✅ Детальні схеми request/response
- ✅ Можливість тестувати
- ✅ Приклади для кожного поля
- ✅ Validation rules
- ✅ Error codes

### **Як читати Swagger:**

1. **Відкрити** http://localhost:8081/api/documentation
2. **Натиснути на endpoint** (наприклад, "POST /auth/register")
3. **Розгорнути секцію** "Request body"
4. **Побачити схему:**
   - required поля позначені *
   - example values для кожного поля
   - типи даних (string, integer, boolean)
   - enum значення для вибору
5. **Розгорнути секцію** "Responses"
6. **Побачити приклади** відповідей (200, 201, 422, 401)

---

## 🎯 **9. QUICK REFERENCE**

### **Authentication:**
```
POST /auth/register          → { name, email, password, password_confirmation }
POST /auth/login             → { email, password }
POST /auth/social-login      → { provider, provider_id, email, name }
POST /auth/logout            → {}
GET  /auth/me                → (no body)
```

### **Habits:**
```
GET    /habits               → (no body)
POST   /habits               → { title, frequency, icon?, color? }
GET    /habits/{id}          → (no body)
PUT    /habits/{id}          → { title?, description?, is_active? }
DELETE /habits/{id}          → (no body)
```

### **Logs:**
```
POST /habits/{id}/log        → { count?, note?, completed_at? }
GET  /habits/{id}/logs       → (no body)
GET  /habits/{id}/stats      → (no body)
```

### **Heroes:**
```
GET  /heroes                 → (no body)
GET  /heroes/{id}            → (no body)
GET  /user/heroes            → (no body)
GET  /user/heroes/active     → (no body)
POST /user/heroes/{id}/unlock    → {}
POST /user/heroes/{id}/activate  → {}
```

---

## ✅ **Всі дані задокументовані в Swagger!**

**Відкрийте:** http://localhost:8081/api/documentation

Там ви знайдете:
- ✅ Точні схеми даних
- ✅ Required/optional поля
- ✅ Типи даних
- ✅ Приклади values
- ✅ Validation rules
- ✅ Error responses

**Тепер frontend розробник має ВСЮ інформацію! 📚**


