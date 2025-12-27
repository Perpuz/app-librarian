# Perpuz Library Management System - API Documentation

Base URL: `http://localhost:8081/api`

> **Note:** Endpoints yang memerlukan autentikasi harus menyertakan header `Authorization: Bearer {token}`

---

## Table of Contents

1. [Authentication](#1-authentication)
2. [Books](#2-books)
3. [Members](#3-members)
4. [Transactions](#4-transactions)
5. [Recommendations](#5-recommendations-daily-recommendations)
6. [Dashboard](#6-dashboard)
7. [OpenLibrary Integration](#7-openlibrary-integration)
8. [External Integration](#8-external-integration)

---

## 1. Authentication

### Login
Login untuk mendapatkan JWT token (admin).

- **Endpoint**: `POST /auth/login`
- **Authentication**: None
- **Body**:
```json
{
  "username": "admin",
  "password": "admin123"
}
```
- **Response Success (200)**:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "username": "admin"
  }
}
```

### Register
Register akun admin baru.

- **Endpoint**: `POST /auth/register`
- **Authentication**: None
- **Body**:
```json
{
  "username": "admin",
  "password": "admin123"
}
```

---

## 2. Books

### Get All Books
Mendapatkan semua buku atau search berdasarkan query.

- **Endpoint**: `GET /books`
- **Authentication**: Required (JWT)
- **Parameters (Optional)**:
  - `q`: Search query (Title, Author, or ISBN)
- **Example**: 
  - `GET /books` (Semua buku)
  - `GET /books?q=harry` (Search "harry")
- **Response Success (200)**:
```json
[
  {
    "id": 1,
    "title": "Harry Potter and the Philosopher's Stone",
    "author": "J.K. Rowling",
    "isbn": "978-0-7475-3269-9",
    "publisher": "Bloomsbury",
    "year": "1997",
    "stock": 5,
    "created_at": "2024-01-01 00:00:00"
  }
]
```

### Get Book by ID
Mendapatkan detail buku berdasarkan ID.

- **Endpoint**: `GET /books/{id}`
- **Authentication**: Required (JWT)
- **Example**: `GET /books/1`
- **Response Success (200)**:
```json
{
  "id": 1,
  "title": "Harry Potter and the Philosopher's Stone",
  "author": "J.K. Rowling",
  "isbn": "978-0-7475-3269-9",
  "publisher": "Bloomsbury",
  "year": "1997",
  "stock": 5,
  "created_at": "2024-01-01 00:00:00"
}
```

### Create Book
Menambahkan buku baru.

- **Endpoint**: `POST /books`
- **Authentication**: Required (JWT)
- **Body**:
```json
{
  "title": "The Great Gatsby",
  "author": "F. Scott Fitzgerald",
  "isbn": "978-0-7432-7356-5",
  "publisher": "Scribner",
  "year": "1925",
  "stock": 10
}
```
- **Response Success (201)**:
```json
{
  "id": 2,
  "title": "The Great Gatsby",
  "author": "F. Scott Fitzgerald",
  "isbn": "978-0-7432-7356-5",
  "publisher": "Scribner",
  "year": "1925",
  "stock": 10
}
```

### Update Book
Update data buku berdasarkan ID.

- **Endpoint**: `PUT /books/{id}`
- **Authentication**: Required (JWT)
- **Body**:
```json
{
  "stock": 15
}
```

### Delete Book
Hapus buku berdasarkan ID.

- **Endpoint**: `DELETE /books/{id}`
- **Authentication**: Required (JWT)
- **Response Success (200)**:
```json
{
  "id": 2,
  "message": "Deleted"
}
```

---

## 3. Members

### Get All Members
Mendapatkan semua anggota perpustakaan.

- **Endpoint**: `GET /members`
- **Authentication**: Required (JWT)
- **Response Success (200)**:
```json
[
  {
    "id": 1,
    "member_code": "M001",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "08123456789",
    "status": "active",
    "created_at": "2024-01-01 00:00:00"
  }
]
```

### Get Member by ID
Mendapatkan detail member berdasarkan ID.

- **Endpoint**: `GET /members/{id}`
- **Authentication**: Required (JWT)

### Create Member
Menambahkan member baru.

- **Endpoint**: `POST /members`
- **Authentication**: Required (JWT)
- **Body**:
```json
{
  "member_code": "M002",
  "name": "Jane Doe",
  "email": "jane@example.com",
  "phone": "08198765432"
}
```

### Update Member
Update data member.

- **Endpoint**: `PUT /members/{id}`
- **Authentication**: Required (JWT)
- **Body**:
```json
{
  "name": "Jane Smith",
  "status": "inactive"
}
```

### Delete Member
Hapus member.

- **Endpoint**: `DELETE /members/{id}`
- **Authentication**: Required (JWT)

---

## 4. Transactions

### Get All Transactions
Mendapatkan semua transaksi dengan detail buku dan member.

- **Endpoint**: `GET /transactions`
- **Authentication**: Required (JWT)
- **Parameters (Optional)**:
  - `member_code`: Filter by member code
- **Response Success (200)**:
```json
[
  {
    "id": 1,
    "member_id": 1,
    "book_id": 1,
    "book_title": "Harry Potter",
    "member_name": "John Doe",
    "borrow_date": "2024-12-01",
    "due_date": "2024-12-15",
    "return_date": null,
    "status": "borrowed",
    "fine": "0.00",
    "created_at": "2024-12-01 10:00:00"
  }
]
```

### Get Transaction by ID
Mendapatkan detail transaksi.

- **Endpoint**: `GET /transactions/{id}`
- **Authentication**: Required (JWT)

### Create Transaction (Borrow Book)
Membuat transaksi peminjaman buku.

- **Endpoint**: `POST /transactions`
- **Authentication**: Required (JWT)
- **Body**:
```json
{
  "member_code": "M001",
  "book_identifier": "978-0-7475-3269-9",
  "borrow_date": "2024-12-27",
  "due_date": "2025-01-10"
}
```
- **Note**: `book_identifier` bisa berupa ISBN atau Title (exact match)
- **Response Success (201)**:
```json
{
  "id": 2,
  "member_id": 1,
  "book_id": 1,
  "borrow_date": "2024-12-27",
  "due_date": "2025-01-10",
  "status": "borrowed"
}
```

### Update Transaction (Return Book)
Update transaksi untuk return buku. Fine otomatis dihitung jika terlambat.

- **Endpoint**: `PUT /transactions/{id}`
- **Authentication**: Required (JWT)
- **Body**:
```json
{
  "status": "returned",
  "return_date": "2025-01-12"
}
```
- **Note**: 
  - Fine dihitung otomatis: **Rp 1.000 per hari** keterlambatan
  - Stok buku otomatis bertambah saat return
- **Response Success (200)**:
```json
{
  "status": "returned",
  "return_date": "2025-01-12",
  "fine": "2000.00"
}
```

### Delete Transaction
Hapus transaksi.

- **Endpoint**: `DELETE /transactions/{id}`
- **Authentication**: Required (JWT)

---

## 5. Recommendations (Daily Recommendations)

### Get Daily Recommendations (Public)
Mendapatkan rekomendasi buku harian (maksimal 5 buku).

- **Endpoint**: `GET /recommendations/daily`
- **Authentication**: None (Public)
- **Response Success (200)**:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "book_id": 5,
      "display_order": 1,
      "is_active": 1,
      "title": "The Great Gatsby",
      "author": "F. Scott Fitzgerald",
      "isbn": "978-0-7432-7356-5",
      "publisher": "Scribner",
      "year": "1925",
      "stock": 5,
      "created_at": "2024-12-27 10:00:00"
    }
  ],
  "count": 1
}
```

### Get All Recommendations (Admin)
Mendapatkan semua rekomendasi untuk admin.

- **Endpoint**: `GET /recommendations`
- **Authentication**: Required (JWT)
- **Response Success (200)**:
```json
[
  {
    "id": 1,
    "book_id": 5,
    "display_order": 1,
    "is_active": 1,
    "title": "The Great Gatsby",
    "author": "F. Scott Fitzgerald",
    "isbn": "978-0-7432-7356-5",
    "created_at": "2024-12-27 10:00:00"
  }
]
```

### Add Book to Recommendations
Menambahkan buku ke rekomendasi harian (maksimal 5 buku).

- **Endpoint**: `POST /recommendations`
- **Authentication**: Required (JWT)
- **Body**:
```json
{
  "book_id": 5
}
```
- **Response Success (201)**:
```json
{
  "id": 1,
  "book_id": 5,
  "display_order": 1,
  "is_active": 1
}
```
- **Response Error (400)** - Jika sudah 5 buku:
```json
{
  "error": "Maximum 5 recommendations allowed. Please remove one before adding a new recommendation."
}
```
- **Response Error (400)** - Jika buku sudah ada:
```json
{
  "error": "This book is already in recommendations"
}
```

### Remove Recommendation
Menghapus buku dari rekomendasi.

- **Endpoint**: `DELETE /recommendations/{id}`
- **Authentication**: Required (JWT)
- **Response Success (200)**:
```json
{
  "id": 1,
  "message": "Recommendation removed"
}
```
- **Note**: Setelah delete, display_order otomatis di-reorder

### Reorder Recommendations
Update urutan tampilan rekomendasi.

- **Endpoint**: `PUT /recommendations/reorder`
- **Authentication**: Required (JWT)
- **Body**:
```json
{
  "recommendations": [
    { "id": 1, "display_order": 2 },
    { "id": 2, "display_order": 1 }
  ]
}
```
- **Response Success (200)**:
```json
{
  "message": "Order updated successfully"
}
```

---

## 6. Dashboard

### Get Dashboard Statistics
Mendapatkan statistik dashboard dengan recent transactions.

- **Endpoint**: `GET /dashboard`
- **Authentication**: Required (JWT)
- **Response Success (200)**:
```json
{
  "total_books": 150,
  "active_members": 45,
  "total_transactions": 230,
  "recent_transactions": [
    {
      "id": 1,
      "book_title": "Harry Potter",
      "member_name": "John Doe",
      "borrow_date": "2024-12-27",
      "due_date": "2025-01-10",
      "return_date": null,
      "status": "borrowed",
      "fine": "0.00"
    }
  ]
}
```

---

## 7. OpenLibrary Integration

### Search Books from OpenLibrary
Search buku dari OpenLibrary API eksternal.

- **Endpoint**: `GET /openlibrary/search`
- **Authentication**: Required (JWT)
- **Parameters**:
  - `q`: Search query (required)
- **Example**: `GET /openlibrary/search?q=harry+potter`
- **Response Success (200)**:
```json
{
  "numFound": 100,
  "docs": [
    {
      "title": "Harry Potter and the Philosopher's Stone",
      "author_name": ["J.K. Rowling"],
      "isbn": ["978-0-7475-3269-9"],
      "publisher": ["Bloomsbury"],
      "first_publish_year": 1997
    }
  ]
}
```

---

## 8. External Integration

### Get Users Data (For External Systems)
Endpoint untuk integrasi sistem eksternal mengambil data user.

- **Endpoint**: `GET /integration/users`
- **Authentication**: Custom Header `X-INTEGRATION-SECRET`
- **Headers**:
  - `X-INTEGRATION-SECRET`: Secret key dari `.env` file
- **Response Success (200)**:
```json
[
  {
    "id": 1,
    "username": "admin",
    "created_at": "2024-01-01 00:00:00"
  }
]
```

---

## Error Responses

Semua endpoint dapat mengembalikan error response dengan format:

```json
{
  "error": "Error message here"
}
```

### Common HTTP Status Codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request / Validation Error
- `401` - Unauthorized (Invalid/Missing Token)
- `404` - Not Found
- `500` - Internal Server Error

---

## Authentication Flow

1. **Login** menggunakan `POST /auth/login` untuk mendapatkan token
2. **Simpan token** di localStorage atau session
3. **Gunakan token** di header untuk setiap request:
   ```
   Authorization: Bearer {token}
   ```
4. Token berlaku hingga logout atau expired

---

## Notes

### Fine Calculation
- Denda dihitung otomatis saat return buku
- Rate: **Rp 1.000 per hari** keterlambatan
- Formula: `(return_date - due_date) * 1000`

### Stock Management
- Stok otomatis berkurang saat borrow
- Stok otomatis bertambah saat return
- Tidak bisa pinjam jika stok = 0

### Recommendations
- Maksimal 5 buku aktif
- Public endpoint tanpa autentikasi
- Auto-reorder setelah delete
- Duplicate prevention
