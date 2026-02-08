# 📱 For Frontend Developer - Quick Start

## 🎯 **Main things you need to know:**

---

## 1️⃣ **API Documentation:**

### **📚 Scribe UI (MAIN!):**
**URL:** http://localhost:8081/docs

**What you'll find there:**
- ✅ **All endpoints** with detailed descriptions
- ✅ **Request schemas** - what fields to send
- ✅ **Response schemas** - what to expect in response
- ✅ **Required/Optional** - which fields are mandatory
- ✅ **Data types** - string, integer, boolean, array
- ✅ **Examples** - example values for each field
- ✅ **Validation rules** - constraints (min, max, enum)
- ✅ **Error responses** - all possible errors
- ✅ **Try it out!** - test endpoints directly from the browser
- ✅ **Code examples** - ready-to-use snippets in bash, JavaScript, and PHP

---

## 2️⃣ **Base URL:**

```javascript
const API_BASE_URL = 'http://localhost:8081/api';
```

---

## 3️⃣ **Authentication:**

### **All protected endpoints require:**
```javascript
headers: {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

### **Public endpoints (no token required):**
- `GET /health`
- `POST /auth/register`
- `POST /auth/login`
- `POST /auth/social-login`

---

## 4️⃣ **Response Format:**

### **Single Object:**
```json
{
  "data": {
    "id": 1,
    "title": "...",
    ...
  }
}
```

### **Collection (Array):**
```json
{
  "data": [
    { "id": 1, ... },
    { "id": 2, ... }
  ]
}
```

### **With Message:**
```json
{
  "message": "Success message",
  "user": { ... },
  "token": "abc123..."
}
```

---

## 5️⃣ **Quick Examples:**

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
  // data.token - save to storage
  // data.user - save user info
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

## 6️⃣ **Error Handling:**

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
        // Unauthenticated - redirect to login
        await AsyncStorage.removeItem('token');
        navigation.navigate('Login');
        throw new Error('Please login again');
        
      case 422:
        // Validation errors
        const errors = data.errors;
        // Show errors near fields
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

## 7️⃣ **TypeScript Types (optional):**

```typescript
interface User {
  id: number;
  name: string;
  email: string;
  avatar: string | null;
  provider: 'email' | 'apple' | 'google';
  locale: 'en' | 'uk';
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

## 8️⃣ **Where to see what to send:**

### **Method 1: Scribe Docs (EASIEST!)**

1. Open http://localhost:8081/docs
2. Find the needed endpoint (e.g., "POST /habits")
3. Click on it to expand
4. **"Request body"** section - shows WHAT to send:
   ```
   ✅ title*        string    "Morning Run"
   ✅ frequency*    string    Enum: daily, weekly, monthly
   ⚪ description   string    "Running for 30 minutes"
   ⚪ icon          string    "🏃"
   ```
   * = required field
5. **"Responses"** section - shows WHAT to expect:
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

### **Method 2: FRONTEND_INTEGRATION_GUIDE.md file**

Contains ready-to-use code examples for each endpoint.

---

## 9️⃣ **Typical App Flow:**

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

## 🔟 **Tips:**

### **✅ DO:**
- Always send `Accept: application/json`
- Store token in secure storage
- Handle all error codes
- Use Scribe docs to check schemas
- Test in Scribe's "Try It Out" first

### **❌ DON'T:**
- Don't store token in plain text
- Don't ignore 401 errors
- Don't forget Content-Type headers
- Don't hardcode URLs (use constants)

---

## 📞 **Support:**

- **Docs:** http://localhost:8081/docs
- **Playground:** http://localhost:8081/playground  
- **Guide:** `/FRONTEND_INTEGRATION_GUIDE.md`

---

## ✨ **Ready for Integration!**

**All you need:**
1. Open Scribe Docs
2. Check schemas
3. Write code!

Scribe shows **EVERYTHING** - types, required fields, examples, validation! 🚀
