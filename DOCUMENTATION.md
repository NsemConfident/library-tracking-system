# Library Management System - Complete Documentation

## Table of Contents

1. [System Overview](#system-overview)
2. [Installation & Setup](#installation--setup)
3. [Architecture](#architecture)
4. [User Guides](#user-guides)
5. [Configuration](#configuration)
6. [API Documentation](#api-documentation)
7. [Development Guide](#development-guide)
8. [Troubleshooting](#troubleshooting)

---

## System Overview

### What is This System?

A comprehensive library management system built with **Laravel 12**, **Livewire 3**, and **Flux UI** components. It provides complete functionality for managing books, circulation, holds, fines, and patron accounts.

### Key Features

- **Book Management**: Complete catalog with search, filtering, and categorization
- **Circulation**: Checkout, return, and renew books with automatic fine calculation
- **Holds System**: Queue-based reservation system for unavailable books
- **Fines Management**: Automatic overdue fine calculation and tracking
- **Role-Based Access**: Admin, Librarian, and Patron roles with appropriate permissions
- **Email Notifications**: Automated reminders and notifications
- **Admin Dashboard**: Comprehensive statistics and charts
- **Self-Service**: Patrons can manage their own loans, holds, and fines

### Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Livewire 3, Flux UI, Alpine.js
- **Admin Panel**: Filament 4
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Fortify

---

## Installation & Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- Database (MySQL, PostgreSQL, or SQLite)
- Web server (Apache/Nginx) or PHP built-in server

### Step 1: Clone and Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Step 2: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Configure Environment Variables

Edit `.env` file with your settings:

```env
APP_NAME="Library Management System"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail Configuration (for email notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@library.com
MAIL_FROM_NAME="${APP_NAME}"

# Optional: Library Configuration Overrides
LIBRARY_LOAN_PERIOD_DAYS=14
LIBRARY_MAX_LOANS=10
LIBRARY_FINE_RATE_PER_DAY=0.50
LIBRARY_LOST_BOOK_FEE=50.00
LIBRARY_HOLD_EXPIRY_DAYS=7
```

### Step 4: Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

This will create:
- Database tables
- Default settings
- Sample books, copies, and users

### Step 5: Build Frontend Assets

```bash
# For development
npm run dev

# For production
npm run build
```

### Step 6: Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

### Step 7: Setup Queue Worker (for Email Notifications)

```bash
# In a separate terminal
php artisan queue:work
```

Or configure a supervisor/systemd service for production.

### Step 8: Setup Scheduled Tasks (for Automated Emails)

Add to your server's cron:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Architecture

### Database Schema

#### Core Tables

- **users**: Extended with `role`, `phone`, `address`
- **books**: Book catalog information
- **copies**: Individual book copies with barcodes
- **loans**: Checkout records
- **holds**: Book reservation queue
- **fines**: Overdue and other fines
- **audit_logs**: System activity logs
- **settings**: System configuration

#### Key Relationships

```
User
├── hasMany → Loans
├── hasMany → Holds
└── hasMany → Fines

Book
├── hasMany → Copies
├── hasMany → Holds
└── hasManyThrough → Loans (via Copies)

Copy
├── belongsTo → Book
└── hasMany → Loans

Loan
├── belongsTo → Copy
├── belongsTo → User
└── hasOne → Fine

Hold
├── belongsTo → Book
└── belongsTo → User

Fine
├── belongsTo → User
└── belongsTo → Loan
```

### Application Structure

```
app/
├── Console/
│   └── Commands/
│       ├── SendDueDateReminders.php
│       └── SendOverdueNotifications.php
├── Filament/
│   ├── Resources/          # Admin panel resources
│   └── Widgets/            # Dashboard widgets
├── Http/
│   ├── Controllers/        # API controllers
│   └── Middleware/          # Custom middleware
├── Livewire/               # Frontend components
│   ├── Books/
│   ├── Circulation/
│   ├── Dashboard/
│   ├── MyLoans/
│   ├── MyHolds/
│   └── MyFines/
├── Models/                 # Eloquent models
├── Notifications/           # Email notifications
├── Providers/              # Service providers
└── Services/               # Business logic
    ├── LibraryService.php
    └── SettingsService.php

database/
├── migrations/             # Database migrations
└── seeders/                # Database seeders

resources/
└── views/
    ├── components/         # Blade components
    └── livewire/           # Livewire views

routes/
├── web.php                 # Web routes
└── console.php             # Scheduled tasks
```

### Service Layer

**LibraryService**: Centralized business logic for:
- Checkout operations
- Return operations
- Renewal operations
- Hold management
- Fine calculation

**SettingsService**: Configuration management:
- Retrieves settings from database or config file
- Provides type-safe getters for all settings
- Handles fallback to defaults

---

## User Guides

### For Administrators

#### Accessing Admin Panel

1. Navigate to `/admin`
2. Login with admin credentials
3. Default: `admin@library.com` / `password`

#### Managing Settings

1. Go to **System → Settings** in admin panel
2. Edit any configuration value
3. Changes take effect immediately
4. Settings are grouped by category:
   - **Loans**: Loan periods, max loans
   - **Fines**: Fine rates, fees
   - **Holds**: Hold expiry
   - **Notifications**: Email timing

#### Managing Books

1. Go to **Library → Books**
2. Click **New Book** to add
3. Fill in book details (title, author, ISBN, etc.)
4. After creating book, add copies:
   - Go to **Library → Copies**
   - Click **New Copy**
   - Select the book
   - Enter barcode and location

#### Managing Users

1. Go to **Users** in admin panel
2. Create new users or edit existing
3. Set user role: `admin`, `librarian`, or `patron`
4. Users can be created via registration or admin panel

#### Viewing Reports

- Dashboard shows real-time statistics
- Charts display trends over time
- Filter and search in resource tables

### For Librarians

#### Checking Out a Book

1. Navigate to **Library → Checkout** (or `/library/circulation/checkout`)
2. Scan or enter book barcode
3. Search for patron by name or email
4. Select patron from results
5. Adjust loan period if needed (default from settings)
6. Click **Checkout Book**

#### Returning a Book

1. Navigate to **Library → Return** (or `/library/circulation/return`)
2. Scan or enter book barcode
3. System automatically:
   - Processes return
   - Calculates fines if overdue
   - Notifies next patron if hold exists

#### Managing Holds

- View all holds in admin panel: **Library → Holds**
- When book is returned, first hold is automatically marked "ready"
- Patron receives email notification

#### Processing Fines

- View fines in admin panel: **Library → Fines**
- Mark fines as paid or waived
- Fines are automatically created when books are returned overdue

### For Patrons

#### Browsing the Catalog

1. Navigate to **Book Catalog** (or `/library/books`)
2. Use search bar to find books by:
   - Title
   - Author
   - ISBN
3. Filter by category using dropdown
4. Click book to view details

#### Placing a Hold

1. Find book in catalog
2. If no copies available, click **Place Hold**
3. You'll receive email when book is ready
4. View your holds: **My Holds** (or `/library/my-holds`)

#### Viewing Your Loans

1. Navigate to **My Loans** (or `/library/my-loans`)
2. View all active loans
3. See due dates and overdue status
4. Click **Renew** to extend loan (if no holds pending)

#### Renewing a Book

1. Go to **My Loans**
2. Find the book you want to renew
3. Click **Renew** button
4. Loan is extended by configured renewal period
5. Cannot renew if:
   - Book has pending holds
   - Loan is not active

#### Viewing Fines

1. Navigate to **My Fines** (or `/library/my-fines`)
2. View pending fines and amounts
3. Contact library to pay fines
4. See total pending and paid amounts

---

## Configuration

### Settings Management

The system uses a two-tier configuration system:

1. **Config File** (`config/library.php`): Default values
2. **Database Settings**: Override config file values

### Available Settings

#### Loan Settings

- `loan_period_days`: Default loan period (default: 14)
- `max_loans_per_patron`: Maximum active loans (default: 10)
- `renewal_period_days`: Days added when renewing (default: 14)

#### Fine Settings

- `fine_rate_per_day`: Overdue fine per day in dollars (default: 0.50)
- `lost_book_fee`: Replacement fee for lost books (default: 50.00)
- `fine_due_days`: Days after fine creation before due (default: 30)

#### Hold Settings

- `hold_expiry_days`: Days before hold expires (default: 7)

#### Notification Settings

- `due_date_reminder_days`: Days before due date to send reminder (default: 3)
- `overdue_notification_days`: Days overdue before sending notification (default: 1)

### Changing Settings

#### Method 1: Admin Panel (Recommended)

1. Login to admin panel (`/admin`)
2. Navigate to **System → Settings**
3. Edit any setting value
4. Changes take effect immediately

#### Method 2: Environment Variables

Add to `.env`:

```env
LIBRARY_LOAN_PERIOD_DAYS=21
LIBRARY_MAX_LOANS=15
LIBRARY_FINE_RATE_PER_DAY=1.00
```

#### Method 3: Config File

Edit `config/library.php` directly (requires code deployment).

### Settings Priority

1. Database settings (highest priority)
2. Environment variables
3. Config file defaults (lowest priority)

---

## API Documentation

### Authentication

All API endpoints require authentication. Include Bearer token in headers:

```
Authorization: Bearer {your-token}
```

### Base URL

```
/api/library
```

### Endpoints

#### Books

**List Books**
```
GET /api/library/books
Query Parameters:
  - page: Page number (default: 1)
  - per_page: Items per page (default: 15)
  - search: Search term (title, author, ISBN)
  - category: Filter by category

Response:
{
  "data": [...],
  "current_page": 1,
  "total": 100
}
```

**Get Book**
```
GET /api/library/books/{id}

Response:
{
  "id": 1,
  "title": "Book Title",
  "author": "Author Name",
  "isbn": "1234567890",
  ...
}
```

**Create Book**
```
POST /api/library/books
Body:
{
  "title": "Book Title",
  "author": "Author Name",
  "isbn": "1234567890",
  "category": "Fiction",
  ...
}

Response: 201 Created
```

**Update Book**
```
PUT /api/library/books/{id}
Body: { ... }

Response: 200 OK
```

**Delete Book**
```
DELETE /api/library/books/{id}

Response: 204 No Content
```

#### Circulation

**Checkout Book**
```
POST /api/library/circulation/checkout
Body:
{
  "barcode": "BOOK123",
  "user_id": 5,
  "loan_days": 14  // Optional, uses default if omitted
}

Response:
{
  "success": true,
  "message": "Book checked out successfully",
  "loan": { ... }
}
```

**Return Book**
```
POST /api/library/circulation/{loan}/return

Response:
{
  "success": true,
  "message": "Book returned successfully",
  "loan": { ... }
}
```

**Renew Loan**
```
POST /api/library/circulation/{loan}/renew
Body:
{
  "additional_days": 14  // Optional, uses default if omitted
}

Response:
{
  "success": true,
  "message": "Loan renewed successfully",
  "loan": { ... }
}
```

**Mark as Lost**
```
POST /api/library/circulation/{loan}/lost

Response:
{
  "success": true,
  "message": "Loan marked as lost"
}
```

#### Holds

**List Holds**
```
GET /api/library/holds
Query Parameters:
  - status: Filter by status (pending, ready, fulfilled, cancelled)
  - user_id: Filter by user
  - book_id: Filter by book
```

**Place Hold**
```
POST /api/library/holds
Body:
{
  "book_id": 10,
  "user_id": 5  // Optional, uses authenticated user
}

Response:
{
  "id": 1,
  "book_id": 10,
  "user_id": 5,
  "status": "pending",
  "position": 1,
  ...
}
```

**Get Hold**
```
GET /api/library/holds/{id}
```

**Cancel Hold**
```
POST /api/library/holds/{hold}/cancel

Response:
{
  "success": true,
  "message": "Hold cancelled successfully"
}
```

#### Fines

**List Fines**
```
GET /api/library/fines
Query Parameters:
  - status: Filter by status (pending, paid, waived)
  - user_id: Filter by user
```

**Get Fine**
```
GET /api/library/fines/{id}
```

**Mark Fine as Paid**
```
POST /api/library/fines/{fine}/pay
Body:
{
  "paid_date": "2024-12-14"  // Optional, uses today if omitted
}

Response:
{
  "success": true,
  "message": "Fine marked as paid"
}
```

**Waive Fine**
```
POST /api/library/fines/{fine}/waive

Response:
{
  "success": true,
  "message": "Fine waived"
}
```

### Error Responses

All endpoints return standard error format:

```json
{
  "success": false,
  "message": "Error message here"
}
```

Status codes:
- `200`: Success
- `201`: Created
- `204`: No Content
- `422`: Validation Error
- `404`: Not Found
- `403`: Forbidden
- `401`: Unauthorized

---

## Development Guide

### Adding a New Feature

#### 1. Database Changes

```bash
php artisan make:migration create_new_table
# Edit migration file
php artisan migrate
```

#### 2. Create Model

```bash
php artisan make:model NewModel
```

#### 3. Add Business Logic

Add methods to `LibraryService` or create new service.

#### 4. Create Controller (for API)

```bash
php artisan make:controller NewController
```

#### 5. Create Livewire Component (for UI)

```bash
php artisan make:livewire NewFeature/Index
```

#### 6. Add Routes

Edit `routes/web.php` or `routes/api.php`.

### Code Style

Run Laravel Pint:

```bash
php artisan pint
```

### Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter TestName
```

### Database Seeding

```bash
# Seed all
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=SettingsSeeder
```

### Clearing Caches

```bash
# Clear all caches
php artisan optimize:clear

# Clear specific cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Scheduled Commands

View scheduled tasks in `routes/console.php`.

Run manually:
```bash
php artisan library:send-due-date-reminders
php artisan library:send-overdue-notifications
```

---

## Troubleshooting

### Common Issues

#### 1. "Unable to locate component" Errors

**Problem**: Flux component not found

**Solution**: 
- Check component name spelling
- Verify Flux UI is installed: `composer show fluxui/livewire`
- Clear view cache: `php artisan view:clear`

#### 2. Email Notifications Not Sending

**Problem**: Emails not being sent

**Solution**:
- Check `.env` mail configuration
- Verify queue worker is running: `php artisan queue:work`
- Check mail logs: `storage/logs/laravel.log`
- Test email: `php artisan tinker` → `Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'))`

#### 3. Settings Not Updating

**Problem**: Configuration changes not taking effect

**Solution**:
- Clear config cache: `php artisan config:clear`
- Verify settings in database: Check `settings` table
- Check SettingsService is being used correctly

#### 4. Permission Denied Errors

**Problem**: 403 Access Denied

**Solution**:
- Verify user role in database
- Check authorization gates in `AppServiceProvider`
- Verify middleware is applied correctly

#### 5. Database Connection Issues

**Problem**: Cannot connect to database

**Solution**:
- Verify `.env` database credentials
- Check database server is running
- Test connection: `php artisan tinker` → `DB::connection()->getPdo()`

#### 6. Livewire Component Not Updating

**Problem**: Component not refreshing after action

**Solution**:
- Check browser console for JavaScript errors
- Verify Livewire scripts are loaded: `@livewireScripts`
- Clear view cache: `php artisan view:clear`
- Check network tab for failed requests

### Performance Issues

#### Slow Queries

- Enable query logging: Add to `.env`: `DB_LOG_QUERIES=true`
- Review queries in `storage/logs/laravel.log`
- Add eager loading: `->with(['relation1', 'relation2'])`
- Add database indexes for frequently queried columns

#### High Memory Usage

- Increase PHP memory limit: `memory_limit=256M` in `php.ini`
- Optimize queries (avoid N+1 problems)
- Use pagination for large datasets
- Enable OPcache for production

### Getting Help

1. Check Laravel documentation: https://laravel.com/docs
2. Check Livewire documentation: https://livewire.laravel.com
3. Check Flux UI documentation: https://flux.laravel.com
4. Review application logs: `storage/logs/laravel.log`

---

## Business Rules

### Checkout Rules

- Maximum active loans per patron: Configurable (default: 10)
- Default loan period: Configurable (default: 14 days)
- Books with pending holds cannot be renewed
- Only available copies can be checked out

### Fine Calculation

- Overdue fine: Configurable rate per day (default: $0.50/day)
- Lost book fee: Configurable (default: $50.00)
- Fines are created automatically when overdue books are returned
- Fine due date: Configurable days after creation (default: 30 days)

### Hold System

- Hold expires after: Configurable days (default: 7 days)
- Position in queue based on request date (FIFO)
- First hold gets priority when book becomes available
- Hold statuses: `pending`, `ready`, `fulfilled`, `cancelled`

### Renewal Rules

- Can only renew active loans
- Cannot renew if book has pending holds
- Renewal extends due date by: Configurable days (default: 14 days)

---

## Security

### Authentication

- Uses Laravel Fortify
- Supports email verification
- Two-factor authentication available

### Authorization

- Role-based access control (RBAC)
- Gates defined in `AppServiceProvider`:
  - `admin`: Full system access
  - `librarian`: Circulation and management access
- Middleware applied to routes

### Data Protection

- CSRF protection enabled
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating
- Input validation on all endpoints

### Recommendations

- Enable rate limiting on API endpoints (not yet implemented)
- Use HTTPS in production
- Regularly update dependencies
- Review and audit user permissions

---

## Maintenance

### Daily Tasks

- Monitor queue worker for email notifications
- Check scheduled tasks are running
- Review error logs

### Weekly Tasks

- Review overdue loans
- Process pending fines
- Check system performance

### Monthly Tasks

- Review and clean audit logs
- Backup database
- Update dependencies
- Review user accounts

### Backup Strategy

```bash
# Database backup
php artisan db:backup

# Or manual backup
mysqldump -u user -p database_name > backup.sql
```

---

## Support & Resources

- **Laravel Docs**: https://laravel.com/docs
- **Livewire Docs**: https://livewire.laravel.com
- **Flux UI Docs**: https://flux.laravel.com
- **Filament Docs**: https://filamentphp.com/docs

---

## Version History

### Current Version: 1.0.0

**Features**:
- Complete library management system
- Role-based access control
- Email notifications
- Configuration management
- Admin panel with charts
- Patron self-service pages

---

## License

This project is open-sourced software licensed under the MIT license.

