<?php
// db.php - Database connection for local offline shop system
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "alnasratfood";

try {
    // First connect without db name to ensure database exists
    $pdoInit = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdoInit->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Connect to specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Create tables if not exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inventory (
            id VARCHAR(64) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            icon VARCHAR(32) DEFAULT '📦',
            quantity DECIMAL(12, 2) NOT NULL DEFAULT 0,
            minimum DECIMAL(12, 2) NOT NULL DEFAULT 0,
            unit VARCHAR(32) NOT NULL DEFAULT 'kg',
            movement VARCHAR(255) DEFAULT 'Initial stock',
            time VARCHAR(100) DEFAULT 'Recently',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS customers (
            id VARCHAR(64) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            business VARCHAR(100) NOT NULL,
            location VARCHAR(150) NOT NULL,
            initials VARCHAR(10) NOT NULL,
            balance DECIMAL(14, 2) NOT NULL DEFAULT 0,
            last_payment VARCHAR(100) DEFAULT 'No payments yet',
            status ENUM('Overdue', 'Due soon', 'Current') NOT NULL DEFAULT 'Current',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS khata (
            id INT AUTO_INCREMENT PRIMARY KEY,
            date VARCHAR(20) NOT NULL,
            title VARCHAR(255) NOT NULL,
            amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            type ENUM('debit','credit') NOT NULL DEFAULT 'debit',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS wasoli (
            id INT AUTO_INCREMENT PRIMARY KEY,
            date VARCHAR(20) NOT NULL,
            customer VARCHAR(255) NOT NULL,
            amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            status VARCHAR(50) NOT NULL DEFAULT 'رسید',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS settings (
            `key` VARCHAR(64) PRIMARY KEY,
            `value` TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            fullname VARCHAR(100) NOT NULL,
            role VARCHAR(20) DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Seed default admin if empty (username: admin, pass: admin123)
    $stmtAdmin = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmtAdmin->fetchColumn() == 0) {
        $insertAdmin = $pdo->prepare("INSERT INTO admins (username, password, fullname, role) VALUES (?, ?, ?, ?)");
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin->execute(['admin', $hashed, 'Shop Owner', 'admin']);
    }

    // Seed default inventory if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM inventory");
    if ($stmt->fetchColumn() == 0) {
        $insertInv = $pdo->prepare("INSERT INTO inventory (id, name, category, icon, quantity, minimum, unit, movement, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $defaultInventory = [
            ['sunflower-oil', 'Sunflower oil', 'Edible oil', '🫒', 2450, 700, 'kg', 'Stock in · 500 kg', 'Today, 09:42'],
            ['wheat-flour', 'Wheat flour', 'Grain & flour', '🌾', 3800, 1000, 'kg', 'Stock out · 120 kg', 'Today, 09:15'],
            ['sugar', 'White sugar', 'Sweeteners', '◈', 1820, 500, 'kg', 'Stock in · 400 kg', 'Today, 08:50'],
            ['soybean-oil', 'Soybean oil', 'Edible oil', '🫙', 960, 500, 'kg', 'Stock out · 50 kg', 'Today, 08:34'],
            ['rice', 'Basmati rice', 'Grain & flour', '🍚', 1240, 400, 'kg', 'Stock in · 250 kg', 'Yesterday, 16:20'],
            ['salt', 'Refined salt', 'Seasoning', '✳', 430, 350, 'kg', 'Stock out · 25 kg', 'Yesterday, 15:08'],
            ['tomato-paste', 'Tomato paste', 'Ingredients', '🍅', 620, 250, 'kg', 'Stock in · 100 kg', 'Yesterday, 13:45'],
            ['packaging', 'Packaging film', 'Packaging', '▤', 230, 300, 'kg', 'Stock out · 80 kg', 'Yesterday, 11:30']
        ];
        foreach ($defaultInventory as $row) {
            $insertInv->execute($row);
        }
    }

    // Seed default customers if empty
    $stmtCust = $pdo->query("SELECT COUNT(*) FROM customers");
    if ($stmtCust->fetchColumn() == 0) {
        $insertCust = $pdo->prepare("INSERT INTO customers (id, name, business, location, initials, balance, last_payment, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $defaultCustomers = [
            ['mohammad-rahim', 'Mohammad Rahim', 'Wholesale', 'Kandahar City', 'MR', 86500, 'Oct 09, 2026', 'Overdue'],
            ['farid-sons', 'Farid & Sons Trading', 'Distributor', 'Spin Boldak', 'FS', 72300, 'Oct 10, 2026', 'Due soon'],
            ['ahmad-khan', 'Ahmad Khan Wholesale', 'Wholesale', 'Kandahar City', 'AK', 58400, 'Oct 08, 2026', 'Overdue'],
            ['nawid-supermarket', 'Nawid Supermarket', 'Retail', 'Aino Mina', 'NS', 42500, 'Oct 10, 2026', 'Current'],
            ['abdul-basit', 'Abdul Basit Grocers', 'Retail', 'Kandahar City', 'AB', 38500, 'Oct 07, 2026', 'Overdue'],
            ['malik-trading', 'Malik Trading House', 'Distributor', 'Daman', 'MT', 34000, 'Oct 10, 2026', 'Due soon'],
            ['sardar-market', 'Sardar Market', 'Wholesale', 'Kandahar City', 'SM', 27000, 'Oct 09, 2026', 'Current'],
            ['habib-foods', 'Habib Foods', 'Retail', 'Spin Boldak', 'HF', 25000, 'Oct 06, 2026', 'Overdue']
        ];
        foreach ($defaultCustomers as $row) {
            $insertCust->execute($row);
        }
    }

} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
