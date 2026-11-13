<?php
// api.php - API endpoint for AJAX operations
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Helper to get JSON or POST input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

try {
    switch ($action) {
        // --- INVENTORY ---
        case 'get_inventory':
            $stmt = $pdo->query("SELECT * FROM inventory ORDER BY name ASC");
            $rows = $stmt->fetchAll();
            // Cast numeric fields
            foreach ($rows as &$r) {
                $r['quantity'] = (float)$r['quantity'];
                $r['minimum'] = (float)$r['minimum'];
            }
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'save_inventory_item':
            // Add or Edit material
            $id = trim($input['id'] ?? '');
            $name = trim($input['name'] ?? '');
            $category = trim($input['category'] ?? '');
            $icon = trim($input['icon'] ?? '📦');
            $quantity = (float)($input['quantity'] ?? 0);
            $minimum = (float)($input['minimum'] ?? 0);
            $unit = trim($input['unit'] ?? 'kg');

            if (empty($name) || empty($category) || empty($unit)) {
                echo json_encode(['success' => false, 'error' => 'All required fields must be filled.']);
                exit;
            }

            if (empty($id)) {
                $id = 'item-' . time() . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
                $stmt = $pdo->prepare("INSERT INTO inventory (id, name, category, icon, quantity, minimum, unit, movement, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$id, $name, $category, $icon, $quantity, $minimum, $unit, 'New item added', 'Just now']);
            } else {
                $stmt = $pdo->prepare("UPDATE inventory SET name = ?, category = ?, icon = ?, quantity = ?, minimum = ?, unit = ? WHERE id = ?");
                $stmt->execute([$name, $category, $icon, $quantity, $minimum, $unit, $id]);
            }

            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'delete_inventory_item':
            $id = trim($input['id'] ?? '');
            if (!empty($id)) {
                $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
                $stmt->execute([$id]);
            }
            echo json_encode(['success' => true]);
            break;

        case 'adjust_stock':
            $id = trim($input['id'] ?? '');
            $amount = (float)($input['amount'] ?? 0);
            $type = $input['type'] ?? 'in'; // 'in' or 'out'
            $note = trim($input['note'] ?? '');

            if (empty($id) || $amount <= 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid item or quantity.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();

            if (!$item) {
                echo json_encode(['success' => false, 'error' => 'Item not found.']);
                exit;
            }

            $newQty = (float)$item['quantity'];
            $prefix = ($type === 'in') ? 'Stock in' : 'Stock out';
            if ($type === 'in') {
                $newQty += $amount;
            } else {
                if ($newQty < $amount) {
                    echo json_encode(['success' => false, 'error' => 'Not enough stock on hand.']);
                    exit;
                }
                $newQty -= $amount;
            }

            $unit = $item['unit'];
            $movement = "{$prefix} · {$amount} {$unit}";
            if (!empty($note)) {
                $movement .= " ({$note})";
            }
            $time = 'Today, ' . date('H:i');

            $updateStmt = $pdo->prepare("UPDATE inventory SET quantity = ?, movement = ?, time = ? WHERE id = ?");
            $updateStmt->execute([$newQty, $movement, $time, $id]);

            echo json_encode(['success' => true, 'newQuantity' => $newQty]);
            break;

        // --- CUSTOMERS / KHATA ---
        case 'get_customers':
            $stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
            $rows = $stmt->fetchAll();
            foreach ($rows as &$r) {
                $r['balance'] = (float)$r['balance'];
                $r['lastPayment'] = $r['last_payment'];
            }
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'add_customer':
            $name = trim($input['name'] ?? '');
            $business = trim($input['business'] ?? '');
            $location = trim($input['location'] ?? '');
            $balance = (float)($input['balance'] ?? 0);

            if (empty($name) || empty($business) || empty($location) || $balance < 0) {
                echo json_encode(['success' => false, 'error' => 'Please provide valid customer details.']);
                exit;
            }

            $words = preg_split('/\s+/', $name);
            $initials = '';
            foreach (array_slice($words, 0, 2) as $w) {
                $initials .= mb_substr($w, 0, 1);
            }
            $initials = strtoupper($initials ?: 'C');

            $id = 'customer-' . time() . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
            $stmt = $pdo->prepare("INSERT INTO customers (id, name, business, location, initials, balance, last_payment, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id, $name, $business, $location, $initials, $balance, 'No payments yet', 'Current']);

            echo json_encode(['success' => true, 'id' => $id]);
            break;

        // --- SETTINGS (Currency) ---
        case 'get_currency':
            $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'currency'");
            $stmt->execute();
            $val = $stmt->fetchColumn();
            echo json_encode(['success' => true, 'currency' => $val ?: 'AFN']);
            break;

        case 'set_currency':
            $curr = trim($input['currency'] ?? 'AFN');
            if (in_array($curr, ['AFN', 'PKR', 'USD'])) {
                $stmt = $pdo->prepare("REPLACE INTO settings (`key`, `value`) VALUES ('currency', ?)");
                $stmt->execute([$curr]);
            }
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action.']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

