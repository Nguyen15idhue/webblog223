-- Database setup for WebBlog223
CREATE DATABASE IF NOT EXISTS webblog223;
USE webblog223;

-- Users table with role column
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a default admin user (password: admin123)
INSERT INTO users (username, email, password, role)
VALUES ('admin', 'admin@example.com', '$2y$10$EwfJcc1p9NyRgIGHbBRCkuLdKFZcYBR4yfV9w5xFhVIBgLLhXK4VC', 'admin');

-- Insert a default regular user (password: user123)
INSERT INTO users (username, email, password, role)
VALUES ('user', 'user@example.com', '$2y$10$7vfwcc9YB2VsVhzPhBVlR.DgaJvnCVGx/RYeiSRKCJ6TJKA3sQzR.', 'user');
