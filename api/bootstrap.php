<?php
declare(strict_types=1);

/**
 * API Bootstrap / Router
 * Handles CORS, JSON headers, and routes requests to the correct endpoint handler.
 * 
 * Usage:
 *   All API calls go through: /api/<endpoint>.php
 *   e.g. POST /api/leads.php  →  saves a contact form lead
 *        POST /api/orders.php →  saves a checkout order
 *        GET  /api/products.php?category=inkjet  → lists products
 */

require_once __DIR__ . '/../database/connection.php';
load_env_file();

// Basic autoloader for backend code.
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../backend/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// ── CORS Headers ──────────────────────────────────────────────────────────────
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');   // Restrict to your domain in production
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Pre-flight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function api_success(array $data = [], string $message = 'OK', int $code = 200): void
{
    http_response_code($code);
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}

function api_error(string $message, int $code = 400, array $errors = []): void
{
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message, 'errors' => $errors]);
    exit;
}

function api_require_method(string ...$methods): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'], $methods, true)) {
        api_error('Method Not Allowed. Expected: ' . implode(', ', $methods), 405);
    }
}

/**
 * Read JSON body or fall back to $_POST for multipart/form-data
 */
function api_body(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains($contentType, 'application/json')) {
        $raw = file_get_contents('php://input');
        return (array) json_decode($raw, true);
    }
    return $_POST;
}
