# ⚙️ Setup Instructions

Покрокова інструкція для запуску проекту

---

## 🚀 Швидкий старт (3 хвилини)

```bash
# 1. Запустити Docker
docker-compose up -d

# 2. Встановити залежності
docker exec habittracker_php composer install

# 3. Налаштувати базу даних
docker exec habittracker_php php artisan migrate
docker exec habittracker_php php artisan db:seed --class=HeroSeeder

# 4. Згенерувати документацію
docker exec habittracker_php php artisan scribe:generate

# 5. Відкрити документацію
# http://localhost:8081/docs
```

---

## 📋 Детальна інструкція

### 1. Вимоги:
- Docker 20.10+
- Docker Compose 2.0+
- 2GB вільного місця

### 2. Клонувати репозиторій:
```bash
git clone <repository-url>
cd habit-tracker-backend
```

### 3. Налаштувати environment:
```bash
# .env вже є, але можна створити з прикладу
cp .env.example .env

# Перевірити порти в .env:
# HTTP_PORT=8081
# MYSQL_PORT=3307
# REDIS_PORT=6380
```

### 4. Запустити Docker:
```bash
docker-compose up -d

# Перевірити що всі контейнери запущені
docker-compose ps
```

Має бути 5 контейнерів:
- `habittracker_nginx` (port 8081)
- `habittracker_php` (port 9000)
- `habittracker_mysql` (port 3307)
- `habittracker_redis` (port 6380)
- `habittracker_mailhog` (ports 1026, 8026)

### 5. Встановити Laravel залежності:
```bash
docker exec habittracker_php composer install
```

### 6. Згенерувати application key:
```bash
docker exec habittracker_php php artisan key:generate
```

### 7. Запустити міграції:
```bash
docker exec habittracker_php php artisan migrate
```

### 8. Створити starter heroes:
```bash
docker exec habittracker_php php artisan db:seed --class=HeroSeeder
```

Створить 4 героїв:
- Warrior (Level 0, Free)
- Sage (Level 5)
- Guardian (Level 10)
- Phoenix (Level 20, Premium)

### 9. Згенерувати API документацію:
```bash
docker exec habittracker_php php artisan scribe:generate
```

### 10. Перевірити що все працює:
```bash
# Health check
curl http://localhost:8081/api/health

# Відповідь має бути:
# {"status":"ok","timestamp":"...","version":"1.0.0"}
```

### 11. Відкрити документацію:
- **Scribe:** http://localhost:8081/docs
- **API Playground:** http://localhost:8081/playground

---

## 🧪 Запустити тести:

```bash
docker exec habittracker_php php artisan test

# Очікуваний результат:
# Tests: 72 passed (211 assertions)
```

---

## 🔧 Корисні команди

### Docker:
```bash
# Зупинити
docker-compose down

# Перезапустити
docker-compose restart

# Логи
docker-compose logs -f php

# Зайти в PHP контейнер
docker exec -it habittracker_php bash

# Зайти в MySQL
docker exec -it habittracker_mysql mysql -u habittracker -phabittracker habittracker
```

### Laravel:
```bash
# Очистити кеш
docker exec habittracker_php php artisan cache:clear
docker exec habittracker_php php artisan config:clear

# Переглянути routes
docker exec habittracker_php php artisan route:list

# Tinker (interactive shell)
docker exec -it habittracker_php php artisan tinker
```

---

## ⚠️ Troubleshooting

### Проблема: "Connection refused" при запуску

**Рішення:**
```bash
# Перевірити що всі контейнери запущені
docker-compose ps

# Перезапустити
docker-compose down
docker-compose up -d
```

### Проблема: "Port already in use"

**Рішення:** Змінити порти в `.env`:
```env
HTTP_PORT=8082        # замість 8081
MYSQL_PORT=3308       # замість 3307
REDIS_PORT=6381       # замість 6380
```

### Проблема: "Permission denied"

**Рішення:**
```bash
# Дати права на storage та bootstrap/cache
sudo chmod -R 777 storage bootstrap/cache
```

---

## ✅ Готово!

Якщо всі кроки виконані, проект має бути доступний на:
- **API:** http://localhost:8081/api
- **Docs:** http://localhost:8081/docs
- **Health:** http://localhost:8081/api/health

**Час setup:** ~5 хвилин  
**Backend готовий до розробки!** 🚀
