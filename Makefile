.PHONY: help up down build restart shell composer artisan test migrate seed fresh install

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Start containers
	docker-compose up -d

down: ## Stop containers
	docker-compose down

build: ## Build containers
	docker-compose build

rebuild: ## Rebuild containers
	docker-compose down
	docker-compose build --no-cache
	docker-compose up -d

restart: ## Restart containers
	docker-compose restart

logs: ## Show logs
	docker-compose logs -f

open-php: ## Enter PHP container
	docker exec -it habittracker_php bash

open-mysql: ## Enter MySQL container
	docker exec -it habittracker_mysql mysql -u habittracker -phabittracker habittracker

composer: ## Run composer install
	docker exec -it habittracker_php composer install

artisan: ## Run artisan command (use: make artisan cmd="migrate")
	docker exec -it habittracker_php php artisan $(cmd)

test: ## Run tests
	docker exec -it habittracker_php php artisan test

migrate: ## Run migrations
	docker exec -it habittracker_php php artisan migrate

seed: ## Run seeders
	docker exec -it habittracker_php php artisan db:seed

fresh: ## Fresh migration with seed
	docker exec -it habittracker_php php artisan migrate:fresh --seed

install: ## Initial project setup
	cp .env.example .env
	docker-compose build
	docker-compose up -d
	docker exec -it habittracker_php composer install
	docker exec -it habittracker_php php artisan key:generate
	docker exec -it habittracker_php php artisan migrate
	@echo "\n✅ Installation complete!"
	@echo "API available at: http://localhost:8081"
	@echo "Mailhog UI at: http://localhost:8026"

pint: ## Run Laravel Pint (code formatter)
	docker exec -it habittracker_php ./vendor/bin/pint

stan: ## Run Larastan (static analysis)
	docker exec -it habittracker_php ./vendor/bin/phpstan analyse

cache-clear: ## Clear all cache
	docker exec -it habittracker_php php artisan cache:clear
	docker exec -it habittracker_php php artisan config:clear
	docker exec -it habittracker_php php artisan route:clear
	docker exec -it habittracker_php php artisan view:clear

optimize: ## Optimize Laravel
	docker exec -it habittracker_php php artisan config:cache
	docker exec -it habittracker_php php artisan route:cache
	docker exec -it habittracker_php php artisan view:cache
