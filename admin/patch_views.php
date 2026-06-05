<?php
$files = [
    'products.php', 'categories.php', 'brands.php', 
    'orders.php', 'leads.php', 'customers.php', 'refunds.php'
];

foreach ($files as $f) {
    $path = __DIR__ . '/pages/' . $f;
    if (!file_exists($path)) continue;
    $content = file_get_contents($path);

    // For grids
    if (in_array($f, ['categories.php', 'leads.php', 'customers.php', 'refunds.php'])) {
        $content = str_replace('<div class="grid grid-cols-1', '<div class="view-wrapper grid grid-cols-1', $content);
        $content = str_replace('<div class="space-y-4">', '<div class="view-wrapper space-y-4">', $content);
    }

    // For tables
    if (in_array($f, ['products.php', 'brands.php', 'orders.php'])) {
        $content = str_replace('<table class="w-full', '<table class="admin-table w-full', $content);
    }

    file_put_contents($path, $content);
}
echo "Patched files\n";
