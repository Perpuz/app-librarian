# API Endpoints for Member Portal & Transactions

Base URL: `http://localhost:8081/api`

## 1. Books (Katalog Buku)
Digunakan untuk menampilkan daftar buku di halaman utama member.

### List All / Search
- **Endpoint**: `GET /books`
- **Parameters (Optional)**:
    - `q`: Search query (Title, Author, or ISBN)
- **Example**: 
    - `GET /books` (All books)
    - `GET /books?q=harry` (Search for "harry")
- **Response**: Array of Book objects.

### Book Detail
- **Endpoint**: `GET /books/{id}`
- **Example**: `GET /books/1`

---

## 2. Transactions (Riwayat Peminjaman)
Digunakan untuk member melihat status peminjaman mereka sendiri.

### My Transactions
- **Endpoint**: `GET /transactions`
- **Parameters**:
    - `member_code`: (Required for member view) Filter by Member Code.
- **Example**: `GET /transactions?member_code=M001`
- **Response**: Array of transactions with book details and status.

---

## 3. Profile (Member Check)
Karena tidak ada login password, member bisa "masuk" hanya dengan mengecek apakah Member Code mereka valid.

### Check Member Exists
- **Endpoint**: `GET /members?code={member_code}`
- **Note**: Saat ini endpoint `/members` mengembalikan list semua. Gunakan filter di sisi frontend.
