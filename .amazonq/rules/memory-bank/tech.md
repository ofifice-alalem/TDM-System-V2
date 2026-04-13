# TDM System V2 - Technology Stack

## Backend
| Technology | Version | Purpose |
|-----------|---------|---------|
| PHP | ^8.2 | Runtime |
| Laravel | ^12.0 (12.50.0) | Web framework |
| Laravel Tinker | ^2.10.1 | REPL / debugging |
| barryvdh/laravel-dompdf | ^3.1 | PDF generation (Arabic RTL) |
| khaled.alshamaa/ar-php | ^7.0 | Arabic language utilities |
| phpoffice/phpspreadsheet | ^5.4 | Excel export |

## Frontend
| Technology | Version | Purpose |
|-----------|---------|---------|
| Tailwind CSS | ^3.1.0 | Utility-first CSS framework |
| @tailwindcss/forms | ^0.5.2 | Form styling plugin |
| Alpine.js | ^3.4.2 | Lightweight JS reactivity |
| Axios | ^1.11.0 | HTTP client for AJAX calls |
| Vite | ^7.0.7 | Asset bundler |
| laravel-vite-plugin | ^2.0.0 | Laravel/Vite integration |
| PostCSS + Autoprefixer | ^8.4 / ^10.4 | CSS processing |

## Database
- **Primary**: SQLite (`database/database.sqlite`)
- **ORM**: Eloquent (Laravel)
- **Migrations**: 50+ migration files
- **Soft Deletes**: Used on `users` table

## Dev Dependencies
| Package | Purpose |
|---------|---------|
| fakerphp/faker | Test data generation |
| laravel/pail | Log viewer |
| laravel/pint | PHP code style fixer |
| laravel/sail | Docker dev environment |
| mockery/mockery | Mocking for tests |
| nunomaduro/collision | Better error reporting |
| phpunit/phpunit ^11.5 | Testing framework |
| concurrently | Run multiple dev processes |

## Fonts
- Cairo (Regular, Bold, ExtraBold) — Arabic font used in PDF generation
- Stored in `public/fonts/` and cached in `storage/fonts/`

## Development Commands

```bash
# Full dev environment (server + queue + logs + vite)
composer dev

# Or individually:
php artisan serve          # Laravel dev server
npm run dev                # Vite HMR
php artisan queue:listen   # Queue worker
php artisan pail           # Log viewer

# Build assets for production
npm run build

# Run tests
composer test
# or
php artisan test

# Code style
./vendor/bin/pint

# First-time setup
composer setup
```

## Key Configuration
- **Auth**: Custom `password_hash` field (not default `password`); `getAuthPassword()` overridden in `User` model
- **Queue**: Used for background jobs (jobs table in DB)
- **Session**: Database-backed (configurable)
- **Mail**: Configured via `config/mail.php` (SMTP)
- **Autoload**: PSR-4, `App\` → `app/`

## Build System
- Vite handles JS/CSS bundling with `laravel-vite-plugin`
- Entry points: `resources/js/app.js`, `resources/css/app.css`
- Output: `public/build/`
