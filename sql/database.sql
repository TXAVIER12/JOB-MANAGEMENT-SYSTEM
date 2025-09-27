-- SQL to create database and tables for Job Management System
CREATE DATABASE IF NOT EXISTS job_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE job_manager;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  pieces_per_pack INT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Purchases (when you buy product packs)
CREATE TABLE IF NOT EXISTS purchases (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  packs INT NOT NULL DEFAULT 0,
  purchase_price DECIMAL(15,2) NOT NULL, -- price per pack
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sales (pack or pieces)
CREATE TABLE IF NOT EXISTS sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  unit_type ENUM('pack','piece') NOT NULL,
  quantity INT NOT NULL,
  sale_price DECIMAL(15,2) NOT NULL, -- price per pack OR per piece depending on unit_type
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert default admin user (password hashed with PHP password_hash of "Admin@123")
-- If your MySQL client does not support PHP hashing, create user manually through register.php
INSERT INTO users (name, email, password) VALUES
('Administrator', 'admin@admin.com', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8/4a3uV8x1f8k1ypc3GfQ2h6aQK1aG');

-- Note: the above hashed password corresponds to "Admin@123" if created with PHP's password_hash.
