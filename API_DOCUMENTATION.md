# Library Management System - API Documentation

## Base Information

**Base URL**: `/api/library`  
**Authentication**: Bearer Token (Laravel Sanctum)  
**Content-Type**: `application/json`

## Authentication

All endpoints require authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your-access-token}
```

To obtain a token, use Laravel's authentication endpoints or login via the web interface.

---

## Books API

### List Books

**Endpoint**: `GET /api/library/books`

**Query Parameters**:
- `page` (integer, optional): Page number (default: 1)
- `per_page` (integer, optional): Items per page (default: 15, max: 100)
- `search` (string, optional): Search in title, author, or ISBN
- `category` (string, optional): Filter by category

**Example Request**:
```bash
GET /api/library/books?search=harry&category=Fiction&page=1
```

**Example Response**:
```json
{
  "data": [
    {
      "id": 1,
      "title": "Harry Potter and the Philosopher's Stone",
      "author": "J.K. Rowling",
      "isbn": "9780747532699",
      "category": "Fiction",
      "publisher": "Bloomsbury",
      "published_year": 1997,
      "pages": 223,
      "language": "en",
      "description": "...",
      "copies_count": 5,
      "available_copies_count": 3,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "current_page": 1,
  "per_page": 15,
  "total": 1,
  "last_page": 1
}
```

### Get Book

**Endpoint**: `GET /api/library/books/{id}`

**Example Response**:
```json
{
  "id": 1,
  "title": "Book Title",
  "author": "Author Name",
  "isbn": "1234567890",
  "category": "Fiction",
  "publisher": "Publisher Name",
  "published_year": 2020,
  "pages": 300,
  "language": "en",
  "description": "Book description...",
  "cover_image": "path/to/image.jpg",
  "copies_count": 5,
  "available_copies_count": 3,
  "copies": [
    {
      "id": 1,
      "barcode": "BOOK001",
      "status": "available",
      "location": "Shelf A1"
    }
  ],
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Create Book

**Endpoint**: `POST /api/library/books`

**Request Body**:
```json
{
  "title": "Book Title",
  "author": "Author Name",
  "isbn": "1234567890",
  "category": "Fiction",
  "publisher": "Publisher Name",
  "published_year": 2020,
  "pages": 300,
  "language": "en",
  "description": "Book description..."
}
```

**Required Fields**: `title`, `author`

**Response**: `201 Created` with book object

### Update Book

**Endpoint**: `PUT /api/library/books/{id}`

**Request Body**: Same as Create Book (all fields optional)

**Response**: `200 OK` with updated book object

### Delete Book

**Endpoint**: `DELETE /api/library/books/{id}`

**Response**: `204 No Content`

---

## Circulation API

### Checkout Book

**Endpoint**: `POST /api/library/circulation/checkout`

**Request Body**:
```json
{
  "barcode": "BOOK001",
  "user_id": 5,
  "loan_days": 14
}
```

**Required Fields**: `barcode`, `user_id`  
**Optional Fields**: `loan_days` (uses default from settings if omitted)

**Example Response**:
```json
{
  "success": true,
  "message": "Book checked out successfully",
  "loan": {
    "id": 1,
    "copy_id": 1,
    "user_id": 5,
    "checkout_date": "2024-12-14",
    "due_date": "2024-12-28",
    "status": "active",
    "copy": {
      "id": 1,
      "barcode": "BOOK001",
      "book": {
        "id": 1,
        "title": "Book Title"
      }
    },
    "user": {
      "id": 5,
      "name": "John Doe"
    }
  }
}
```

**Error Response** (422):
```json
{
  "success": false,
  "message": "This copy is not available for checkout."
}
```

### Return Book

**Endpoint**: `POST /api/library/circulation/{loan}/return`

**Example Response**:
```json
{
  "success": true,
  "message": "Book returned successfully",
  "loan": {
    "id": 1,
    "status": "returned",
    "returned_date": "2024-12-14",
    ...
  }
}
```

**Note**: If book is overdue, fine is automatically created and user is notified.

### Renew Loan

**Endpoint**: `POST /api/library/circulation/{loan}/renew`

**Request Body**:
```json
{
  "additional_days": 14
}
```

**Optional Fields**: `additional_days` (uses default from settings if omitted)

**Example Response**:
```json
{
  "success": true,
  "message": "Loan renewed successfully",
  "loan": {
    "id": 1,
    "due_date": "2025-01-11",  // Extended by 14 days
    ...
  }
}
```

**Error Response** (422):
```json
{
  "success": false,
  "message": "Cannot renew: there are pending holds for this book."
}
```

### Mark Loan as Lost

**Endpoint**: `POST /api/library/circulation/{loan}/lost`

**Example Response**:
```json
{
  "success": true,
  "message": "Loan marked as lost"
}
```

**Note**: Automatically creates lost book fine ($50.00 by default).

---

## Holds API

### List Holds

**Endpoint**: `GET /api/library/holds`

**Query Parameters**:
- `status` (string, optional): Filter by status (`pending`, `ready`, `fulfilled`, `cancelled`)
- `user_id` (integer, optional): Filter by user
- `book_id` (integer, optional): Filter by book

**Example Response**:
```json
{
  "data": [
    {
      "id": 1,
      "book_id": 10,
      "user_id": 5,
      "requested_date": "2024-12-10",
      "expiry_date": "2024-12-17",
      "status": "pending",
      "position": 1,
      "book": {
        "id": 10,
        "title": "Book Title"
      },
      "user": {
        "id": 5,
        "name": "John Doe"
      }
    }
  ]
}
```

### Place Hold

**Endpoint**: `POST /api/library/holds`

**Request Body**:
```json
{
  "book_id": 10,
  "user_id": 5
}
```

**Required Fields**: `book_id`  
**Optional Fields**: `user_id` (uses authenticated user if omitted)

**Example Response**:
```json
{
  "id": 1,
  "book_id": 10,
  "user_id": 5,
  "requested_date": "2024-12-14",
  "expiry_date": "2024-12-21",
  "status": "pending",
  "position": 1,
  "book": { ... },
  "user": { ... }
}
```

**Error Response** (422):
```json
{
  "success": false,
  "message": "Book has available copies. No hold needed."
}
```

### Get Hold

**Endpoint**: `GET /api/library/holds/{id}`

**Example Response**: Same structure as hold object in List Holds

### Cancel Hold

**Endpoint**: `POST /api/library/holds/{hold}/cancel`

**Example Response**:
```json
{
  "success": true,
  "message": "Hold cancelled successfully"
}
```

---

## Fines API

### List Fines

**Endpoint**: `GET /api/library/fines`

**Query Parameters**:
- `status` (string, optional): Filter by status (`pending`, `paid`, `waived`, `cancelled`)
- `user_id` (integer, optional): Filter by user

**Example Response**:
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 5,
      "loan_id": 10,
      "amount": "15.50",
      "type": "overdue",
      "status": "pending",
      "due_date": "2025-01-13",
      "description": "Overdue fine for 31 days",
      "loan": {
        "id": 10,
        "copy": {
          "book": {
            "title": "Book Title"
          }
        }
      },
      "user": {
        "id": 5,
        "name": "John Doe"
      }
    }
  ]
}
```

### Get Fine

**Endpoint**: `GET /api/library/fines/{id}`

**Example Response**: Same structure as fine object in List Fines

### Mark Fine as Paid

**Endpoint**: `POST /api/library/fines/{fine}/pay`

**Request Body**:
```json
{
  "paid_date": "2024-12-14"
}
```

**Optional Fields**: `paid_date` (uses today if omitted)

**Example Response**:
```json
{
  "success": true,
  "message": "Fine marked as paid",
  "fine": {
    "id": 1,
    "status": "paid",
    "paid_date": "2024-12-14",
    ...
  }
}
```

### Waive Fine

**Endpoint**: `POST /api/library/fines/{fine}/waive`

**Example Response**:
```json
{
  "success": true,
  "message": "Fine waived",
  "fine": {
    "id": 1,
    "status": "waived",
    ...
  }
}
```

---

## Error Handling

### Standard Error Format

All errors return this format:

```json
{
  "success": false,
  "message": "Error message describing what went wrong"
}
```

### HTTP Status Codes

- `200 OK`: Successful request
- `201 Created`: Resource created successfully
- `204 No Content`: Successful deletion
- `400 Bad Request`: Invalid request format
- `401 Unauthorized`: Authentication required
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation error or business rule violation
- `500 Internal Server Error`: Server error

### Common Error Messages

**Checkout Errors**:
- `"This copy is not available for checkout."`
- `"User has reached the maximum number of active loans (10)."`
- `"Copy not found."`
- `"User not found."`

**Return Errors**:
- `"This loan is not active."`
- `"Loan not found."`

**Renewal Errors**:
- `"Only active loans can be renewed."`
- `"Cannot renew: there are pending holds for this book."`

**Hold Errors**:
- `"Book has available copies. No hold needed."`
- `"User already has an active hold for this book."`
- `"Book not found."`

**Fine Errors**:
- `"Fine not found."`
- `"Fine is already paid."`

---

## Rate Limiting

**Note**: Rate limiting is not currently implemented but recommended for production.

Recommended limits:
- Authenticated users: 60 requests per minute
- Unauthenticated: 10 requests per minute

---

## Pagination

List endpoints support pagination:

**Query Parameters**:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

**Response Format**:
```json
{
  "data": [...],
  "current_page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7,
  "from": 1,
  "to": 15
}
```

---

## Filtering and Searching

### Search

Most list endpoints support search via `search` parameter:
- Searches in relevant fields (title, author, name, etc.)
- Case-insensitive
- Partial matching

### Filtering

Use query parameters to filter results:
- `status`: Filter by status
- `user_id`: Filter by user
- `book_id`: Filter by book
- `category`: Filter by category

### Sorting

Default sorting is applied. Custom sorting may be added in future versions.

---

## Examples

### Complete Checkout Flow

```bash
# 1. Find book
GET /api/library/books?search=harry

# 2. Checkout book
POST /api/library/circulation/checkout
{
  "barcode": "BOOK001",
  "user_id": 5,
  "loan_days": 14
}

# 3. Return book (later)
POST /api/library/circulation/1/return
```

### Hold and Checkout Flow

```bash
# 1. Place hold when book unavailable
POST /api/library/holds
{
  "book_id": 10
}

# 2. Check hold status
GET /api/library/holds?user_id=5

# 3. When ready, checkout the book
POST /api/library/circulation/checkout
{
  "barcode": "BOOK001",
  "user_id": 5
}
```

---

## Testing the API

### Using cURL

```bash
# List books
curl -X GET "http://localhost:8000/api/library/books" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# Checkout book
curl -X POST "http://localhost:8000/api/library/circulation/checkout" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "barcode": "BOOK001",
    "user_id": 5
  }'
```

### Using Postman

1. Set base URL: `http://localhost:8000/api/library`
2. Add header: `Authorization: Bearer {token}`
3. Add header: `Accept: application/json`
4. Create requests for each endpoint

---

## Webhooks

**Note**: Webhooks are not currently implemented.

Potential webhook events:
- `loan.created`
- `loan.returned`
- `loan.overdue`
- `hold.ready`
- `fine.created`

---

## Versioning

**Current Version**: v1

API versioning not yet implemented. All endpoints are under `/api/library`.

---

## Support

For API support:
- Review this documentation
- Check main documentation file
- Review error messages for guidance
- Contact system administrator

