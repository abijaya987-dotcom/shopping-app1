CREATE DATABASE IF NOT EXISTS shopping_app;
USE shopping_app;

-- Tabel Users: Menyimpan data pengguna
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Shopping Items: Menyimpan daftar belanja
CREATE TABLE IF NOT EXISTS shopping_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity VARCHAR(50) DEFAULT '1',
    category ENUM('food', 'drinks', 'toiletries', 'cleaning', 'others') DEFAULT 'food',
    is_purchased BOOLEAN DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
