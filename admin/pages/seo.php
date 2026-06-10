<?php
$editing = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';

    if ($postAction === 'save_seo') {
        $pdo->prepare('UPDATE seo_meta SET page_name=?, page_file=?, meta_title=?, meta_description=?, keywords=? WHERE id=?')
            ->execute([
                trim($_POST['page_name'] ?? ''),
                trim($_POST['page_file'] ?? ''),
                trim($_POST['meta_title'] ?? ''),
                trim($_POST['meta_description'] ?? ''),
                trim($_POST['keywords'] ?? ''),
                (int)($_POST['id'] ?? 0),
            ]);
        sync_seo_assets($pdo);
        set_flash('SEO settings updated.');
        redirect_admin('seo');
    }

    if ($postAction === 'save_analytics') {
        $gaId = trim($_POST['google_analytics_id'] ?? '');
        $gtmId = trim($_POST['google_tag_manager_id'] ?? '');
        $gscId = trim($_POST['google_site_verification'] ?? '');
        
        $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        $stmt->execute(['google_analytics_id', $gaId]);
        $stmt->execute(['google_tag_manager_id', $gtmId]);
        $stmt->execute(['google_site_verification', $gscId]);
        
        // Refresh local settings array
        $settings['google_analytics_id'] = $gaId;
        $settings['google_tag_manager_id'] = $gtmId;
        $settings['google_site_verification'] = $gscId;
        
        set_flash('Analytics & verification settings updated.');
        redirect_admin('seo');
    }

    if ($postAction === 'sync_seo_assets') {
        sync_seo_assets($pdo);
        set_flash('Sitemap, sitelinks and product schema synced from database.');
        redirect_admin('seo');
    }

    if ($postAction === 'save_sitelink') {
        $pdo->prepare('INSERT INTO sitelinks (label, url, icon, sort_order, active) VALUES (?, ?, ?, ?, ?)')
            ->execute([
                trim($_POST['label'] ?? ''),
                trim($_POST['url'] ?? ''),
                trim($_POST['icon'] ?? 'ri-links-line'),
                (int)($_POST['sort_order'] ?? 1),
                (int)($_POST['active'] ?? 1),
            ]);
        set_flash('Sitelink added.');
        redirect_admin('seo');
    }

    if ($postAction === 'delete_sitelink') {
        admin_delete_record($pdo, 'sitelinks', (int)($_POST['id'] ?? 0), 'Sitelink');
        redirect_admin('seo');
    }

    if ($postAction === 'toggle_sitemap') {
        $pdo->prepare('UPDATE sitemap_entries SET active = IF(active = 1, 0, 1) WHERE id = ?')->execute([(int)$_POST['id']]);
        set_flash('Sitemap entry updated.');
        redirect_admin('seo');
    }

    if ($postAction === 'save_schema') {
        $json = trim($_POST['schema_json'] ?? '');
        json_decode($json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            set_flash('Schema JSON is invalid.', 'error');
            redirect_admin('seo');
        }
        $pdo->prepare('INSERT INTO schema_markup (name, target_type, target_id, schema_json, active) VALUES (?, ?, ?, ?, ?)')
            ->execute([
                trim($_POST['name'] ?? ''),
                trim($_POST['target_type'] ?? 'site'),
                (int)($_POST['target_id'] ?? 0),
                $json,
                (int)($_POST['active'] ?? 1),
            ]);
        set_flash('Schema markup added.');
        redirect_admin('seo');
    }

    if ($postAction === 'delete_schema') {
        admin_delete_record($pdo, 'schema_markup', (int)($_POST['id'] ?? 0), 'Schema');
        redirect_admin('seo');
    }
}

$editItem = null;
if ($editing > 0) {
    $stmt = $pdo->prepare('SELECT * FROM seo_meta WHERE id = ?');
    $stmt->execute([$editing]);
    $editItem = $stmt->fetch();
}

$pages = $pdo->query('SELECT * FROM seo_meta ORDER BY id')->fetchAll();
$sitemapEntries = $pdo->query('SELECT * FROM sitemap_entries ORDER BY active DESC, source_type, label LIMIT 30')->fetchAll();
$sitelinks = $pdo->query('SELECT * FROM sitelinks ORDER BY sort_order, id')->fetchAll();
$schemas = $pdo->query('SELECT * FROM schema_markup ORDER BY target_type, id LIMIT 40')->fetchAll();
$productCount = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
$activeSitemapCount = (int)$pdo->query('SELECT COUNT(*) FROM sitemap_entries WHERE active = 1')->fetchColumn();
$activeSchemaCount = (int)$pdo->query('SELECT COUNT(*) FROM schema_markup WHERE active = 1')->fetchColumn();
$optimized = 0;
foreach ($pages as $row) {
    if (strlen($row['meta_title']) >= 35 && strlen($row['meta_description']) >= 80) {
        $optimized++;
    }
}
?>
<div class="animate-slide">
<?php render_flash(); ?>

<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-black text-slate-800 flex items-center gap-2"><i class="ri-search-eye-line text-navy-600"></i> SEO / Meta Manager</h2>
        <p class="text-sm text-slate-400">Meta, sitemap, sitelinks and schema are database-driven.</p>
    </div>
    <form method="POST">
        <input type="hidden" name="form_action" value="sync_seo_assets">
        <button class="flex items-center gap-2 bg-navy-600 hover:bg-navy-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm"><i class="ri-refresh-line"></i> Sync SEO Assets</button>
    </form>
</div>

<?php if($editItem): ?>
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=seo" class="text-slate-500 hover:text-slate-800 p-2 rounded-xl hover:bg-slate-100"><i class="ri-arrow-left-line text-xl"></i></a>
        <div><h3 class="text-lg font-black text-slate-800">Edit SEO: <?php echo e($editItem['page_name']); ?></h3><p class="text-xs text-slate-400">File: <?php echo e($editItem['page_file']); ?></p></div>
    </div>
    <form method="POST" class="space-y-5">
        <input type="hidden" name="form_action" value="save_seo">
        <input type="hidden" name="id" value="<?php echo (int)$editItem['id']; ?>">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            <h4 class="font-bold text-slate-700 text-sm mb-4 flex items-center gap-2 pb-3 border-b border-slate-100"><i class="ri-price-tag-3-line text-navy-600"></i> Basic Meta Tags</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Page Name</label><input name="page_name" value="<?php echo e($editItem['page_name']); ?>" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Page File</label><input name="page_file" value="<?php echo e($editItem['page_file']); ?>" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div>
            </div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-2">Meta Title</label><input name="meta_title" maxlength="255" value="<?php echo e($editItem['meta_title']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"><p class="text-xs text-slate-400 mt-1"><?php echo strlen($editItem['meta_title']); ?> characters</p></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-2">Meta Description</label><textarea name="meta_description" rows="4" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 resize-none"><?php echo e($editItem['meta_description']); ?></textarea><p class="text-xs text-slate-400 mt-1"><?php echo strlen($editItem['meta_description']); ?> characters</p></div>
            <div><label class="block text-sm font-semibold text-slate-700 mb-2">Keywords</label><input name="keywords" value="<?php echo e($editItem['keywords']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h4 class="font-bold text-slate-700 text-sm mb-4 flex items-center gap-2"><i class="ri-google-line text-navy-600"></i> Search Preview</h4>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200"><div class="text-xs text-slate-400 mb-2">geeksupportllc.com / <?php echo e($editItem['page_file']); ?></div><div class="text-lg text-navy-700 font-medium leading-tight mb-1"><?php echo e($editItem['meta_title']); ?></div><div class="text-sm text-slate-600 leading-relaxed"><?php echo e($editItem['meta_description']); ?></div></div>
        </div>
        <div class="flex gap-3 pb-4"><button class="bg-navy-600 hover:bg-navy-700 text-white font-bold px-8 py-3 rounded-xl flex items-center gap-2 text-sm"><i class="ri-save-line"></i> Save SEO Settings</button><a href="?page=seo" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-3 rounded-xl text-sm">Cancel</a></div>
    </form>
</div>

<?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-slate-200 rounded-2xl p-4"><div class="flex items-center gap-3"><div class="bg-navy-100 rounded-xl w-10 h-10 flex items-center justify-center"><i class="ri-checkbox-circle-line text-navy-600 text-xl"></i></div><div><div class="text-2xl font-black text-slate-800"><?php echo $optimized; ?>/<?php echo count($pages); ?></div><div class="text-xs text-slate-400">Pages Optimized</div></div></div></div>
    <div class="bg-white border border-slate-200 rounded-2xl p-4"><div class="flex items-center gap-3"><div class="bg-navy-100 rounded-xl w-10 h-10 flex items-center justify-center"><i class="ri-map-2-line text-navy-600 text-xl"></i></div><div><div class="text-2xl font-black text-slate-800"><?php echo $activeSitemapCount; ?></div><div class="text-xs text-slate-400">Sitemap URLs</div></div></div></div>
    <div class="bg-white border border-slate-200 rounded-2xl p-4"><div class="flex items-center gap-3"><div class="bg-navy-100 rounded-xl w-10 h-10 flex items-center justify-center"><i class="ri-code-box-line text-navy-600 text-xl"></i></div><div><div class="text-2xl font-black text-slate-800"><?php echo $activeSchemaCount; ?></div><div class="text-xs text-slate-400">Schema Blocks</div></div></div></div>
    <div class="bg-white border border-slate-200 rounded-2xl p-4"><div class="flex items-center gap-3"><div class="bg-navy-100 rounded-xl w-10 h-10 flex items-center justify-center"><i class="ri-printer-line text-navy-600 text-xl"></i></div><div><div class="text-2xl font-black text-slate-800"><?php echo $productCount; ?></div><div class="text-xs text-slate-400">Product URLs</div></div></div></div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"><h3 class="font-bold text-slate-800 text-sm flex items-center gap-2"><i class="ri-list-check-2 text-navy-600"></i> Pages Meta Overview</h3><span class="text-xs text-slate-400">Each page meta is editable</span></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider"><tr><th class="px-5 py-3.5 text-left">Page</th><th class="px-5 py-3.5 text-left">Meta Title</th><th class="px-5 py-3.5 text-left">Description</th><th class="px-5 py-3.5 text-left">Keywords</th><th class="px-5 py-3.5 text-left">Action</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($pages as $p): ?>
                <tr class="hover:bg-slate-50 transition"><td class="px-5 py-4"><div class="font-bold text-slate-800"><?php echo e($p['page_name']); ?></div><div class="text-xs text-slate-400"><?php echo e($p['page_file']); ?></div></td><td class="px-5 py-4"><div class="max-w-[220px] truncate text-slate-700 text-xs font-medium"><?php echo e($p['meta_title']); ?></div><div class="text-[10px] text-slate-400 mt-1"><?php echo strlen($p['meta_title']); ?> chars</div></td><td class="px-5 py-4"><div class="max-w-[260px] truncate text-slate-600 text-xs"><?php echo e($p['meta_description']); ?></div></td><td class="px-5 py-4 text-xs text-slate-500 max-w-[180px] truncate"><?php echo e($p['keywords']); ?></td><td class="px-5 py-4"><a href="?page=seo&edit=<?php echo (int)$p['id']; ?>" class="inline-flex items-center gap-1.5 bg-navy-600 hover:bg-navy-700 text-white text-xs font-semibold px-3 py-1.5 rounded-xl"><i class="ri-edit-line"></i> Edit</a></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800 text-sm flex items-center gap-2"><i class="ri-map-pin-line text-navy-600"></i> Sitemap Manager</h3><p class="text-xs text-slate-400 mt-1">Pages and active products sync automatically.</p></div>
        <div class="overflow-x-auto max-h-[440px]">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider sticky top-0"><tr><th class="px-5 py-3 text-left">Label</th><th class="px-5 py-3 text-left">Type</th><th class="px-5 py-3 text-left">Priority</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Action</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach($sitemapEntries as $entry): ?>
                    <tr class="hover:bg-slate-50"><td class="px-5 py-3"><div class="font-bold text-slate-800 text-xs"><?php echo e($entry['label']); ?></div><div class="text-[10px] text-slate-400 max-w-[260px] truncate"><?php echo e($entry['url']); ?></div></td><td class="px-5 py-3 text-xs text-slate-600"><?php echo e(ucfirst($entry['source_type'])); ?></td><td class="px-5 py-3 text-xs font-bold text-slate-700"><?php echo e($entry['priority']); ?></td><td class="px-5 py-3"><span class="<?php echo (int)$entry['active']===1?'bg-navy-100 text-navy-700':'bg-slate-100 text-slate-500'; ?> text-xs font-bold px-2 py-1 rounded-full"><?php echo (int)$entry['active']===1?'Active':'Off'; ?></span></td><td class="px-5 py-3"><form method="POST"><input type="hidden" name="form_action" value="toggle_sitemap"><input type="hidden" name="id" value="<?php echo (int)$entry['id']; ?>"><button class="text-navy-600 hover:bg-navy-50 rounded-lg px-2 py-1 text-xs font-bold">Toggle</button></form></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 mb-4"><i class="ri-links-line text-navy-600"></i> Sitelinks</h3>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-5">
            <input type="hidden" name="form_action" value="save_sitelink">
            <input name="label" required placeholder="Label" class="border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600">
            <input name="url" required placeholder="/products.php" class="border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600">
            <input name="icon" placeholder="ri-printer-line" class="border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600">
            <input name="sort_order" type="number" value="1" class="border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600">
            <button class="md:col-span-2 bg-navy-600 hover:bg-navy-700 text-white font-bold rounded-xl px-4 py-2 text-sm">Add Sitelink</button>
        </form>
        <div class="space-y-2">
            <?php foreach($sitelinks as $link): ?>
            <div class="flex items-center gap-3 bg-slate-50 rounded-xl p-3"><i class="<?php echo e($link['icon']); ?> text-navy-600"></i><div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e($link['label']); ?></div><div class="text-xs text-slate-400 truncate"><?php echo e($link['url']); ?></div></div><form method="POST" onsubmit="return confirm('Delete this sitelink?')"><input type="hidden" name="form_action" value="delete_sitelink"><input type="hidden" name="id" value="<?php echo (int)$link['id']; ?>"><button class="text-red-500 hover:bg-red-50 rounded-lg w-8 h-8"><i class="ri-delete-bin-line"></i></button></form></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-5">
            <div><h3 class="font-bold text-slate-800 text-sm flex items-center gap-2"><i class="ri-code-box-line text-navy-600"></i> Schema Markup</h3><p class="text-xs text-slate-400 mt-1">Organization, website and product schema are stored in database.</p></div>
        </div>
        <form method="POST" class="grid grid-cols-1 lg:grid-cols-4 gap-3 mb-5">
            <input type="hidden" name="form_action" value="save_schema">
            <input name="name" required placeholder="Schema name" class="border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600 col-span-2">
            <select name="target_type" class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600"><option value="site">Site</option><option value="page">Page</option><option value="product">Product</option></select>
            <input name="target_id" type="number" value="0" class="border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600">
            <button class="lg:col-span-4 bg-navy-600 hover:bg-navy-700 text-white font-bold rounded-xl px-4 py-2 text-sm">Add Schema</button>
            <textarea name="schema_json" required rows="4" placeholder='{"@context":"https://schema.org","@type":"Organization"}' class="lg:col-span-4 border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600 font-mono resize-none"></textarea>
        </form>
        <div class="grid grid-cols-1 gap-3 overflow-y-auto max-h-[300px] pr-1">
            <?php foreach($schemas as $schema): ?>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200"><div class="flex items-start justify-between gap-3 mb-2"><div><div class="font-bold text-slate-800 text-sm"><?php echo e($schema['name']); ?></div><div class="text-xs text-slate-400"><?php echo e($schema['target_type']); ?> #<?php echo (int)$schema['target_id']; ?></div></div><form method="POST" onsubmit="return confirm('Delete this schema?')"><input type="hidden" name="form_action" value="delete_schema"><input type="hidden" name="id" value="<?php echo (int)$schema['id']; ?>"><button class="text-red-500 hover:bg-red-50 rounded-lg w-8 h-8"><i class="ri-delete-bin-line"></i></button></form></div><pre class="text-[10px] text-slate-500 bg-white border border-slate-200 rounded-lg p-2 overflow-auto max-h-24"><?php echo e($schema['schema_json']); ?></pre></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 mb-2"><i class="ri-google-line text-navy-600"></i> Search Console & Analytics</h3>
        <p class="text-xs text-slate-400 mb-4">Integrate tracking and verification tags dynamically across your storefront.</p>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="form_action" value="save_analytics">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Google Analytics ID (GA4)</label>
                <input name="google_analytics_id" value="<?php echo e($settings['google_analytics_id'] ?? ''); ?>" placeholder="e.g. G-9Y0SCZN83K" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 transition">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Google Tag Manager ID (GTM)</label>
                <input name="google_tag_manager_id" value="<?php echo e($settings['google_tag_manager_id'] ?? ''); ?>" placeholder="e.g. GTM-XXXXXX" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 transition">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Google Search Console Verification ID</label>
                <input name="google_site_verification" value="<?php echo e($settings['google_site_verification'] ?? ''); ?>" placeholder="e.g. site-verification-token" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 transition">
            </div>
            <button class="w-full bg-navy-600 hover:bg-navy-700 text-white font-bold rounded-xl px-4 py-3 text-sm transition shadow-sm mt-2"><i class="ri-save-line mr-1"></i> Save Integration Tags</button>
        </form>
    </div>
</div>
<?php endif; ?>
</div>
