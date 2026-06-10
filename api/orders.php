<?php
declare(strict_types=1);

/**
 * POST /api/orders.php
 * Saves a checkout order into the `orders` table.
 *
 * Expected POST body (application/x-www-form-urlencoded or JSON):
 *   customer_name  – required
 *   email          – optional
 *   phone          – optional
 *   product_name   – required  (comma-joined list from cart)
 *   amount         – required  (numeric total)
 *   order_no       – required  (e.g. GSS-12345)
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config/database.php';

api_require_method('POST');

$body = api_body();

$customerName = trim($body['customer_name'] ?? '');
$email        = trim($body['email']         ?? '');
$phone        = trim($body['phone']         ?? '');
$address      = trim($body['address']       ?? '');
$city         = trim($body['city']          ?? '');
$state        = trim($body['state']         ?? '');
$zip          = trim($body['zip']           ?? '');
$productName  = trim($body['product_name']  ?? '');
$amount       = (float) ($body['amount']    ?? 0);
$orderNo      = trim($body['order_no']      ?? '');
$paymentMethod = trim($body['payment_method'] ?? 'Credit / Debit Card');

// ── Validation ────────────────────────────────────────────────────────────────
$errors = [];
if ($customerName === '') $errors['customer_name'] = 'Customer name is required.';
if ($productName  === '') $errors['product_name']  = 'Product name is required.';
if ($orderNo      === '') $errors['order_no']       = 'Order number is required.';
if ($amount       <= 0)   $errors['amount']         = 'Order amount must be greater than 0.';

if (!empty($errors)) {
    api_error('Validation failed.', 422, $errors);
}

// Strip leading '#' if present
$orderNoClean = ltrim($orderNo, '#');

// ── Insert ────────────────────────────────────────────────────────────────────
try {
    $pdo  = api_db();

    $columns = [];
    foreach ($pdo->query('SHOW COLUMNS FROM orders') as $column) {
        $columns[] = strtolower((string)$column['Field']);
    }
    if (!in_array('address', $columns, true)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN address VARCHAR(255) DEFAULT NULL AFTER phone");
    }
    if (!in_array('city', $columns, true)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN city VARCHAR(100) DEFAULT NULL AFTER address");
    }
    if (!in_array('state', $columns, true)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN state VARCHAR(50) DEFAULT NULL AFTER city");
    }
    if (!in_array('zip', $columns, true)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN zip VARCHAR(20) DEFAULT NULL AFTER state");
    }
    if (!in_array('payment_method', $columns, true)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'manual' AFTER amount");
    }
    if (!in_array('created_at', $columns, true)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }

    // Prevent duplicate order numbers
    $check = $pdo->prepare('SELECT id FROM orders WHERE order_no = ?');
    $check->execute([$orderNoClean]);
    if ($check->fetch()) {
        api_error('Duplicate order number. Please try again.', 409);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO orders (order_no, customer_name, email, phone, address, city, state, zip, product_name, amount, payment_method, order_date, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?)'
    );
    $stmt->execute([$orderNoClean, $customerName, $email, $phone, $address, $city, $state, $zip, $productName, $amount, $paymentMethod, 'pending']);
    $orderId = (int) $pdo->lastInsertId();

    api_success(
        ['order_id' => $orderId, 'order_no' => $orderNoClean],
        'Order placed successfully.',
        201
    );

} catch (Throwable $e) {
    api_error('Server error: could not save your order. Please try again later.', 500);
}
