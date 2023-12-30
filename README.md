# Aplikasi Inventory - Test

Aplikasi ini merupakan sistem inventory yang memungkinkan pengguna untuk mengelola penjualan (sales) dan pembelian (purchases) item. dibuat dengan Laravel 10 dan vite

## Data Pengguna (Seeder)

Saya telah menyuntikkan pengguna ke dalam tabel, dengan masing-masing memiliki peran yang sesuai:

| Role       | Username | Password  |
|------------|----------|-----------|
| SuperAdmin | admin    | admin     |
| Sales      | sales    | sales     |
| Purchase   | purchase | purchase  |
| Manager    | manager  | manager   |

## Petunjuk

1. **Instalasi:**
   - Jalankan `composer install` dan `npm i` untuk menginstal dependensi.
   - Salin file `.env.example` menjadi `.env` dan atur pengaturan database.
   - Jalankan `php artisan key:generate` untuk menghasilkan kunci aplikasi.
   - jalankan `npm run dev` untuk compile nya.

2. **Menyuntikkan Data Pengguna:**
   - Untuk seed data pengguna ke dalam database, jalankan perintah:
     ```bash
     php artisan db:seed UserSeeder
     ```
     Perintah ini akan menambahkan pengguna SuperAdmin, Sales, Purchase, dan Manager ke dalam tabel pengguna.

3. **Akses Aplikasi:**
   - SuperAdmin: Dapat melakukan CRUD untuk inventory, sales, dan purchases.
   - Sales: Hanya dapat melakukan CRUD untuk sales dan melihat riwayat sales yang dibuat olehnya.
   - Purchase: Hanya dapat melakukan CRUD untuk purchases dan melihat riwayat purchases yang dibuat olehnya.
   - Manager: Hanya dapat melihat dan mencetak (view and print) purchases & sales.

...
