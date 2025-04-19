# RESM - SKSU Project

## About RESM Project


## System Requirements

- PHP
- Composer
- Node.js and npm

## Initial Setup
- composer install (php dependencies)
- npm install (npm package dependencies)
- cp .env.example .env (setup env)
- php artisan key:generate
- php artisan migrate:fresh --seed (setup database first)
- npm run dev or prod
- php artisan serve
