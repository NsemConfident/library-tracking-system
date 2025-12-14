# Library Management System - User Guide

## For Patrons

### Getting Started

1. **Login**: Visit the website and login with your credentials
2. **Dashboard**: View your library statistics and recent loans
3. **Navigation**: Use the sidebar menu to access different features

### Browsing Books

#### Search for Books

1. Click **Book Catalog** in the sidebar
2. Use the search bar to find books by:
   - Book title
   - Author name
   - ISBN number
3. Results update as you type

#### Filter by Category

1. In the Book Catalog page
2. Use the **Category** dropdown
3. Select a category to filter results
4. Select "All Categories" to remove filter

#### View Book Details

1. Click on any book card or **View Details** button
2. See complete book information:
   - Title, author, description
   - ISBN, publisher, publication year
   - Number of available copies
   - Copy status and locations

### Placing Holds

#### When to Place a Hold

- Book has no available copies
- You want to reserve a book that's currently checked out

#### How to Place a Hold

1. Find the book in the catalog
2. If no copies are available, you'll see a **Place Hold** button
3. Click **Place Hold**
4. You'll receive a confirmation message
5. You'll receive an email when the book is ready

#### Managing Your Holds

1. Go to **My Holds** in the sidebar
2. View all your active holds:
   - **Pending**: Waiting in queue
   - **Ready**: Available for checkout
3. See your position in the queue
4. Cancel holds if no longer needed

### Managing Loans

#### View Your Loans

1. Go to **My Loans** in the sidebar
2. See all active loans:
   - Book title and author
   - Checkout date
   - Due date
   - Days overdue (if applicable)
   - Status (Active/Overdue)

#### Renew a Book

1. Go to **My Loans**
2. Find the book you want to renew
3. Click **Renew** button
4. Loan is extended automatically
5. New due date is displayed

**Note**: You cannot renew if:
- The book has pending holds from other patrons
- The loan is not active

#### Understanding Due Dates

- **Green badge**: Book is due soon or on time
- **Red badge**: Book is overdue
- Days overdue are shown in red text

### Managing Fines

#### View Your Fines

1. Go to **My Fines** in the sidebar
2. See summary:
   - Total pending fines
   - Total paid fines
3. View detailed list:
   - Book information
   - Fine type (Overdue/Lost)
   - Amount
   - Due date
   - Description

#### Understanding Fines

- **Overdue fines**: Calculated per day after due date
- **Lost book fees**: Charged when book is marked as lost
- Fines are due 30 days after creation
- Contact the library to pay fines

### Tips

- Renew books before they're due to avoid fines
- Check your email for hold notifications
- Return books on time to avoid overdue fines
- Contact library staff if you have questions

---

## For Librarians

### Circulation Operations

#### Checking Out Books

1. Navigate to **Checkout** (or `/library/circulation/checkout`)
2. **Enter Barcode**:
   - Scan book barcode with scanner, OR
   - Type barcode manually
3. **Find Patron**:
   - Type patron name or email in search box
   - Select patron from results
   - Patron information is displayed
4. **Set Loan Period**:
   - Default period is shown (from settings)
   - Adjust if needed (1-90 days)
5. **Complete Checkout**:
   - Click **Checkout Book**
   - Success message appears
   - Form resets for next checkout

#### Returning Books

1. Navigate to **Return** (or `/library/circulation/return`)
2. **Enter Barcode**:
   - Scan or type book barcode
3. **Automatic Processing**:
   - System finds active loan
   - Processes return
   - Calculates fines if overdue
   - Notifies next patron if hold exists
4. **Success Message**:
   - Confirmation appears
   - Any fines are displayed

### Using the Admin Panel

#### Accessing Admin Panel

1. Navigate to `/admin`
2. Login with librarian/admin credentials
3. Dashboard shows:
   - Library statistics
   - Charts and graphs
   - Recent activity

#### Managing Books

1. Go to **Library → Books**
2. **Add New Book**:
   - Click **New Book**
   - Fill in details (title, author, ISBN, etc.)
   - Save
3. **Add Copies**:
   - Go to **Library → Copies**
   - Click **New Copy**
   - Select book
   - Enter barcode and location
   - Set initial status

#### Managing Loans

1. Go to **Library → Loans**
2. **View All Loans**:
   - See all active and returned loans
   - Filter by status, user, or date
   - Search by book title or patron name
3. **Actions**:
   - **Return**: Mark loan as returned
   - **Renew**: Extend loan period
   - **Mark as Lost**: If book cannot be found

#### Managing Holds

1. Go to **Library → Holds**
2. **View Holds**:
   - See all pending and ready holds
   - Filter by status or book
3. **When Book Returns**:
   - System automatically marks first hold as "ready"
   - Patron receives email notification
   - Patron can then checkout the book

#### Managing Fines

1. Go to **Library → Fines**
2. **View Fines**:
   - See all pending, paid, and waived fines
   - Filter by status or user
3. **Process Payments**:
   - Click on fine
   - Click **Mark as Paid**
   - Enter payment date
4. **Waive Fines**:
   - Click **Waive Fine** for special circumstances
   - Fine is marked as waived

#### Managing Settings

1. Go to **System → Settings**
2. **Edit Configuration**:
   - Loan periods
   - Fine rates
   - Hold expiry
   - Notification timing
3. **Changes Take Effect Immediately**

### Best Practices

- Always verify patron before checkout
- Check for existing holds before renewing
- Process returns promptly
- Review overdue loans regularly
- Communicate with patrons about fines

---

## For Administrators

### System Configuration

#### Managing Settings

1. Go to **System → Settings** in admin panel
2. Settings are organized by group:
   - **Loans**: Loan periods, max loans
   - **Fines**: Fine rates, fees
   - **Holds**: Hold expiry
   - **Notifications**: Email timing
3. Click **Edit** on any setting
4. Change value and save
5. Changes apply immediately

#### User Management

1. Go to **Users** in admin panel
2. **Create Users**:
   - Click **New User**
   - Fill in name, email, password
   - Set role: `admin`, `librarian`, or `patron`
   - Save
3. **Edit Users**:
   - Click on user
   - Update information
   - Change role if needed

### Monitoring

#### Dashboard Statistics

- **Total Books**: Number of books in catalog
- **Total Copies**: Number of physical copies
- **Active Loans**: Currently checked out books
- **Pending Holds**: Books reserved by patrons
- **Pending Fines**: Unpaid fines amount
- **Total Patrons**: Number of registered users

#### Charts and Analytics

- **Loans Over Time**: Line chart showing loan trends
- **Books by Category**: Pie chart of catalog distribution
- **Fines by Status**: Polar area chart of fine statuses
- **Popular Books**: Bar chart of most checked-out books

### Maintenance

#### Database Maintenance

- Regular backups recommended
- Review audit logs periodically
- Clean up old records if needed

#### Email Configuration

- Verify email settings in `.env`
- Test email sending
- Monitor email queue

#### Scheduled Tasks

- Due date reminders: Daily at 9:00 AM
- Overdue notifications: Daily at 9:00 AM
- Ensure cron job is configured

### Security

#### Access Control

- Review user roles regularly
- Remove inactive users
- Monitor admin access

#### Audit Logs

- All operations are logged
- Review logs in admin panel
- Track system activity

---

## Quick Reference

### Keyboard Shortcuts

- **Search**: Start typing in search boxes
- **Navigation**: Use sidebar menu
- **Refresh**: F5 or browser refresh

### Common Tasks

#### Patron Tasks
- Browse catalog: `/library/books`
- View loans: `/library/my-loans`
- View holds: `/library/my-holds`
- View fines: `/library/my-fines`

#### Librarian Tasks
- Checkout: `/library/circulation/checkout`
- Return: `/library/circulation/return`
- Admin panel: `/admin`

### Contact Information

For technical support or questions:
- Check system documentation
- Contact system administrator
- Review help sections in admin panel

---

## FAQ

### For Patrons

**Q: How long can I keep a book?**
A: Default loan period is 14 days, but this can be configured by your library.

**Q: Can I renew a book?**
A: Yes, if there are no pending holds. Click "Renew" in My Loans.

**Q: What happens if I return a book late?**
A: Overdue fines are calculated automatically. Check "My Fines" for details.

**Q: How do I know when my hold is ready?**
A: You'll receive an email notification when the book becomes available.

**Q: Can I cancel a hold?**
A: Yes, go to "My Holds" and click "Cancel" on any pending or ready hold.

### For Librarians

**Q: How do I change loan periods?**
A: Go to System → Settings in admin panel, edit `loan_period_days`.

**Q: Can I override the default loan period?**
A: Yes, when checking out, you can set a custom loan period (1-90 days).

**Q: How are fines calculated?**
A: Automatically when overdue books are returned. Rate is configurable in settings.

**Q: What if a book is lost?**
A: In admin panel, go to the loan and click "Mark as Lost". Fine is created automatically.

**Q: How do I process a fine payment?**
A: Go to Library → Fines, click on the fine, then "Mark as Paid".

---

## Support

For additional help:
- Review this documentation
- Check the main documentation file
- Contact your library administrator
- Review system logs if you have access

