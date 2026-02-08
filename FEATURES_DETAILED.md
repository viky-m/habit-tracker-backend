# 🎯 Detailed Feature Description & How They Work

**Habit Tracker Backend API - Full Functionality Description**

---

## 📋 Table of Contents

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

#### **Registration (`POST /api/auth/register`)**

**How it works:**
1. User sends: `name`, `email`, `password`, `password_confirmation`, `locale` (optional)
2. Backend validates data:
   - Email must be unique
   - Password minimum 8 characters
   - Passwords must match
3. Creates user with hashed password
4. **Automatically creates the first hero** (Warrior) via `GamificationService::createFirstHero()`
5. Generates Sanctum token
6. Returns: user data, token, first_hero info

**Request Example:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "locale": "en"
}
```

**Response Example:**
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

**How it works:**
1. User sends: `email`, `password`
2. Backend checks credentials via Laravel Auth
3. Generates a new Sanctum token
4. Returns user data and token

#### **Logout (`POST /api/auth/logout`)**

**How it works:**
1. Deletes current Sanctum token
2. User can no longer use this token

---

### **1.2. Social Authentication**

#### **Apple/Google Sign-In (`POST /api/auth/social-login`)**

**How it works:**
1. Frontend receives token from Apple/Google
2. Frontend sends: `provider` (apple/google), `provider_id`, `name`, `email`, `avatar`
3. Backend checks:
   - If user already exists by `provider_id` → login
   - If user exists by `email` → link provider to existing account
   - If user doesn't exist → create new user
4. **For new users**, automatically creates the first hero
5. Generates Sanctum token
6. Returns user data, token, is_new_user flag

**Request Example:**
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

### **2.1. Creating a Habit (`POST /api/habits`)**

**How it works:**
1. User sends habit data
2. Backend validates and creates a record in the DB
3. Returns the created habit

**Habit Fields:**
- `title` (required) - Habit name
- `description` (optional) - Description
- `icon` (optional) - Emoji or icon
- `color` (optional) - Hex color (default: #6366f1)
- `frequency` (required) - `daily`, `weekly`, `monthly`
- `frequency_days` (optional) - Array of days [1,2,3,4,5] for custom frequency
- `target_count` (optional) - Target count per period (default: 1)
- `is_active` (optional) - Active/inactive (default: true)

**Initial Values:**
- `streak` = 0
- `best_streak` = 0
- `total_completions` = 0
- `last_completed_at` = null

**Request Example:**
```json
{
  "title": "Morning exercise",
  "description": "Do exercises every morning",
  "icon": "💪",
  "color": "#6366f1",
  "frequency": "daily",
  "target_count": 1
}
```

---

### **2.2. Viewing Habits (`GET /api/habits`)**

**How it works:**
1. Retrieves all user habits
2. Optional filtering: `?is_active=true`
3. Sorting: by creation date (desc)
4. Returns list with all statistics

**Response includes:**
- Basic data (title, icon, color, frequency)
- Statistics (streak, best_streak, total_completions)
- State (is_active, is_completed_today, last_completed_at)

---

### **2.3. Updating a Habit (`PUT /api/habits/{id}`)**

**How it works:**
1. Checks authorization (only owner can update)
2. Validates data
3. Updates only provided fields
4. Returns updated habit

---

### **2.4. Deleting a Habit (`DELETE /api/habits/{id}`)**

**How it works:**
1. Checks authorization
2. Performs **soft delete** (marks as deleted instead of removing from DB)
3. Returns 204 No Content

---

## 3. Habit Logging

### **3.1. Logging Completion (`POST /api/habits/{id}/log`)**

**This is the most important endpoint - where the magic happens! 🎉**

**How it works (step-by-step):**

#### **Step 1: Creating log**
Creates or updates a completion log. Supports custom dates for retro-logging.

#### **Step 2: Updating streak**
Calculates streak status: `started`, `increased`, `broken`, or `maintained`. Detects milestones every 7 days.

#### **Step 3: Awarding XP**
Calculates XP awarded based on base points (10) plus a streak bonus (+20% every 3 days). XP is added to the active hero.

#### **Step 4: Checking achievements**
Checks if any new achievements were unlocked by this action.

**Response Example:**
```json
{
  "data": {
    "id": 1,
    "completed_at": "2025-01-15",
    "note": "Felt great!",
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

---

## 4. Gamification System

### **4.1. XP (Experience Points) System**
Base XP is 10. Bonus tiers start at a 3-day streak and increase every 3 days thereafter.

### **4.2. Level System**
XP required for next level follows an exponential curve. Automatic level up happens when enough XP is accumulated.

### **4.3. Streak System**
Automatically tracks consecutive days. Resets if a day is missed. Milestones reached every 7 days.

---

## 5. Heroes System
Users have hero instances with levels, experience, and stats that increase with level. XP is only awarded to the active hero.

---

## 6. Reminders System
Allows users to set habits reminders for specific times and days with timezone support and notification type choice (push/email).

---

## 7. Achievements System
A set of achievements with specific requirements (total habits, completions, streaks, levels). Unlocking grants XP rewards.

---

## 8. User Statistics
General statistics for habits, streaks, and heroes, providing a comprehensive overview of user progress.

---

## 9. Onboarding
Automatic assignment of the "Warrior" hero upon registration to provide an immediate gamification experience.
