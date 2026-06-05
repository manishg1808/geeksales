<?php
declare(strict_types=1);

/**
 * POST /api/leads.php
 * Saves a contact form inquiry into the `leads` table.
 *
 * Expected POST body (application/x-www-form-urlencoded or JSON):
 *   first_name  – required
 *   last_name   – required
 *   email       – required
 *   phone       – optional
 *   topic       – optional  (becomes subject)
 *   order_no    – optional  (appended to subject)
 *   message     – required
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config/database.php';

api_require_method('POST');

$body = api_body();

$firstName = trim($body['first_name'] ?? '');
$lastName  = trim($body['last_name']  ?? '');
$email     = trim($body['email']      ?? '');
$phone     = trim($body['phone']      ?? '');
$topic     = trim($body['topic']      ?? '');
$orderNo   = trim($body['order_no']   ?? '');
$message   = trim($body['message']    ?? '');

// ── Validation ────────────────────────────────────────────────────────────────
$errors = [];
if ($firstName === '')   $errors['first_name'] = 'First name is required.';
if ($lastName  === '')   $errors['last_name']  = 'Last name is required.';
if ($email     === '')   $errors['email']       = 'Email is required.';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address.';
if ($message   === '')   $errors['message']     = 'Message is required.';

if (!empty($errors)) {
    api_error('Validation failed. Please check your inputs.', 422, $errors);
}

// ── Build subject ─────────────────────────────────────────────────────────────
$fullName = $firstName . ' ' . $lastName;
$subject  = $topic ?: 'General Inquiry';
if ($orderNo !== '') {
    $subject .= " (Order: $orderNo)";
}

// ── Insert ────────────────────────────────────────────────────────────────────
try {
    $pdo  = api_db();
    $stmt = $pdo->prepare(
        'INSERT INTO leads (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$fullName, $email, $phone, $subject, $message, 'new']);
    $leadId = (int) $pdo->lastInsertId();

    api_success(['lead_id' => $leadId], 'Your message has been received. We will be in touch shortly.', 201);

} catch (Throwable $e) {
    api_error('Server error: could not save your inquiry. Please try again later.', 500);
}
