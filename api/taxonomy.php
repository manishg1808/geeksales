<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config/database.php';

api_require_method('GET');

$pdo = api_db();

$categories = $pdo->query(
    "SELECT c.id, c.name, c.slug, c.description, c.icon, c.color, COUNT(p.id) AS product_count
     FROM categories c
     LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
     WHERE c.active = 1
     GROUP BY c.id
     ORDER BY c.name"
)->fetchAll();

$brands = $pdo->query(
    "SELECT b.id, b.name, b.slug, b.color, COUNT(p.id) AS product_count
     FROM brands b
     LEFT JOIN products p ON p.brand_id = b.id AND p.status = 'active'
     WHERE b.active = 1
     GROUP BY b.id
     ORDER BY b.name"
)->fetchAll();

api_success([
    'categories' => array_map(static fn (array $row): array => [
        'id' => (int)$row['id'],
        'name' => (string)$row['name'],
        'slug' => (string)$row['slug'],
        'frontend_key' => match ((string)$row['slug']) {
            'all-in-one' => 'allinone',
            'ink-toner' => 'ink',
            default => (string)$row['slug'],
        },
        'description' => (string)($row['description'] ?? ''),
        'icon' => (string)($row['icon'] ?: 'ri-printer-line'),
        'color' => (string)($row['color'] ?: 'navy'),
        'product_count' => (int)$row['product_count'],
    ], $categories),
    'brands' => array_map(static fn (array $row): array => [
        'id' => (int)$row['id'],
        'name' => (string)$row['name'],
        'slug' => (string)$row['slug'],
        'color' => (string)($row['color'] ?: 'navy'),
        'product_count' => (int)$row['product_count'],
    ], $brands),
]);
