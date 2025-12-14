# Library Book Tracking System

A comprehensive library management system built with Laravel 12, Livewire, and Flux UI components.

## Features

### Core Functionality
- **Book Management**: Add, edit, search, and manage books in the catalog
- **Copy Management**: Track individual copies of books with barcodes
- **Circulation**: Checkout, return, and renew books
- **Holds System**: Place and manage holds on books
- **Fines Management**: Track and manage overdue fines
- **Audit Logging**: Complete audit trail of all library operations
- **Role-Based Access**: Admin, Librarian, and Patron roles

### User Roles

1. **Admin**: Full system access
   - All librarian features
   - User management
   - System configuration

2. **Librarian**: Circulation and management
   - Checkout/return books
   - Manage holds
   - Process fines
   - View all loans and statistics

3. **Patron**: Self-service features
   - Browse catalog
   - Place holds
   - View own loans and fines
   - Renew books

## Installation

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Build Assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   ```

## Default Users

After seeding, you can login with:

- **Admin**: `admin@library.com` / `password`
- **Librarian**: `librarian@library.com` / `password`
- **Patrons**: Created via factory (check database)

## Database Structure

### Tables

- **books**: Book catalog information
- **copies**: Individual book copies with barcodes
- **loans**: Checkout records
- **holds**: Book reservation queue
- **fines**: Overdue and other fines
- **audit_logs**: System activity logs
- **users**: Extended with role, phone, address

### Key Relationships

- Book → hasMany → Copies
- Book → hasMany → Holds
- Copy → belongsTo → Book
- Copy → hasMany → Loans
- Loan → belongsTo → Copy, User
- Hold → belongsTo → Book, User
- Fine → belongsTo → User, Loan

## Usage Guide

### For Librarians

#### Checkout a Book
1. Navigate to `/library/circulation/checkout`
2. Scan or enter book barcode
3. Search and select patron
4. Set loan period (default: 14 days)
5. Click "Checkout"

#### Return a Book
1. Navigate to `/library/circulation/return`
2. Scan or enter book barcode
3. System automatically processes return
4. Fines are calculated if overdue

#### Manage Holds
- View pending holds in dashboard
- When book becomes available, first hold is marked "ready"
- Patron can then checkout the book

### For Patrons

#### Browse Catalog
1. Navigate to `/library/books`
2. Search by title, author, or ISBN
3. Filter by category
4. Click book to view details

#### Place a Hold
1. Find book in catalog
2. If no copies available, click "Place Hold"
3. You'll be notified when book is ready

#### View Your Loans
- Dashboard shows active loans
- See due dates and overdue status
- Renew books (if no holds pending)

## API Endpoints

All API routes are prefixed with `/api/library` and require authentication.

### Books
- `GET /api/library/books` - List books (with pagination)
- `POST /api/library/books` - Create book
- `GET /api/library/books/{id}` - Show book details
- `PUT /api/library/books/{id}` - Update book
- `DELETE /api/library/books/{id}` - Delete book

### Circulation
- `POST /api/library/circulation/checkout` - Checkout book
- `POST /api/library/circulation/{loan}/return` - Return book
- `POST /api/library/circulation/{loan}/renew` - Renew loan
- `POST /api/library/circulation/{loan}/lost` - Mark as lost

### Holds
- `GET /api/library/holds` - List holds
- `POST /api/library/holds` - Place hold
- `GET /api/library/holds/{id}` - Show hold
- `POST /api/library/holds/{id}/cancel` - Cancel hold

### Fines
- `GET /api/library/fines` - List fines
- `GET /api/library/fines/{id}` - Show fine
- `POST /api/library/fines/{id}/pay` - Mark fine as paid
- `POST /api/library/fines/{id}/waive` - Waive fine

## Business Rules

### Checkout Rules
- Maximum 10 active loans per patron
- Default loan period: 14 days
- Books with pending holds cannot be renewed

### Fine Calculation
- Overdue: $0.50 per day
- Lost book: $50.00 replacement fee
- Fines due 30 days after creation

### Hold System
- Hold expires after 7 days if not fulfilled
- Position in queue based on request date
- First hold gets priority when book available

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
php artisan pint
```

### Creating New Features

1. **Models**: Use Eloquent relationships
2. **Services**: Business logic in `app/Services`
3. **Controllers**: API endpoints
4. **Livewire**: UI components
5. **Migrations**: Database changes

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── BookController.php
│       ├── CirculationController.php
│       ├── HoldController.php
│       └── FineController.php
├── Livewire/
│   ├── Books/
│   ├── Circulation/
│   └── Dashboard/
├── Models/
│   ├── Book.php
│   ├── Copy.php
│   ├── Loan.php
│   ├── Hold.php
│   ├── Fine.php
│   └── AuditLog.php
└── Services/
    └── LibraryService.php

database/
├── migrations/
└── seeders/
    └── LibrarySeeder.php

resources/
└── views/
    └── livewire/
```

## Security Features

- Role-based access control (RBAC)
- Authentication via Laravel Fortify
- Authorization gates for librarian/admin actions
- Audit logging for all operations
- Input validation on all endpoints

## Future Enhancements

- Email notifications for due dates and holds
- Barcode scanner integration
- Advanced reporting and analytics
- Multi-branch library support
- RFID integration
- Mobile app API
- Public catalog view

## Support

For issues or questions, please check:
- Laravel documentation: https://laravel.com/docs
- Livewire documentation: https://livewire.laravel.com
- Flux UI documentation: https://flux.laravel.com

## License

This project is open-sourced software licensed under the MIT license.

