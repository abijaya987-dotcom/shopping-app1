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