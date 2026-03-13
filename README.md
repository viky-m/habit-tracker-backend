# Habit Tracker API 🎮

A professional, gamified backend solution for habit tracking, featuring 3D hero progression, streak mechanics, and smart reminders. Built with high-quality standards (SOLID, Service Layer) and full test coverage.

## 🛠 Tech Stack

- **Core:** PHP 8.4 + Laravel 12.x
- **Database:** MariaDB 10.7
- **Caching:** Redis
- **Authentication:** Laravel Sanctum (Bearer Tokens) + Social Auth (Apple & Google)
- **Infrastructure:** Docker & Nginx
- **Documentation:** Scribe (OpenAPI/Swagger)
- **Testing:** Pest PHP

## ✨ Key Features

- **Habit Management:** Complete CRUD with custom frequencies (daily, weekly, custom days).
- **Gamification Engine:** Automatic XP calculation, leveling system, and streak bonuses.
- **Heroes System:** Unlock and level up different heroes with unique stats based on your consistency.
- **Smart Reminders:** Multi-channel notifications (Push, Email) with timezone support.
- **Achievements:** 30+ automatic achievements to keep users engaged.
- **Analytics:** Comprehensive statistics for habit performance and user growth.

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose installed.

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/habit-tracker-backend.git
   cd habit-tracker-backend
   ```

2. **Setup environment:**
   ```bash
   cp .env.example .env
   ```

3. **Start the containers:**
   ```bash
   docker-compose up -d
   ```

4. **Initialize the database:**
   ```bash
   docker-compose exec php php artisan migrate --seed
   ```

The API will be available at `http://localhost:8081`. 
Documentation can be accessed at `http://localhost:8081/docs`.

## 🧪 Testing

Run the full test suite with Pest:
```bash
php artisan test
```

## 📚 Documentation

Detailed documentation is available in the repository:
- [FEATURES.md](./FEATURES.md): Detailed feature list and roadmap.
- [FRONTEND_GUIDE.md](./FRONTEND_GUIDE.md): Technical guide for frontend integration.
- [DOCKER_OPS.md](./DOCKER_OPS.md): Docker management and deployment instructions.
