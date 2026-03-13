#!/bin/bash

echo "🚀 Installing Habit Tracker API packages..."
echo ""

# Sanctum for API authentication
echo "📦 Installing Laravel Sanctum..."
composer require laravel/sanctum

# Swagger for API documentation  
echo "📦 Installing Swagger..."
composer require darkaonline/l5-swagger

# PEST for testing
echo "📦 Installing PEST..."
composer require pestphp/pest pestphp/pest-plugin-laravel --dev
./vendor/bin/pest --init

# Larastan for static analysis
echo "📦 Installing Larastan..."
composer require larastan/larastan --dev

echo ""
echo "✅ All packages installed!"
echo ""

# Publish configurations
echo "📝 Publishing configurations..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"

echo ""
echo "✅ Configurations published!"
echo ""

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate

echo ""
echo "✅ Setup complete!"
echo ""
echo "Next steps:"
echo "1. Create migrations for habits, habit_logs, user_progress"
echo "2. Create models and controllers"
echo "3. Setup API routes"
echo "4. Add multi-language support"
echo ""


