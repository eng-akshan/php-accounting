<<<<<<< HEAD
# Accounting Management System

A modern, secure, and scalable accounting system built with Laravel PHP and MySQL for small-to-medium businesses.

## Features

### Core Functionality
- **Authentication & Authorization**: Login/Register with role-based access (Admin, Accountant, Staff)
- **Dashboard**: KPI cards, charts, recent transactions
- **Chart of Accounts**: Full double-entry accounting with account types (Assets, Liabilities, Equity, Revenue, Expenses)
- **Journal Entries**: Double-entry transaction system with balance validation
- **Income & Expense Management**: Track income and expenses with categories
- **Invoices & Payments**: Create invoices, track payments with multiple methods
- **Reports**: Profit & Loss, Balance Sheet, Cash Flow, General Ledger

### Additional Features
- Multi-branch support
- Audit logging
- Settings management
- Customer management

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js (for assets)

## Installation

1. Clone and install dependencies:
```bash
composer install
```

2. Configure environment:
```bash
cp .env.example .env
```

3. Update `.env` with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accounting_system
DB_USERNAME=root
DB_PASSWORD=
```

4. Run migrations and seed:
```bash
php artisan migrate --force
php artisan db:seed --force
```

5. Start the server:
```bash
php artisan serve
```

## Default Login Credentials

| Role | Email | Password |
|------|-------|---------|
| Admin | admin@accounting.com | password123 |
| Accountant | accountant@accounting.com | password123 |

## Project Structure

```
app/
├── Http/
│   ├── Controllers/    # Business logic
│   └── Middleware/    # Auth middleware
├── Models/           # Eloquent models
database/
├── migrations/       # Database schema
└── seeders/         # Demo data
public/              # Public assets
resources/
└── views/           # Blade templates
routes/
└── web.php          # Web routes
```

## Account Types

- **Assets**: Debit normal (Cash, Equipment, Receivables)
- **Liabilities**: Credit normal (Payables, Loans)
- **Equity**: Credit normal (Capital, Retained Earnings)
- **Revenue**: Credit normal (Sales, Services)
- **Expense**: Debit normal (Salaries, Rent, Utilities)

## Technology Stack

- Laravel 12.x
- MySQL
- Bootstrap 5
- Chart.js
- Font Awesome

## Security

- CSRF protection
- SQL injection prevention (Eloquent ORM)
- Role-based authorization
- Input validation

## License

MIT
=======
# php-accounting
>>>>>>> 349994662a6ac25a804126686c6e5c3aa7d56018
