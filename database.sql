-- Database: alnasratfood
-- Export / Backup Schema & Initial Data for offline shop

CREATE DATABASE IF NOT EXISTS `alnasratfood` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `alnasratfood`;

-- Table structure for table `inventory`
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `icon` varchar(32) DEFAULT '📦',
  `quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `minimum` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(32) NOT NULL DEFAULT 'kg',
  `movement` varchar(255) DEFAULT 'Initial stock',
  `time` varchar(100) DEFAULT 'Recently',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `customers`
CREATE TABLE IF NOT EXISTS `customers` (
  `id` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `business` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `initials` varchar(10) NOT NULL,
  `balance` decimal(14,2) NOT NULL DEFAULT 0.00,
  `last_payment` varchar(100) DEFAULT 'No payments yet',
  `status` enum('Overdue','Due soon','Current') NOT NULL DEFAULT 'Current',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `settings`
CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default sample data for `inventory`
INSERT IGNORE INTO `inventory` (`id`, `name`, `category`, `icon`, `quantity`, `minimum`, `unit`, `movement`, `time`) VALUES
('sunflower-oil', 'Sunflower oil', 'Edible oil', '🫒', 2450.00, 700.00, 'kg', 'Stock in · 500 kg', 'Today, 09:42'),
('wheat-flour', 'Wheat flour', 'Grain & flour', '🌾', 3800.00, 1000.00, 'kg', 'Stock out · 120 kg', 'Today, 09:15'),
('sugar', 'White sugar', 'Sweeteners', '◈', 1820.00, 500.00, 'kg', 'Stock in · 400 kg', 'Today, 08:50'),
('soybean-oil', 'Soybean oil', 'Edible oil', '🫙', 960.00, 500.00, 'kg', 'Stock out · 50 kg', 'Today, 08:34'),
('rice', 'Basmati rice', 'Grain & flour', '🍚', 1240.00, 400.00, 'kg', 'Stock in · 250 kg', 'Yesterday, 16:20'),
('salt', 'Refined salt', 'Seasoning', '✳', 430.00, 350.00, 'kg', 'Stock out · 25 kg', 'Yesterday, 15:08'),
('tomato-paste', 'Tomato paste', 'Ingredients', '🍅', 620.00, 250.00, 'kg', 'Stock in · 100 kg', 'Yesterday, 13:45'),
('packaging', 'Packaging film', 'Packaging', '▤', 230.00, 300.00, 'kg', 'Stock out · 80 kg', 'Yesterday, 11:30');

-- Default sample data for `customers`
INSERT IGNORE INTO `customers` (`id`, `name`, `business`, `location`, `initials`, `balance`, `last_payment`, `status`) VALUES
('mohammad-rahim', 'Mohammad Rahim', 'Wholesale', 'Kandahar City', 'MR', 86500.00, 'Oct 09, 2026', 'Overdue'),
('farid-sons', 'Farid & Sons Trading', 'Distributor', 'Spin Boldak', 'FS', 72300.00, 'Oct 10, 2026', 'Due soon'),
('ahmad-khan', 'Ahmad Khan Wholesale', 'Wholesale', 'Kandahar City', 'AK', 58400.00, 'Oct 08, 2026', 'Overdue'),
('nawid-supermarket', 'Nawid Supermarket', 'Retail', 'Aino Mina', 'NS', 42500.00, 'Oct 10, 2026', 'Current'),
('abdul-basit', 'Abdul Basit Grocers', 'Retail', 'Kandahar City', 'AB', 38500.00, 'Oct 07, 2026', 'Overdue'),
('malik-trading', 'Malik Trading House', 'Distributor', 'Daman', 'MT', 34000.00, 'Oct 10, 2026', 'Due soon'),
('sardar-market', 'Sardar Market', 'Wholesale', 'Kandahar City', 'SM', 27000.00, 'Oct 09, 2026', 'Current'),
('habib-foods', 'Habib Foods', 'Retail', 'Spin Boldak', 'HF', 25000.00, 'Oct 06, 2026', 'Overdue');

