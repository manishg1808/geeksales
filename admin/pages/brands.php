<?php
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';
    if ($postAction === 'save_brand') {
        $brandId = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
        $data = [$name, $slug, '', '', trim($_POST['color'] ?? 'navy'), (int)($_POST['active'] ?? 1)];
        if ($name === '') {
            set_flash('Brand name is required.', 'error');
        } elseif ($brandId > 0) {
            $pdo->prepare('UPDATE brands SET name=?, slug=?, origin=?, website=?, color=?, active=? WHERE id=?')->execute([...$data, $brandId]);
            set_flash('Brand updated successfully.');
        } else {
            $pdo->prepare('INSERT INTO brands (name, slug, origin, website, color, active) VALUES (?, ?, ?, ?, ?, ?)')->execute($data);
            set_flash('Brand added successfully.');
        }
        redirect_admin('brands');
    }
    if ($postAction === 'delete_brand') {
        admin_delete_record($pdo, 'brands', (int)($_POST['id'] ?? 0), 'Brand');
        redirect_admin('brands');
    }

    if ($postAction === 'delete_multiple') {
        $ids = $_POST['selected_ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $validIds = array_filter(array_map('intval', $ids), fn($id) => $id > 0);
            if (!empty($validIds)) {
                $inQuery = implode(',', array_fill(0, count($validIds), '?'));
                $stmt = $pdo->prepare("DELETE FROM brands WHERE id IN ($inQuery)");
                $stmt->execute(array_values($validIds));
                set_flash('Selected brands deleted successfully.');
            }
        } else {
            set_flash('No brands selected.', 'error');
        }
        redirect_admin('brands');
    }
}

$brand = ['id'=>0,'name'=>'','slug'=>'','origin'=>'','website'=>'','color'=>'navy','active'=>1];
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM brands WHERE id = ?');
    $stmt->execute([$id]);
    $brand = $stmt->fetch() ?: $brand;
}

$q = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(b.name LIKE ? OR b.slug LIKE ? OR b.origin LIKE ?)';
    $term = '%' . $q . '%';
    array_push($params, $term, $term, $term);
}
if (in_array($statusFilter, ['active', 'inactive'], true)) {
    $where[] = 'b.active = ?';
    $params[] = $statusFilter === 'active' ? 1 : 0;
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM brands b $sqlWhere");
$countStmt->execute($params);
$totalBrands = (int)$countStmt->fetchColumn();
$pagination = pagination_state($totalBrands, 10);
$brandsStmt = $pdo->prepare("
    SELECT b.*, COUNT(p.id) AS product_count
    FROM brands b
    LEFT JOIN products p ON p.brand_id = b.id
    $sqlWhere
    GROUP BY b.id
    ORDER BY b.name
    LIMIT 10 OFFSET " . (int)$pagination['offset'] . "
");
$brandsStmt->execute($params);
$brands = $brandsStmt->fetchAll();
$iconColors = ['navy'=>'bg-navy-100 text-navy-600','red'=>'bg-red-100 text-red-600','amber'=>'bg-amber2-100 text-amber2-600','emerald'=>'bg-emerald-100 text-emerald-600','slate'=>'bg-slate-100 text-slate-600','navy'=>'bg-navy-100 text-navy-600'];
?>
<div class="animate-slide">
<?php render_flash(); ?>

<?php if($action === 'add' || $action === 'edit'): ?>
<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=brands" class="text-slate-500 hover:text-slate-800 transition"><i class="ri-arrow-left-line text-xl"></i></a>
        <h2 class="text-xl font-black text-slate-800"><?php echo $action==='add' ? 'Add Brand' : 'Edit Brand'; ?></h2>
    </div>
    <form method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <input type="hidden" name="form_action" value="save_brand">
        <input type="hidden" name="id" value="<?php echo (int)$brand['id']; ?>">
        <div><label class="block text-sm font-semibold text-slate-700 mb-2">Brand Name</label><input name="name" id="brand_name" required value="<?php echo e($brand['name']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="e.g. HP"></div>
        <div><label class="block text-sm font-semibold text-slate-700 mb-2">Slug</label><input name="slug" id="brand_slug" value="<?php echo e($brand['slug']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="auto-generated"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-slate-700 mb-2">Color</label><select name="color" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white"><?php foreach(array_keys($iconColors) as $color): ?><option value="<?php echo e($color); ?>" <?php echo $brand['color']===$color?'selected':''; ?>><?php echo e($color); ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-2">Status</label><select name="active" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white"><option value="1" <?php echo (int)$brand['active']===1?'selected':''; ?>>Active</option><option value="0" <?php echo (int)$brand['active']===0?'selected':''; ?>>Inactive</option></select></div>
        </div>
        <div class="flex gap-3 pt-2"><button class="bg-navy-600 hover:bg-navy-700 text-white font-bold px-8 py-2.5 rounded-xl text-sm flex items-center gap-2"><i class="ri-save-line"></i> Save Brand</button><a href="?page=brands" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-xl text-sm">Cancel</a></div>
    </form>
</div>

<script>
(function () {
    const nameInput = document.getElementById('brand_name');
    const slugInput = document.getElementById('brand_slug');
    if (!nameInput || !slugInput) return;
    nameInput.addEventListener('input', () => {
        slugInput.value = nameInput.value.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
    });
})();
</script>

<?php else: ?>
<div class="flex items-center justify-between mb-6">
    <div><h2 class="text-xl font-black text-slate-800">Brands</h2><p class="text-sm text-slate-400"><?php echo $totalBrands; ?> brands registered, 10 per page</p></div>
    <div class="flex gap-2">
        <button onclick="deleteSelected()" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-xl transition text-sm"><i class="ri-delete-bin-line text-lg"></i> Delete Selected</button>
        <a href="?page=brands&action=add" class="flex items-center gap-2 bg-navy-600 hover:bg-navy-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm"><i class="ri-add-line text-lg"></i> Add Brand</a>
    </div>
</div>

<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="page" value="brands">
    <div class="flex-1 min-w-[200px] relative">
        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input name="q" value="<?php echo e($q); ?>" placeholder="Search brands..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-navy-600">
    </div>
    <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="">All Status</option>
        <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
    </select>
    <button class="bg-navy-600 text-white font-bold rounded-xl px-4 py-2 text-sm">Filter</button>
    <?php if($q !== '' || $statusFilter !== ''): ?><a href="?page=brands" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl px-4 py-2 text-sm">Clear</a><?php endif; ?>
</form>

<form id="bulkDeleteForm" method="POST" action="">
    <input type="hidden" name="form_action" value="delete_multiple">

<?php $adminView = $_COOKIE['admin_view'] ?? 'list'; ?>
<?php if($adminView === 'grid'): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <div class="col-span-full mb-2">
        <label class="flex items-center gap-2 text-sm text-slate-600 font-semibold cursor-pointer w-fit bg-white px-3 py-1.5 rounded-lg border border-slate-200">
            <input type="checkbox" id="selectAllGrid" class="w-4 h-4 rounded text-indigo-600 border-slate-300"> Select All
        </label>
    </div>
    <?php foreach($brands as $b): ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-md transition relative group">
        <input type="checkbox" name="selected_ids[]" value="<?php echo (int)$b['id']; ?>" class="item-checkbox absolute top-4 right-4 w-4 h-4 rounded text-indigo-600 border-slate-300 z-10">
        <div class="flex items-start gap-3 mb-4">
            <div class="bg-navy-50 rounded-xl w-12 h-12 flex items-center justify-center shrink-0"><i class="ri-award-fill text-navy-600 text-xl"></i></div>
            <div class="pr-6">
                <h4 class="font-bold text-slate-800 text-sm line-clamp-1" title="<?php echo e($b['name']); ?>"><?php echo e($b['name']); ?></h4>
                <div class="text-xs text-slate-400 mt-1"><?php echo e($b['slug']); ?></div>
            </div>
        </div>
        <p class="text-xs text-slate-500 mb-4 line-clamp-1"><i class="ri-link text-slate-400"></i> products.php?brand=<?php echo e($b['name']); ?></p>
        <div class="flex items-center justify-between mb-4">
            <span class="<?php echo $b['active']?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500'; ?> text-[10px] font-bold px-2 py-0.5 rounded-full block"><?php echo $b['active'] ? 'Active' : 'Inactive'; ?></span>
            <span class="text-xs text-slate-500 font-semibold"><?php echo (int)$b['product_count']; ?> Products</span>
        </div>
        <div class="flex gap-2 border-t border-slate-100 pt-4">
            <a href="?page=brands&action=edit&id=<?php echo (int)$b['id']; ?>" class="flex-1 border border-navy-200 hover:bg-navy-50 text-navy-600 font-semibold py-1.5 rounded-lg text-center text-xs transition"><i class="ri-edit-line"></i> Edit</a>
            <button type="button" onclick="deleteSingle(<?php echo (int)$b['id']; ?>)" class="flex-1 border border-red-200 hover:bg-red-50 text-red-500 font-semibold py-1.5 rounded-lg text-center text-xs transition"><i class="ri-delete-bin-line"></i> Delete</button>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(!$brands): ?><div class="col-span-full py-8 text-center text-slate-400 bg-white border border-slate-200 rounded-2xl">No brands found.</div><?php endif; ?>
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
                    <th class="px-5 py-3.5 text-left">Brand</th>
                    <th class="px-5 py-3.5 text-left hidden sm:table-cell">Catalog Path</th>
                    <th class="px-5 py-3.5 text-left">Products</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($brands as $b): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5"><input type="checkbox" name="selected_ids[]" value="<?php echo (int)$b['id']; ?>" class="item-checkbox w-4 h-4 rounded text-indigo-600 border-slate-300"></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="bg-navy-50 rounded-xl w-10 h-10 flex items-center justify-center shrink-0"><i class="ri-award-fill text-navy-600 text-lg"></i></div>
                            <div>
                                <div class="font-bold text-slate-800"><?php echo e($b['name']); ?></div>
                                <div class="text-[10px] text-slate-400">/<?php echo e($b['slug']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-slate-500 text-xs max-w-xs truncate hidden sm:table-cell">products.php?brand=<?php echo e($b['name']); ?></td>
                    <td class="px-5 py-3.5"><span class="bg-slate-100 text-slate-700 text-xs font-bold px-2 py-1 rounded-lg"><?php echo (int)$b['product_count']; ?></span></td>
                    <td class="px-5 py-3.5"><span class="<?php echo $b['active']?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500'; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo $b['active'] ? 'Active' : 'Inactive'; ?></span></td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="?page=brands&action=edit&id=<?php echo (int)$b['id']; ?>" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center"><i class="ri-edit-line"></i></a>
                            <button type="button" onclick="deleteSingle(<?php echo (int)$b['id']; ?>)" class="text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(!$brands): ?><tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">No brands found.</td></tr><?php endif; ?>
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
    <?php render_pagination($totalBrands, $pagination); ?>
</div>
</form>

<form method="POST" id="singleDeleteForm" class="hidden">
    <input type="hidden" name="form_action" value="delete_brand">
    <input type="hidden" name="id" id="singleDeleteId" value="">
</form>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});

function deleteSelected() {
    const selected = document.querySelectorAll('.item-checkbox:checked');
    if(selected.length === 0) {
        alert('Please select at least one brand to delete.');
        return;
    }
    if(confirm('Are you sure you want to delete the selected brands? Products will lose their brand.')) {
        document.getElementById('bulkDeleteForm').submit();
    }
}

function deleteSingle(id) {
    if(confirm('Delete this brand? Products will lose this brand.')) {
        document.getElementById('singleDeleteId').value = id;
        document.getElementById('singleDeleteForm').submit();
    }
}
</script>
<?php endif; ?>
</div>
