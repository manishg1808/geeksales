<?php
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

function category_icon_for_name(string $name): string
{
    $text = strtolower($name);
    if (str_contains($text, 'ink') || str_contains($text, 'toner')) return 'ri-ink-bottle-line';
    if (str_contains($text, 'laser')) return 'ri-fire-line';
    if (str_contains($text, 'all') || str_contains($text, 'copy') || str_contains($text, 'scan')) return 'ri-file-copy-2-line';
    if (str_contains($text, 'business') || str_contains($text, 'office')) return 'ri-building-2-line';
    if (str_contains($text, 'photo')) return 'ri-camera-line';
    if (str_contains($text, 'wireless') || str_contains($text, 'wifi')) return 'ri-wifi-line';
    if (str_contains($text, 'deal') || str_contains($text, 'sale')) return 'ri-flashlight-line';
    return 'ri-printer-line';
}

function category_description_for_name(string $name): string
{
    $clean = trim($name);
    if ($clean === '') return '';
    $text = strtolower($clean);
    if (str_contains($text, 'ink') || str_contains($text, 'toner')) return 'Ink, toner, and replacement supplies for everyday printing.';
    if (str_contains($text, 'laser')) return 'Fast laser printers for sharp documents and high-volume work.';
    if (str_contains($text, 'all') || str_contains($text, 'copy') || str_contains($text, 'scan')) return 'All-in-one printers for printing, scanning, and copying.';
    if (str_contains($text, 'business') || str_contains($text, 'office')) return 'Reliable office printers for teams and business workloads.';
    if (str_contains($text, 'photo')) return 'Photo printers for detailed images and vibrant color output.';
    return $clean . ' products with expert setup support.';
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';
    if ($postAction === 'save_category') {
        $categoryId = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
        $description = trim($_POST['description'] ?? '') ?: category_description_for_name($name);
        $icon = trim($_POST['icon'] ?? '') ?: category_icon_for_name($name);
        $imageUrl = $_POST['existing_image_url'] ?? '';
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['image_file']['tmp_name'];
            $origName = preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image_file']['name']);
            $fileName = time() . '_' . $origName;
            $uploadDir = __DIR__ . '/../../uploads/categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                $imageUrl = 'uploads/categories/' . $fileName;
            }
        }
        $data = [$name, $slug, $description, $icon, trim($imageUrl), trim($_POST['color'] ?? 'navy'), (int)($_POST['active'] ?? 1)];
        if ($name === '') {
            set_flash('Category name is required.', 'error');
        } elseif ($categoryId > 0) {
            $pdo->prepare('UPDATE categories SET name=?, slug=?, description=?, icon=?, image_url=?, color=?, active=? WHERE id=?')->execute([...$data, $categoryId]);
            set_flash('Category updated successfully.');
        } else {
            $pdo->prepare('INSERT INTO categories (name, slug, description, icon, image_url, color, active) VALUES (?, ?, ?, ?, ?, ?, ?)')->execute($data);
            set_flash('Category added successfully.');
        }
        redirect_admin('categories');
    }
    if ($postAction === 'delete_category') {
        admin_delete_record($pdo, 'categories', (int)($_POST['id'] ?? 0), 'Category');
        redirect_admin('categories');
    }

    if ($postAction === 'delete_multiple') {
        $ids = $_POST['selected_ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $validIds = array_filter(array_map('intval', $ids), fn($id) => $id > 0);
            if (!empty($validIds)) {
                $inQuery = implode(',', array_fill(0, count($validIds), '?'));
                $stmt = $pdo->prepare("DELETE FROM categories WHERE id IN ($inQuery)");
                $stmt->execute(array_values($validIds));
                set_flash('Selected categories deleted successfully.');
            }
        } else {
            set_flash('No categories selected.', 'error');
        }
        redirect_admin('categories');
    }
}

$category = ['id'=>0,'name'=>'','slug'=>'','description'=>'','icon'=>'ri-printer-line','image_url'=>'','color'=>'navy','active'=>1];
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $category = $stmt->fetch() ?: $category;
}

$q = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(c.name LIKE ? OR c.slug LIKE ? OR c.description LIKE ?)';
    $term = '%' . $q . '%';
    array_push($params, $term, $term, $term);
}
if (in_array($statusFilter, ['active', 'inactive'], true)) {
    $where[] = 'c.active = ?';
    $params[] = $statusFilter === 'active' ? 1 : 0;
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM categories c $sqlWhere");
$countStmt->execute($params);
$totalCategories = (int)$countStmt->fetchColumn();
$pagination = pagination_state($totalCategories, 10);
$categoriesStmt = $pdo->prepare("
    SELECT c.*, COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    $sqlWhere
    GROUP BY c.id
    ORDER BY c.name
    LIMIT 10 OFFSET " . (int)$pagination['offset'] . "
");
$categoriesStmt->execute($params);
$categoriesStmt = $categoriesStmt->fetchAll();
$categories = $categoriesStmt;

$colorMap = [
    'navy' => ['bg'=>'bg-navy-50','text'=>'text-navy-600','count'=>'bg-navy-100 text-navy-700'],
    'slate' => ['bg'=>'bg-slate-100','text'=>'text-slate-600','count'=>'bg-slate-200 text-slate-700'],
    'emerald' => ['bg'=>'bg-emerald-50','text'=>'text-emerald-600','count'=>'bg-emerald-100 text-emerald-700'],
    'amber' => ['bg'=>'bg-amber2-50','text'=>'text-amber2-600','count'=>'bg-amber2-100 text-amber2-700'],
    'red' => ['bg'=>'bg-red-50','text'=>'text-red-600','count'=>'bg-red-100 text-red-700'],
    'navy' => ['bg'=>'bg-navy-50','text'=>'text-navy-600','count'=>'bg-navy-100 text-navy-700'],
];
?>
<div class="animate-slide">
<?php render_flash(); ?>

<?php if($action === 'add' || $action === 'edit'): ?>
<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=categories" class="text-slate-500 hover:text-slate-800 transition"><i class="ri-arrow-left-line text-xl"></i></a>
        <div>
            <h2 class="text-xl font-black text-slate-800"><?php echo $action==='add' ? 'Add Category' : 'Edit Category'; ?></h2>
            <p class="text-sm text-slate-400">Saved in geeksales.categories</p>
        </div>
    </div>
    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <input type="hidden" name="form_action" value="save_category">
        <input type="hidden" name="id" value="<?php echo (int)$category['id']; ?>">
        <div><label class="block text-sm font-semibold text-slate-700 mb-2">Category Name</label><input name="name" id="category_name" required value="<?php echo e($category['name']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="e.g. Photo Printers"></div>
        <div><label class="block text-sm font-semibold text-slate-700 mb-2">Slug</label><input name="slug" id="category_slug" value="<?php echo e($category['slug']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="auto-generated"></div>
        <div><label class="block text-sm font-semibold text-slate-700 mb-2">Description</label><textarea name="description" id="category_description" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 resize-none" placeholder="auto-generated from category name"><?php echo e($category['description']); ?></textarea></div>
        <div><label class="block text-sm font-semibold text-slate-700 mb-2">Category Image</label><input type="file" name="image_file" accept="image/*" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:border-navy-600 bg-white"><input type="hidden" name="existing_image_url" value="<?php echo e($category['image_url'] ?? ''); ?>"><?php if(!empty($category['image_url'])): ?><div class="mt-2 text-xs text-slate-500">Current: <a href="../<?php echo e($category['image_url']); ?>" target="_blank" class="text-indigo-600 hover:underline"><?php echo e($category['image_url']); ?></a></div><?php endif; ?></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-slate-700 mb-2">Color</label><select name="color" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white"><?php foreach(array_keys($colorMap) as $color): ?><option value="<?php echo e($color); ?>" <?php echo $category['color']===$color?'selected':''; ?>><?php echo e($color); ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-2">Status</label><select name="active" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white"><option value="1" <?php echo (int)$category['active']===1?'selected':''; ?>>Active</option><option value="0" <?php echo (int)$category['active']===0?'selected':''; ?>>Inactive</option></select></div>
        </div>
        <div class="flex gap-3 pt-2"><button class="bg-navy-600 hover:bg-navy-700 text-white font-bold px-8 py-2.5 rounded-xl text-sm flex items-center gap-2"><i class="ri-save-line"></i> Save</button><a href="?page=categories" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-xl text-sm">Cancel</a></div>
    </form>
</div>

<script>
(function () {
    const nameInput = document.getElementById('category_name');
    const slugInput = document.getElementById('category_slug');
    const descInput = document.getElementById('category_description');
    if (!nameInput) return;

    function slugifyClient(value) {
        return value.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
    }

    function descriptionFor(value) {
        const clean = value.trim();
        const text = clean.toLowerCase();
        if (!clean) return '';
        if (text.includes('ink') || text.includes('toner')) return 'Ink, toner, and replacement supplies for everyday printing.';
        if (text.includes('laser')) return 'Fast laser printers for sharp documents and high-volume work.';
        if (text.includes('all') || text.includes('copy') || text.includes('scan')) return 'All-in-one printers for printing, scanning, and copying.';
        if (text.includes('business') || text.includes('office')) return 'Reliable office printers for teams and business workloads.';
        if (text.includes('photo')) return 'Photo printers for detailed images and vibrant color output.';
        return `${clean} products with expert setup support.`;
    }

    nameInput.addEventListener('input', () => {
        slugInput.value = slugifyClient(nameInput.value);
        descInput.value = descriptionFor(nameInput.value);
    });
})();
</script>

<?php else: ?>
<div class="flex items-center justify-between mb-6">
    <div><h2 class="text-xl font-black text-slate-800">Categories</h2><p class="text-sm text-slate-400"><?php echo $totalCategories; ?> categories, 10 per page</p></div>
    <div class="flex items-center gap-2">
        <label class="flex items-center gap-2 text-sm text-slate-600 font-semibold cursor-pointer mr-2">
            <input type="checkbox" id="selectAll" class="w-4 h-4 rounded text-indigo-600 border-slate-300"> Select All
        </label>
        <button onclick="deleteSelected()" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm"><i class="ri-delete-bin-line text-lg"></i> Delete Selected</button>
        <a href="?page=categories&action=add" class="flex items-center gap-2 bg-navy-600 hover:bg-navy-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm"><i class="ri-add-line text-lg"></i> Add Category</a>
    </div>
</div>

<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="page" value="categories">
    <div class="flex-1 min-w-[200px] relative">
        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input name="q" value="<?php echo e($q); ?>" placeholder="Search categories..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-navy-600">
    </div>
    <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="">All Status</option>
        <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
    </select>
    <button class="bg-navy-600 text-white font-bold rounded-xl px-4 py-2 text-sm">Filter</button>
    <?php if($q !== '' || $statusFilter !== ''): ?><a href="?page=categories" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl px-4 py-2 text-sm">Clear</a><?php endif; ?>
</form>

<form id="bulkDeleteForm" method="POST">
<input type="hidden" name="form_action" value="delete_multiple">

<?php $adminView = $_COOKIE['admin_view'] ?? 'list'; ?>
<?php if($adminView === 'grid'): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <div class="col-span-full mb-2">
        <label class="flex items-center gap-2 text-sm text-slate-600 font-semibold cursor-pointer w-fit bg-white px-3 py-1.5 rounded-lg border border-slate-200">
            <input type="checkbox" id="selectAllGrid" class="w-4 h-4 rounded text-indigo-600 border-slate-300"> Select All
        </label>
    </div>
    <?php foreach($categories as $cat): $c = $colorMap[$cat['color']] ?? $colorMap['navy']; ?>
    <div class="card-hover bg-white border border-slate-200 rounded-2xl p-5 relative">
        <input type="checkbox" name="selected_ids[]" value="<?php echo (int)$cat['id']; ?>" class="item-checkbox absolute top-4 right-4 w-4 h-4 rounded text-indigo-600 border-slate-300 z-10">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="<?php echo $c['bg']; ?> rounded-xl w-12 h-12 flex items-center justify-center overflow-hidden"><?php if(!empty($cat['image_url'])): ?><img src="../<?php echo e($cat['image_url']); ?>" alt="<?php echo e($cat['name']); ?>" class="w-full h-full object-cover"><?php else: ?><i class="<?php echo e($cat['icon']); ?> <?php echo $c['text']; ?> text-xl"></i><?php endif; ?></div>
                <div><h4 class="font-bold text-slate-800 text-sm"><?php echo e($cat['name']); ?></h4><span class="text-xs text-slate-400">/<?php echo e($cat['slug']); ?></span></div>
            </div>
            <span class="<?php echo $c['count']; ?> text-xs font-bold px-2 py-1 rounded-lg"><?php echo (int)$cat['product_count']; ?> products</span>
        </div>
        <p class="text-xs text-slate-500 mb-4"><?php echo e($cat['description']); ?></p>
        <div class="flex gap-2">
            <a href="?page=categories&action=edit&id=<?php echo (int)$cat['id']; ?>" class="flex-1 text-center border border-navy-200 text-navy-600 hover:bg-navy-50 text-xs font-semibold py-2 rounded-xl"><i class="ri-edit-line"></i> Edit</a>
            <button type="button" onclick="deleteSingle(<?php echo (int)$cat['id']; ?>)" class="flex-1 border border-red-200 text-red-500 hover:bg-red-50 text-xs font-semibold py-2 rounded-xl"><i class="ri-delete-bin-line"></i> Delete</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<script>
document.getElementById('selectAllGrid')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
<?php else: ?>
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5 text-left w-10"><input type="checkbox" id="selectAllList" class="w-4 h-4 rounded text-indigo-600 border-slate-300"></th>
                    <th class="px-5 py-3.5 text-left">Category</th>
                    <th class="px-5 py-3.5 text-left hidden sm:table-cell">Description</th>
                    <th class="px-5 py-3.5 text-left">Products</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($categories as $cat): $c = $colorMap[$cat['color']] ?? $colorMap['navy']; ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5"><input type="checkbox" name="selected_ids[]" value="<?php echo (int)$cat['id']; ?>" class="item-checkbox w-4 h-4 rounded text-indigo-600 border-slate-300"></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="<?php echo $c['bg']; ?> rounded-xl w-10 h-10 flex items-center justify-center shrink-0 overflow-hidden"><?php if(!empty($cat['image_url'])): ?><img src="../<?php echo e($cat['image_url']); ?>" alt="<?php echo e($cat['name']); ?>" class="w-full h-full object-cover"><?php else: ?><i class="<?php echo e($cat['icon']); ?> <?php echo $c['text']; ?> text-lg"></i><?php endif; ?></div>
                            <div><div class="font-bold text-slate-800"><?php echo e($cat['name']); ?></div><div class="text-[10px] text-slate-400">/<?php echo e($cat['slug']); ?></div></div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-slate-500 text-xs max-w-xs truncate hidden sm:table-cell"><?php echo e($cat['description']); ?></td>
                    <td class="px-5 py-3.5"><span class="<?php echo $c['count']; ?> text-xs font-bold px-2 py-1 rounded-lg"><?php echo (int)$cat['product_count']; ?></span></td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="?page=categories&action=edit&id=<?php echo (int)$cat['id']; ?>" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center"><i class="ri-edit-line"></i></a>
                            <button type="button" onclick="deleteSingle(<?php echo (int)$cat['id']; ?>)" class="text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(!$categories): ?><tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">No categories found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.getElementById('selectAllList')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
<?php endif; ?>
</form>

<form method="POST" id="singleDeleteForm" class="hidden">
    <input type="hidden" name="form_action" value="delete_category">
    <input type="hidden" name="id" id="singleDeleteId" value="">
</form>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});

function deleteSelected() {
    const selected = document.querySelectorAll('.item-checkbox:checked');
    if(selected.length === 0) {
        alert('Please select at least one category to delete.');
        return;
    }
    if(confirm('Are you sure you want to delete the selected categories? Products will lose their category.')) {
        document.getElementById('bulkDeleteForm').submit();
    }
}

function deleteSingle(id) {
    if(confirm('Delete this category? Products will lose this category.')) {
        document.getElementById('singleDeleteId').value = id;
        document.getElementById('singleDeleteForm').submit();
    }
}
</script>

<?php render_pagination($totalCategories, $pagination); ?>
<?php endif; ?>
</div>
