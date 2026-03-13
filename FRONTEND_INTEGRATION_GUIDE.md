# 📱 Frontend Integration Guide - Detailed Instructions

## 🎯 **For Frontend Developer**

---

## 🔗 **Base URL:**
```
http://localhost:8081/api
```

**Production:** Will be changed to your domain

---

## 🔑 **Authorization:**

### **Format:**
```javascript
headers: {
  'Authorization': 'Bearer YOUR_TOKEN_HERE',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

**⚠️ Important:** 
- All endpoints (except `/health`, `/auth/register`, `/auth/login`, `/auth/social-login`) require a Bearer token
- Token is obtained on login/register
- Store token in AsyncStorage (React Native) or SecureStore (Expo)

---

## 📚 **1. AUTHENTICATION**

### **1.1. Registration (Email/Password)**

**Endpoint:** `POST /auth/register`

**Request Body:**
```json
{
  "name": "John Doe",                    // required, string, max:255
  "email": "john@example.com",           // required, email, unique
  "password": "password123",             // required, min:8
  "password_confirmation": "password123", // required, must match password
  "locale": "en"                         // optional, enum: en|uk, default: en
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

**Frontend code (React Native):**
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
      // Save token
      await AsyncStorage.setItem('token', data.token);
      await AsyncStorage.setItem('user', JSON.stringify(data.user));
      return data;
    } else {
      // Show validation errors
      throw new Error(JSON.stringify(data.errors));
    }
  } catch (error) {
    console.error('Registration error:', error);
    throw error;
  }
};
```

---

### **1.2. Login (Email/Password)**

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
  "token": "2|def456uvw789...",
  "token_type": "Bearer"
}
```

**Error Response (401):**
```json
{
  "message": "Invalid credentials"
}
```

---

### **1.3. Social Login (Apple/Google)**

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
  "locale": "en"
}
```

**Success Response (200 or 201):**
```json
{
  "message": "Login successful",          // or "User created successfully" for new users
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
  "is_new_user": false                    // true if user was just created
}
```

---

### **1.4. Logout**

**Endpoint:** `POST /auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Request Body:** Empty `{}`

**Success Response (200):**
```json
{
  "message": "Logout successful"
}
```

---

### **1.5. Current User**

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

## ✅ **2. HABITS**

### **2.1. Habit List**

**Endpoint:** `GET /habits`

**Query Parameters:**
- `is_active` (optional, boolean) - filter by activity

**Examples:**
```
GET /habits                    // all habits
GET /habits?is_active=true     // active only
GET /habits?is_active=false    // inactive only
```

**Success Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Morning exercise",
      "description": "Do exercises every morning",
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

---

### **2.2. Create Habit**

**Endpoint:** `POST /habits`

**Request Body:**
```json
{
  "title": "Morning exercise",        // required, string, max:255
  "description": "Do pushups...",     // optional, string
  "icon": "💪",                       // optional, string (emoji), max:10
  "color": "#6366f1",                 // optional, hex color, default: #6366f1
  "frequency": "daily",               // required, enum: daily|weekly|monthly
  "frequency_days": [1, 3, 5],        // optional, array of integers 0-6 (only for custom frequency)
                                      // 0=Sunday, 1=Monday, 2=Tuesday ... 6=Saturday
  "target_count": 1                   // optional, integer, min:1, default: 1
}
```

**Success Response (200):**
```json
{
  "data": {
    "id": 2,
    "title": "Drink 2L water",
    ...
  }
}
```

---

### **2.3. Habit Details**

**Endpoint:** `GET /habits/{id}`

**Success Response (200):**
```json
{
  "data": {
    "id": 1,
    "title": "Morning exercise",
    ...
  }
}
```

---

### **2.4. Update Habit**

**Endpoint:** `PUT /habits/{id}` or `PATCH /habits/{id}`

**Request Body (all fields optional):**
```json
{
  "title": "New title",
  "description": "New description",
  "is_active": false,              // deactivate habit
  "color": "#ef4444"
}
```

---

### **2.5. Delete Habit**

**Endpoint:** `DELETE /habits/{id}`

**Success Response (204):** Empty response

---

## 📊 **3. HABIT LOGS**

### **3.1. Mark Completion**

**Endpoint:** `POST /habits/{id}/log`

**Request Body:**
```json
{
  "completed_at": "2025-10-29",          // optional, date (YYYY-MM-DD), default: today
  "note": "Felt great!",                 // optional, string, max:500
  "count": 1                             // optional, integer, min:1, default: 1
}
```

**Success Response (201):**
```json
{
  "data": {
    "id": 1,
    "habit_id": 1,
    "completed_at": "2025-10-29",
    "note": "Felt great!",
    "count": 1,
    "created_at": "2025-10-29T11:00:00.000000Z"
  }
}
```

---

### **3.2. Execution History**

**Endpoint:** `GET /habits/{id}/logs`

**Success Response (200):**
```json
{
  "data": [
    {
      "id": 5,
      "habit_id": 1,
      "completed_at": "2025-10-29",
      "note": "Felt great!",
      ...
    }
  ]
}
```

---

### **3.3. Habit Statistics**

**Endpoint:** `GET /habits/{id}/stats`

**Success Response (200):**
```json
{
  "total_completions": 50,
  "current_streak": 10,
  "best_streak": 15,
  "completion_rate_30_days": 83.33,
  "last_completed_at": "2025-10-29T11:00:00.000000Z",
  "is_completed_today": true
}
```

---

## 🦸 **4. HEROES**

### **4.1. All Heroes List**

**Endpoint:** `GET /heroes`

**Success Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Warrior",
      ...
    }
  ]
}
```

---

### **4.2. My Heroes**

**Endpoint:** `GET /user/heroes`

**Success Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "hero": { ... },
      "level": 5,
      "experience": 450,
      "experience_to_next_level": 1118,
      "level_progress": 40.25,
      ...
    }
  ]
}
```

---

### **4.3. Active Hero**

**Endpoint:** `GET /user/heroes/active`

**Success Response (200):**
```json
{
  "data": {
    "id": 1,
    "hero": { ... },
    "level": 5,
    "is_active": true,
    ...
  }
}
```

---

### **4.4. Unlock Hero**

**Endpoint:** `POST /user/heroes/{hero_id}/unlock`

---

### **4.5. Activate Hero**

**Endpoint:** `POST /user/heroes/{user_hero_id}/activate`

---

## 🎮 **5. GAMIFICATION - How it works**

### **Habit completion flow:**

1. **User marks habit complete:**
```javascript
POST /habits/1/log
{ "count": 1 }
```

2. **Backend automatically:**
- ✅ Creates log
- ✅ Increments `total_completions`
- ✅ Updates `last_completed_at`
- ✅ Updates `streak`
- ✅ Awards XP to active hero
- ✅ Checks for Level Up
- ✅ Checks for Achievements

3. **Check response for updates:**
Responses from `POST /habits/{id}/log` contain gamification info (XP awarded, level status, achievements unlocked).

---

## 🔔 **6. REMINDERS**

### **6.1. Create Reminder**
**Endpoint:** `POST /api/reminders`

### **6.2. Reminder Settings**
Configurable time, days, timezone, and notification type (push/email).

---

**Full API details available in Scribe documentation: http://localhost:8081/docs** 🚀
