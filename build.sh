#!/usr/bin/env bash
# Salir inmediatamente si un comando falla
set -e

echo "--- Iniciando proceso de construcción ---"

# Instalar dependencias de PHP
composer install --no-dev --optimize-autoloader

# Instalar y compilar assets (Vite)
npm install
npm run build

# Enlazar storage
php artisan storage:link

# Cachear configuración y rutas para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "--- Construcción finalizada con éxito ---"
