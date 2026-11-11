# Restaurant Reservations API

Laravel 12 REST API for restaurant reservations. Practice project covering Eloquent models/relationships, Form Requests, API Resources, unified responses, factories/seeders, Sanctum authentication, and custom roles/permissions.

> Case study PDF (Spanish): [`docs/restaurant-reservations-case.pdf`](docs/restaurant-reservations-case.pdf)

## Requirements

- PHP 8.2+
- Composer
- SQLite (default) or MySQL

## Setup

```bash
composer install
cp .env.example .env   # if needed
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

## Demo users

Password for all: `password`

| Email | Role |
|-------|------|
| admin@demo.test | admin |
| manager@demo.test | manager |
| staff@demo.test | staff |
| customer@demo.test | customer |

## Auth examples

```bash
# Login
curl -s -X POST http://localhost:8000/api/auth/login \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"email":"customer@demo.test","password":"password"}'

# Create reservation (replace TOKEN)
curl -s -X POST http://localhost:8000/api/reservations \
  -H 'Authorization: Bearer TOKEN' -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"table_id":1,"date":"2026-12-01","time":"20:00","party_size":2}'
```

## Tests

```bash
php artisan test
```

## Domain (English)

`restaurants`, `tables`, `customers`, `dishes`, `employees`, `reservations`, `orders`, `roles`, `permissions`

Employee `position` (waiter/chef/manager) is a job title, not an auth role.
