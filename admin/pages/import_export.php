<?php
require_once __DIR__ . '/../includes/import_export_helpers.php';

admin_ie_ensure_schema($pdo);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_action'] ?? '') === 'import_products') {
    $result = admin_ie_import_products($pdo, $_FILES['csv_file'] ?? [], $_POST['import_mode'] ?? 'add');
    set_flash($result['message'], ($result['success'] && empty($result['errors'])) ? 'success' : 'error');
    redirect_admin('import_export');
}

$totalProducts = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$activeProducts = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
$brands = $pdo->query('SELECT id, name FROM brands WHERE active = 1 ORDER BY name')->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories WHERE active = 1 ORDER BY name')->fetchAll();
$availableColumns = admin_ie_export_columns($pdo);
$defaultColumns = admin_ie_default_export_columns($pdo);
$history = admin_ie_import_history($pdo);
?>

<div class="animate-slide">
<?php render_flash(); ?>

<div class="mb-6">
    <h2 class="text-xl font-black text-slate-800 flex items-center gap-2"><i class="ri-upload-cloud-2-line text-navy-600"></i> Product Import / Export</h2>
    <p class="text-sm text-slate-400">Bulk manage products via CSV files</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">

    <!-- Import -->
    <form method="POST" enctype="multipart/form-data" id="import-form" onsubmit="return confirmImport(this)" class="bg-white border border-slate-200 rounded-2xl p-6">
        <input type="hidden" name="form_action" value="import_products">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="bg-emerald-100 rounded-xl w-11 h-11 flex items-center justify-center shrink-0">
                <i class="ri-upload-2-line text-emerald-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Import Products</h3>
                <p class="text-xs text-slate-400">Upload CSV to add or update products</p>
            </div>
        </div>

        <div id="drop-zone" class="border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-emerald-400 hover:bg-emerald-50/50 transition cursor-pointer group mb-5"
            ondragover="event.preventDefault();this.classList.add('border-emerald-500','bg-emerald-50')"
            ondragleave="this.classList.remove('border-emerald-500','bg-emerald-50')"
            ondrop="handleDrop(event)"
            onclick="document.getElementById('csv-input').click()">
            <input type="file" name="csv_file" id="csv-input" accept=".csv,text/csv" class="hidden" onchange="handleFile(this)">
            <i class="ri-file-excel-2-line text-slate-300 group-hover:text-emerald-500 text-5xl mb-3 transition"></i>
            <p class="font-bold text-slate-600 group-hover:text-emerald-700 transition">Drag & drop your CSV here</p>
            <p class="text-sm text-slate-400 mt-1">or <span class="text-emerald-600 font-semibold underline">browse file</span></p>
            <p class="text-xs text-slate-400 mt-3">Supported: .csv - Max 10MB</p>
        </div>

        <div id="file-selected" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3 mb-4">
            <i class="ri-file-excel-2-fill text-emerald-600 text-2xl shrink-0"></i>
            <div class="flex-1 min-w-0">
                <p id="file-name" class="font-bold text-slate-800 text-sm truncate">products.csv</p>
                <p id="file-size" class="text-xs text-slate-400">0 MB</p>
            </div>
            <button type="button" onclick="clearFile()" class="text-slate-400 hover:text-red-500 transition"><i class="ri-close-line text-lg"></i></button>
        </div>

        <div class="space-y-3 mb-5">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-wider">Import Options</p>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="radio" name="import_mode" value="add" checked class="accent-navy-600">
                <div>
                    <span class="text-sm font-semibold text-slate-700">Add New Products Only</span>
                    <p class="text-xs text-slate-400">Skip existing products matched by ID, slug, or name</p>
                </div>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="radio" name="import_mode" value="update" class="accent-navy-600">
                <div>
                    <span class="text-sm font-semibold text-slate-700">Update Existing + Add New</span>
                    <p class="text-xs text-slate-400">Update matches and insert the rest</p>
                </div>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="radio" name="import_mode" value="replace" class="accent-navy-600">
                <div>
                    <span class="text-sm font-semibold text-slate-700">Replace All Products</span>
                    <p class="text-xs text-red-500 font-semibold flex items-center gap-1"><i class="ri-error-warning-line"></i> This deletes existing products before import</p>
                </div>
            </label>
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm">
            <i class="ri-upload-2-line"></i> Start Import
        </button>
    </form>

    <!-- Export -->
    <form method="POST" action="import_export_download.php" class="bg-white border border-slate-200 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="bg-navy-100 rounded-xl w-11 h-11 flex items-center justify-center shrink-0">
                <i class="ri-download-2-line text-navy-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Export Products</h3>
                <p class="text-xs text-slate-400">Download your product data as CSV</p>
            </div>
        </div>

        <div class="space-y-3 mb-5">
            <p class="text-xs font-bold text-slate-600 uppercase tracking-wider">Export Scope</p>
            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-slate-200 hover:border-navy-400 hover:bg-navy-50 transition">
                <input type="radio" name="export_scope" value="all" checked class="accent-navy-600">
                <div class="flex-1">
                    <span class="text-sm font-semibold text-slate-700">All Products</span>
                    <p class="text-xs text-slate-400"><?php echo (int)$totalProducts; ?> products</p>
                </div>
                <span class="text-xs bg-navy-100 text-navy-700 font-bold px-2 py-0.5 rounded-lg"><?php echo (int)$totalProducts; ?></span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-slate-200 hover:border-navy-400 hover:bg-navy-50 transition">
                <input type="radio" name="export_scope" value="active" class="accent-navy-600">
                <div class="flex-1">
                    <span class="text-sm font-semibold text-slate-700">Active Products Only</span>
                    <p class="text-xs text-slate-400"><?php echo (int)$activeProducts; ?> products</p>
                </div>
                <span class="text-xs bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-lg"><?php echo (int)$activeProducts; ?></span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-slate-200 hover:border-navy-400 hover:bg-navy-50 transition">
                <input type="radio" name="export_scope" value="filtered" id="scope-filtered" class="accent-navy-600">
                <div class="flex-1">
                    <span class="text-sm font-semibold text-slate-700">Filter by Category / Brand</span>
                    <p class="text-xs text-slate-400">Choose below</p>
                </div>
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Category</label>
                <select name="category_id" onchange="selectFilteredScope()" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600 transition">
                    <option value="0">All Categories</option>
                    <?php foreach($categories as $category): ?>
                    <option value="<?php echo (int)$category['id']; ?>"><?php echo e($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Brand</label>
                <select name="brand_id" onchange="selectFilteredScope()" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600 transition">
                    <option value="0">All Brands</option>
                    <?php foreach($brands as $brand): ?>
                    <option value="<?php echo (int)$brand['id']; ?>"><?php echo e($brand['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Include Columns</p>
        <div class="grid grid-cols-2 gap-2 mb-5 max-h-48 overflow-y-auto pr-1">
            <?php foreach($availableColumns as $key => $label): ?>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="columns[]" value="<?php echo e($key); ?>" <?php echo in_array($key, $defaultColumns, true) ? 'checked' : ''; ?> class="accent-navy-600 rounded">
                <span class="text-sm text-slate-600"><?php echo e($label); ?></span>
            </label>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="w-full bg-navy-600 hover:bg-navy-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm">
            <i class="ri-download-2-line"></i> Export to CSV
        </button>
    </form>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

    <!-- CSV Template -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <h4 class="font-bold text-slate-800 text-sm mb-1 flex items-center gap-2"><i class="ri-file-list-3-line text-emerald-600"></i> CSV Template</h4>
        <p class="text-xs text-slate-400 mb-4">Download a sample CSV to format your data correctly</p>
        <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden mb-4">
            <div class="overflow-x-auto">
                <table class="text-xs w-full">
                    <thead class="bg-slate-100">
                        <tr>
                            <?php foreach(['name','brand','category','price','old_price','stock','status','badge'] as $header): ?>
                            <th class="px-3 py-2 text-left font-bold text-slate-600 whitespace-nowrap"><?php echo e($header); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-200">
                            <td class="px-3 py-2 text-slate-500 whitespace-nowrap">HP DeskJet 4155e</td>
                            <td class="px-3 py-2 text-slate-500">HP</td>
                            <td class="px-3 py-2 text-slate-500">inkjet</td>
                            <td class="px-3 py-2 text-slate-500">89.99</td>
                            <td class="px-3 py-2 text-slate-500">119.99</td>
                            <td class="px-3 py-2 text-slate-500">45</td>
                            <td class="px-3 py-2 text-slate-500">active</td>
                            <td class="px-3 py-2 text-slate-500">SALE</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <a href="import_export_download.php?type=sample" class="w-full flex items-center justify-center gap-2 border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-semibold py-2.5 rounded-xl transition text-sm">
            <i class="ri-download-line"></i> Download Sample CSV
        </a>
    </div>

    <!-- Import History -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <h4 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2"><i class="ri-history-line text-slate-500"></i> Import History</h4>
        <div class="space-y-3">
            <?php if (!$history): ?>
            <div class="p-5 text-center bg-slate-50 rounded-xl text-sm text-slate-400">No imports yet.</div>
            <?php endif; ?>
            <?php
            $statusClasses = [
                'success' => ['bg-emerald-100 text-emerald-700', 'ri-checkbox-circle-fill', 'Success'],
                'warning' => ['bg-amber2-100 text-amber2-700', 'ri-error-warning-fill', 'Warning'],
                'error' => ['bg-red-100 text-red-600', 'ri-close-circle-fill', 'Error'],
            ];
            foreach($history as $item):
                $status = $item['status'] === 'warning' ? 'warning' : ($item['status'] === 'error' ? 'error' : 'success');
                $classes = $statusClasses[$status];
                $summary = sprintf(
                    '%d added, %d updated, %d skipped, %d errors',
                    (int)$item['added_count'],
                    (int)$item['updated_count'],
                    (int)$item['skipped_count'],
                    (int)$item['error_count']
                );
            ?>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                <i class="<?php echo $classes[1]; ?> text-lg <?php echo explode(' ', $classes[0])[1]; ?>"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($item['filename']); ?></p>
                    <p class="text-xs text-slate-400"><?php echo e(date('Y-m-d H:i', strtotime((string)$item['created_at']))); ?> - <?php echo e($summary); ?></p>
                </div>
                <span class="<?php echo $classes[0]; ?> text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo e($classes[2]); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</div>
<script>
function handleFile(input){
    if(input.files.length){
        document.getElementById('file-selected').classList.remove('hidden');
        document.getElementById('file-name').textContent = input.files[0].name;
        const sizeMb = (input.files[0].size / 1024 / 1024).toFixed(2);
        document.getElementById('file-size').textContent = sizeMb + ' MB';
    }
}
function handleDrop(event){
    event.preventDefault();
    event.currentTarget.classList.remove('border-emerald-500','bg-emerald-50');
    if(event.dataTransfer.files.length){
        document.getElementById('csv-input').files = event.dataTransfer.files;
        handleFile(document.getElementById('csv-input'));
    }
}
function clearFile(){
    document.getElementById('file-selected').classList.add('hidden');
    document.getElementById('csv-input').value = '';
}
function confirmImport(form){
    const file = document.getElementById('csv-input');
    if(!file.files.length){
        alert('Please choose a CSV file first.');
        return false;
    }
    const mode = form.querySelector('input[name="import_mode"]:checked')?.value;
    if(mode === 'replace'){
        return confirm('Replace all existing products with this CSV? This cannot be undone automatically.');
    }
    return true;
}
function selectFilteredScope(){
    const filtered = document.getElementById('scope-filtered');
    if(filtered){
        filtered.checked = true;
    }
}
</script>
