# Panduan Penggunaan Perpuz - Sistem Manajemen Perpustakaan Digital

Dokumen ini berisi panduan lengkap cara instalasi, menjalankan aplikasi, dan alur kerja sistem Perpuz.

## 📋 Prasyarat

Sebelum memulai, pastikan komputer Anda telah terinstal:
1.  **PHP** (versi 8.1 atau lebih baru).
2.  **Composer** (Dependency Manager untuk PHP).
3.  **MySQL/MariaDB** (Database).
4.  **Terminal/Command Prompt**.

---

## 🚀 Instalasi & Konfigurasi Awal

Ikuti langkah-langkah ini jika Anda baru pertama kali menjalankan aplikasi.

### 1. Ekstrak & Masuk ke Direktori
Buka terminal dan arahkan ke folder `backend` proyek.
```bash
cd /path/to/Perpuz/backend
```

### 2. Instal Dependensi
Jalankan perintah berikut untuk mengunduh semua library yang dibutuhkan (CodeIgniter 4, JWT, dll).
```bash
composer install
```

### 3. Konfigurasi Environment (.env)
Salin file `env` menjadi `.env` lalu sesuaikan konfigurasinya:
```bash
cp env .env
```
Buka file `.env` dan pastikan konfigurasi berikut:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8081/'

# Database
database.default.hostname = localhost
database.default.database = perpuz_db
database.default.username = root
database.default.password = ''  # Sesuaikan dengan password database Anda
database.default.DBDriver = MySQLi

# Keamanan
JWT_SECRET = 'kunci-rahasia-anda-disini'
INTEGRATION_SECRET = 'rahasia-kita-bersama'
```

### 4. Setup Database
Buat database dan jalankan migrasi untuk membuat tabel-tabel (users, books, members, transactions).
```bash
php spark migrate
```
(Optional) Isi data awal (Seeding):
```bash
php spark db:seed UserSeeder
php spark db:seed BookSeeder
```

---

## ▶️ Menjalankan Aplikasi

Aplikasi Perpuz menggunakan port **8081** (sesuai konfigurasi kita). Jalankan server dengan perintah:

```bash
php spark serve --port 8081
```

Akses aplikasi melalui browser di:
👉 **[http://localhost:8081/index.html](http://localhost:8081/index.html)**

---

## 🔄 Alur Aplikasi (Flow)

Berikut adalah panduan fitur utama dalam aplikasi:

### 1. Login (Autentikasi)
*   Halaman pertama yang muncul adalah Login.
*   Masukkan Username dan Password Admin.
*   Sistem akan memberikan **Token JWT** yang disimpan otomatis oleh browser.
*   Jika berhasil, Anda akan diarahkan ke Dashboard.

### 2. Dashboard
*   Menampilkan ringkasan: Total Buku, Anggota Aktif, dan Transaksi.
*   Melihat tabel transaksi terbaru.

### 3. Manajemen Buku (Manage Books)
*   **Lihat Buku**: Tampilan grid daftar buku beserta stoknya.
*   **Tambah Buku (Manual)**:
    *   Klik "Add New Book".
    *   Isi Judul, Penulis, ISBN, Stok.
    *   **Upload Cover**: Pilih file gambar dari komputer Anda.
*   **Cari Buku (OpenLibrary)**: Cari buku dari internet dan tambahkan ke database secara otomatis.
*   **Edit Buku**: Klik ikon pensil pada kartu buku untuk mengubah data atau mengganti cover.

### 4. Manajemen Anggota (Members)
*   Melihat daftar anggota perpustakaan.
*   Menambah anggota baru (Kode Anggota, Nama, Email, dll).
*   Mengedit status anggota (Active/Inactive).

### 5. Transaksi (Peminjaman & Pengembalian)
*   **Peminjaman**:
    *   Klik "Borrow Book".
    *   Masukkan **Kode Anggota** (misal: `M001`).
    *   Masukkan **Judul Buku** (persis) atau **ISBN**.
    *   Tentukan tanggal pinjam dan tanggal kembali.
*   **Pengembalian**:
    *   Cari transaksi dengan status `borrowed`.
    *   Klik tombol **Return**.
    *   Stok buku akan bertambah otomatis dan status berubah menjadi `returned`.

---

## 🔌 Integrasi Eksternal (API)

Aplikasi ini menyediakan endpoint khusus untuk diakses oleh sistem lain (misalnya Portal Admin Pusat).

*   **Endpoint**: `GET /api/integration/users`
*   **Syarat**: Wajib menyertakan Header `X-INTEGRATION-SECRET`.
*   **Fungsi**: Mengambil daftar user admin tanpa perlu login password.

---

## 🛠️ Troubleshooting

*   **Error "Access blocked / CORS"**: Pastikan Anda mengakses lewat `http://localhost:8081`.
*   **Gagal Login**: Cek kembali database `users` apakah user admin sudah ada.
*   **Gambar Cover Tidak Muncul**: Pastikan folder `public/uploads/cover` memiliki izin tulis (write permission).
