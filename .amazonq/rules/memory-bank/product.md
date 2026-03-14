# TDM System V2 - Product Overview

## Project Purpose
نظام إدارة التوزيع والمبيعات (Trade Distribution Management) - a Laravel-based web application for managing product distribution, sales, marketer commissions, and store debt tracking in Arabic-speaking markets.

## Key Features & Capabilities

### Core Business Flows
1. **Marketer Stock Requests** - Marketers request products from warehouse; warehouse approves/rejects
2. **Marketer Returns** - Marketers return unsold stock back to warehouse
3. **Store Sales** - Marketers sell products to stores, generating sales invoices
4. **Store Payments** - Stores pay their debts; payment receipts generated
5. **Marketer Commission Withdrawal** - Marketers withdraw earned commissions
6. **Store Returns** - Stores return products back to marketers
7. **Invoice Discounts** - Tiered discount system based on invoice amount
8. **Product Promotions** - Time-based promotional pricing per product
9. **Direct Customer Sales** - Sales team sells directly to end customers

### Management Modules
- **Admin Panel** - Full system control: users, products, stock, factory invoices, backups, statistics
- **Warehouse Panel** - Manage incoming factory stock, approve marketer requests, handle returns
- **Marketer Panel** - View personal stock, create sales, manage commissions and withdrawals
- **Sales Panel** - Manage customers, customer invoices, payments, and returns

### Supporting Features
- PDF invoice generation (Arabic RTL support via DomPDF + Cairo font)
- Excel export (PhpSpreadsheet)
- In-app notification system
- Database backup & restore
- Role-based access control (Admin, Warehouse, Marketer, Sales)
- Soft deletes for users
- Debt ledger tracking per store and customer

## Target Users
| Role | ID | Responsibilities |
|------|----|-----------------|
| Admin | 1 | Full system management, reports, user control |
| Warehouse | 2 | Stock management, request fulfillment |
| Marketer | 3 | Field sales, store management, commission tracking |
| Sales | 4 | Direct customer sales and invoicing |

## Value Proposition
Provides end-to-end traceability of product flow from factory → main stock → marketer stock → store/customer, with full financial tracking of debts, payments, and commissions.
