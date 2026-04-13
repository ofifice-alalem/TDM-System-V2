# TDM System V2 - Project Structure

## Directory Layout

```
tdm.motafwiqon.com.ly/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/        # Admin-only controllers
│   │   │   ├── Auth/         # Laravel Breeze auth controllers
│   │   │   ├── Marketer/     # Marketer role controllers
│   │   │   ├── Sales/        # Sales role controllers
│   │   │   ├── Shared/       # Shared controllers (PDF invoices, stats, stores)
│   │   │   └── Warehouse/    # Warehouse role controllers
│   │   ├── Middleware/       # Custom middleware (role-based access)
│   │   └── Requests/         # Form request validation classes
│   ├── Models/               # 35+ Eloquent models
│   ├── Services/
│   │   ├── Admin/            # AdminWithdrawalService
│   │   ├── Marketer/         # Request, Return, Payment, Sales, Withdrawal services
│   │   ├── Sales/            # CustomerInvoice, Payment, Return services
│   │   ├── Warehouse/        # Stock, Request, Return, Sales services
│   │   └── NotificationService.php
│   ├── Jobs/                 # Queue jobs
│   ├── View/Components/      # Blade components
│   └── Providers/
├── database/
│   ├── migrations/           # 50+ migration files (dated 2026-02-xx to 2026-03-xx)
│   ├── seeders/              # DatabaseSeeder + LargeDataSeeder
│   └── database.sqlite       # SQLite database file
├── resources/
│   ├── views/
│   │   ├── admin/            # Admin Blade views
│   │   ├── auth/             # Login/register views
│   │   ├── components/       # Reusable Blade components
│   │   ├── layouts/          # App layout templates
│   │   ├── marketer/         # Marketer Blade views
│   │   ├── sales/            # Sales Blade views
│   │   ├── shared/           # Shared views (invoices, receipts)
│   │   └── warehouse/        # Warehouse Blade views
│   ├── js/
│   │   ├── app.js            # Alpine.js + Axios entry point
│   │   └── bootstrap.js      # Axios config
│   └── css/app.css           # Tailwind CSS entry
├── routes/
│   ├── web.php               # Root + dashboard redirect
│   ├── admin.php             # Admin routes (prefix: /admin)
│   ├── marketer.php          # Marketer routes (prefix: /marketer)
│   ├── warehouse.php         # Warehouse routes (prefix: /warehouse)
│   ├── sales.php             # Sales routes (prefix: /sales)
│   └── auth.php              # Auth routes
├── public/
│   └── fonts/                # Cairo Arabic font files (Regular, Bold, ExtraBold)
├── storage/
│   ├── app/backups/          # Database backup files
│   └── fonts/                # DomPDF cached font files
├── التطوير_1/                # Development docs v1 (Arabic business flow specs)
├── التطوير_2/                # Development docs v2 (backend/frontend/routes specs)
└── التطوير_3/                # Development docs v3 (DB structure, inventory movements)
```

## Core Models & Relationships

### Stock Flow Models
- `MainStock` → warehouse-level stock per product
- `MarketerReservedStock` → stock reserved for marketer (pending delivery)
- `MarketerActualStock` → stock physically held by marketer
- `StoreActualStock` / `StorePendingStock` → store-level stock tracking
- `WarehouseStockLog` → audit log of all stock movements

### Transaction Models
- `MarketerRequest` / `MarketerRequestItem` → marketer stock requests
- `MarketerReturnRequest` / `MarketerReturnItem` → marketer returns
- `SalesInvoice` / `SalesInvoiceItem` → marketer-to-store sales
- `SalesReturn` / `SalesReturnItem` → store returns to marketer
- `FactoryInvoice` / `FactoryInvoiceItem` → factory-to-warehouse stock intake
- `CustomerInvoice` / `CustomerInvoiceItem` → direct customer sales
- `CustomerReturn` / `CustomerReturnItem` → customer returns

### Financial Models
- `StoreDebtLedger` → running balance of store debt
- `CustomerDebtLedger` → running balance of customer debt
- `StorePayment` → store debt payments
- `CustomerPayment` → customer payments
- `MarketerCommission` → commission earned per sale
- `MarketerWithdrawalRequest` → commission withdrawal requests

### Reference Models
- `User` (roles: Admin=1, Warehouse=2, Marketer=3, Sales=4)
- `Role` → role definitions
- `Product` → product catalog with pricing
- `Store` → store registry (linked to marketer)
- `InvoiceDiscountTier` → tiered discount rules
- `ProductPromotion` → time-based product promotions
- `Notification` → in-app notifications

## Architectural Patterns

### Role-Based Routing
Each role has its own route file with `middleware(['web', 'auth', 'role:ROLENAME'])` and a dedicated URL prefix:
- `/admin/*` → Admin
- `/warehouse/*` → Warehouse  
- `/marketer/*` → Marketer
- `/sales/*` → Sales

### Service Layer Pattern
Business logic is extracted into `app/Services/{Role}/` service classes. Controllers are thin — they delegate to services and return views/redirects.

### Shared Controllers
PDF invoice generation and shared views live in `app/Http/Controllers/Shared/` to avoid duplication across roles.

### Dashboard Routing
`/dashboard` uses `match()` on `role_id` to redirect each user to their role-specific landing page.
