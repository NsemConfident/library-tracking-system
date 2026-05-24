# Library Tracking System Features by User Type

This document summarizes the main features available to each user role in the library tracking system.

## User Roles

- **Admin**: Full administrative access through the Filament admin panel plus librarian capabilities.
- **Librarian**: Library staff who can perform circulation tasks and view reports.
- **Patron**: Library members who can browse the catalog and manage their own loans, holds, and fines.

## Shared Features (Authenticated Users)

- **Dashboard**
  - Access a summary page with quick navigation and status information.
- **Book Catalog**
  - Browse all library books with search and category filters.
  - View book details, status, availability, and cover images.
- **Profile & Settings**
  - Edit user profile information.
  - Change password, appearance settings, and two-factor authentication settings.
- **Authenticated API Access**
  - Authenticated users can use library API endpoints for books, circulation, holds, and fines.

## Patron Features

- **My Loans**
  - View a personal list of active and past loans.
  - See due dates, loan status, and current loan counts.
- **My Holds**
  - View holds placed by the patron.
  - Track hold statuses and waiting lists.
- **My Fines**
  - View outstanding fines and payment status.
  - Access fine details and payment workflow.

## Librarian Features

- **Checkout**
  - Process book checkout transactions for patrons.
  - Create new loan records via the circulation interface.
- **Return**
  - Process returned books and update loan status.
  - Manage returns through the librarian circulation pages.
- **Book Details**
  - Access book detail pages from the catalog.
- **Book Catalog**
  - Use the same book catalog interface as patrons, with library circulation context.
 - **RFID Circulation**
   - Connect an Arduino RFID reader and scan student cards or book tags.
   - Start and end library sessions by scanning patron RFID cards.
   - Borrow or return books by scanning book RFID tags while a student session is active.
   - The system validates active student sessions and provides scan-based checkout/return workflows.

## Admin Features

- **Filament Admin Panel**
  - Full access to the Filament admin dashboard.
  - Manage the following library resources:
    - Books
    - Copies
    - Loans
    - Holds
    - Fines
    - Users
    - Settings
- **Manage Books**
  - Create, edit, and delete book records.
  - Upload and manage book cover images.
- **Manage Copies**
  - Create and maintain individual physical book copy records.
- **Manage Loans**
  - View, edit, and create loan records for library circulation.
- **Manage Holds**
  - View and manage hold requests from patrons.
- **Manage Fines**
  - Create fines, mark fines paid, and waive fines.
- **Manage Users**
  - Create and edit library users.
  - Assign roles such as admin, librarian, or patron.
- **Manage Settings**
  - Configure library settings such as maximum loans per patron.
- **Analytics Dashboard Widgets**
  - View widgets for library statistics, loans trends, book categories, fines, and popular books.
 - **RFID UID Management**
   - Assign and edit RFID UIDs for patrons in the Users resource.
   - Assign and edit RFID UIDs for book copies in the Copies resource.
   - Prevent duplicate RFID assignments between student cards and book copy tags.

## Role Relationship Notes

- **Admin** includes all librarian permissions and adds administrative resource management.
- **Librarian** includes circulation actions and access to library workflows but does not include the full Filament resource management interface unless assigned admin role.
- **Patron** is limited to personal account and library browsing features.
