<?php
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$brands = $pdo->query('SELECT id, name FROM brands WHERE active = 1 ORDER BY name')->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories WHERE active = 1 ORDER BY name')->fetchAll();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';

    if ($postAction === 'save_product') {
        $productId = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
        $model = trim($_POST['model'] ?? '');
        $rating = (float)($_POST['rating'] ?? 5.0);
        $shortDesc = trim($_POST['short_description'] ?? '');
        $specs = trim($_POST['specifications'] ?? '');
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDesc = trim($_POST['meta_description'] ?? '');
        $metaKeys = trim($_POST['meta_keywords'] ?? '');
        $topPick = isset($_POST['top_pick']) ? 1 : 0;
        $featured = isset($_POST['featured']) ? 1 : 0;
        $isRelated = isset($_POST['is_related']) ? 1 : 0;
        
        // Handle file upload
        $imageUrl = $_POST['existing_image_url'] ?? '';
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['image_file']['tmp_name'];
            $origName = preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image_file']['name']);
            $fileName = time() . '_' . $origName;
            $uploadDir = __DIR__ . '/../../uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                $imageUrl = 'uploads/products/' . $fileName;
            }
        }

        $data = [
            $name,
            $slug,
            $model,
            (int)($_POST['brand_id'] ?? 0) ?: null,
            (int)($_POST['category_id'] ?? 0) ?: null,
            (float)($_POST['price'] ?? 0),
            (float)($_POST['old_price'] ?? 0),
            trim($_POST['badge'] ?? ''),
            $rating,
            (int)($_POST['stock'] ?? 0),
            trim($_POST['description'] ?? ''),
            $shortDesc,
            $specs,
            $metaTitle,
            $metaDesc,
            $metaKeys,
            $_POST['status'] === 'inactive' ? 'inactive' : 'active',
            $imageUrl,
            $topPick,
            $featured,
            $isRelated
        ];

        if ($name === '') {
            set_flash('Product name is required.', 'error');
        } elseif ($productId > 0) {
            $sql = 'UPDATE products SET name=?, slug=?, model=?, brand_id=?, category_id=?, price=?, old_price=?, badge=?, rating=?, stock=?, description=?, short_description=?, specifications=?, meta_title=?, meta_description=?, meta_keywords=?, status=?, image_url=?, top_pick=?, featured=?, is_related=? WHERE id=?';
            $pdo->prepare($sql)->execute([...$data, $productId]);
            set_flash('Product updated successfully.');
        } else {
            $sql = 'INSERT INTO products (name, slug, model, brand_id, category_id, price, old_price, badge, rating, stock, description, short_description, specifications, meta_title, meta_description, meta_keywords, status, image_url, top_pick, featured, is_related) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $pdo->prepare($sql)->execute($data);
            set_flash('Product added successfully.');
        }
        redirect_admin('products');
    }

    if ($postAction === 'delete_product') {
        admin_delete_record($pdo, 'products', (int)($_POST['id'] ?? 0), 'Product');
        redirect_admin('products');
    }

    if ($postAction === 'delete_multiple') {
        $ids = $_POST['selected_ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $validIds = array_filter(array_map('intval', $ids), fn($id) => $id > 0);
            if (!empty($validIds)) {
                $inQuery = implode(',', array_fill(0, count($validIds), '?'));
                $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($inQuery)");
                $stmt->execute(array_values($validIds));
                set_flash('Selected products deleted successfully.');
            }
        } else {
            set_flash('No products selected.', 'error');
        }
        redirect_admin('products');
    }
}

$product = [
    'id' => 0, 'name' => '', 'slug' => '', 'model' => '', 'brand_id' => '', 'category_id' => '',
    'price' => '', 'old_price' => '', 'badge' => '', 'rating' => '5.0', 'stock' => '',
    'description' => '', 'short_description' => '', 'specifications' => '',
    'meta_title' => '', 'meta_description' => '', 'meta_keywords' => '',
    'status' => 'active', 'image_url' => '', 'top_pick' => 0, 'featured' => 0, 'is_related' => 0,
];
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch() ?: $product;
}

$where = [];
$params = [];
$q = trim($_GET['q'] ?? '');
$brandFilter = (int)($_GET['brand'] ?? 0);
$categoryFilter = (int)($_GET['category'] ?? 0);
$statusFilter = $_GET['status'] ?? '';

if ($q !== '') {
    $where[] = '(p.name LIKE ? OR p.slug LIKE ? OR p.model LIKE ? OR p.badge LIKE ? OR p.description LIKE ? OR p.short_description LIKE ? OR b.name LIKE ? OR c.name LIKE ?)';
    $term = '%' . $q . '%';
    array_push($params, $term, $term, $term, $term, $term, $term, $term, $term);
}
if ($brandFilter > 0) {
    $where[] = 'p.brand_id = ?';
    $params[] = $brandFilter;
}
if ($categoryFilter > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $categoryFilter;
}
if (in_array($statusFilter, ['active', 'inactive'], true)) {
    $where[] = 'p.status = ?';
    $params[] = $statusFilter;
}

$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$countStmt = $pdo->prepare("SELECT COUNT(*)
    FROM products p
    LEFT JOIN brands b ON b.id = p.brand_id
    LEFT JOIN categories c ON c.id = p.category_id
    $sqlWhere");
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$pagination = pagination_state($totalProducts, 10);

$stmt = $pdo->prepare("
    SELECT p.*, b.name AS brand, c.name AS category
    FROM products p
    LEFT JOIN brands b ON b.id = p.brand_id
    LEFT JOIN categories c ON c.id = p.category_id
    $sqlWhere
    ORDER BY p.id DESC
    LIMIT 10 OFFSET " . (int)$pagination['offset'] . "
");
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="animate-slide">
<?php render_flash(); ?>

<?php if($action === 'add' || $action === 'edit'): ?>
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=products" class="text-slate-500 hover:text-slate-800 transition"><i class="ri-arrow-left-line text-xl"></i></a>
        <div>
            <h2 class="text-xl font-black text-slate-800"><?php echo $action==='add' ? 'Add New Product' : 'Edit Product'; ?></h2>
            <p class="text-sm text-slate-400">Product details save directly into geeksales database</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
        <input type="hidden" name="form_action" value="save_product">
        <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
        
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-2"><i class="ri-information-line text-navy-600"></i> Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name</label>
                    <input name="name" id="product_name" value="<?php echo e($product['name']); ?>" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 transition" placeholder="e.g. HP DeskJet 4155e">
                    <span class="text-xs text-red-500 font-medium mt-1 block">Product Name is required.</span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">URL Slug</label>
                    <input name="slug" id="product_slug" value="<?php echo e($product['slug']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 transition" placeholder="auto-generated-slug">
                    <p class="text-[11px] text-slate-400 mt-1 leading-normal">Auto generated from product name<br>Slug can be left empty. It will be generated automatically from product name.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Model</label>
                    <input name="model" value="<?php echo e($product['model']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 transition" placeholder="Enter model number">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Category</label>
                    <select name="category_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white">
                        <option value="">Choose Category</option>
                        <?php foreach($categories as $category): ?>
                        <option value="<?php echo (int)$category['id']; ?>" <?php echo (int)$product['category_id'] === (int)$category['id'] ? 'selected' : ''; ?>><?php echo e($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Brand</label>
                    <select name="brand_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white">
                        <option value="">Select Brand</option>
                        <?php foreach($brands as $brand): ?>
                        <option value="<?php echo (int)$brand['id']; ?>" <?php echo (int)$product['brand_id'] === (int)$brand['id'] ? 'selected' : ''; ?>><?php echo e($brand['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Price (USD)</label>
                    <input name="price" type="number" step="0.01" value="<?php echo e($product['price']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Old Price (USD)</label>
                    <input name="old_price" type="number" step="0.01" value="<?php echo e($product['old_price']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Rating</label>
                    <input name="rating" type="number" step="0.1" min="0" max="5" value="<?php echo e($product['rating']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="5.0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Stock Quantity</label>
                    <input name="stock" type="number" value="<?php echo e($product['stock']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Badge</label>
                    <input name="badge" value="<?php echo e($product['badge']); ?>" placeholder="SALE, NEW, BEST SELLER" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">File Upload Image</label>
                    <input type="file" name="image_file" accept="image/*" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:border-navy-600 bg-white">
                    <input type="hidden" name="existing_image_url" value="<?php echo e($product['image_url']); ?>">
                    <?php if($product['image_url']): ?>
                    <div class="mt-2 text-xs text-slate-500 flex items-center gap-2">
                         <span>Current Image:</span>
                         <a href="../<?php echo e($product['image_url']); ?>" target="_blank" class="text-indigo-600 hover:underline"><?php echo e($product['image_url']); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Short Description</label>
                    <textarea name="short_description" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 resize-none" placeholder="Brief summary of the product..."><?php echo e($product['short_description']); ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Specifications</label>
                    <textarea name="specifications" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 resize-none" placeholder="Enter key specs, e.g. Resolution: 4800x1200 dpi, Connectivity: Wi-Fi, USB..."><?php echo e($product['specifications']); ?></textarea>
                </div>
                <div class="md:col-span-2 flex flex-col md:flex-row md:items-end gap-6">
                    <div class="w-full md:w-1/2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white outline-none focus:border-navy-600">
                            <option value="active" <?php echo $product['status']==='active'?'selected':''; ?>>Active</option>
                            <option value="inactive" <?php echo $product['status']==='inactive'?'selected':''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="flex flex-wrap gap-5 pb-3">
                        <label class="flex items-center gap-2.5 cursor-pointer text-sm text-slate-700 font-semibold select-none">
                            <input type="checkbox" name="top_pick" value="1" <?php echo $product['top_pick'] ? 'checked' : ''; ?> class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            Top Pick
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer text-sm text-slate-700 font-semibold select-none">
                            <input type="checkbox" name="featured" value="1" <?php echo $product['featured'] ? 'checked' : ''; ?> class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            Featured Product
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer text-sm text-slate-700 font-semibold select-none bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">
                            <input type="checkbox" name="is_related" value="1" <?php echo ($product['is_related'] ?? 0) ? 'checked' : ''; ?> class="w-4 h-4 rounded text-indigo-600 border-indigo-300 focus:ring-indigo-500">
                            <span class="text-indigo-800">Related Product</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <h3 class="font-bold text-slate-700 mt-6 mb-4 text-sm flex items-center gap-2 border-t border-slate-100 pt-6"><i class="ri-search-eye-line text-indigo-600"></i> SEO Information products</h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Title</label>
                    <input name="meta_title" value="<?php echo e($product['meta_title']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 transition" placeholder="SEO optimized title">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 resize-none" placeholder="Brief SEO description..."><?php echo e($product['meta_description']); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Keywords</label>
                    <input name="meta_keywords" value="<?php echo e($product['meta_keywords']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 transition" placeholder="keywords, comma, separated">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-navy-600 hover:bg-navy-700 text-white font-bold px-8 py-3 rounded-xl transition flex items-center gap-2 text-sm"><i class="ri-save-line"></i> Save Product</button>
            <a href="?page=products" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-3 rounded-xl transition text-sm">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('product_name')?.addEventListener('input', function() {
    const name = this.value;
    const slugField = document.getElementById('product_slug');
    if (slugField) {
        slugField.value = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // remove non-alphanumeric chars
            .replace(/\s+/g, '-') // replace spaces with hyphens
            .replace(/-+/g, '-') // collapse consecutive hyphens
            .trim();
    }
});
</script>

<?php else: ?>
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-800">All Products</h2>
        <p class="text-sm text-slate-400"><?php echo $totalProducts; ?> products listed, 10 per page</p>
    </div>
    <div class="flex gap-2">
        <button onclick="deleteSelected()" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-xl transition text-sm"><i class="ri-delete-bin-line text-lg"></i> Delete Selected</button>
        <button onclick="openAddProduct()" class="flex items-center gap-2 bg-navy-600 hover:bg-navy-700 text-white font-bold px-5 py-2.5 rounded-xl transition text-sm"><i class="ri-add-line text-lg"></i> Add Product</button>
    </div>
</div>

<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="page" value="products">
    <div class="flex-1 min-w-[200px] relative">
        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input name="q" value="<?php echo e($q); ?>" placeholder="Search products..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-navy-600">
    </div>
    <select name="brand" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="0">All Brands</option>
        <?php foreach($brands as $brand): ?><option value="<?php echo (int)$brand['id']; ?>" <?php echo $brandFilter === (int)$brand['id'] ? 'selected' : ''; ?>><?php echo e($brand['name']); ?></option><?php endforeach; ?>
    </select>
    <select name="category" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="0">All Categories</option>
        <?php foreach($categories as $category): ?><option value="<?php echo (int)$category['id']; ?>" <?php echo $categoryFilter === (int)$category['id'] ? 'selected' : ''; ?>><?php echo e($category['name']); ?></option><?php endforeach; ?>
    </select>
    <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="">All Status</option>
        <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
    </select>
    <button class="bg-navy-600 text-white font-bold rounded-xl px-4 py-2 text-sm">Filter</button>
</form>

<form id="bulkDeleteForm" method="POST" action="">
    <input type="hidden" name="form_action" value="delete_multiple">
<?php $adminView = $_COOKIE['admin_view'] ?? 'list'; ?>
<?php if($adminView === 'grid'): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    <div class="col-span-full mb-2">
        <label class="flex items-center gap-2 text-sm text-slate-600 font-semibold cursor-pointer w-fit bg-white px-3 py-1.5 rounded-lg border border-slate-200">
            <input type="checkbox" id="selectAllGrid" class="w-4 h-4 rounded text-indigo-600 border-slate-300"> Select All
        </label>
    </div>
    <?php foreach($products as $p): ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-md transition relative group">
        <input type="checkbox" name="selected_ids[]" value="<?php echo (int)$p['id']; ?>" class="item-checkbox absolute top-4 right-4 w-4 h-4 rounded text-indigo-600 border-slate-300 z-10">
        <div class="flex items-start gap-3 mb-4">
            <?php if(!empty($p['image_url'])): ?><img src="../<?php echo e($p['image_url']); ?>" class="rounded-xl w-12 h-12 object-cover shrink-0"><?php else: ?><div class="bg-navy-50 rounded-xl w-12 h-12 flex items-center justify-center shrink-0"><i class="<?php echo e($p['image_icon'] ?? 'ri-printer-line'); ?> text-navy-600 text-xl"></i></div><?php endif; ?>
            <div class="pr-6">
                <h4 class="font-bold text-slate-800 text-sm line-clamp-1" title="<?php echo e($p['name']); ?>"><?php echo e($p['name']); ?></h4>
                <div class="text-xs text-slate-400 mt-1"><?php echo e($p['brand'] ?? '-'); ?> &bull; <?php echo e($p['category'] ?? '-'); ?></div>
            </div>
        </div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="font-black text-slate-800 text-base">$<?php echo number_format((float)$p['price'],2); ?></div>
                <?php if((float)$p['old_price'] > 0): ?><div class="text-[10px] text-slate-400 line-through">$<?php echo number_format((float)$p['old_price'],2); ?></div><?php endif; ?>
            </div>
            <div class="text-right">
                <span class="<?php echo $p['status']==='active'?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500'; ?> text-[10px] font-bold px-2 py-0.5 rounded-full block mb-1"><?php echo ucfirst($p['status']); ?></span>
                <span class="text-[10px] font-semibold <?php echo (int)$p['stock'] < 15 ? 'text-red-600' : 'text-slate-500'; ?>">Stock: <?php echo (int)$p['stock']; ?></span>
            </div>
        </div>
        <div class="flex gap-2 border-t border-slate-100 pt-4">
            <a href="?page=products&action=edit&id=<?php echo (int)$p['id']; ?>" class="flex-1 border border-navy-200 hover:bg-navy-50 text-navy-600 font-semibold py-1.5 rounded-lg text-center text-xs transition"><i class="ri-edit-line"></i> Edit</a>
            <button type="button" onclick="deleteSingle(<?php echo (int)$p['id']; ?>)" class="flex-1 border border-red-200 hover:bg-red-50 text-red-500 font-semibold py-1.5 rounded-lg text-center text-xs transition"><i class="ri-delete-bin-line"></i> Delete</button>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(!$products): ?><div class="col-span-full py-8 text-center text-slate-400 bg-white border border-slate-200 rounded-2xl">No products found.</div><?php endif; ?>
</div>
<script>
document.getElementById('selectAllGrid')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
<?php else: ?>
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="admin-table w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5 text-left w-10"><input type="checkbox" id="selectAll" class="w-4 h-4 rounded text-indigo-600 border-slate-300"></th>
                    <th class="px-5 py-3.5 text-left">#</th><th class="px-5 py-3.5 text-left">Product</th><th class="px-5 py-3.5 text-left">Category</th><th class="px-5 py-3.5 text-left">Brand</th><th class="px-5 py-3.5 text-left">Price</th><th class="px-5 py-3.5 text-left">Stock</th><th class="px-5 py-3.5 text-left">Status</th><th class="px-5 py-3.5 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($products as $p): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5"><input type="checkbox" name="selected_ids[]" value="<?php echo (int)$p['id']; ?>" class="item-checkbox w-4 h-4 rounded text-indigo-600 border-slate-300"></td>
                    <td class="px-5 py-3.5 text-slate-400 font-medium"><?php echo (int)$p['id']; ?></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <?php if(!empty($p['image_url'])): ?><img src="../<?php echo e($p['image_url']); ?>" class="rounded-xl w-10 h-10 object-cover shrink-0"><?php else: ?><div class="bg-navy-50 rounded-xl w-10 h-10 flex items-center justify-center shrink-0"><i class="<?php echo e($p['image_icon'] ?? 'ri-printer-fill'); ?> text-navy-600 text-lg"></i></div><?php endif; ?>
                            <div>
                                <div class="font-bold text-slate-800"><?php echo e($p['name']); ?></div>
                                <?php if($p['badge']): ?><span class="text-[10px] font-bold bg-amber2-100 text-amber2-700 px-1.5 py-0.5 rounded-md"><?php echo e($p['badge']); ?></span><?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-slate-600"><?php echo e($p['category'] ?? '-'); ?></td>
                    <td class="px-5 py-3.5 text-slate-600"><?php echo e($p['brand'] ?? '-'); ?></td>
                    <td class="px-5 py-3.5">
                        <div class="font-black text-slate-800">$<?php echo number_format((float)$p['price'],2); ?></div>
                        <?php if((float)$p['old_price'] > 0): ?><div class="text-xs text-slate-400 line-through">$<?php echo number_format((float)$p['old_price'],2); ?></div><?php endif; ?>
                    </td>
                    <td class="px-5 py-3.5"><span class="font-semibold <?php echo (int)$p['stock'] < 15 ? 'text-red-600' : 'text-slate-700'; ?>"><?php echo (int)$p['stock']; ?></span></td>
                    <td class="px-5 py-3.5"><span class="<?php echo $p['status']==='active'?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500'; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo ucfirst($p['status']); ?></span></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <a href="?page=products&action=edit&id=<?php echo (int)$p['id']; ?>" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center" title="Edit"><i class="ri-edit-line"></i></a>
                            <button type="button" onclick="deleteSingle(<?php echo (int)$p['id']; ?>)" class="text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center" title="Delete"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(!$products): ?><tr><td colspan="8" class="px-5 py-8 text-center text-slate-400">No products found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
    <?php render_pagination($totalProducts, $pagination); ?>
</div>
</form>

<form method="POST" id="singleDeleteForm" class="hidden">
    <input type="hidden" name="form_action" value="delete_product">
    <input type="hidden" name="id" id="singleDeleteId" value="">
</form>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});

function deleteSelected() {
    const selected = document.querySelectorAll('.item-checkbox:checked');
    if(selected.length === 0) {
        alert('Please select at least one item to delete.');
        return;
    }
    if(confirm('Are you sure you want to delete the selected items?')) {
        document.getElementById('bulkDeleteForm').submit();
    }
}

function deleteSingle(id) {
    if(confirm('Delete this product?')) {
        document.getElementById('singleDeleteId').value = id;
        document.getElementById('singleDeleteForm').submit();
    }
}
</script>
<?php endif; ?>
</div>

<!-- Add Product Slide Drawer -->
<div id="addProductOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300" onclick="closeAddProduct()"></div>
<div id="addProductDrawer" class="fixed top-0 right-0 h-full w-full max-w-2xl bg-white shadow-2xl z-50 translate-x-full transition-transform duration-300 flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-navy-700 to-indigo-700 text-white shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="ri-add-box-line text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-base">Add Product</h3>
                <p class="text-[11px] text-indigo-200">Fill in all details to add a new product</p>
            </div>
        </div>
        <button onclick="closeAddProduct()" class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/10 hover:bg-white/20 transition">
            <i class="ri-close-line text-lg"></i>
        </button>
    </div>

    <div class="overflow-y-auto flex-1 p-6">
        <form method="POST" enctype="multipart/form-data" id="addProductForm" class="space-y-5">
            <input type="hidden" name="form_action" value="save_product">
            <input type="hidden" name="id" value="0">

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5">
                <h4 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-2">
                    <i class="ri-information-line text-navy-600"></i> Basic Information
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                        <input name="name" id="add_product_name" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white transition" placeholder="e.g. HP DeskJet 4155e">
                        <span class="text-[11px] text-red-500 mt-0.5 block hidden" id="add_name_err">Product Name is required.</span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">URL Slug</label>
                        <input name="slug" id="add_product_slug" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white transition" placeholder="auto-generated-slug">
                        <p class="text-[10px] text-slate-400 mt-1">Auto generated from product name. Can be left empty.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Model</label>
                        <input name="model" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white transition" placeholder="Enter model number">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Category</label>
                        <select name="category_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white">
                            <option value="">Choose Category</option>
                            <?php foreach($categories as $category): ?>
                            <option value="<?php echo (int)$category['id']; ?>"><?php echo e($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Brand</label>
                        <select name="brand_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white">
                            <option value="">Select Brand</option>
                            <?php foreach($brands as $brand): ?>
                            <option value="<?php echo (int)$brand['id']; ?>"><?php echo e($brand['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Price (USD)</label>
                        <input name="price" type="number" step="0.01" min="0" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Old Price (USD)</label>
                        <input name="old_price" type="number" step="0.01" min="0" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Rating</label>
                        <input name="rating" type="number" step="0.1" min="0" max="5" value="5.0" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white" placeholder="5.0">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Stock Quantity</label>
                        <input name="stock" type="number" min="0" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Badge</label>
                        <input name="badge" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white" placeholder="SALE, NEW, BEST SELLER">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">File Upload Image</label>
                        <input type="file" name="image_file" accept="image/*" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:border-navy-600 bg-white">
                        <input type="hidden" name="existing_image_url" value="">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Short Description</label>
                        <textarea name="short_description" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white resize-none" placeholder="Brief summary of the product..."></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Specifications</label>
                        <textarea name="specifications" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white resize-none" placeholder="Resolution: 4800x1200 dpi, Connectivity: Wi-Fi, USB..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white outline-none focus:border-navy-600">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-semibold text-slate-700 select-none">
                            <input type="checkbox" name="top_pick" value="1" class="w-4 h-4 rounded text-indigo-600 border-slate-300">
                            Top Pick
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-semibold text-slate-700 select-none">
                            <input type="checkbox" name="featured" value="1" class="w-4 h-4 rounded text-indigo-600 border-slate-300">
                            Featured Product
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-semibold select-none bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">
                            <input type="checkbox" name="is_related" value="1" class="w-4 h-4 rounded text-indigo-600 border-indigo-300">
                            <span class="text-indigo-800">Related Product</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5">
                <h4 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-2">
                    <i class="ri-search-eye-line text-indigo-600"></i> SEO Information products
                </h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Title</label>
                        <input name="meta_title" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white transition" placeholder="SEO optimized title">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white resize-none" placeholder="Brief SEO description..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Keywords</label>
                        <input name="meta_keywords" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 bg-white transition" placeholder="keywords, comma, separated">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex gap-3 shrink-0">
        <button type="button" onclick="document.getElementById('addProductForm').submit()" class="flex-1 bg-navy-600 hover:bg-navy-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm">
            <i class="ri-save-line"></i> Save Product
        </button>
        <button type="button" onclick="closeAddProduct()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancel</button>
    </div>
</div>

<script>
function openAddProduct() {
    const overlay = document.getElementById('addProductOverlay');
    const drawer = document.getElementById('addProductDrawer');
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
        overlay.classList.remove('opacity-0');
        drawer.classList.remove('translate-x-full');
    }, 10);
    // Wire up slug autogeneration
    const nameInput = document.getElementById('add_product_name');
    const slugInput = document.getElementById('add_product_slug');
    if (nameInput && !nameInput._slugBound) {
        nameInput._slugBound = true;
        nameInput.addEventListener('input', function() {
            slugInput.value = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        });
    }
}

function closeAddProduct() {
    const overlay = document.getElementById('addProductOverlay');
    const drawer = document.getElementById('addProductDrawer');
    overlay.classList.add('opacity-0');
    drawer.classList.add('translate-x-full');
    document.body.style.overflow = '';
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, 300);
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAddProduct();
});
</script>
