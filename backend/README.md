# Perpuz - Sistem Manajemen Perpustakaan Digital

Selamat datang di repositori **Perpuz**, aplikasi manajemen perpustakaan modern berbasis web. Aplikasi ini dibangun menggunakan **CodeIgniter 4** (Backend) dan **Vanilla JS + Bootstrap 5** (Frontend).

## 📋 Fitur Utama

-   **Dashboard Admin**: Ringkasan statistik dan aktivitas terbaru.
-   **Manajemen Buku**: Tambah, edit, cari (via OpenLibrary), dan kelola stok buku.
-   **Manajemen Anggota**: Kelola data anggota perpustakaan.
-   **Transaksi Peminjaman**: Catat peminjaman dan pengembalian buku.
-   **Keamanan**: Autentikasi berbasis **JWT (JSON Web Token)**.
-   **Integrasi Eksternal**: API endpoint khusus untuk akses dari sistem lain.

---

## 🚀 Instalasi & Menjalankan Aplikasi

### Pemasangan (Setup)

1.  **Clone Repositori**:
    ```bash
    git clone https://github.com/username/perpuz.git
    cd perpuz/backend
    ```

2.  **Instal Dependensi**:
    ```bash
    composer install
    ```

3.  **Konfigurasi Environment**:
    Salin file `env` menjadi `.env`, lalu atur koneksi database Anda:
    ```ini
    database.default.hostname = localhost
    database.default.database = perpuz_db
    database.default.username = root
    database.default.password = ''
    ```

4.  **Setup Database**:
    ```bash
    php spark migrate
    php spark db:seed UserSeeder
    ```

### Menjalankan Server

Gunakan perintah berikut untuk menjalankan server lokal:

```bash
php spark serve --port 8081
```

Akses aplikasi di browser: **[http://localhost:8081/index.html](http://localhost:8081/index.html)**

---

## 📚 Dokumentasi API

Untuk dokumentasi lengkap mengenai endpoint API yang tersedia, silakan lihat di folder `docs/`:

-   [Dokumentasi API Member & Transaksi](docs/api_docs.md)

### Endpoint Integrasi

Untuk sistem eksternal yang ingin mengakses data user:
-   **URL**: `GET /api/integration/users`
-   **Header**: `X-INTEGRATION-SECRET: rahasia-kita-bersama`

---

## 📂 Struktur Folder

-   `app/`: Logika backend (Controllers, Models, Filters).
-   `public/`: File frontend (HTML, CSS, JS) dan aset gambar/upload.
-   `public/uploads/`: Direktori penyimpanan cover buku.
-   `writable/`: Log dan cache aplikasi.

---

*Dibuat untuk Tugas Besar Pemrograman Web.*
