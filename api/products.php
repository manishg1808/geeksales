<?php
declare(strict_types=1);

/**
 * GET  /api/products.php                     → list all active products (paginated)
 * GET  /api/products.php?id=5                → single product by ID
 * GET  /api/products.php?category=inkjet     → filter by category slug
 * GET  /api/products.php?brand=hp            → filter by brand slug
 * GET  /api/products.php?q=deskjet           → full-text search
 * GET  /api/products.php?featured=1          → only featured products
 * GET  /api/products.php?top_pick=1          → only top-pick products
 * GET  /api/products.php?limit=10&page=2     → pagination
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/config/database.php';

use App\Models\ProductModel;

api_require_method('GET');

$pdo = api_db();
$model = new ProductModel($pdo);

// ── Single product by ID ──────────────────────────────────────────────────────
if (!empty($_GET['id'])) {
    $id   = (int) $_GET['id'];
    $product = $model->getById($id);

    if (!$product) {
        api_error('Product not found.', 404);
    }
    
    $related = $model->getRelated((int)$product['id'], (int)($product['category_id'] ?? 0));

    api_success(['product' => $product, 'related' => $related]);
}

// ── Build WHERE clause ────────────────────────────────────────────────────────
$where  = ['p.status = "active"'];
$params = [];

if (!empty($_GET['q'])) {
    $where[]  = '(p.name LIKE ? OR p.slug LIKE ? OR p.model LIKE ? OR p.badge LIKE ? OR p.description LIKE ? OR p.short_description LIKE ? OR b.name LIKE ? OR c.name LIKE ?)';
    $term     = '%' . $_GET['q'] . '%';
    $params   = array_merge($params, [$term, $term, $term, $term, $term, $term, $term, $term]);
}

if (!empty($_GET['category'])) {
    $where[]  = 'c.slug = ?';
    $params[] = match ($_GET['category']) {
        'allinone' => 'all-in-one',
        'ink' => 'ink-toner',
        default => $_GET['category'],
    };
}

if (!empty($_GET['brand'])) {
    $where[]  = 'b.slug = ?';
    $params[] = $_GET['brand'];
}

if (isset($_GET['featured']) && (int)$_GET['featured'] === 1) {
    $where[] = 'p.featured = 1';
}

if (isset($_GET['top_pick']) && (int)$_GET['top_pick'] === 1) {
    $where[] = 'p.top_pick = 1';
}

$whereSQL = 'WHERE ' . implode(' AND ', $where);

// ── Pagination ────────────────────────────────────────────────────────────────
$limit  = max(1, min(100, (int) ($_GET['limit'] ?? 12)));
$page   = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// ── Sorting ───────────────────────────────────────────────────────────────────
$allowedSort = ['price_asc', 'price_desc', 'rating', 'newest', 'name'];
$sortParam   = in_array($_GET['sort'] ?? '', $allowedSort, true) ? $_GET['sort'] : 'newest';
$orderBy = match ($sortParam) {
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'rating'     => 'p.rating DESC',
    'name'       => 'p.name ASC',
    default      => 'p.id DESC',
};

// ── Count total ───────────────────────────────────────────────────────────────
$countStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM products p
     LEFT JOIN brands b ON b.id = p.brand_id
     LEFT JOIN categories c ON c.id = p.category_id
     $whereSQL"
);
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

// ── Fetch products ────────────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    "SELECT p.id, p.name, p.slug, p.model, p.category_id, p.price, p.old_price, p.badge,
            p.rating, p.stock, p.short_description, p.description,
            p.specifications, p.image_icon, p.image_url,
            p.top_pick, p.featured, p.status,
            b.name AS brand_name, b.slug AS brand_slug,
            c.name AS category_name, c.slug AS category_slug
     FROM products p
     LEFT JOIN brands b ON b.id = p.brand_id
     LEFT JOIN categories c ON c.id = p.category_id
     $whereSQL
     ORDER BY $orderBy
     LIMIT $limit OFFSET $offset"
);
$stmt->execute($params);
$products = array_map(fn (array $row): array => $model->formatProduct($row), $stmt->fetchAll());

api_success([
    'products'   => $products,
    'pagination' => [
        'total'        => $total,
        'page'         => $page,
        'limit'        => $limit,
        'total_pages'  => (int) ceil($total / $limit),
    ],
]);
