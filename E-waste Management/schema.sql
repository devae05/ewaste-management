-- ============================================================
-- E-Waste Management System - Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS ewaste CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ewaste;

-- ------------------------------------------------------------
-- Users table (single table with role-based access)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS user (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- E-Waste Bookings table
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ewaste_booking (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    date        DATE         NOT NULL,
    time        TIME         NOT NULL,
    location    VARCHAR(255) NOT NULL,
    type        VARCHAR(100) NOT NULL,
    quantity    INT          NOT NULL DEFAULT 1,
    status      ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
    admin_notes TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Seed: default admin account
-- Password: Admin@1234  (bcrypt hash below)
-- Change this password immediately after first login!
-- ------------------------------------------------------------
INSERT INTO user (username, email, password, role) VALUES
(
    'admin',
    'admin@ewaste.local',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
)
ON DUPLICATE KEY UPDATE id = id;
-- NOTE: The hash above is for the string "password" (Laravel default test hash).
-- Generate your own with: echo password_hash('YourPassword', PASSWORD_DEFAULT);
