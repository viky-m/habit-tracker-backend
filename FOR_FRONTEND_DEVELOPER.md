# 📱 Для Frontend Розробника - Швидкий старт

## 🎯 **Головне що потрібно знати:**

---

## 1️⃣ **Документація API:**

### **📚 Swagger UI (ГОЛОВНЕ!):**
**URL:** http://localhost:8081/api/documentation

**Що там знайдете:**
- ✅ **Всі 22 endpoints** з детальними описами
- ✅ **Request схеми** - які поля відправляти
- ✅ **Response схеми** - що прилетить у відповідь
- ✅ **Required/Optional** - які поля обов'язкові
- ✅ **Типи даних** - string, integer, boolean, array
- ✅ **Приклади** - example values для кожного поля
- ✅ **Validation rules** - обмеження (min, max, enum)
- ✅ **Error responses** - всі можливі помилки
- ✅ **Try it out!** - можна тестувати прямо там

---

## 2️⃣ **Base URL:**

```javascript
const API_BASE_URL = 'http://localhost:8081/api';
```

---

## 3️⃣ **Авторизація:**

### **Всі protected endpoints вимагають:**
```javascript
headers: {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

### **Public endpoints (без токена):**
- `GET /health`
- `POST /auth/register`
- `POST /auth/login`
- `POST /auth/social-login`

---

## 4️⃣ **Формат відповідей:**

### **Одиничний об'єкт:**
```json
{
  "data": {
    "id": 1,
    "title": "...",
    ...
  }
}
```

### **Колекція (масив):**
```json
{
  "data": [
    { "id": 1, ... },
    { "id": 2, ... }
  ]
}
```

### **З повідомленням:**
```json
{
  "message": "Success message",
  "user": { ... },
  "token": "abc123..."
}
```

---

## 5️⃣ **Швидкі приклади:**

### **Login:**
```javascript
const login = async (email, password) => {
  const response = await fetch('http://localhost:8081/api/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ email, password })
  });
  
  const data = await response.json();
  // data.token - зберегти в storage
  // data.user - зберегти user info
  return data;
};
```

### **Get Habits:**
```javascript
const getHabits = async (token) => {
  const response = await fetch('http://localhost:8081/api/habits', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  
  const { data: habits } = await response.json();
  return habits; // Array of habits
};
```

### **Create Habit:**
```javascript
const createHabit = async (token, habitData) => {
  const response = await fetch('http://localhost:8081/api/habits', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      title: habitData.title,
      icon: habitData.icon,
      color: habitData.color,
      frequency: habitData.frequency  // daily|weekly|monthly
    })
  });
  
  const { data: habit } = await response.json();
  return habit;
};
```

### **Log Completion:**
```javascript
const logHabit = async (token, habitId, note = null) => {
  const response = await fetch(`http://localhost:8081/api/habits/${habitId}/log`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
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

## 6️⃣ **Обробка помилок:**

```javascript
const apiCall = async (url, options) => {
  try {
    const response = await fetch(url, options);
    const data = await response.json();
    
    // Success
    if (response.ok) {
      return data;
    }
    
    // Error handling
    switch (response.status) {
      case 401:
        // Unauthenticated - редірект на login
        await AsyncStorage.removeItem('token');
        navigation.navigate('Login');
        throw new Error('Please login again');
        
      case 422:
        // Validation errors
        const errors = data.errors;
        // Показати помилки біля полів
        throw new Error(Object.values(errors).flat().join('\n'));
        
      case 404:
        throw new Error('Not found');
        
      case 403:
        throw new Error('Access denied');
        
      default:
        throw new Error('Something went wrong');
    }
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
};
```

---

## 7️⃣ **TypeScript Types (опціонально):**

```typescript
interface User {
  id: number;
  name: string;
  email: string;
  avatar: string | null;
  provider: 'email' | 'apple' | 'google';
  locale: 'en' | 'uk' | 'ru';
  created_at: string;
}

interface Habit {
  id: number;
  title: string;
  description: string | null;
  icon: string | null;
  color: string;
  frequency: 'daily' | 'weekly' | 'monthly';
  frequency_days: number[] | null;
  target_count: number;
  streak: number;
  best_streak: number;
  total_completions: number;
  is_active: boolean;
  is_completed_today: boolean;
  last_completed_at: string | null;
  created_at: string;
  updated_at: string;
}

interface Hero {
  id: number;
  name: string;
  description: string | null;
  model_url: string;
  thumbnail_url: string | null;
  rarity: 'common' | 'rare' | 'epic' | 'legendary';
  unlock_level: number;
  unlock_cost: number;
  is_premium: boolean;
  stats: object;
  customization_options: object | null;
}

interface UserHero {
  id: number;
  hero: Hero;
  level: number;
  experience: number;
  experience_to_next_level: number;
  level_progress: number;
  total_habits_completed: number;
  current_streak: number;
  best_streak: number;
  is_active: boolean;
  is_unlocked: boolean;
  customization: object | null;
  stats: object;
  achievements: string[] | null;
  last_active_at: string | null;
}
```

---

## 8️⃣ **Де дивитися що відправляти:**

### **Спосіб 1: Swagger UI (НАЙЛЕГШЕ!)**

1. Відкрити http://localhost:8081/api/documentation
2. Знайти потрібний endpoint (наприклад, "POST /habits")
3. Клікнути на нього - розгорнеться
4. Секція **"Request body"** - показує ЩО відправляти:
   ```
   ✅ title*        string    "Ранкова зарядка"
   ✅ frequency*    string    Enum: daily, weekly, monthly
   ⚪ description   string    "Робити зарядку..."
   ⚪ icon          string    "💪"
   ```
   * = required поле
5. Секція **"Responses"** - показує ЩО прилетить:
   ```
   200 - Success
   {
     "data": {
       "id": 1,
       "title": "...",
       ...
     }
   }
   ```

### **Спосіб 2: Файл FRONTEND_INTEGRATION_GUIDE.md**

Там є готові приклади коду для кожного endpoint.

---

## 9️⃣ **Типовий flow застосунку:**

```javascript
// 1. App Launch
const token = await AsyncStorage.getItem('token');
if (!token) {
  navigation.navigate('Login');
  return;
}

// 2. Load data
const user = await getMe(token);
const habits = await getHabits(token);
const activeHero = await getActiveHero(token);

// 3. Render UI
<View>
  <HeroAvatar hero={activeHero} />
  <LevelProgress level={activeHero.level} progress={activeHero.level_progress} />
  <HabitsList habits={habits} onComplete={logHabit} />
</View>

// 4. User marks habit complete
const logHabit = async (habitId) => {
  await fetch(`${API_URL}/habits/${habitId}/log`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}` },
    body: JSON.stringify({ count: 1 })
  });
  
  // Refresh data
  await refreshHabits();
  await refreshActiveHero(); // Check for level up!
};
```

---

## 🔟 **Поради:**

### **✅ DO:**
- Завжди відправляти `Accept: application/json`
- Зберігати token в secure storage
- Обробляти всі коди помилок
- Використовувати Swagger для перевірки схем
- Тестувати в API Playground спочатку

### **❌ DON'T:**
- Не зберігати token в plain text
- Не ігнорувати 401 errors
- Не забувати про Content-Type headers
- Не хардкодити URLs (використовувати constants)

---

## 📞 **Підтримка:**

- **Swagger:** http://localhost:8081/api/documentation
- **API Playground:** http://localhost:8081/playground  
- **Guide:** `/FRONTEND_INTEGRATION_GUIDE.md`

---

## ✨ **Готово до інтеграції!**

**Все що потрібно:**
1. Відкрити Swagger
2. Подивитися схеми
3. Писати код!

Swagger показує **ВСЕ** - types, required fields, examples, validation! 🚀


