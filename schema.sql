CREATE DATABASE IF NOT EXISTS food_donation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE food_donation;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'orphanage') NOT NULL DEFAULT 'user',
    address VARCHAR(255) NULL,
    latitude DECIMAL(10, 7) NULL,
    longitude DECIMAL(10, 7) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_location (latitude, longitude)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS donations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donor_id INT UNSIGNED NOT NULL,
    assigned_orphanage_id INT UNSIGNED NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    quantity VARCHAR(120) NOT NULL,
    pickup_address VARCHAR(255) NOT NULL,
    pickup_latitude DECIMAL(10, 7) NULL,
    pickup_longitude DECIMAL(10, 7) NULL,
    status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    assigned_at TIMESTAMP NULL,
    decided_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_donations_donor FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_donations_orphanage FOREIGN KEY (assigned_orphanage_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_donations_status (status),
    INDEX idx_donations_assigned (assigned_orphanage_id),
    INDEX idx_donations_pickup (pickup_latitude, pickup_longitude)
) ENGINE=InnoDB;

INSERT INTO users (name, email, password, role, address)
VALUES ('System Admin', 'admin@ngo.local', '$2y$12$W9lyKP8Hlp3EsEQUIS6uPerSgY8mEIDcdWcbvSBcpminajdemDIyC', 'admin', 'NGO HQ')
ON DUPLICATE KEY UPDATE email = email;
