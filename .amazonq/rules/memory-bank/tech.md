# Technology Stack

## Programming Languages

### PHP 8.2+
- Primary backend language
- Modern PHP features (match expressions, named arguments, attributes)
- Type declarations and strict typing
- Null-safe operator usage

### JavaScript (ES6+)
- Frontend interactivity
- Alpine.js for reactive components
- Axios for HTTP requests
- Module-based architecture

### SQL
- Database queries and migrations
- MySQL/SQLite support
- Complex joins and aggregations

### HTML/Blade
- Blade templating engine
- Component-based views
- RTL-aware markup for Arabic

### CSS (Tailwind)
- Utility-first styling
- Custom RTL configurations
- Responsive design

## Framework and Core Dependencies

### Laravel Framework 12.0
**Core Packages:**
- `laravel/framework: ^12.0` - Main framework
- `laravel/tinker: ^2.10.1` - REPL for debugging
- `laravel/pail: ^1.2.2` - Log viewer
- `laravel/pint: ^1.24` - Code style fixer
- `laravel/sail: ^1.41` - Docker development environment

**Key Features Used:**
- Eloquent ORM with relationships
- Blade templating with components
- Authentication scaffolding
- Queue system for background jobs
- Cache system for performance
- Migration system for database versioning
- Validation and form requests
- Middleware for authorization

### Document Generation
- `barryvdh/laravel-dompdf: ^3.1` - PDF generation with Arabic support
- `phpoffice/phpspreadsheet: ^5.4` - Excel file generation and export

### Arabic Language Support
- `khaled.alshamaa/ar-php: ^7.0` - Arabic text processing and number conversion

### Frontend Stack
- `tailwindcss: ^3.1.0` - Utility-first CSS framework
- `@tailwindcss/forms: ^0.5.2` - Form styling
- `@tailwindcss/vite: ^4.0.0` - Vite integration
- `alpinejs: ^3.4.2` - Lightweight JavaScript framework
- `axios: ^1.11.0` - HTTP client
- `vite: ^7.0.7` - Build tool and dev server
- `laravel-vite-plugin: ^2.0.0` - Laravel integration

### Development Dependencies
- `fakerphp/faker: ^1.23` - Test data generation
- `phpunit/phpunit: ^11.5.3` - Testing framework
- `mockery/mockery: ^1.6` - Mocking library
- `nunomaduro/collision: ^8.6` - Error reporting
- `concurrently: ^9.0.1` - Run multiple commands

## Build System

### Composer (PHP Dependency Manager)
**Configuration:** `composer.json`

**Key Scripts:**
```bash
composer setup          # Full project setup
composer dev           # Start development environment
composer test          # Run test suite
```

**Autoloading:**
- PSR-4 autoloading for App namespace
- Optimized autoloader for production

### NPM (Node Package Manager)
**Configuration:** `package.json`

**Scripts:**
```bash
npm run dev            # Start Vite dev server
npm run build          # Build for production
```

### Vite (Build Tool)
**Configuration:** `vite.config.js`
- Hot module replacement (HMR)
- Asset bundling and optimization
- Laravel plugin integration
- PostCSS processing

### Tailwind CSS
**Configuration:** `tailwind.config.js`
- Custom color schemes
- RTL support
- Form plugin integration
- Content paths for purging

## Database System

### Supported Databases
- **MySQL** - Primary production database
- **SQLite** - Development and testing

### Migration System
- 40+ migration files defining complete schema
- Timestamp-based versioning
- Foreign key constraints
- Index optimization

### Seeding System
- `DatabaseSeeder.php` - Main seeder
- `LargeDataSeeder.php` - Test data generation with realistic volumes

## Development Commands

### Laravel Artisan Commands
```bash
# Development
php artisan serve                    # Start development server
php artisan queue:listen             # Process queue jobs
php artisan pail                     # View logs in real-time

# Database
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed     # Fresh database with seed data
php artisan db:seed                  # Run seeders

# Cache Management
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear config cache
php artisan view:clear               # Clear compiled views

# Code Quality
php artisan pint                     # Fix code style
php artisan test                     # Run tests

# Custom Commands
php artisan backup:create            # Create database backup
php artisan backup:restore           # Restore from backup
```

### Composer Commands
```bash
composer install                     # Install dependencies
composer update                      # Update dependencies
composer dump-autoload               # Regenerate autoload files
composer setup                       # Full project setup
composer dev                         # Start all dev services
composer test                        # Run test suite
```

### NPM Commands
```bash
npm install                          # Install frontend dependencies
npm run dev                          # Start Vite dev server
npm run build                        # Build production assets
```

### Combined Development Workflow
```bash
# Using composer dev script (recommended)
composer dev
# Runs concurrently:
# - php artisan serve (server on port 8000)
# - php artisan queue:listen (background jobs)
# - php artisan pail (log viewer)
# - npm run dev (Vite HMR on port 5173)
```

## Environment Configuration

### Required Environment Variables
```env
APP_NAME=TDM-System-V2
APP_ENV=local|production
APP_KEY=                            # Generated by artisan key:generate
APP_DEBUG=true|false
APP_URL=http://localhost

DB_CONNECTION=mysql|sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tdm_system
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file|redis
QUEUE_CONNECTION=sync|database|redis
SESSION_DRIVER=file|database
```

### Configuration Files
- `config/app.php` - Application settings, timezone, locale
- `config/database.php` - Database connections
- `config/logging.php` - Log channels and formatting
- `config/cache.php` - Cache drivers
- `config/queue.php` - Queue configuration
- `config/session.php` - Session management

## Testing Infrastructure

### PHPUnit Configuration
**File:** `phpunit.xml`
- Feature tests for HTTP workflows
- Unit tests for business logic
- Database testing with transactions
- Test environment configuration

### Test Structure
```
tests/
├── Feature/              # Integration tests
│   ├── Auth/            # Authentication tests
│   ├── ExampleTest.php
│   └── ProfileTest.php
├── Unit/                # Unit tests
│   └── ExampleTest.php
└── TestCase.php         # Base test case
```

## Code Quality Tools

### Laravel Pint
- Opinionated PHP code style fixer
- PSR-12 compliance
- Automatic formatting

### EditorConfig
**File:** `.editorconfig`
- Consistent coding styles across editors
- Indentation and line ending rules

## Version Control

### Git Configuration
**Files:** `.gitignore`, `.gitattributes`

**Ignored Directories:**
- `/vendor/` - Composer dependencies
- `/node_modules/` - NPM dependencies
- `/storage/` - Runtime files (except structure)
- `.env` - Environment configuration

## Production Deployment

### Optimization Commands
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### Server Requirements
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and NPM
- MySQL 8.0+ or SQLite 3.x
- Web server (Apache/Nginx)
- SSL certificate for HTTPS

### Recommended PHP Extensions
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD (for image processing)
- Zip (for backups)
