# ⚙️ Setup Instructions

Step-by-step instructions for running the project

---

## 🚀 Quick Start (3 minutes)

```bash
# 1. Start Docker
docker-compose up -d

# 2. Install dependencies
docker exec habittracker_php composer install

# 3. Setup database
docker exec habittracker_php php artisan migrate
docker exec habittracker_php php artisan db:seed --class=HeroSeeder

# 4. Generate documentation
docker exec habittracker_php php artisan scribe:generate

# 5. Open documentation
# http://localhost:8081/docs
```

---

## 📋 Detailed Instructions

### 1. Requirements:
- Docker 20.10+
- Docker Compose 2.0+
- 2GB free space

### 2. Clone repository:
```bash
git clone <repository-url>
cd habit-tracker-backend
```

### 3. Configure environment:
```bash
# .env already exists, but you can create it from example
cp .env.example .env

# Check ports in .env:
# HTTP_PORT=8081
# MYSQL_PORT=3307
# REDIS_PORT=6380
```

### 4. Start Docker:
```bash
docker-compose up -d

# Verify all containers are running
docker-compose ps
```

There should be 5 containers:
- `habittracker_nginx` (port 8081)
- `habittracker_php` (port 9000)
- `habittracker_mysql` (port 3307)
- `habittracker_redis` (port 6380)
- `habittracker_mailhog` (ports 1026, 8026)

### 5. Install Laravel dependencies:
```bash
docker exec habittracker_php composer install
```

### 6. Generate application key:
```bash
docker exec habittracker_php php artisan key:generate
```

### 7. Run migrations:
```bash
docker exec habittracker_php php artisan migrate
```

### 8. Create starter heroes:
```bash
docker exec habittracker_php php artisan db:seed --class=HeroSeeder
```

Creates 4 heroes:
- Warrior (Level 0, Free)
- Sage (Level 5)
- Guardian (Level 10)
- Phoenix (Level 20, Premium)

### 9. Generate API documentation:
```bash
docker exec habittracker_php php artisan scribe:generate
```

### 10. Check that everything works:
```bash
# Health check
curl http://localhost:8081/api/health

# Response should be:
# {"status":"ok","timestamp":"...","version":"1.0.0"}
```

### 11. Open documentation:
- **Scribe:** http://localhost:8081/docs
- **API Playground:** http://localhost:8081/playground

---

## 🧪 Run tests:

```bash
docker exec habittracker_php php artisan test

# Expected result:
# Tests: 72 passed (211 assertions)
```

---

## 🔧 Useful Commands

### Docker:
```bash
# Stop
docker-compose down

# Restart
docker-compose restart

# Logs
docker-compose logs -f php

# Enter PHP container
docker exec -it habittracker_php bash

# Enter MySQL
docker exec -it habittracker_mysql mysql -u habittracker -phabittracker habittracker
```

### Laravel:
```bash
# Clear cache
docker exec habittracker_php php artisan cache:clear
docker exec habittracker_php php artisan config:clear

# View routes
docker exec habittracker_php php artisan route:list

# Tinker (interactive shell)
docker exec -it habittracker_php php artisan tinker
```

---

## ⚠️ Troubleshooting

### Problem: "Connection refused" on start

**Solution:**
```bash
# Verify all containers are running
docker-compose ps

# Restart
docker-compose down
docker-compose up -d
```

### Problem: "Port already in use"

**Solution:** Change ports in `.env`:
```env
HTTP_PORT=8082        # instead of 8081
MYSQL_PORT=3308       # instead of 3307
REDIS_PORT=6381       # instead of 6380
```

### Problem: "Permission denied"

**Solution:**
```bash
# Give permissions to storage and bootstrap/cache
sudo chmod -R 777 storage bootstrap/cache
```

---

## ✅ Done!

If all steps are completed, the project should be available at:
- **API:** http://localhost:8081/api
- **Docs:** http://localhost:8081/docs
- **Health:** http://localhost:8081/api/health

**Setup time:** ~5 minutes  
**Backend ready for development!** 🚀
