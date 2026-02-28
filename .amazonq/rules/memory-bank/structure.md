# Project Structure

## Directory Organization

### Core Application (`app/`)
```
app/
├── Console/Commands/          # Artisan custom commands
├── Http/
│   ├── Controllers/          # Request handlers organized by role
│   │   ├── Admin/           # Admin-specific controllers
│   │   ├── Warehouse/       # Warehouse operations
│   │   ├── Marketer/        # Marketer workflows
│   │   ├── Sales/           # Sales representative functions
│   │   └── Shared/          # Cross-role controllers (Statistics, Notifications)
│   ├── Middleware/          # Request filtering and authentication
│   └── Requests/            # Form validation classes
├── Models/                   # Eloquent ORM models (35+ models)
├── Services/                 # Business logic layer
│   ├── Admin/               # Admin service classes
│   ├── Warehouse/           # Warehouse service classes
│   ├── Marketer/            # Marketer service classes
│   ├── Sales/               # Sales service classes
│   └── NotificationService.php
├── Providers/               # Service providers
└── View/Components/         # Blade components
```

### Database Layer (`database/`)
```
database/
├── migrations/              # 40+ migration files defining schema
├── seeders/                # Database seeders
│   ├── DatabaseSeeder.php
│   └── LargeDataSeeder.php # Test data generation
└── factories/              # Model factories for testing
```

### Frontend Resources (`resources/`)
```
resources/
├── views/
│   ├── admin/              # Admin panel views
│   ├── warehouse/          # Warehouse interface
│   ├── marketer/           # Marketer dashboard
│   ├── sales/              # Sales representative views
│   ├── shared/             # Shared views (statistics, notifications)
│   ├── auth/               # Authentication pages
│   ├── layouts/            # Layout templates
│   └── components/         # Reusable Blade components
├── css/                    # Stylesheets
└── js/                     # JavaScript files
```

### Routing (`routes/`)
```
routes/
├── web.php                 # Main routes and dashboard routing
├── admin.php               # Admin-specific routes
├── warehouse.php           # Warehouse routes
├── marketer.php            # Marketer routes
├── sales.php               # Sales routes
├── auth.php                # Authentication routes
└── api.php                 # API endpoints
```

### Configuration (`config/`)
- Standard Laravel configuration files
- Custom logging configuration for Arabic support
- Database, cache, queue, and session configurations

### Public Assets (`public/`)
```
public/
├── fonts/                  # Cairo font family for Arabic
├── images/                 # Company logo and assets
└── storage/                # Symlink to storage/app/public
```

### Storage (`storage/`)
```
storage/
├── app/
│   ├── backups/           # Database backup files
│   ├── public/            # Publicly accessible files
│   └── private/           # Private file storage
├── fonts/                 # PDF font cache (Cairo fonts)
├── framework/             # Framework cache and sessions
└── logs/                  # Application logs
```

## Core Components and Relationships

### Model Relationships

**User Model** (Central entity)
- Belongs to Role (Admin, Warehouse, Marketer, Sales)
- Has many MarketerRequests, SalesInvoices, CustomerInvoices
- Has many MarketerCommissions, MarketerWithdrawalRequests

**Product Model**
- Has many MainStock entries
- Has many MarketerActualStock entries
- Has many StoreActualStock entries
- Has many ProductPromotions
- Tracks store_price and customer_price

**Stock Flow Models**
1. **MainStock** → Factory invoices add stock
2. **MarketerReservedStock** → Created when request is approved
3. **MarketerActualStock** → Created when products delivered
4. **StorePendingStock** → Created when sales invoice generated
5. **StoreActualStock** → Created when invoice approved

**Financial Models**
- **StoreDebtLedger**: Tracks store balances (invoices increase, payments/returns decrease)
- **CustomerDebtLedger**: Tracks customer balances
- **MarketerCommission**: Calculated from approved sales invoices
- **StorePayment/CustomerPayment**: Payment processing with approval workflow

**Request/Approval Models**
- **MarketerRequest** → MarketerRequestItem (pending → approved → delivered)
- **MarketerReturnRequest** → MarketerReturnItem (pending → approved)
- **SalesInvoice** → SalesInvoiceItem (pending → approved/rejected)
- **CustomerInvoice** → CustomerInvoiceItem (pending → approved/rejected)

### Architectural Patterns

**Service Layer Pattern**
- Business logic extracted from controllers into service classes
- Services handle complex operations (stock updates, debt calculations, commission processing)
- Controllers remain thin, delegating to services

**Repository Pattern (Implicit)**
- Models act as repositories with query scopes
- Complex queries encapsulated in model methods
- Relationships defined at model level

**Role-Based Access Control**
- Middleware enforces role-based routing
- Dashboard routing uses match expression for role-based redirection
- Each role has dedicated controller namespace

**Transaction Management**
- Database transactions wrap multi-step operations
- Stock updates and financial records updated atomically
- Rollback on failure ensures data consistency

**Event-Driven Notifications**
- NotificationService handles cross-role notifications
- Notifications created for pending approvals and status changes
- Real-time updates for user actions

## Data Flow Architecture

### Request-Approval-Delivery Flow
```
Marketer Request → Admin Approval → Warehouse Delivery
     ↓                  ↓                  ↓
  (pending)        (approved)         (delivered)
     ↓                  ↓                  ↓
  Reserved Stock   Reserved Stock    Actual Stock
```

### Sales-Debt-Payment Flow
```
Sales Invoice → Store Receives → Debt Created → Payment Made
     ↓               ↓               ↓              ↓
  Pending       Store Pending    Debt Ledger    Debt Reduced
                    Stock         Entry          & Approved
```

### Commission Flow
```
Approved Invoice → Commission Calculated → Withdrawal Request → Admin Approval
       ↓                    ↓                      ↓                  ↓
   Store Sale         Commission Record      Pending Request      Balance Updated
```

## Technology Stack Integration

**Backend**: Laravel 12 with PHP 8.2
- Eloquent ORM for database operations
- Blade templating for views
- Queue system for background jobs
- Cache system for performance

**Frontend**: Tailwind CSS + Alpine.js
- Utility-first CSS framework
- Reactive components with Alpine.js
- RTL support for Arabic interface

**PDF Generation**: DomPDF with Arabic support
- Cairo font family for proper Arabic rendering
- Invoice and report generation

**Excel Export**: PhpSpreadsheet
- Data export functionality
- Report generation in Excel format

**Arabic Support**: ar-php library
- Arabic text processing
- Number to Arabic text conversion
