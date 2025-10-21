# Habit Tracker API

Backend API for mobile Habit Tracker application with gamification and 3D heroes.

## Technologies

- **PHP 8.4** - programming language
- **Laravel 12** - PHP framework
- **MySQL (MariaDB 10.7)** - database
- **Redis** - caching and queues
- **Docker** - containerization
- **Nginx** - web server
- **Swagger/OpenAPI** - API documentation

## Features

- ✅ RESTful API
- ✅ Multi-language support (en, uk, de, fr, es)
- ✅ JWT Authentication (Sanctum)
- ✅ Swagger documentation
- ✅ Redis for caching
- ✅ Email via Mailhog (dev)
- ✅ Xdebug for debugging

## Quick Start

### Requirements

- Docker
- Docker Compose
- Make (optional)

### Installation

1. **Clone repository:**
```bash
git clone <repository-url>
cd habit-tracker-backend
```

2. **Run automatic installation:**
```bash
make install
```

Or manually:

```bash
# Copy .env file
cp .env.example .env

# Build containers
docker-compose build

# Start containers
docker-compose up -d

# Install dependencies
docker exec -it habittracker_php composer install

# Generate key
docker exec -it habittracker_php php artisan key:generate

# Run migrations
docker exec -it habittracker_php php artisan migrate
```

3. **Check operation:**
- API: http://localhost:8081
- Mailhog: http://localhost:8026
- API Docs: http://localhost:8081/api/documentation

## Makefile Commands

```bash
make help          # Show all commands
make up            # Start containers
make down          # Stop containers
make restart       # Restart containers
make shell         # Enter PHP container
make logs          # Show logs
make composer      # Run composer install
make migrate       # Run migrations
make seed          # Run seeders
make fresh         # Fresh migration with seeds
make test          # Run tests
make pint          # Code formatting
make stan          # Static analysis
```

## API Endpoints

### Authentication
- `POST /api/register` - Registration
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/user` - Current user

### Habits
- `GET /api/habits` - List habits
- `POST /api/habits` - Create habit
- `GET /api/habits/{id}` - Habit details
- `PUT /api/habits/{id}` - Update habit
- `DELETE /api/habits/{id}` - Delete habit
- `POST /api/habits/{id}/complete` - Mark completion

### Statistics
- `GET /api/stats/daily` - Daily statistics
- `GET /api/stats/weekly` - Weekly statistics
- `GET /api/stats/monthly` - Monthly statistics

### Hero
- `GET /api/hero` - Hero information
- `GET /api/hero/stats` - Hero characteristics

## Multi-language Support

API supports multi-language through `Accept-Language` header:

```bash
curl -H "Accept-Language: uk" http://localhost:8081/api/habits
```

Supported languages:
- `en` - English (default)
- `uk` - Ukrainian
- `de` - German
- `fr` - French
- `es` - Spanish

## Development

### Project Structure

```
.
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/  # API controllers
│   │   ├── Requests/         # Form Requests
│   │   ├── Resources/        # API Resources
│   │   └── Middleware/       # Middleware (SetLocale)
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic
│   └── Repositories/         # Repositories
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Seeders
│   └── factories/            # Test factories
├── lang/                     # Localization
│   ├── en/
│   ├── uk/
│   └── ...
├── routes/
│   └── api.php              # API routes
├── tests/                   # Tests
└── docker-compose.yaml      # Docker configuration
```

### Container Access

**PHP container:**
```bash
docker exec -it habittracker_php bash
```

**MySQL:**
```bash
docker exec -it habittracker_mysql mysql -u habittracker -phabittracker habittracker
```

**Redis:**
```bash
docker exec -it habittracker_redis redis-cli
```

### Testing

```bash
# Run all tests
make test

# Or via PHPUnit
docker exec -it habittracker_php php artisan test

# Run specific test
docker exec -it habittracker_php php artisan test --filter=HabitTest
```

### Code Quality

```bash
# Code formatting (Laravel Pint)
make pint

# Static analysis (Larastan)
make stan
```

## Troubleshooting

### Ports in use
If ports 8081, 3307 are busy, change them in `.env`:
```
HTTP_PORT=8082
MYSQL_PORT=3308
```

### Permissions
If you have permission issues:
```bash
docker exec -it habittracker_php chmod -R 775 storage bootstrap/cache
docker exec -it habittracker_php chown -R xdocker:xdocker storage bootstrap/cache
```

### Clean everything
```bash
docker-compose down -v
rm -rf data/db/mysql/*
make install
```

## License

MIT