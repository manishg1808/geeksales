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
$productName  = trim($body['product_name']  ?? '');
$amount       = (float) ($body['amount']    ?? 0);
$orderNo      = trim($body['order_no']      ?? '');

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

    // Prevent duplicate order numbers
    $check = $pdo->prepare('SELECT id FROM orders WHERE order_no = ?');
    $check->execute([$orderNoClean]);
    if ($check->fetch()) {
        api_error('Duplicate order number. Please try again.', 409);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO orders (order_no, customer_name, email, phone, product_name, amount, order_date, status)
         VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)'
    );
    $stmt->execute([$orderNoClean, $customerName, $email, $phone, $productName, $amount, 'pending']);
    $orderId = (int) $pdo->lastInsertId();

    api_success(
        ['order_id' => $orderId, 'order_no' => $orderNoClean],
        'Order placed successfully.',
        201
    );

} catch (Throwable $e) {
    api_error('Server error: could not save your order. Please try again later.', 500);
}
