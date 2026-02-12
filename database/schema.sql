-- Subdivision Housing Availability and Mapping System Database Schema
-- MySQL Database

-- Create Database
CREATE DATABASE IF NOT EXISTS `subdi_housing_system`;
USE `subdi_housing_system`;

-- Users Table (Admin and Agents)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `role` ENUM('admin', 'agent', 'viewer') DEFAULT 'viewer',
  `phone` VARCHAR(20),
  `profile_image` VARCHAR(255),
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subdivisions Table
CREATE TABLE `subdivisions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `location` VARCHAR(300),
  `latitude` DECIMAL(10, 8),
  `longitude` DECIMAL(11, 8),
  `total_units` INT,
  `map_image` VARCHAR(255),
  `developer_name` VARCHAR(150),
  `contact_info` VARCHAR(255),
  `created_by` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_name` (`name`),
  FULLTEXT INDEX `ft_search` (`name`, `location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Properties/Units Table
CREATE TABLE `properties` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subdivision_id` INT NOT NULL,
  `unit_number` VARCHAR(50) NOT NULL,
  `property_type` ENUM('house', 'lot', 'condo', 'apartment') DEFAULT 'house',
  `status` ENUM('occupied', 'vacant', 'for_sale') DEFAULT 'vacant',
  `price` DECIMAL(12, 2),
  `area_sqm` DECIMAL(10, 2),
  `bedrooms` INT,
  `bathrooms` INT,
  `features` TEXT,
  `description` TEXT,
  `latitude` DECIMAL(10, 8),
  `longitude` DECIMAL(11, 8),
  `images` JSON,
  `owner_name` VARCHAR(200),
  `owner_contact` VARCHAR(100),
  `agent_id` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`subdivision_id`) REFERENCES `subdivisions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`agent_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `unique_unit` (`subdivision_id`, `unit_number`),
  INDEX `idx_status` (`status`),
  INDEX `idx_type` (`property_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inquiries Table
CREATE TABLE `inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `visitor_name` VARCHAR(200) NOT NULL,
  `visitor_email` VARCHAR(100) NOT NULL,
  `visitor_phone` VARCHAR(20),
  `message` TEXT,
  `status` ENUM('new', 'contacted', 'interested', 'closed') DEFAULT 'new',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  INDEX `idx_status` (`status`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Availability Log Table
CREATE TABLE `availability_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `previous_status` VARCHAR(50),
  `new_status` VARCHAR(50),
  `changed_by` INT,
  `reason` TEXT,
  `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_property` (`property_id`),
  INDEX `idx_date` (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Default Admin User
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`)
VALUES ('admin', 'admin@subdi-housing.com', SHA2('admin123', 256), 'Administrator', 'admin');

-- Create Sample Subdivision
INSERT INTO `subdivisions` (`name`, `description`, `location`, `latitude`, `longitude`, `total_units`, `developer_name`, `contact_info`, `created_by`)
VALUES ('Villa Purita', 'Premium residential development in Minglanilla, Cebu with modern amenities and family-friendly facilities', 'Minglanilla, Cebu, Philippines', 10.257609202484922, 123.80114720758648, 250, 'Villa Purita Developers', 'contact@villapurita.com', 1);
