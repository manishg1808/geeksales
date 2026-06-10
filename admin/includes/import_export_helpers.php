<?php
declare(strict_types=1);

function admin_ie_ensure_schema(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS product_import_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            import_mode VARCHAR(30) NOT NULL DEFAULT 'add',
            added_count INT NOT NULL DEFAULT 0,
            updated_count INT NOT NULL DEFAULT 0,
            skipped_count INT NOT NULL DEFAULT 0,
            error_count INT NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT 'success',
            message TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
}

function admin_ie_existing_product_columns(PDO $pdo): array
{
    static $columns = null;
    if (is_array($columns)) {
        return $columns;
    }

    $columns = [];
    $stmt = $pdo->query('SHOW COLUMNS FROM products');
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
        $columns[strtolower((string)$column['Field'])] = true;
    }
    return $columns;
}

function admin_ie_export_columns(PDO $pdo): array
{
    $existing = admin_ie_existing_product_columns($pdo);
    $columns = [
        'id' => 'ID',
        'name' => 'Name',
        'slug' => 'Slug',
        'model' => 'Model',
        'brand' => 'Brand',
        'category' => 'Category',
        'price' => 'Price',
        'old_price' => 'Old Price',
        'stock' => 'Stock',
        'status' => 'Status',
        'badge' => 'Badge',
        'rating' => 'Rating',
        'description' => 'Description',
        'short_description' => 'Short Description',
        'specifications' => 'Specifications',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'meta_keywords' => 'Meta Keywords',
        'image_url' => 'Image URL',
        'top_pick' => 'Top Pick',
        'featured' => 'Featured',
        'is_related' => 'Related',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ];

    return array_filter(
        $columns,
        static function (string $label, string $key) use ($existing): bool {
            return in_array($key, ['brand', 'category'], true) || isset($existing[$key]);
        },
        ARRAY_FILTER_USE_BOTH
    );
}

function admin_ie_default_export_columns(PDO $pdo): array
{
    $available = admin_ie_export_columns($pdo);
    $defaults = ['id', 'name', 'brand', 'category', 'price', 'old_price', 'stock', 'status', 'description', 'badge'];
    return array_values(array_filter($defaults, static fn(string $key): bool => isset($available[$key])));
}

function admin_ie_normalize_header(string $header): string
{
    $header = preg_replace("/^\xEF\xBB\xBF/", '', trim($header)) ?? trim($header);
    $header = strtolower($header);
    $header = preg_replace('/[^a-z0-9]+/', '_', $header) ?? $header;
    $header = trim($header, '_');

    $aliases = [
        'product_name' => 'name',
        'title' => 'name',
        'url_slug' => 'slug',
        'brand_name' => 'brand',
        'category_name' => 'category',
        'oldprice' => 'old_price',
        'regular_price' => 'old_price',
        'sale_price' => 'price',
        'qty' => 'stock',
        'quantity' => 'stock',
        'desc' => 'description',
        'short_desc' => 'short_description',
        'image' => 'image_url',
        'image_url_path' => 'image_url',
        'top' => 'top_pick',
        'top_pick_product' => 'top_pick',
        'featured_product' => 'featured',
        'related' => 'is_related',
    ];

    return $aliases[$header] ?? $header;
}

function admin_ie_string(array $row, string $key, string $fallback = ''): string
{
    if (!array_key_exists($key, $row)) {
        return $fallback;
    }
    return trim((string)$row[$key]);
}

function admin_ie_float(array $row, string $key, float $fallback = 0.0): float
{
    if (!array_key_exists($key, $row)) {
        return $fallback;
    }
    $value = str_replace([',', '$'], '', trim((string)$row[$key]));
    return $value === '' ? 0.0 : (float)$value;
}

function admin_ie_int(array $row, string $key, int $fallback = 0): int
{
    if (!array_key_exists($key, $row)) {
        return $fallback;
    }
    $value = str_replace(',', '', trim((string)$row[$key]));
    return $value === '' ? 0 : (int)$value;
}

function admin_ie_bool(array $row, string $key, int $fallback = 0): int
{
    if (!array_key_exists($key, $row)) {
        return $fallback;
    }
    $value = strtolower(trim((string)$row[$key]));
    if (in_array($value, ['1', 'yes', 'true', 'y', 'on'], true)) {
        return 1;
    }
    if (in_array($value, ['0', 'no', 'false', 'n', 'off', ''], true)) {
        return 0;
    }
    return ((int)$value) > 0 ? 1 : 0;
}

function admin_ie_status(array $row, string $key, string $fallback = 'active'): string
{
    if (!array_key_exists($key, $row)) {
        return in_array($fallback, ['active', 'inactive'], true) ? $fallback : 'active';
    }
    $value = strtolower(trim((string)$row[$key]));
    return in_array($value, ['inactive', 'disabled', 'draft', '0'], true) ? 'inactive' : 'active';
}

function admin_ie_label_from_key(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }
    if ($value === strtolower($value)) {
        return ucwords(str_replace(['-', '_'], ' ', $value));
    }
    return $value;
}

function admin_ie_unique_slug(PDO $pdo, string $table, string $slug, int $ignoreId = 0): string
{
    if (!in_array($table, ['products', 'brands', 'categories'], true)) {
        return slugify($slug);
    }

    $base = slugify($slug);
    $candidate = $base;
    $suffix = 2;
    do {
        $sql = "SELECT id FROM `$table` WHERE slug = ?";
        $params = [$candidate];
        if ($ignoreId > 0) {
            $sql .= ' AND id <> ?';
            $params[] = $ignoreId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $candidate;
        }
        $candidate = $base . '-' . $suffix;
        $suffix++;
    } while ($suffix < 10000);

    return $base . '-' . time();
}

function admin_ie_valid_taxonomy_id(PDO $pdo, string $table, int $id): ?int
{
    if ($id <= 0 || !in_array($table, ['brands', 'categories'], true)) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT id FROM `$table` WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch() ? $id : null;
}

function admin_ie_lookup_taxonomy(PDO $pdo, string $table, string $value): ?int
{
    if (!in_array($table, ['brands', 'categories'], true)) {
        return null;
    }

    $value = trim($value);
    if ($value === '') {
        return null;
    }

    $slug = slugify($value);
    $stmt = $pdo->prepare("SELECT id FROM `$table` WHERE slug = ? OR name = ? LIMIT 1");
    $stmt->execute([$slug, $value]);
    $existing = $stmt->fetch();
    if ($existing) {
        return (int)$existing['id'];
    }

    $name = admin_ie_label_from_key($value);
    $uniqueSlug = admin_ie_unique_slug($pdo, $table, $slug);

    if ($table === 'brands') {
        $stmt = $pdo->prepare("INSERT INTO brands (name, slug, origin, website, color, active) VALUES (?, ?, '', '', 'navy', 1)");
        $stmt->execute([$name, $uniqueSlug]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, icon, color, active) VALUES (?, ?, ?, 'ri-printer-line', 'navy', 1)");
        $stmt->execute([$name, $uniqueSlug, $name . ' products']);
    }

    return (int)$pdo->lastInsertId();
}

function admin_ie_find_existing_product(PDO $pdo, array $row): ?array
{
    $id = admin_ie_int($row, 'id', 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product) {
            return $product;
        }
    }

    $slug = admin_ie_string($row, 'slug');
    if ($slug !== '') {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ? LIMIT 1');
        $stmt->execute([slugify($slug)]);
        $product = $stmt->fetch();
        if ($product) {
            return $product;
        }
    }

    $name = admin_ie_string($row, 'name');
    if ($name !== '') {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE name = ? LIMIT 1');
        $stmt->execute([$name]);
        $product = $stmt->fetch();
        if ($product) {
            return $product;
        }
    }

    return null;
}

function admin_ie_product_data(PDO $pdo, array $row, ?array $existing = null): array
{
    $existingId = (int)($existing['id'] ?? 0);
    $name = admin_ie_string($row, 'name', (string)($existing['name'] ?? ''));
    if ($name === '') {
        throw new RuntimeException('Product name is required.');
    }

    $slugFallback = (string)($existing['slug'] ?? slugify($name));
    $slug = admin_ie_string($row, 'slug', $slugFallback);
    $slug = admin_ie_unique_slug($pdo, 'products', $slug !== '' ? $slug : $name, $existingId);

    $brandId = (int)($existing['brand_id'] ?? 0);
    if (array_key_exists('brand_id', $row)) {
        $brandId = admin_ie_valid_taxonomy_id($pdo, 'brands', admin_ie_int($row, 'brand_id')) ?? 0;
    }
    if (array_key_exists('brand', $row)) {
        $brandId = admin_ie_lookup_taxonomy($pdo, 'brands', admin_ie_string($row, 'brand')) ?? 0;
    }

    $categoryId = (int)($existing['category_id'] ?? 0);
    if (array_key_exists('category_id', $row)) {
        $categoryId = admin_ie_valid_taxonomy_id($pdo, 'categories', admin_ie_int($row, 'category_id')) ?? 0;
    }
    if (array_key_exists('category', $row)) {
        $categoryId = admin_ie_lookup_taxonomy($pdo, 'categories', admin_ie_string($row, 'category')) ?? 0;
    }

    $data = [
        'name' => $name,
        'slug' => $slug,
        'model' => admin_ie_string($row, 'model', (string)($existing['model'] ?? '')),
        'brand_id' => $brandId > 0 ? $brandId : null,
        'category_id' => $categoryId > 0 ? $categoryId : null,
        'price' => admin_ie_float($row, 'price', (float)($existing['price'] ?? 0)),
        'old_price' => admin_ie_float($row, 'old_price', (float)($existing['old_price'] ?? 0)),
        'badge' => admin_ie_string($row, 'badge', (string)($existing['badge'] ?? '')),
        'rating' => max(0, min(5, admin_ie_float($row, 'rating', (float)($existing['rating'] ?? 5.0)))),
        'stock' => admin_ie_int($row, 'stock', (int)($existing['stock'] ?? 0)),
        'description' => admin_ie_string($row, 'description', (string)($existing['description'] ?? '')),
        'short_description' => admin_ie_string($row, 'short_description', (string)($existing['short_description'] ?? '')),
        'specifications' => admin_ie_string($row, 'specifications', (string)($existing['specifications'] ?? '')),
        'meta_title' => admin_ie_string($row, 'meta_title', (string)($existing['meta_title'] ?? '')),
        'meta_description' => admin_ie_string($row, 'meta_description', (string)($existing['meta_description'] ?? '')),
        'meta_keywords' => admin_ie_string($row, 'meta_keywords', (string)($existing['meta_keywords'] ?? '')),
        'status' => admin_ie_status($row, 'status', (string)($existing['status'] ?? 'active')),
        'image_url' => admin_ie_string($row, 'image_url', (string)($existing['image_url'] ?? '')),
        'top_pick' => admin_ie_bool($row, 'top_pick', (int)($existing['top_pick'] ?? 0)),
        'featured' => admin_ie_bool($row, 'featured', (int)($existing['featured'] ?? 0)),
        'is_related' => admin_ie_bool($row, 'is_related', (int)($existing['is_related'] ?? 0)),
    ];

    $existingColumns = admin_ie_existing_product_columns($pdo);
    return array_filter(
        $data,
        static fn($value, string $key): bool => isset($existingColumns[$key]),
        ARRAY_FILTER_USE_BOTH
    );
}

function admin_ie_insert_product(PDO $pdo, array $data): void
{
    $columns = array_keys($data);
    $quoted = array_map(static fn(string $column): string => "`$column`", $columns);
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $sql = 'INSERT INTO products (' . implode(', ', $quoted) . ') VALUES (' . $placeholders . ')';
    $pdo->prepare($sql)->execute(array_values($data));
}

function admin_ie_update_product(PDO $pdo, int $id, array $data): void
{
    $set = implode(', ', array_map(static fn(string $column): string => "`$column` = ?", array_keys($data)));
    $values = array_values($data);
    $values[] = $id;
    $pdo->prepare('UPDATE products SET ' . $set . ' WHERE id = ?')->execute($values);
}

function admin_ie_normalize_csv_row(array $headers, array $data): array
{
    $row = [];
    foreach ($headers as $index => $key) {
        if ($key === '') {
            continue;
        }
        $row[$key] = isset($data[$index]) ? trim((string)$data[$index]) : '';
    }
    return $row;
}

function admin_ie_row_is_empty(array $row): bool
{
    foreach ($row as $value) {
        if (trim((string)$value) !== '') {
            return false;
        }
    }
    return true;
}

function admin_ie_record_history(PDO $pdo, string $filename, string $mode, array $summary): void
{
    admin_ie_ensure_schema($pdo);
    $message = $summary['errors']
        ? implode("\n", array_slice($summary['errors'], 0, 25))
        : ($summary['message'] ?? '');
    $stmt = $pdo->prepare(
        'INSERT INTO product_import_history (filename, import_mode, added_count, updated_count, skipped_count, error_count, status, message)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $filename,
        $mode,
        (int)$summary['added'],
        (int)$summary['updated'],
        (int)$summary['skipped'],
        (int)count($summary['errors']),
        $summary['status'],
        $message,
    ]);
}

function admin_ie_import_products(PDO $pdo, array $file, string $mode): array
{
    admin_ie_ensure_schema($pdo);
    $mode = in_array($mode, ['add', 'update', 'replace'], true) ? $mode : 'add';
    $summary = [
        'success' => false,
        'status' => 'error',
        'added' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => [],
        'message' => '',
    ];

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        $summary['message'] = 'Please choose a CSV file first.';
        return $summary;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $summary['message'] = 'CSV upload failed. Please try again.';
        return $summary;
    }
    if ((int)($file['size'] ?? 0) > 10 * 1024 * 1024) {
        $summary['message'] = 'CSV file is too large. Maximum size is 10MB.';
        return $summary;
    }

    $filename = basename((string)($file['name'] ?? 'products.csv'));
    if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'csv') {
        $summary['message'] = 'Only .csv files are supported.';
        return $summary;
    }

    $handle = fopen((string)$file['tmp_name'], 'rb');
    if (!$handle) {
        $summary['message'] = 'Could not read the uploaded CSV.';
        return $summary;
    }

    $rawHeaders = fgetcsv($handle);
    if (!$rawHeaders) {
        fclose($handle);
        $summary['message'] = 'CSV header row is missing.';
        return $summary;
    }

    $headers = array_map(static fn($header): string => admin_ie_normalize_header((string)$header), $rawHeaders);
    if (!in_array('name', $headers, true) && !in_array('id', $headers, true) && !in_array('slug', $headers, true)) {
        fclose($handle);
        $summary['message'] = 'CSV must include at least one identifier column: name, id, or slug.';
        return $summary;
    }

    $line = 1;
    try {
        $pdo->beginTransaction();
        if ($mode === 'replace') {
            $pdo->exec('DELETE FROM products');
        }

        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            $row = admin_ie_normalize_csv_row($headers, $data);
            if (admin_ie_row_is_empty($row)) {
                continue;
            }

            try {
                $existing = admin_ie_find_existing_product($pdo, $row);
                if ($mode === 'add' && $existing) {
                    $summary['skipped']++;
                    continue;
                }

                $productData = admin_ie_product_data($pdo, $row, $existing);
                if ($existing) {
                    admin_ie_update_product($pdo, (int)$existing['id'], $productData);
                    $summary['updated']++;
                } else {
                    admin_ie_insert_product($pdo, $productData);
                    $summary['added']++;
                }
            } catch (Throwable $rowError) {
                $summary['errors'][] = 'Line ' . $line . ': ' . $rowError->getMessage();
            }
        }

        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fclose($handle);
        $summary['message'] = 'Import failed: ' . $error->getMessage();
        admin_ie_record_history($pdo, $filename, $mode, $summary);
        return $summary;
    }

    fclose($handle);

    try {
        if (function_exists('sync_seo_assets')) {
            sync_seo_assets($pdo);
        }
    } catch (Throwable $syncError) {
        $summary['errors'][] = 'SEO sync warning: ' . $syncError->getMessage();
    }

    $summary['success'] = true;
    $summary['status'] = $summary['errors'] ? 'warning' : 'success';
    $summary['message'] = sprintf(
        'Import complete: %d added, %d updated, %d skipped, %d errors.',
        $summary['added'],
        $summary['updated'],
        $summary['skipped'],
        count($summary['errors'])
    );
    admin_ie_record_history($pdo, $filename, $mode, $summary);
    return $summary;
}

function admin_ie_import_history(PDO $pdo, int $limit = 8): array
{
    admin_ie_ensure_schema($pdo);
    $limit = max(1, min(50, $limit));
    return $pdo->query("SELECT * FROM product_import_history ORDER BY created_at DESC, id DESC LIMIT $limit")->fetchAll();
}

function admin_ie_export_rows(PDO $pdo, string $scope, int $categoryId, int $brandId): array
{
    $where = [];
    $params = [];

    if ($scope === 'active') {
        $where[] = "p.status = 'active'";
    }
    if ($scope === 'filtered') {
        if ($categoryId > 0) {
            $where[] = 'p.category_id = ?';
            $params[] = $categoryId;
        }
        if ($brandId > 0) {
            $where[] = 'p.brand_id = ?';
            $params[] = $brandId;
        }
    }

    $sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $stmt = $pdo->prepare(
        "SELECT p.*, b.name AS brand, c.name AS category
         FROM products p
         LEFT JOIN brands b ON b.id = p.brand_id
         LEFT JOIN categories c ON c.id = p.category_id
         $sqlWhere
         ORDER BY p.id DESC"
    );
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function admin_ie_send_csv(string $filename, array $columns, array $labels, array $rows): void
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'wb');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, array_map(static fn(string $key): string => $labels[$key] ?? $key, $columns));
    foreach ($rows as $row) {
        $line = [];
        foreach ($columns as $column) {
            $line[] = $row[$column] ?? '';
        }
        fputcsv($out, $line);
    }
    fclose($out);
    exit;
}

function admin_ie_send_sample_csv(PDO $pdo): void
{
    $columns = ['name', 'brand', 'category', 'price', 'old_price', 'stock', 'status', 'badge', 'description', 'model', 'rating', 'image_url', 'top_pick', 'featured'];
    $labels = array_combine($columns, $columns);
    $rows = [[
        'name' => 'HP DeskJet 4155e',
        'brand' => 'HP',
        'category' => 'inkjet',
        'price' => '89.99',
        'old_price' => '119.99',
        'stock' => '45',
        'status' => 'active',
        'badge' => 'SALE',
        'description' => 'Wireless all-in-one inkjet printer for home printing, scanning, and copying.',
        'model' => '4155e',
        'rating' => '4.8',
        'image_url' => 'uploads/products/example.jpg',
        'top_pick' => '1',
        'featured' => '1',
    ]];
    admin_ie_send_csv('products_sample.csv', $columns, $labels ?: [], $rows);
}
