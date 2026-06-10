<?php
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';
    if ($postAction === 'save_policy') {
        $policyId = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
        $content = trim($_POST['content'] ?? '');
        
        if ($title === '') {
            set_flash('Policy title is required.', 'error');
        } elseif ($policyId > 0) {
            $pdo->prepare('UPDATE policies SET title=?, slug=?, content=? WHERE id=?')->execute([$title, $slug, $content, $policyId]);
            set_flash('Policy updated successfully.');
        } else {
            $pdo->prepare('INSERT INTO policies (title, slug, content) VALUES (?, ?, ?)')->execute([$title, $slug, $content]);
            set_flash('Policy added successfully.');
        }
        redirect_admin('policies');
    }
    if ($postAction === 'delete_policy') {
        admin_delete_record($pdo, 'policies', (int)($_POST['id'] ?? 0), 'Policy');
        redirect_admin('policies');
    }
}

$policy = ['id'=>0,'title'=>'','slug'=>'','content'=>''];
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM policies WHERE id = ?');
    $stmt->execute([$id]);
    $policy = $stmt->fetch() ?: $policy;
}

$policiesStmt = $pdo->query("SELECT * FROM policies ORDER BY id ASC");
$policies = $policiesStmt->fetchAll();
?>

<div class="animate-slide">
<?php render_flash(); ?>

<?php if($action === 'add' || $action === 'edit'): ?>
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=policies" class="text-slate-500 hover:text-slate-800 transition"><i class="ri-arrow-left-line text-xl"></i></a>
        <div>
            <h2 class="text-xl font-black text-slate-800"><?php echo $action==='add' ? 'Add Policy' : 'Edit Policy'; ?></h2>
            <p class="text-sm text-slate-400">Saved in geeksales.policies</p>
        </div>
    </div>
    <form method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <input type="hidden" name="form_action" value="save_policy">
        <input type="hidden" name="id" value="<?php echo (int)$policy['id']; ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Policy Title</label>
                <input name="title" required value="<?php echo e($policy['title']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="e.g. Privacy Policy">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Slug</label>
                <input name="slug" value="<?php echo e($policy['slug']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="auto-generated-slug">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Policy Content</label>
                <textarea name="content" rows="18" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-navy-600 leading-relaxed" placeholder="Return Policy&#10;&#10;Write each section on a new line."><?php echo e($policy['content']); ?></textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 pt-4">
            <a href="?page=policies" class="px-5 py-2.5 text-slate-600 font-semibold hover:bg-slate-50 rounded-xl transition">Cancel</a>
            <button type="submit" class="bg-navy-600 hover:bg-navy-700 text-white font-bold px-6 py-2.5 rounded-xl transition shadow-sm">Save Policy</button>
        </div>
    </form>
</div>

<?php else: ?>
<!-- List View -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-800">Policies</h2>
        <p class="text-sm text-slate-400 mt-1">Manage footer policy pages</p>
    </div>
    <a href="?page=policies&action=add" class="bg-navy-600 hover:bg-navy-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
        <i class="ri-add-line"></i> Add Policy
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <?php if(empty($policies)): ?>
        <div class="p-10 text-center">
            <i class="ri-file-list-3-line text-4xl text-slate-300 mb-3 block"></i>
            <p class="text-slate-500 font-medium">No policies found.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="p-4 pl-6 w-16">ID</th>
                        <th class="p-4">Title</th>
                        <th class="p-4 hidden sm:table-cell">Slug</th>
                        <th class="p-4 hidden md:table-cell">Updated</th>
                        <th class="p-4 pr-6 text-right w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php foreach($policies as $p): ?>
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="p-4 pl-6 text-slate-400">#<?php echo $p['id']; ?></td>
                            <td class="p-4 font-bold text-slate-800"><?php echo e($p['title']); ?></td>
                            <td class="p-4 hidden sm:table-cell text-slate-500"><?php echo e($p['slug']); ?></td>
                            <td class="p-4 hidden md:table-cell text-slate-400 text-xs"><?php echo date('M d, Y', strtotime($p['updated_at'])); ?></td>
                            <td class="p-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="?page=policies&action=edit&id=<?php echo $p['id']; ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition" title="Edit">
                                        <i class="ri-edit-2-line"></i>
                                    </a>
                                    <form method="POST" onsubmit="return confirm('Delete this policy?');" class="inline">
                                        <input type="hidden" name="form_action" value="delete_policy">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 hover:bg-red-50 transition" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>
