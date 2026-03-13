# 💻 Frontend Integration Guide

This guide provides technical specifications for frontend developers (Vue.js, React, Mobile) integrating with the Habit Tracker API.

## 📍 Environment Details

- **Base URL:** `http://localhost:8080/api/v1`
- **Default Headers:**
  ```http
  Accept: application/json
  Content-Type: application/json
  ```

## 🔑 Authentication

Most endpoints require a **Bearer Token** obtained via `/auth/login` or `/auth/register`.

```http
Authorization: Bearer <your_token_here>
```

## 🚀 Main Endpoints

### Habits
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/habits` | List all active habits for the user. |
| `POST` | `/habits` | Create a new habit. |
| `GET` | `/habits/{id}` | Get detailed info for a specific habit. |
| `PATCH` | `/habits/{id}` | Update habit settings or title. |
| `POST` | `/habits/{id}/log` | Log a completion for the habit. |

### Stats & Heroes
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/user/stats` | Get overview of user progress and active hero. |
| `GET` | `/heroes` | List all available hero templates. |
| `POST` | `/user/heroes/{id}/activate` | Change the current active hero. |

## 📦 Data Objects

### Habit Object
```json
{
  "id": 42,
  "title": "Morning Meditation",
  "description": "10 minutes of mindfulness",
  "icon": "🧘",
  "color": "#4F46E5",
  "frequency": "daily",
  "streak": 12,
  "best_streak": 25,
  "is_completed_today": false,
  "last_completed_at": "2024-03-10T08:00:00Z",
  "created_at": "2024-01-15T12:30:00Z"
}
```

## 🛡 Error Handling

The API uses standard HTTP status codes:
- `200/201`: Success
- `401`: Unauthorized (Missing/expired token)
- `403`: Forbidden (Trying to access another user's habit)
- `422`: Unprocessable Content (Validation failed)

**Validation Error Example:**
```json
{
  "message": "The title field is required.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```
