-- Divisional Secretariat Service Management System - Minipe
-- Database Schema

CREATE DATABASE IF NOT EXISTS minipe_dssms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE minipe_dssms;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nic VARCHAR(12) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('citizen', 'admin') NOT NULL DEFAULT 'citizen',
    language ENUM('en', 'si') NOT NULL DEFAULT 'en',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    purpose TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    request_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    document_path VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'processing', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    remarks TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default users are created by setup.php with hashed passwords:
-- Admin: admin@minipe.gov.lk / admin123
-- Citizen: kamal@example.com / citizen123
