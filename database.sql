-- ==============================================================
-- AL NASRAT FOODS (النصرت فوډز) - Complete Offline Database Schema
-- Database: alnasratfood
-- Compatible with Laragon / XAMPP (MySQL 8+ / MariaDB)
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `alnasratfood` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `alnasratfood`;

-- --------------------------------------------------------
-- Table: admins (Admin Panel Users & Authentication)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'admin',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin User (Username: admin | Password: password_hash of admin123)
INSERT INTO `admins` (`id`, `username`, `password`, `fullname`, `role`) VALUES
(1, 'admin', '$2y$10$w0956lPzJzO7yF8F02/n.eZ6qHwGZ1g0yG/zR4aR2eLgYtqH9iNre', 'Shop Owner', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- --------------------------------------------------------
-- Table: inventory (Raw Materials & Stock)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` VARCHAR(64) NOT NULL PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(32) DEFAULT '📦',
  `quantity` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `minimum` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `unit` VARCHAR(32) NOT NULL DEFAULT 'kg',
  `movement` VARCHAR(255) DEFAULT 'Initial stock',
  `time` VARCHAR(100) DEFAULT 'Recently',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `inventory` (`id`, `name`, `category`, `icon`, `quantity`, `minimum`, `unit`, `movement`, `time`) VALUES
('sunflower-oil', 'Sunflower oil', 'Edible oil', '🫒', 2450.00, 700.00, 'kg', 'Stock in · 500 kg', 'Today, 09:42'),
('wheat-flour', 'Wheat flour', 'Grain & flour', '🌾', 3800.00, 1000.00, 'kg', 'Stock out · 120 kg', 'Today, 09:15'),
('sugar', 'White sugar', 'Sweeteners', '◈', 1820.00, 500.00, 'kg', 'Stock in · 400 kg', 'Today, 08:50'),
('soybean-oil', 'Soybean oil', 'Edible oil', '🫙', 960.00, 500.00, 'kg', 'Stock out · 50 kg', 'Today, 08:34'),
('rice', 'Basmati rice', 'Grain & flour', '🍚', 1240.00, 400.00, 'kg', 'Stock in · 250 kg', 'Yesterday, 16:20'),
('salt', 'Refined salt', 'Seasoning', '✳', 430.00, 350.00, 'kg', 'Stock out · 25 kg', 'Yesterday, 15:08'),
('tomato-paste', 'Tomato paste', 'Ingredients', '🍅', 620.00, 250.00, 'kg', 'Stock in · 100 kg', 'Yesterday, 13:45'),
('packaging', 'Packaging film', 'Packaging', '▤', 230.00, 300.00, 'kg', 'Stock out · 80 kg', 'Yesterday, 11:30');

-- --------------------------------------------------------
-- Table: customers (Khata Accounts & Outstanding Balances)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customers` (
  `id` VARCHAR(64) NOT NULL PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `business` VARCHAR(100) NOT NULL,
  `location` VARCHAR(150) NOT NULL,
  `initials` VARCHAR(10) NOT NULL,
  `balance` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `last_payment` VARCHAR(100) DEFAULT 'No payments yet',
  `status` ENUM('Overdue','Due soon','Current') NOT NULL DEFAULT 'Current',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `customers` (`id`, `name`, `business`, `location`, `initials`, `balance`, `last_payment`, `status`) VALUES
('mohammad-rahim', 'Mohammad Rahim', 'Wholesale', 'Kandahar City', 'MR', 86500.00, 'Oct 09, 2026', 'Overdue'),
('farid-sons', 'Farid & Sons Trading', 'Distributor', 'Spin Boldak', 'FS', 72300.00, 'Oct 10, 2026', 'Due soon'),
('ahmad-khan', 'Ahmad Khan Wholesale', 'Wholesale', 'Kandahar City', 'AK', 58400.00, 'Oct 08, 2026', 'Overdue'),
('nawid-supermarket', 'Nawid Supermarket', 'Retail', 'Aino Mina', 'NS', 42500.00, 'Oct 10, 2026', 'Current'),
('abdul-basit', 'Abdul Basit Grocers', 'Retail', 'Kandahar City', 'AB', 38500.00, 'Oct 07, 2026', 'Overdue'),
('malik-trading', 'Malik Trading House', 'Distributor', 'Daman', 'MT', 34000.00, 'Oct 10, 2026', 'Due soon'),
('sardar-market', 'Sardar Market', 'Wholesale', 'Kandahar City', 'SM', 27000.00, 'Oct 09, 2026', 'Current'),
('habib-foods', 'Habib Foods', 'Retail', 'Spin Boldak', 'HF', 25000.00, 'Oct 06, 2026', 'Overdue');

-- --------------------------------------------------------
-- Table: khata (Daily Ledger: Debits & Credits)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `khata` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `date` VARCHAR(20) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `type` ENUM('debit','credit') NOT NULL DEFAULT 'debit',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `khata` (`id`, `date`, `title`, `amount`, `type`) VALUES
(1, '2026-10-10', 'د خامو موادو خرڅ', 32000.00, 'debit'),
(2, '2026-10-10', 'د پدېویل ډیلیت', 18000.00, 'credit'),
(3, '2026-10-09', 'د برنج د سپکو', 22000.00, 'debit');

-- --------------------------------------------------------
-- Table: wasoli (Payment Collections)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `wasoli` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `date` VARCHAR(20) NOT NULL,
  `customer` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `status` VARCHAR(50) NOT NULL DEFAULT 'رسید',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wasoli` (`id`, `date`, `customer`, `amount`, `status`) VALUES
(1, '2026-10-10', 'محمد رحیم', 25000.00, 'رسید'),
(2, '2026-10-09', 'احمد خان', 18500.00, 'منتظر'),
(3, '2026-10-08', 'نوید سپر', 43000.00, 'رسید');

-- --------------------------------------------------------
-- Table: settings (Application & Currency Preferences)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(64) NOT NULL PRIMARY KEY,
  `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`key`, `value`) VALUES ('currency', 'AFN')
ON DUPLICATE KEY UPDATE `value`='AFN';
