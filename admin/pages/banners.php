<?php
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';
    if ($postAction === 'save_banner') {
        $bannerId = (int)($_POST['id'] ?? 0);
        $imageUrl = $_POST['existing_image_url'] ?? '';
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['image_file']['tmp_name'];
            $origName = preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image_file']['name']);
            $fileName = time() . '_' . $origName;
            $uploadDir = __DIR__ . '/../../uploads/banners/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                $imageUrl = 'uploads/banners/' . $fileName;
            }
        }
        $data = [
            trim($_POST['title'] ?? ''),
            trim($_POST['subtitle'] ?? ''),
            trim($_POST['badge'] ?? ''),
            trim($_POST['button_text'] ?? 'Shop Now'),
            trim($_POST['link_url'] ?? ''),
            trim($_POST['secondary_button_text'] ?? ''),
            trim($_POST['secondary_link_url'] ?? ''),
            trim($imageUrl),
            in_array($_POST['poster_style'] ?? 'standard', ['standard','poster_light','poster_dark','poster_teal'], true) ? $_POST['poster_style'] : 'standard',
            trim($_POST['location'] ?? 'Homepage Hero'),
            trim($_POST['bg_theme'] ?? 'navy'),
            in_array($_POST['status'] ?? 'active', ['active','inactive','scheduled'], true) ? $_POST['status'] : 'active',
            $_POST['start_date'] ?: null,
            $_POST['end_date'] ?: null,
            (int)($_POST['sort_order'] ?? 1),
        ];
        if ($data[0] === '') {
            set_flash('Banner title is required.', 'error');
        } elseif ($bannerId > 0) {
            $pdo->prepare('UPDATE banners SET title=?, subtitle=?, badge=?, button_text=?, link_url=?, secondary_button_text=?, secondary_link_url=?, image_url=?, poster_style=?, location=?, bg_theme=?, status=?, start_date=?, end_date=?, sort_order=? WHERE id=?')->execute([...$data, $bannerId]);
            set_flash('Banner updated successfully.');
        } else {
            $pdo->prepare('INSERT INTO banners (title, subtitle, badge, button_text, link_url, secondary_button_text, secondary_link_url, image_url, poster_style, location, bg_theme, status, start_date, end_date, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')->execute($data);
            set_flash('Banner added successfully.');
        }
        redirect_admin('banners');
    }
    if ($postAction === 'delete_banner') {
        admin_delete_record($pdo, 'banners', (int)($_POST['id'] ?? 0), 'Banner');
        redirect_admin('banners');
    }
    if ($postAction === 'toggle_banner') {
        $pdo->prepare("UPDATE banners SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?")->execute([(int)$_POST['id']]);
        set_flash('Banner status updated.');
        redirect_admin('banners');
    }
}

$banner = ['id'=>0,'title'=>'','subtitle'=>'','badge'=>'','button_text'=>'Shop Now','link_url'=>'','secondary_button_text'=>'','secondary_link_url'=>'','image_url'=>'','poster_style'=>'standard','location'=>'Homepage Hero','bg_theme'=>'navy','status'=>'active','start_date'=>'','end_date'=>'','sort_order'=>1];
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM banners WHERE id = ?');
    $stmt->execute([$id]);
    $banner = $stmt->fetch() ?: $banner;
}
$banners = $pdo->query('SELECT * FROM banners ORDER BY sort_order, id DESC')->fetchAll();
$themes = ['navy'=>'from-navy-900 to-navy-700','emerald'=>'from-emerald-700 to-emerald-500','amber'=>'from-amber2-600 to-amber2-400','slate'=>'from-slate-800 to-slate-500'];
?>
<div class="animate-slide">
<?php render_flash(); ?>

<?php if($action === 'add' || $action === 'edit'): ?>
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=banners" class="p-2 rounded-xl hover:bg-slate-100 text-slate-500"><i class="ri-arrow-left-line text-xl"></i></a>
        <div><h2 class="text-xl font-black text-slate-800"><?php echo $action==='add'?'Add New Banner':'Edit Banner'; ?></h2><p class="text-sm text-slate-400">Configure banner details and display settings</p></div>
    </div>
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <input type="hidden" name="form_action" value="save_banner"><input type="hidden" name="id" value="<?php echo (int)$banner['id']; ?>">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                <h4 class="font-bold text-slate-700 text-sm border-b border-slate-100 pb-3 flex items-center gap-2"><i class="ri-image-2-line text-navy-600"></i> Banner Content</h4>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Banner Title</label><input name="title" required value="<?php echo e($banner['title']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Subtitle</label><textarea name="subtitle" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 resize-none"><?php echo e($banner['subtitle']); ?></textarea></div>
                <div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-semibold text-slate-700 mb-2">Badge Text</label><input name="badge" value="<?php echo e($banner['badge']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div><div><label class="block text-sm font-semibold text-slate-700 mb-2">Button Text</label><input name="button_text" value="<?php echo e($banner['button_text']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Link URL</label><input name="link_url" value="<?php echo e($banner['link_url']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div>
                <div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-semibold text-slate-700 mb-2">Second Button</label><input name="secondary_button_text" value="<?php echo e($banner['secondary_button_text'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div><div><label class="block text-sm font-semibold text-slate-700 mb-2">Second Link</label><input name="secondary_link_url" value="<?php echo e($banner['secondary_link_url'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600"></div></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Poster Image</label><input type="file" name="image_file" accept="image/*" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:border-navy-600 bg-white"><input type="hidden" name="existing_image_url" value="<?php echo e($banner['image_url'] ?? ''); ?>"><?php if(!empty($banner['image_url'])): ?><div class="mt-2 text-xs text-slate-500">Current: <a href="../<?php echo e($banner['image_url']); ?>" target="_blank" class="text-indigo-600 hover:underline"><?php echo e($banner['image_url']); ?></a></div><?php endif; ?></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Poster Style</label><select name="poster_style" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm bg-white"><?php foreach(['standard'=>'Standard','poster_light'=>'Poster Light','poster_dark'=>'Poster Dark','poster_teal'=>'Poster Teal'] as $style=>$label): ?><option value="<?php echo e($style); ?>" <?php echo ($banner['poster_style'] ?? 'standard')===$style?'selected':''; ?>><?php echo e($label); ?></option><?php endforeach; ?></select></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Background Theme</label><select name="bg_theme" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm bg-white"><?php foreach(array_keys($themes) as $theme): ?><option value="<?php echo e($theme); ?>" <?php echo $banner['bg_theme']===$theme?'selected':''; ?>><?php echo e(ucfirst($theme)); ?></option><?php endforeach; ?></select></div>
            </div>
        </div>
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
                <h4 class="font-bold text-slate-700 text-sm border-b border-slate-100 pb-3 flex items-center gap-2"><i class="ri-settings-3-line text-slate-500"></i> Settings</h4>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Display Location</label><input name="location" value="<?php echo e($banner['location']); ?>" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-navy-600"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Status</label><select name="status" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm bg-white"><?php foreach(['active','inactive','scheduled'] as $status): ?><option value="<?php echo e($status); ?>" <?php echo $banner['status']===$status?'selected':''; ?>><?php echo e(ucfirst($status)); ?></option><?php endforeach; ?></select></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Start Date</label><input name="start_date" type="date" value="<?php echo e($banner['start_date']); ?>" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-navy-600"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">End Date</label><input name="end_date" type="date" value="<?php echo e($banner['end_date']); ?>" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-navy-600"></div>
                <div><label class="block text-sm font-semibold text-slate-700 mb-2">Sort Order</label><input name="sort_order" type="number" value="<?php echo (int)$banner['sort_order']; ?>" min="1" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-navy-600"></div>
            </div>
            <button class="w-full bg-navy-600 hover:bg-navy-700 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 text-sm"><i class="ri-save-line"></i> Save Banner</button>
            <a href="?page=banners" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 rounded-xl text-sm flex items-center justify-center">Cancel</a>
        </div>
    </form>
</div>

<?php else: ?>
<div class="flex items-center justify-between mb-6">
    <div><h2 class="text-xl font-black text-slate-800 flex items-center gap-2"><i class="ri-image-2-line text-navy-600"></i> Banner Manager</h2><p class="text-sm text-slate-400">Manage homepage and page banners</p></div>
    <a href="?page=banners&action=add" class="flex items-center gap-2 bg-navy-600 hover:bg-navy-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm"><i class="ri-add-line text-lg"></i> Add Banner</a>
</div>

<div class="space-y-4 mb-6">
<?php foreach($banners as $b): $bg = $themes[$b['bg_theme']] ?? $themes['navy']; ?>
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
    <div class="flex flex-col md:flex-row">
        <div class="w-full md:w-64 h-36 bg-gradient-to-br <?php echo $bg; ?> flex items-center justify-center relative shrink-0 overflow-hidden">
            <?php if(!empty($b['image_url'])): ?><img src="../<?php echo e($b['image_url']); ?>" alt="<?php echo e($b['title']); ?>" class="absolute inset-0 w-full h-full object-cover opacity-80"><?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/70 to-transparent"></div>
            <div class="text-center text-white p-4"><?php if($b['badge']): ?><span class="bg-red-500 text-white text-xs font-black px-2 py-1 rounded-lg mb-2 inline-block"><?php echo e($b['badge']); ?></span><?php endif; ?><div class="font-black text-sm"><?php echo e($b['title']); ?></div></div>
            <span class="absolute top-2 right-2 <?php echo $b['status']==='active'?'bg-emerald-500':'bg-slate-400'; ?> text-white text-[10px] font-bold px-2 py-1 rounded-full"><?php echo e(ucfirst($b['status'])); ?></span>
        </div>
        <div class="flex-1 p-5 flex flex-col justify-between">
            <div><h4 class="font-bold text-slate-800"><?php echo e($b['title']); ?></h4><span class="text-xs bg-navy-100 text-navy-700 font-semibold px-2 py-0.5 rounded-lg"><?php echo e($b['location']); ?></span><p class="text-xs text-slate-500 mt-2"><?php echo e($b['subtitle']); ?></p><div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-3 text-xs text-slate-500"><div><span class="font-semibold text-slate-600">Start:</span> <?php echo e($b['start_date']); ?></div><div><span class="font-semibold text-slate-600">End:</span> <?php echo e($b['end_date']); ?></div><div><span class="font-semibold text-slate-600">Link:</span> <span class="text-navy-600"><?php echo e($b['link_url']); ?></span></div></div></div>
            <div class="flex items-center gap-2 mt-4">
                <a href="?page=banners&action=edit&id=<?php echo (int)$b['id']; ?>" class="flex items-center gap-1.5 bg-navy-600 hover:bg-navy-700 text-white text-xs font-bold px-4 py-2 rounded-xl"><i class="ri-edit-line"></i> Edit</a>
                <form method="POST"><input type="hidden" name="form_action" value="toggle_banner"><input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>"><button class="<?php echo $b['status']==='active'?'bg-amber2-100 text-amber2-700 hover:bg-amber2-200':'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'; ?> text-xs font-bold px-4 py-2 rounded-xl"><?php echo $b['status']==='active'?'Deactivate':'Activate'; ?></button></form>
                <form method="POST" class="ml-auto" onsubmit="return confirm('Delete this banner?')"><input type="hidden" name="form_action" value="delete_banner"><input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>"><button class="flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-500 text-xs font-bold px-4 py-2 rounded-xl"><i class="ri-delete-bin-line"></i> Delete</button></form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
