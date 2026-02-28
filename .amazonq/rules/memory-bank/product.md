# Product Overview

## Project Purpose
TDM-System-V2 (Trade Distribution Management System - Version 2) is a comprehensive Laravel-based web application designed to manage the complete distribution and sales lifecycle for trading businesses. The system orchestrates the flow of products from main warehouse through marketers to stores and end customers, with full financial tracking and commission management.

## Value Proposition
- **Complete Distribution Chain Management**: Tracks products from warehouse to end customer with multi-level stock management
- **Multi-Role Architecture**: Supports Admin, Warehouse, Marketer, and Sales roles with role-specific workflows
- **Financial Transparency**: Automated debt tracking, payment management, and commission calculations
- **Arabic-First Design**: Built with RTL support and Arabic language as primary interface
- **Real-Time Inventory**: Synchronized stock levels across main warehouse, marketer inventory, and store inventory

## Key Features

### Stock Management
- Main warehouse stock control with factory invoice processing
- Marketer request system with approval workflow (pending → approved → delivered)
- Reserved stock tracking during pending requests
- Actual stock management for marketers and stores
- Return processing for both marketers and stores

### Sales Operations
- Sales invoice generation with automatic discount tier application
- Product promotion management with date-based activation
- Customer invoice system for direct sales
- Multi-level pricing (store price vs customer price)
- Invoice rejection workflow with reason tracking

### Financial Management
- Store debt ledger with automatic balance tracking
- Customer debt ledger for direct sales
- Payment processing with approval workflow
- Marketer commission calculation and withdrawal requests
- Invoice discount tiers based on amount thresholds

### Reporting & Analytics
- Statistics dashboard for each role
- Warehouse stock logs for audit trail
- Debt tracking and payment history
- Commission reports and withdrawal history
- Backup and restore functionality

### Notification System
- Real-time notifications for pending approvals
- Request status updates
- Payment confirmations
- Return processing alerts

## Target Users

### Admin (Role ID: 1)
- System configuration and user management
- Main stock oversight and factory invoice processing
- Approval of marketer requests and returns
- Financial report generation and backup management

### Warehouse Keeper (Role ID: 2)
- Processing approved marketer requests
- Managing delivery of products to marketers
- Handling return requests from marketers
- Stock log maintenance

### Marketer (Role ID: 3)
- Requesting products from warehouse
- Managing personal inventory
- Creating sales invoices for stores
- Processing store returns
- Tracking commissions and requesting withdrawals

### Sales Representative (Role ID: 4)
- Managing customer relationships
- Creating customer invoices for direct sales
- Processing customer payments
- Handling customer returns
- Tracking customer debt

## Use Cases

### Primary Workflows
1. **Marketer Product Request**: Marketer requests → Admin approves → Warehouse delivers → Stock updated
2. **Store Sales**: Marketer creates invoice → Store receives products → Debt recorded → Payment processed
3. **Customer Sales**: Sales rep creates invoice → Customer receives products → Debt tracked → Payment collected
4. **Return Processing**: Store/Customer initiates return → Marketer/Sales approves → Stock adjusted → Debt reduced
5. **Commission Management**: Sales generate commissions → Marketer requests withdrawal → Admin approves → Balance updated
6. **Factory Restocking**: Admin creates factory invoice → Main stock increased → Products available for distribution

### Business Scenarios
- Wholesale distribution with multiple marketers covering different territories
- Store credit management with payment terms
- Direct customer sales with debt tracking
- Promotional campaigns with time-based discounts
- Commission-based marketer compensation
- Multi-tier invoice discounts to encourage larger orders
