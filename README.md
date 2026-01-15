# Aplikasi Todo List – Daftar Belanja

## Deskripsi Singkat
Aplikasi ini merupakan sebuah **platform web sederhana untuk manajemen daftar belanja (Todo List)** yang dikembangkan untuk memenuhi tugas proyek mata kuliah **Back-End Web Development**.

Aplikasi ini memungkinkan pengguna untuk:
- Mendaftar dan masuk ke sistem
- Mengelola item belanja
- Menambah, mengedit, dan menghapus item
- Menandai status barang sudah dibeli atau belum

---

## Daftar Anggota Kelompok

- **Anggota 1 (Infrastruktur & Auth)**  
`[AbiJaya] - [240030076]`

- **Anggota 2 (Backend Logic & Quality Control)**  
`[Dek Dwi] - [240030065]`

- **Anggota 3 (Frontend UI & Dokumentasi)**  
`[Dimas] - [240030101]`

- **Infrastruktur & Auth**
  - database.php
  - database-schema.sql
  - register.php
  - login.php
  - logout.php
  - auth-cehk.php

- **Backend Logic & Quality Control**
  - items.php
  - add-item.php
  - edit-item.php
  - Testing & Debugging

- **Frontend UI & Dokumentasi**
  - dashboard.php
  - header.php
  - footer.php
  - Styling (Bootstrap 5)
  - README.md
  
---

## Lingkungan Pengembangan
Aplikasi ini dikembangkan menggunakan teknologi berikut:

- **Bahasa Pemrograman**: PHP Native (tanpa framework back-end)
- **Database**: MySQL / MariaDB
- **Front-End**:
  - HTML
  - CSS (Bootstrap 5)
  - JavaScript
- **Server**: XAMPP (Apache)
- **Text Editor**: Visual Studio Code

---

## Hasil Pengembangan

### 1. Autentikasi Pengguna
- **Registrasi & Login**  
  Pengguna dapat membuat akun baru dan masuk ke sistem.
- **Keamanan Password**  
  Password dienkripsi menggunakan `password_hash()` dengan algoritma bcrypt.
- **Proteksi Halaman**  
  Halaman dashboard dan manajemen item tidak dapat diakses tanpa login.

---

## Fitur Keamanan (Security)
Aplikasi ini diimplementasikan dengan standar keamanan Back-End sebagai berikut:

1. **Anti-SQL Injection**: Semua query database menggunakan *Prepared Statements* dan *Parameter Binding* melalui PDO untuk mencegah manipulasi query.
2. **Password Security**: Menggunakan fungsi `password_hash()` bawaan PHP untuk enkripsi satu arah. Tidak ada password yang disimpan dalam bentuk teks biasa.
3. **Session Management**: 
   - Memastikan sesi unik untuk setiap pengguna.
   - Fitur *Session Timeout* yang otomatis mengeluarkan pengguna jika tidak ada aktivitas dalam 30 menit.
4. **Data Privacy**: Query database selalu menyertakan `WHERE user_id = :id`, sehingga pengguna hanya bisa melihat data milik mereka sendiri.

---

### 2. Manajemen Tugas (Daftar Belanja)
- **Create (Tambah)**  
  Menambahkan barang belanja dengan detail:
  - Nama barang
  - Jumlah
  - Kategori
  - Catatan
- **Read (Lihat)**  
  Menampilkan daftar belanja dalam bentuk kartu dengan filter:
  - Semua
  - Belum Dibeli
  - Sudah Dibeli
- **Update (Edit)**  
  Mengubah detail barang atau menandai status barang menjadi *sudah dibeli*.
- **Delete (Hapus)**  
  Menghapus barang dari daftar belanja.

---

### 3. Session Management
- Menggunakan `session_start()` untuk mengelola sesi login pengguna.
- Sesi akan berakhir ketika pengguna melakukan **logout** atau terjadi **session timeout**.

---

## Struktur Folder Proyek

```text
todo-list/
├── config/
│   └── database.php          # Konfigurasi koneksi database
│
├── includes/
│   ├── header.php            # Header layout
│   ├── footer.php            # Footer layout
│   └── auth-check.php        # Proteksi halaman (cek login)
│
├── index.php                 # Redirect halaman utama
├── login.php                 # Halaman login
├── register.php              # Halaman registrasi
├── dashboard.php             # Dashboard setelah login
├── items.php                 # Halaman daftar belanja
├── add-item.php              # Form tambah & edit item
├── logout.php                # Proses logout
│
└── database-schema.sql       # Skema database


```
---

## Skema Database (database-schema.sql)

Proyek ini menggunakan database relasional MySQL dengan struktur sebagai berikut. Anda dapat mengimpor kode ini melalui tab SQL di phpMyAdmin.

```sql
-- 1. Membuat Database
CREATE DATABASE IF NOT EXISTS shopping_app;
USE shopping_app;

-- 2. Membuat Tabel Users
-- Digunakan untuk menyimpan kredensial pengguna
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Menyimpan hash bcrypt
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Membuat Tabel Shopping Items
-- Berisi daftar belanja yang terhubung ke masing-masing pengguna
CREATE TABLE IF NOT EXISTS shopping_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 1,
    category ENUM('Makanan', 'Minuman', 'Kebutuhan Rumah', 'Elektronik', 'Lainnya') DEFAULT 'Lainnya',
    is_purchased TINYINT(1) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Menjamin integritas data: Jika user dihapus, item belanjanya juga terhapus
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

```

Aplikasi ini menggunakan dua tabel utama dengan relasi *One-to-Many*:
1. **Tabel `users`**: Menyimpan data akun (ID, Username, Email, Password Hash).
2. **Tabel `shopping_items`**: Menyimpan detail barang belanjaan yang terhubung ke `user_id` tertentu.

**Fitur Database:**
- **Foreign Key**: Menghubungkan item dengan pemiliknya.
- **On Delete Cascade**: Menjamin integritas data (jika user dihapus, listnya hilang).
- **Enum Category**: Memberikan pilihan kategori belanja yang terstruktur.

---

## Cara Instalasi dan Menjalankan Aplikasi

### 1. Persiapan Lingkungan
- Pastikan XAMPP sudah terinstall
- Aktifkan Apache dan MySQL melalui XAMPP Control Panel

---

### 2. Setup Database
1. Buka phpMyAdmin  
   `http://localhost/phpmyadmin`
2. Buat database baru dengan nama:
shopping_app
3. Import file `database-schema.sql` untuk membuat tabel `users` dan `shopping_items`

---

### 3. Setup Aplikasi
- Salin folder proyek ke:
C:\xampp\htdocs\todo-list

- Sesuaikan konfigurasi database pada file:
config/database.php

---

### 4. Menjalankan Aplikasi
- Akses aplikasi melalui browser:
http://localhost/shopping-app/login.php


- Lakukan registrasi akun baru untuk mulai menggunakan aplikasi

