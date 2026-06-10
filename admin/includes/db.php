<?php
declare(strict_types=1);

require_once __DIR__ . '/../../database/connection.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = database_connection();
    ensure_schema($pdo);
    return $pdo;
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect_admin(string $page, array $params = []): void
{
    $query = http_build_query(array_merge(['page' => $page], $params));
    $location = 'index.php?' . $query;

    if (!headers_sent()) {
        header('Location: ' . $location);
        exit;
    }

    $jsLocation = json_encode($location, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    echo '<script>window.location.href=' . $jsLocation . ';</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . e($location) . '">';
    echo '<a href="' . e($location) . '">Continue</a></noscript>';
    exit;
}

function set_flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function admin_delete_record(PDO $pdo, string $table, int $id, string $label): void
{
    if ($id <= 0) {
        set_flash('Invalid ' . strtolower($label) . ' selected.', 'error');
        return;
    }

    if (!preg_match('/^[a-z_]+$/', $table)) {
        set_flash($label . ' delete failed.', 'error');
        return;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `$table` WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            set_flash($label . ' deleted successfully.');
        } else {
            set_flash($label . ' not found.', 'error');
        }
    } catch (Throwable $e) {
        set_flash($label . ' delete failed. Please try again.', 'error');
    }
}

function render_flash(): void
{
    if (empty($_SESSION['flash'])) {
        return;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $class = $flash['type'] === 'error'
        ? 'bg-red-50 border-red-200 text-red-700'
        : 'bg-emerald-50 border-emerald-200 text-emerald-700';
    echo '<div class="' . $class . ' border rounded-xl px-4 py-3 mb-5 text-sm font-semibold flex items-center gap-2">';
    echo '<i class="' . ($flash['type'] === 'error' ? 'ri-error-warning-line' : 'ri-checkbox-circle-line') . '"></i>';
    echo e($flash['message']);
    echo '</div>';
}

function slugify(string $text): string
{
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $text), '-'));
    return $slug !== '' ? $slug : 'item-' . time();
}

function table_count(PDO $pdo, string $table): int
{
    return (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
}

function schema_marker_path(): string
{
    return __DIR__ . '/../../database/.admin-schema-ready';
}

function schema_is_ready(PDO $pdo): bool
{
    if (!is_file(schema_marker_path())) {
        return false;
    }

    $requiredColumns = [
        'categories' => ['image_url'],
        'banners' => ['secondary_button_text', 'secondary_link_url', 'image_url', 'poster_style'],
        'orders' => ['payment_method'],
    ];
    try {
        foreach ($requiredColumns as $table => $columns) {
            $existing = [];
            $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
            if (!$stmt) {
                return false;
            }
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
                $existing[] = strtolower($col['Field']);
            }
            foreach ($columns as $column) {
                if (!in_array(strtolower($column), $existing, true)) {
                    return false;
                }
            }
        }
    } catch (Throwable $e) {
        return false;
    }

    return true;
}

function mark_schema_ready(): void
{
    @file_put_contents(schema_marker_path(), date('c'));
}

function pagination_state(int $total, int $perPage = 10, string $param = 'p'): array
{
    $totalPages = max(1, (int)ceil($total / $perPage));
    $current = max(1, min((int)($_GET[$param] ?? 1), $totalPages));
    return [
        'current' => $current,
        'total_pages' => $totalPages,
        'per_page' => $perPage,
        'offset' => ($current - 1) * $perPage,
        'from' => $total > 0 ? (($current - 1) * $perPage) + 1 : 0,
        'to' => min($total, $current * $perPage),
    ];
}

function render_pagination(int $total, array $state, string $param = 'p'): void
{
    if ($total <= $state['per_page']) {
        return;
    }

    echo '<div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">';
    echo '<span class="text-sm text-slate-400">Showing ' . (int)$state['from'] . '-' . (int)$state['to'] . ' of ' . (int)$total . '</span>';
    echo '<div class="flex gap-1">';

    for ($i = 1; $i <= $state['total_pages']; $i++) {
        $query = $_GET;
        $query[$param] = $i;
        $href = '?' . http_build_query($query);
        $class = $i === $state['current']
            ? 'bg-navy-600 text-white'
            : 'bg-slate-100 text-slate-600 hover:bg-navy-100';
        echo '<a href="' . e($href) . '" class="w-8 h-8 rounded-lg ' . $class . ' font-bold text-sm flex items-center justify-center transition">' . $i . '</a>';
    }

    echo '</div></div>';
}

function generate_physical_sitemap(PDO $pdo, string $siteUrl): void
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    try {
        $stmt = $pdo->query('SELECT url, priority, changefreq FROM sitemap_entries WHERE active = 1 ORDER BY priority DESC');
        if ($stmt) {
            foreach ($stmt as $row) {
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($row['url'], ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= "    <changefreq>" . htmlspecialchars($row['changefreq'], ENT_XML1, 'UTF-8') . "</changefreq>\n";
                $xml .= "    <priority>" . htmlspecialchars($row['priority'], ENT_XML1, 'UTF-8') . "</priority>\n";
                $xml .= "  </url>\n";
            }
        }
    } catch (Throwable $e) {
        // ignore
    }
    $xml .= '</urlset>';
    
    @file_put_contents(dirname(__DIR__, 2) . '/sitemap.xml', $xml);
}

function sync_seo_assets(PDO $pdo): void
{
    $siteUrl = rtrim((string)($pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'store_url'")->fetchColumn() ?: 'https://geeksupportllc.com'), '/');

    // Cleanup old sitemap entries with different store URLs
    try {
        $pdo->prepare("DELETE FROM sitemap_entries WHERE url NOT LIKE ?")->execute([$siteUrl . '%']);
    } catch (Throwable $e) {
        // ignore
    }

    // Ensure all default pages exist in seo_meta
    $defaultSeoPages = [
        ['Homepage', 'index.php', 'geeksupportllc - Printer Sales & Setup Support', 'Shop printers, ink, and toner with expert setup support.', 'printer sales, printer setup, ink, toner'],
        ['Products', 'products.php', 'Shop Printers, Ink & Toner', 'Browse top printer models and supplies for home and business.', 'printers, inkjet, laser, toner'],
        ['Support', 'support.php', 'Printer Setup Support', 'Get expert help with wireless printer setup, drivers, and offline issues.', 'printer support, setup help'],
        ['Contact Us', 'contact.php', 'Contact Geek Support LLc - 24/7 Printer Support', 'Get in touch with Geek Support LLc for printer sales, installation, and troubleshooting support.', 'contact printer support, printer setup phone number'],
        ['Privacy Policy', 'policy.php', 'Privacy Policy - Geek Support LLc', 'Read the privacy policy of Geek Support LLc regarding customer information and data protection.', 'privacy policy, terms of service'],
        ['Checkout', 'checkout.php', 'Secure Checkout - Geek Support LLc', 'Complete your purchase securely. Free shipping and 2-year warranty included on printers.', 'checkout, buy printer, secure payment']
    ];
    $checkSeo = $pdo->prepare('SELECT COUNT(*) FROM seo_meta WHERE page_file = ?');
    $insertSeo = $pdo->prepare('INSERT INTO seo_meta (page_name, page_file, meta_title, meta_description, keywords) VALUES (?, ?, ?, ?, ?)');
    foreach ($defaultSeoPages as $row) {
        $checkSeo->execute([$row[1]]);
        if ((int)$checkSeo->fetchColumn() === 0) {
            $insertSeo->execute($row);
        }
    }

    $pageStmt = $pdo->query('SELECT page_name, page_file FROM seo_meta ORDER BY id');
    $upsertSitemap = $pdo->prepare('INSERT INTO sitemap_entries (label, url, source_type, source_id, priority, changefreq, active) VALUES (?, ?, ?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE label=VALUES(label), priority=VALUES(priority), changefreq=VALUES(changefreq), active=1');
    foreach ($pageStmt as $page) {
        $path = $page['page_file'] === 'index.php' ? '/' : '/' . $page['page_file'];
        $upsertSitemap->execute([$page['page_name'], $siteUrl . $path, 'page', 0, $page['page_file'] === 'index.php' ? '1.0' : '0.8', 'weekly']);
    }

    $productStmt = $pdo->query("SELECT id, name, slug FROM products WHERE status = 'active' ORDER BY id");
    foreach ($productStmt as $product) {
        $upsertSitemap->execute([$product['name'], $siteUrl . '/product-detail.php?slug=' . rawurlencode($product['slug']), 'product', (int)$product['id'], '0.7', 'weekly']);
    }

    if (table_count($pdo, 'sitelinks') === 0) {
        $links = [
            ['Shop Printers', '/products.php', 'ri-printer-line', 1],
            ['Tech Support', '/support.php', 'ri-customer-service-2-line', 2],
            ['Contact', '/contact.php', 'ri-phone-line', 3],
            ['Checkout', '/checkout.php', 'ri-shopping-bag-line', 4],
        ];
        $stmt = $pdo->prepare('INSERT INTO sitelinks (label, url, icon, sort_order, active) VALUES (?, ?, ?, ?, 1)');
        foreach ($links as $link) {
            $stmt->execute($link);
        }
    }

    if (table_count($pdo, 'schema_markup') === 0) {
        $schemas = [
            ['Organization', 'site', 0, '{"@context":"https://schema.org","@type":"Organization","name":"geeksupportllc","url":"' . $siteUrl . '"}', 1],
            ['WebSite', 'site', 0, '{"@context":"https://schema.org","@type":"WebSite","name":"geeksupportllc","url":"' . $siteUrl . '"}', 1],
            ['LocalBusiness', 'site', 0, '{"@context":"https://schema.org","@type":"LocalBusiness","name":"geeksupportllc","telephone":"407-246-9887"}', 1],
        ];
        $stmt = $pdo->prepare('INSERT INTO schema_markup (name, target_type, target_id, schema_json, active) VALUES (?, ?, ?, ?, ?)');
        foreach ($schemas as $schema) {
            $stmt->execute($schema);
        }
    }

    $pdo->exec("DELETE FROM schema_markup WHERE target_type = 'product' AND name LIKE '% Product Schema'");
    $productSchemaStmt = $pdo->prepare('INSERT INTO schema_markup (name, target_type, target_id, schema_json, active) VALUES (?, ?, ?, ?, 1)');
    $products = $pdo->query("SELECT id, name, slug, price, description FROM products WHERE status = 'active' ORDER BY id");
    foreach ($products as $product) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'description' => $product['description'],
            'url' => $siteUrl . '/product-detail.php?slug=' . rawurlencode($product['slug']),
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'USD',
                'price' => (string)$product['price'],
                'availability' => 'https://schema.org/InStock',
            ],
        ];
        $productSchemaStmt->execute([$product['name'] . ' Product Schema', 'product', (int)$product['id'], json_encode($schema, JSON_UNESCAPED_SLASHES)]);
    }

    generate_physical_sitemap($pdo, $siteUrl);
}

function ensure_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    if (schema_is_ready($pdo)) {
        return;
    }

    $schema = [
        "CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            email VARCHAR(160) DEFAULT NULL,
            role VARCHAR(80) NOT NULL DEFAULT 'Admin',
            active TINYINT(1) NOT NULL DEFAULT 1,
            last_login DATETIME DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS brands (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            slug VARCHAR(140) NOT NULL UNIQUE,
            origin VARCHAR(80) DEFAULT '',
            website VARCHAR(255) DEFAULT '',
            color VARCHAR(30) DEFAULT 'navy',
            active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            slug VARCHAR(160) NOT NULL UNIQUE,
            description TEXT,
            icon VARCHAR(80) DEFAULT 'ri-printer-line',
            image_url VARCHAR(255) DEFAULT '',
            color VARCHAR(30) DEFAULT 'navy',
            active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(220) NOT NULL,
            slug VARCHAR(240) NOT NULL UNIQUE,
            model VARCHAR(100) DEFAULT '',
            brand_id INT DEFAULT NULL,
            category_id INT DEFAULT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            old_price DECIMAL(10,2) NOT NULL DEFAULT 0,
            badge VARCHAR(60) DEFAULT '',
            rating DECIMAL(2,1) DEFAULT 0.0,
            stock INT NOT NULL DEFAULT 0,
            description TEXT,
            short_description TEXT DEFAULT NULL,
            specifications TEXT DEFAULT NULL,
            meta_title VARCHAR(255) DEFAULT '',
            meta_description TEXT DEFAULT NULL,
            meta_keywords VARCHAR(255) DEFAULT '',
            status ENUM('active','inactive') NOT NULL DEFAULT 'active',
            image_icon VARCHAR(80) DEFAULT 'ri-printer-fill',
            image_url VARCHAR(255) DEFAULT '',
            top_pick TINYINT(1) DEFAULT 0,
            featured TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_products_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
            CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_no VARCHAR(60) NOT NULL UNIQUE,
            customer_name VARCHAR(160) NOT NULL,
            email VARCHAR(160) DEFAULT '',
            phone VARCHAR(40) DEFAULT '',
            product_name VARCHAR(220) NOT NULL,
            amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            payment_method VARCHAR(50) DEFAULT 'manual',
            order_date DATE NOT NULL,
            status ENUM('pending','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS leads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(160) NOT NULL,
            email VARCHAR(160) DEFAULT '',
            phone VARCHAR(40) DEFAULT '',
            subject VARCHAR(220) DEFAULT '',
            message TEXT,
            status ENUM('new','contacted','follow_up','closed') NOT NULL DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS banners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(180) NOT NULL,
            subtitle TEXT,
            badge VARCHAR(60) DEFAULT '',
            button_text VARCHAR(80) DEFAULT 'Shop Now',
            link_url VARCHAR(255) DEFAULT '',
            secondary_button_text VARCHAR(80) DEFAULT '',
            secondary_link_url VARCHAR(255) DEFAULT '',
            image_url VARCHAR(255) DEFAULT '',
            poster_style VARCHAR(40) DEFAULT 'standard',
            location VARCHAR(120) NOT NULL,
            bg_theme VARCHAR(40) DEFAULT 'navy',
            status ENUM('active','inactive','scheduled') NOT NULL DEFAULT 'active',
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            sort_order INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS seo_meta (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_name VARCHAR(120) NOT NULL,
            page_file VARCHAR(120) NOT NULL UNIQUE,
            meta_title VARCHAR(255) DEFAULT '',
            meta_description TEXT,
            keywords TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS refunds (
            id INT AUTO_INCREMENT PRIMARY KEY,
            refund_no VARCHAR(60) NOT NULL UNIQUE,
            order_no VARCHAR(60) DEFAULT '',
            customer_name VARCHAR(160) DEFAULT '',
            amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            reason VARCHAR(255) DEFAULT '',
            status ENUM('requested','approved','rejected','completed') NOT NULL DEFAULT 'requested',
            requested_at DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS settings (
            setting_key VARCHAR(120) PRIMARY KEY,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS sitemap_entries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            label VARCHAR(180) NOT NULL,
            url VARCHAR(255) NOT NULL UNIQUE,
            source_type VARCHAR(40) NOT NULL DEFAULT 'page',
            source_id INT NOT NULL DEFAULT 0,
            priority DECIMAL(2,1) NOT NULL DEFAULT 0.8,
            changefreq VARCHAR(40) NOT NULL DEFAULT 'weekly',
            active TINYINT(1) NOT NULL DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS sitelinks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            label VARCHAR(160) NOT NULL,
            url VARCHAR(255) NOT NULL,
            icon VARCHAR(80) DEFAULT 'ri-links-line',
            sort_order INT NOT NULL DEFAULT 1,
            active TINYINT(1) NOT NULL DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS schema_markup (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(160) NOT NULL,
            target_type VARCHAR(40) NOT NULL DEFAULT 'site',
            target_id INT NOT NULL DEFAULT 0,
            schema_json LONGTEXT NOT NULL,
            active TINYINT(1) NOT NULL DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(160) NOT NULL,
            email VARCHAR(160) NOT NULL UNIQUE,
            phone VARCHAR(40) DEFAULT '',
            address TEXT DEFAULT NULL,
            city VARCHAR(100) DEFAULT '',
            state VARCHAR(80) DEFAULT '',
            zip VARCHAR(20) DEFAULT '',
            country VARCHAR(80) DEFAULT 'USA',
            notes TEXT DEFAULT NULL,
            status ENUM('active','blocked') NOT NULL DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];

    foreach ($schema as $sql) {
        $pdo->exec($sql);
    }

    // Dynamic Migration: Add columns to products table if they don't exist
    try {
        $colsToAdd = [
            'model' => "VARCHAR(100) DEFAULT '' AFTER slug",
            'rating' => "DECIMAL(2,1) DEFAULT 0.0 AFTER badge",
            'short_description' => "TEXT DEFAULT NULL AFTER description",
            'specifications' => "TEXT DEFAULT NULL AFTER short_description",
            'meta_title' => "VARCHAR(255) DEFAULT '' AFTER specifications",
            'meta_description' => "TEXT DEFAULT NULL AFTER meta_title",
            'meta_keywords' => "VARCHAR(255) DEFAULT '' AFTER meta_description",
            'image_url' => "VARCHAR(255) DEFAULT '' AFTER image_icon",
            'top_pick' => "TINYINT(1) DEFAULT 0 AFTER image_url",
            'featured' => "TINYINT(1) DEFAULT 0 AFTER top_pick"
        ];
        
        $existingCols = [];
        $stmtCols = $pdo->query("SHOW COLUMNS FROM products");
        if ($stmtCols) {
            foreach ($stmtCols->fetchAll(PDO::FETCH_ASSOC) as $col) {
                $existingCols[] = strtolower($col['Field']);
            }
            
            foreach ($colsToAdd as $colName => $definition) {
                if (!in_array(strtolower($colName), $existingCols, true)) {
                    $pdo->exec("ALTER TABLE products ADD COLUMN $colName $definition");
                }
            }
        }
    } catch (Exception $e) {
        // Suppress or log migration error
    }

    try {
        $tableColumns = [
            'categories' => [
                'image_url' => "VARCHAR(255) DEFAULT '' AFTER icon",
            ],
            'banners' => [
                'secondary_button_text' => "VARCHAR(80) DEFAULT '' AFTER link_url",
                'secondary_link_url' => "VARCHAR(255) DEFAULT '' AFTER secondary_button_text",
                'image_url' => "VARCHAR(255) DEFAULT '' AFTER secondary_link_url",
                'poster_style' => "VARCHAR(40) DEFAULT 'standard' AFTER image_url",
            ],
            'orders' => [
                'payment_method' => "VARCHAR(50) DEFAULT 'manual' AFTER amount",
            ],
        ];

        foreach ($tableColumns as $table => $columns) {
            $existingCols = [];
            $stmtCols = $pdo->query("SHOW COLUMNS FROM `$table`");
            if (!$stmtCols) {
                continue;
            }
            foreach ($stmtCols->fetchAll(PDO::FETCH_ASSOC) as $col) {
                $existingCols[] = strtolower($col['Field']);
            }
            foreach ($columns as $colName => $definition) {
                if (!in_array(strtolower($colName), $existingCols, true)) {
                    $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$colName` $definition");
                }
            }
        }
    } catch (Exception $e) {
        // Suppress or log migration error
    }

    seed_database($pdo);
    mark_schema_ready();
}

function seed_database(PDO $pdo): void
{
    if (table_count($pdo, 'admin_users') === 0) {
        $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash, email, role) VALUES (?, ?, ?, ?)');
        $stmt->execute(['support@geeksupportllc.com', password_hash('407-246-9887!@#', PASSWORD_DEFAULT), 'support@geeksupportllc.com', 'Super Admin']);
    }

    if (table_count($pdo, 'brands') === 0) {
        $brands = [
            ['HP', 'hp', 'USA', '', 'navy', 1],
            ['Canon', 'canon', 'Japan', '', 'red', 1],
            ['Brother', 'brother', 'Japan', '', 'amber', 1],
            ['Epson', 'epson', 'Japan', '', 'emerald', 1],
            ['Xerox', 'xerox', 'USA', '', 'slate', 0],
            ['Lexmark', 'lexmark', 'USA', '', 'purple', 1],
        ];
        $stmt = $pdo->prepare('INSERT INTO brands (name, slug, origin, website, color, active) VALUES (?, ?, ?, ?, ?, ?)');
        foreach ($brands as $brand) {
            $stmt->execute($brand);
        }
    }

    if (table_count($pdo, 'categories') === 0) {
        $categories = [
            ['Inkjet Printers', 'inkjet', 'Vivid color output printers', 'ri-drop-line', 'navy'],
            ['Laser Printers', 'laser', 'Fast, sharp text printers', 'ri-fire-line', 'slate'],
            ['All-in-One', 'all-in-one', 'Print, scan and copy', 'ri-file-copy-2-line', 'emerald'],
            ['Business', 'business', 'High-volume printing', 'ri-building-2-line', 'amber'],
            ['Ink & Toner', 'ink-toner', 'OEM and compatible cartridges', 'ri-ink-bottle-line', 'red'],
            ['Flash Deals', 'deals', 'Limited time offers', 'ri-flashlight-line', 'purple'],
        ];
        $stmt = $pdo->prepare('INSERT INTO categories (name, slug, description, icon, color) VALUES (?, ?, ?, ?, ?)');
        foreach ($categories as $category) {
            $stmt->execute($category);
        }
    }

    $categoryImages = [
        'inkjet' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=500&q=80',
        'laser' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=500&q=80',
        'all-in-one' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=500&q=80',
        'business' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=500&q=80',
        'ink-toner' => 'https://images.unsplash.com/photo-1606229365485-93a3b8ee0385?auto=format&fit=crop&w=500&q=80',
        'deals' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=500&q=80',
    ];
    $imageStmt = $pdo->prepare("UPDATE categories SET image_url = ? WHERE slug = ? AND (image_url IS NULL OR image_url = '')");
    foreach ($categoryImages as $slug => $imageUrl) {
        $imageStmt->execute([$imageUrl, $slug]);
    }

    if (table_count($pdo, 'products') === 0) {
        $ids = lookup_ids($pdo);
        $products = [
            ['HP DeskJet 4155e', 'hp', 'inkjet', 89.99, 119.99, 45, 'SALE', 'Wireless all-in-one inkjet printer for home printing, scanning, and copying.', 'active'],
            ['Canon PIXMA TR8620', 'canon', 'all-in-one', 149.99, 179.99, 30, 'NEW', 'Home office all-in-one printer with compact wireless convenience.', 'active'],
            ['Brother HL-L2350DW', 'brother', 'laser', 109.99, 139.99, 22, 'BEST SELLER', 'Compact monochrome laser printer for sharp documents.', 'active'],
            ['Epson EcoTank ET-2800', 'epson', 'inkjet', 174.99, 249.99, 18, 'SALE', 'Supertank inkjet printer with low-cost refills.', 'active'],
            ['HP LaserJet Pro M404n', 'hp', 'laser', 249.00, 399.00, 12, 'DEAL', 'Fast business laser printer for teams and offices.', 'active'],
            ['Xerox B215 Multifunction', 'xerox', 'business', 299.99, 0, 8, '', 'Business multifunction printer for reliable workgroups.', 'inactive'],
            ['HP OfficeJet Pro 9015e', 'hp', 'all-in-one', 229.99, 269.99, 20, 'NEW', 'Smart office all-in-one with setup support included.', 'active'],
            ['Canon imageCLASS MF455dw', 'canon', 'business', 329.99, 399.99, 14, 'BEST SELLER', 'Business laser multifunction printer.', 'active'],
            ['Brother MFC-J4335DW', 'brother', 'inkjet', 159.99, 199.99, 25, '', 'Inkjet all-in-one with strong page yield.', 'active'],
            ['Epson WorkForce Pro WF-4830', 'epson', 'business', 199.99, 249.99, 16, 'DEAL', 'Business color all-in-one printer.', 'active'],
            ['HP 65XL Black Ink', 'hp', 'ink-toner', 24.99, 29.99, 70, '', 'Original HP high-yield black ink cartridge.', 'active'],
            ['Brother TN760 Toner', 'brother', 'ink-toner', 49.99, 59.99, 55, '', 'High-yield toner cartridge for Brother laser printers.', 'active'],
        ];
        $stmt = $pdo->prepare('INSERT INTO products (name, slug, brand_id, category_id, price, old_price, stock, badge, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($products as $product) {
            [$name, $brandSlug, $catSlug, $price, $oldPrice, $stock, $badge, $desc, $status] = $product;
            $stmt->execute([$name, slugify($name), $ids['brands'][$brandSlug] ?? null, $ids['categories'][$catSlug] ?? null, $price, $oldPrice, $stock, $badge, $desc, $status]);
        }
    }

    if (table_count($pdo, 'orders') === 0) {
        $orders = [
            ['ORD-1045', 'John Smith', 'john@email.com', '407-246-9887', 'HP DeskJet 4155e', 89.99, '2026-06-03', 'delivered'],
            ['ORD-1044', 'Sarah Lee', 'sarah@email.com', '407-246-9887', 'Canon PIXMA TR8620', 149.99, '2026-06-03', 'pending'],
            ['ORD-1043', 'Mike Johnson', 'mike@email.com', '407-246-9887', 'Brother HL-L2350DW', 109.99, '2026-06-02', 'shipped'],
            ['ORD-1042', 'Emily Davis', 'emily@email.com', '407-246-9887', 'Epson EcoTank ET-2800', 174.99, '2026-06-02', 'cancelled'],
            ['ORD-1041', 'Tom Wilson', 'tom@email.com', '407-246-9887', 'HP LaserJet Pro M404n', 249.00, '2026-06-01', 'delivered'],
        ];
        $stmt = $pdo->prepare('INSERT INTO orders (order_no, customer_name, email, phone, product_name, amount, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($orders as $order) {
            $stmt->execute($order);
        }
    }

    if (table_count($pdo, 'leads') === 0) {
        $leads = [
            ['Alice Brown', 'alice@email.com', '407-246-9887', 'Interested in HP DeskJet 4155e', 'I would like to know more about setup support included.', 'new', '2026-06-04 10:00:00'],
            ['Robert Clark', 'rob@email.com', '407-246-9887', 'Bulk order inquiry', 'We need 20 printers for our office. Please send pricing.', 'contacted', '2026-06-03 09:00:00'],
            ['Mary White', 'mary@email.com', '407-246-9887', 'Ink subscription', 'Do you offer a monthly ink subscription plan?', 'follow_up', '2026-06-03 12:00:00'],
            ['David Green', 'david@email.com', '407-246-9887', 'Warranty question', 'What does the 2-year warranty cover exactly?', 'closed', '2026-06-02 11:00:00'],
        ];
        $stmt = $pdo->prepare('INSERT INTO leads (name, email, phone, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        foreach ($leads as $lead) {
            $stmt->execute($lead);
        }
    }

    if (table_count($pdo, 'banners') === 0) {
        $banners = [
            ['Home Banner 1', 'IMAGE/1.jpg', 1],
            ['Home Banner 2', 'IMAGE/8.png', 2],
            ['Home Banner 3', 'IMAGE/2.jpg', 3],
            ['Home Banner 4', 'IMAGE/6.png', 4],
        ];
        $stmt = $pdo->prepare("INSERT INTO banners (title, subtitle, badge, button_text, link_url, secondary_button_text, secondary_link_url, image_url, poster_style, location, bg_theme, status, start_date, end_date, sort_order) VALUES (?, '', '', '', '', '', '', ?, 'standard', 'Homepage Hero', 'navy', 'active', NULL, NULL, ?)");
        foreach ($banners as $banner) {
            $stmt->execute($banner);
        }
    }

    $posterBanners = [];
    $existsStmt = $pdo->prepare('SELECT COUNT(*) FROM banners WHERE title = ? AND location = ?');
    $insertPoster = $pdo->prepare('INSERT INTO banners (title, subtitle, badge, button_text, link_url, secondary_button_text, secondary_link_url, image_url, poster_style, location, bg_theme, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($posterBanners as $banner) {
        $existsStmt->execute([$banner[0], $banner[9]]);
        if ((int)$existsStmt->fetchColumn() === 0) {
            $insertPoster->execute($banner);
        }
    }

    if (table_count($pdo, 'seo_meta') === 0) {
        $seo = [
            ['Homepage', 'index.php', 'geeksupportllc - Printer Sales & Setup Support', 'Shop printers, ink, and toner with expert setup support.', 'printer sales, printer setup, ink, toner'],
            ['Products', 'products.php', 'Shop Printers, Ink & Toner', 'Browse top printer models and supplies for home and business.', 'printers, inkjet, laser, toner'],
            ['Support', 'support.php', 'Printer Setup Support', 'Get expert help with wireless printer setup, drivers, and offline issues.', 'printer support, setup help'],
        ];
        $stmt = $pdo->prepare('INSERT INTO seo_meta (page_name, page_file, meta_title, meta_description, keywords) VALUES (?, ?, ?, ?, ?)');
        foreach ($seo as $row) {
            $stmt->execute($row);
        }
    }

    if (table_count($pdo, 'refunds') === 0) {
        $refunds = [
            ['REF-1001', 'ORD-1042', 'Emily Davis', 174.99, 'Changed mind', 'requested', '2026-06-03'],
            ['REF-1002', 'ORD-1038', 'Anna King', 49.99, 'Wrong toner ordered', 'approved', '2026-06-02'],
            ['REF-1003', 'ORD-1039', 'James Hall', 19.99, 'Damaged packaging', 'completed', '2026-06-01'],
        ];
        $stmt = $pdo->prepare('INSERT INTO refunds (refund_no, order_no, customer_name, amount, reason, status, requested_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        foreach ($refunds as $refund) {
            $stmt->execute($refund);
        }
    }

    if (table_count($pdo, 'settings') === 0) {
        $settings = [
            'store_name' => 'geeksupportllc',
            'tagline' => 'Your Printer Experts',
            'store_email' => 'support@geeksupportllc.com',
            'phone' => '407-246-9887',
            'store_url' => 'https://geeksupportllc.com',
            'store_address' => '4307 Vineland Road, Suite H-12 Orlando, FL 3281',
            'currency' => 'USD ($)',
            'timezone' => 'Asia/Kolkata',
            'announcement_text' => 'Free Shipping on orders over $99 | Free Expert Setup | 24/7 Tech Support',
            'google_analytics_id' => 'G-9Y0SCZN83K',
            'google_tag_manager_id' => '',
            'google_site_verification' => '',
        ];
        $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)');
        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value]);
        }
    } else {
        // Ensure new settings keys exist
        $ensureSettings = [
            'google_analytics_id' => 'G-9Y0SCZN83K',
            'google_tag_manager_id' => '',
            'google_site_verification' => '',
        ];
        $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM settings WHERE setting_key = ?');
        $insertStmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)');
        foreach ($ensureSettings as $key => $value) {
            $checkStmt->execute([$key]);
            if ((int)$checkStmt->fetchColumn() === 0) {
                $insertStmt->execute([$key, $value]);
            }
        }
    }

    sync_seo_assets($pdo);
}

function lookup_ids(PDO $pdo): array
{
    $brands = [];
    foreach ($pdo->query('SELECT id, slug FROM brands') as $row) {
        $brands[$row['slug']] = (int)$row['id'];
    }
    $categories = [];
    foreach ($pdo->query('SELECT id, slug FROM categories') as $row) {
        $categories[$row['slug']] = (int)$row['id'];
    }
    return ['brands' => $brands, 'categories' => $categories];
}

/* ─────────────────────────────────────────────────────────────────────────────
   GLOBAL SETTINGS & DYNAMIC SEO/ANALYTICS HELPERS
   ───────────────────────────────────────────────────────────────────────────── */

function get_settings(PDO $pdo): array
{
    $settings = [
        'store_name' => 'geeksupportllc',
        'tagline' => 'Your Printer Experts',
        'store_email' => 'support@geeksupportllc.com',
        'phone' => '407-246-9887',
        'store_url' => 'https://geeksupportllc.com',
        'store_address' => '4307 Vineland Road, Suite H-12 Orlando, FL 3281',
        'currency' => 'USD ($)',
        'timezone' => 'Asia/Kolkata',
        'announcement_text' => 'Free Shipping on orders over $99 | Free Expert Setup | 24/7 Tech Support',
        'google_analytics_id' => 'G-9Y0SCZN83K',
        'google_tag_manager_id' => '',
        'google_site_verification' => '',
    ];

    try {
        $stmt = $pdo->query('SELECT setting_key, setting_value FROM settings');
        if ($stmt) {
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $settings[$row['setting_key']] = (string)$row['setting_value'];
            }
        }
    } catch (Throwable $e) {
        // ignore
    }
    return $settings;
}

function get_page_seo(string $pageFile = ''): array
{
    global $settings;
    if ($pageFile === '') {
        $pageFile = basename($_SERVER['PHP_SELF']);
    }
    
    $title = $settings['default_meta_title'] ?? 'geeksupportllc - Printer Sales & Setup Support in Orlando';
    $description = $settings['default_meta_description'] ?? 'Shop printers, ink, and toner with expert setup support.';
    $keywords = $settings['default_meta_keywords'] ?? 'printer sales, printer setup, ink, toner';

    try {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT meta_title, meta_description, keywords FROM seo_meta WHERE page_file = ?');
        $stmt->execute([$pageFile]);
        $row = $stmt->fetch();
        if ($row) {
            if (!empty($row['meta_title'])) {
                $title = $row['meta_title'];
            }
            if (!empty($row['meta_description'])) {
                $description = $row['meta_description'];
            }
            if (!empty($row['keywords'])) {
                $keywords = $row['keywords'];
            }
        }
    } catch (Throwable $e) {
        // ignore
    }

    return [
        'title' => $title,
        'description' => $description,
        'keywords' => $keywords
    ];
}

function render_google_analytics(): void
{
    global $settings;
    $gaId = $settings['google_analytics_id'] ?? '';
    if ($gaId !== '' && $gaId !== 'disabled') {
        $gaId = htmlspecialchars($gaId, ENT_QUOTES, 'UTF-8');
        echo "\n<!-- Google tag (gtag.js) -->\n";
        echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$gaId}\"></script>\n";
        echo "<script>\n";
        echo "  window.dataLayer = window.dataLayer || [];\n";
        echo "  function gtag(){dataLayer.push(arguments);}\n";
        echo "  gtag('js', new Date());\n";
        echo "  gtag('config', '{$gaId}');\n";
        echo "</script>\n";
    }
}

function render_google_tag_manager_head(): void
{
    global $settings;
    $gtmId = $settings['google_tag_manager_id'] ?? '';
    if ($gtmId !== '' && $gtmId !== 'disabled') {
        $gtmId = htmlspecialchars($gtmId, ENT_QUOTES, 'UTF-8');
        echo "\n<!-- Google Tag Manager -->\n";
        echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\n";
        echo "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\n";
        echo "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n";
        echo "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n";
        echo "})(window,document,'script','dataLayer','{$gtmId}');</script>\n";
        echo "<!-- End Google Tag Manager -->\n";
    }
}

function render_google_tag_manager_body(): void
{
    global $settings;
    $gtmId = $settings['google_tag_manager_id'] ?? '';
    if ($gtmId !== '' && $gtmId !== 'disabled') {
        $gtmId = htmlspecialchars($gtmId, ENT_QUOTES, 'UTF-8');
        echo "\n<!-- Google Tag Manager (noscript) -->\n";
        echo "<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id={$gtmId}\"\n";
        echo "height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>\n";
        echo "<!-- End Google Tag Manager (noscript) -->\n";
    }
}

function render_google_site_verification(): void
{
    global $settings;
    $verification = $settings['google_site_verification'] ?? '';
    if ($verification !== '' && $verification !== 'disabled') {
        $verification = htmlspecialchars($verification, ENT_QUOTES, 'UTF-8');
        echo "\n<meta name=\"google-site-verification\" content=\"{$verification}\" />\n";
    }
}

// Load settings globally
$settings = [];
try {
    $settings = get_settings(db());
} catch (Throwable $e) {
    // ignore
}

