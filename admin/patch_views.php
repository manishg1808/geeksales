<?php
declare(strict_types=1);

$files = [
    'products.php',
    'categories.php',
    'brands.php',
    'orders.php',
    'leads.php',
    'customers.php',
    'refunds.php',
];

$gridFiles = ['categories.php', 'leads.php', 'customers.php', 'refunds.php'];
$tableFiles = ['products.php', 'brands.php', 'orders.php'];

foreach ($files as $file) {
    $path = __DIR__ . '/pages/' . $file;
    if (!is_file($path)) {
        continue;
    }

    $content = (string)file_get_contents($path);

    if (in_array($file, $gridFiles, true)) {
        if (str_contains($content, '<div class="grid ') && !str_contains($content, '<div class="view-wrapper grid ')) {
            $content = str_replace('<div class="grid ', '<div class="view-wrapper grid ', $content);
        } elseif (str_contains($content, '<div class="space-y-4">') && !str_contains($content, '<div class="view-wrapper space-y-4">')) {
            $content = str_replace('<div class="space-y-4">', '<div class="view-wrapper space-y-4">', $content);
        }
    }

    if (in_array($file, $tableFiles, true) && str_contains($content, '<table') && !str_contains($content, 'admin-table')) {
        $content = str_replace('<table', '<table class="admin-table"', $content);
    }

    file_put_contents($path, $content);
}

echo "Patched individual pages." . PHP_EOL;
