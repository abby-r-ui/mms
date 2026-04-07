# MMS - Motorcycle Rental Management System

## 🏍️ Overview
Production-ready monorepo with **Laravel API Backend** + **Custom PHP Frontend** (API-driven).

**Features:**
- User auth (register/login - customers/admins via API tokens)
- Customer: Browse motorcycles, search/filter, rent (date picker, price calc)
- Admin: Dashboard CRUD motorcycles (add/edit/delete/status), view rentals
- API-first: All frontend calls backend /api/*
- Responsive design, AJAX updates (no page reloads)
- Production: Nginx + PHP-FPM setup script

![Architecture](https://via.placeholder.com/800x200?text=Frontend+(%2F)+%3E+AJAX+%3E+Laravel+API+(%2Fapi%2A))

## 📁 Structure
```
.
├── backend/           # Laravel 11 API
│   ├── app/Models/    # User, Motorcycle, Rental
│   ├── app/Http/Controllers/ # Auth, Motorcycle, Rental
│   ├── routes/api.php # Sanctum protected
│   └── ...
├── frontend/          # Custom PHP pages
│   ├── pages/         # home, login, dashboard, admin, rent
│   ├── public/index.php
│   └── pages/router.php
├── setup_monorepo_nginx.sh  # Prod deploy
├── TODO.md            # Progress
└── README.md
```

## 🚀 Quick Start (Development)

1. **Backend (Terminal 1):**
```bash
cd backend
cp .env.example .env
php artisan key:generate
# Edit .env DB_*: use sqlite (default) or mysql
php artisan migrate
php artisan db:seed  # Sample data
php artisan serve  # Runs on http://127.0.0.1:8000/api/*
```

2. **Frontend (Terminal 2):** (after frontend creation)
```bash
cd frontend
php -S localhost:8001 -t public  # http://localhost:8001/
```

**Test API:**
```bash
curl -X POST http://127.0.0.1:8000/api/register \\
  -H \"Content-Type: application/json\" \\
  -d '{\"name\":\"Test\",\"email\":\"test@example.com\",\"password\":\"password\",\"role\":\"customer\"}'
```

## 🔧 Production Deploy
```bash
sudo chmod +x setup_monorepo_nginx.sh
sudo ./setup_monorepo_nginx.sh
```

## 🛠️ Tech Stack
- Backend: Laravel 11, Sanctum, Eloquent, SQLite/MySQL
- Frontend: PHP 8.2+, Bootstrap 5, Vanilla JS
- Deploy: Nginx + PHP-FPM

## 📊 Schema Overview
- users: role enum (admin/customer)
- motorcycles: make, model, year, price_per_day, status enum
- rentals: dates, total_price, foreign keys

See [TODO.md](TODO.md) for progress. Contributions welcome!
