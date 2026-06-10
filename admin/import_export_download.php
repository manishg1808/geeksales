<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/import_export_helpers.php';

$pdo = db();
admin_ie_ensure_schema($pdo);

if (($_GET['type'] ?? '') === 'sample') {
    admin_ie_send_sample_csv($pdo);
}

$availableColumns = admin_ie_export_columns($pdo);
$requestedColumns = $_POST['columns'] ?? [];
if (!is_array($requestedColumns)) {
    $requestedColumns = [];
}

$columns = [];
foreach ($requestedColumns as $column) {
    $column = (string)$column;
    if (isset($availableColumns[$column])) {
        $columns[] = $column;
    }
}

if (!$columns) {
    $columns = admin_ie_default_export_columns($pdo);
}

$scope = $_POST['export_scope'] ?? 'all';
$scope = in_array($scope, ['all', 'active', 'filtered'], true) ? $scope : 'all';
$categoryId = max(0, (int)($_POST['category_id'] ?? 0));
$brandId = max(0, (int)($_POST['brand_id'] ?? 0));

$rows = admin_ie_export_rows($pdo, $scope, $categoryId, $brandId);
$filename = 'products_export_' . date('Ymd_His') . '.csv';
admin_ie_send_csv($filename, $columns, $availableColumns, $rows);
