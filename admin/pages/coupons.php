<?php
// ─── Coupons / Discount Codes ─────────────────────────────────────────────────

$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';

    if ($postAction === 'save_coupon') {
        $couponId = (int)($_POST['id'] ?? 0);
        $code = strtoupper(trim($_POST['code'] ?? ''));
        if ($code === '') {
            set_flash('Coupon code is required.', 'error');
        } else {
            $data = [
                $code,
                trim($_POST['description'] ?? ''),
                in_array($_POST['discount_type'] ?? '', ['percentage','fixed'], true) ? $_POST['discount_type'] : 'percentage',
                (float)($_POST['discount_value'] ?? 0),
                (float)($_POST['min_order_amount'] ?? 0),
                $_POST['max_discount'] !== '' ? (float)$_POST['max_discount'] : null,
                $_POST['usage_limit'] !== '' ? (int)$_POST['usage_limit'] : null,
                $_POST['start_date'] ?: null,
                $_POST['end_date'] ?: null,
                (int)($_POST['active'] ?? 1),
            ];
            if ($couponId > 0) {
                $pdo->prepare('UPDATE coupons SET code=?, description=?, discount_type=?, discount_value=?, min_order_amount=?, max_discount=?, usage_limit=?, start_date=?, end_date=?, active=? WHERE id=?')
                    ->execute([...$data, $couponId]);
                set_flash('Coupon updated successfully.');
            } else {
                try {
                    $pdo->prepare('INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, max_discount, usage_limit, start_date, end_date, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
                        ->execute($data);
                    set_flash('Coupon created successfully.');
                } catch (\PDOException $e) {
                    set_flash('Coupon code already exists.', 'error');
                }
            }
        }
        redirect_admin('coupons');
    }

    if ($postAction === 'delete_coupon') {
        admin_delete_record($pdo, 'coupons', (int)($_POST['id'] ?? 0), 'Coupon');
        redirect_admin('coupons');
    }

    if ($postAction === 'toggle_coupon') {
        $pdo->prepare("UPDATE coupons SET active = IF(active=1,0,1) WHERE id=?")->execute([(int)$_POST['id']]);
        set_flash('Coupon status updated.');
        redirect_admin('coupons');
    }
}

$coupon = ['id'=>0,'code'=>'','description'=>'','discount_type'=>'percentage','discount_value'=>'','min_order_amount'=>'0','max_discount'=>'','usage_limit'=>'','start_date'=>'','end_date'=>'','active'=>1];
if ($action === 'edit' && $editId > 0) {
    $stmtE = $pdo->prepare('SELECT * FROM coupons WHERE id = ?');
    $stmtE->execute([$editId]);
    $coupon = $stmtE->fetch() ?: $coupon;
}

$totalCoupons = (int)$pdo->query('SELECT COUNT(*) FROM coupons')->fetchColumn();
$pagination = pagination_state($totalCoupons, 10);
$coupons = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC LIMIT 10 OFFSET " . (int)$pagination['offset'])->fetchAll();

// Stats
$today = date('Y-m-d');
$activeCoupons = (int)$pdo->query("SELECT COUNT(*) FROM coupons WHERE active=1 AND (end_date IS NULL OR end_date >= '$today')")->fetchColumn();
$expiredCoupons = (int)$pdo->query("SELECT COUNT(*) FROM coupons WHERE end_date < '$today'")->fetchColumn();
$totalUsed = (int)$pdo->query('SELECT COALESCE(SUM(used_count),0) FROM coupons')->fetchColumn();
?>
<div class="animate-slide">
<?php render_flash(); ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<!-- ── Add / Edit Coupon ─────────────────────────── -->
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=coupons" class="text-slate-500 hover:text-slate-800 p-2 rounded-xl hover:bg-slate-100 transition"><i class="ri-arrow-left-line text-xl"></i></a>
        <div>
            <h2 class="text-xl font-black text-slate-800"><?php echo $action==='add'?'Create Coupon':'Edit Coupon'; ?></h2>
            <p class="text-sm text-slate-400">Set discount type, value and usage rules</p>
        </div>
    </div>

    <form method="POST" class="bg-white border border-slate-200 rounded-2xl p-6 space-y-5">
        <input type="hidden" name="form_action" value="save_coupon">
        <input type="hidden" name="id" value="<?php echo (int)$coupon['id']; ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Coupon Code <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input name="code" id="coupon_code" value="<?php echo e(strtoupper($coupon['code'])); ?>" required
                        class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 font-mono uppercase"
                        placeholder="e.g. SAVE20">
                    <button type="button" onclick="generateCode()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl text-sm transition">
                        <i class="ri-refresh-line"></i> Generate
                    </button>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                <input name="description" value="<?php echo e($coupon['description']); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="Internal description">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Discount Type</label>
                <select name="discount_type" id="discount_type" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white outline-none focus:border-navy-600" onchange="toggleDiscountType()">
                    <option value="percentage" <?php echo $coupon['discount_type']==='percentage'?'selected':''; ?>>Percentage (%)</option>
                    <option value="fixed" <?php echo $coupon['discount_type']==='fixed'?'selected':''; ?>>Fixed Amount ($)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2" id="discount_value_label">
                    <?php echo $coupon['discount_type']==='fixed'?'Discount Amount ($)':'Discount Percentage (%)'; ?>
                </label>
                <input name="discount_value" type="number" step="0.01" min="0" value="<?php echo e($coupon['discount_value']); ?>"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="e.g. 20">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Min. Order Amount ($)</label>
                <input name="min_order_amount" type="number" step="0.01" min="0" value="<?php echo e($coupon['min_order_amount']); ?>"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="0 = no minimum">
            </div>

            <div id="max_discount_wrap">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Max Discount ($) <span class="text-slate-400 font-normal">(for %)</span></label>
                <input name="max_discount" type="number" step="0.01" min="0" value="<?php echo e($coupon['max_discount']); ?>"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="Leave blank = unlimited">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Usage Limit</label>
                <input name="usage_limit" type="number" min="1" value="<?php echo e($coupon['usage_limit']); ?>"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600" placeholder="Leave blank = unlimited">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select name="active" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white outline-none focus:border-navy-600">
                    <option value="1" <?php echo (int)$coupon['active']===1?'selected':''; ?>>Active</option>
                    <option value="0" <?php echo (int)$coupon['active']===0?'selected':''; ?>>Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Start Date</label>
                <input name="start_date" type="date" value="<?php echo e($coupon['start_date']); ?>"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Expiry Date</label>
                <input name="end_date" type="date" value="<?php echo e($coupon['end_date']); ?>"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button class="bg-navy-600 hover:bg-navy-700 text-white font-bold px-8 py-3 rounded-xl text-sm flex items-center gap-2"><i class="ri-save-line"></i> Save Coupon</button>
            <a href="?page=coupons" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-3 rounded-xl text-sm">Cancel</a>
        </div>
    </form>
</div>

<script>
function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 8; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('coupon_code').value = code;
}
function toggleDiscountType() {
    const type = document.getElementById('discount_type').value;
    const label = document.getElementById('discount_value_label');
    const maxWrap = document.getElementById('max_discount_wrap');
    label.textContent = type === 'fixed' ? 'Discount Amount ($)' : 'Discount Percentage (%)';
    maxWrap.style.opacity = type === 'fixed' ? '0.4' : '1';
    maxWrap.style.pointerEvents = type === 'fixed' ? 'none' : 'auto';
}
toggleDiscountType();
</script>

<?php else: ?>
<!-- ── Coupons List ─────────────────────────────── -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-navy-600 to-navy-700 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-coupon-3-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo $totalCoupons; ?></div>
        <div class="text-navy-200 text-xs mt-0.5">Total Coupons</div>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-checkbox-circle-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo $activeCoupons; ?></div>
        <div class="text-emerald-100 text-xs mt-0.5">Active Now</div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-time-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo $expiredCoupons; ?></div>
        <div class="text-red-100 text-xs mt-0.5">Expired</div>
    </div>
    <div class="bg-gradient-to-br from-amber2-500 to-amber2-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-bar-chart-grouped-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo $totalUsed; ?></div>
        <div class="text-amber2-100 text-xs mt-0.5">Total Used</div>
    </div>
</div>

<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-black text-slate-800">Discount Coupons</h2>
        <p class="text-sm text-slate-400"><?php echo $totalCoupons; ?> coupon codes created</p>
    </div>
    <a href="?page=coupons&action=add" class="flex items-center gap-2 bg-navy-600 hover:bg-navy-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm">
        <i class="ri-add-line text-lg"></i> Create Coupon
    </a>
</div>

<?php if ($coupons): ?>
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5 text-left">Code</th>
                    <th class="px-5 py-3.5 text-left">Type</th>
                    <th class="px-5 py-3.5 text-left">Discount</th>
                    <th class="px-5 py-3.5 text-left">Min Order</th>
                    <th class="px-5 py-3.5 text-left">Used / Limit</th>
                    <th class="px-5 py-3.5 text-left">Expiry</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach($coupons as $cp):
                $today = date('Y-m-d');
                $isExpired = $cp['end_date'] && $cp['end_date'] < $today;
                $isLimitReached = $cp['usage_limit'] && (int)$cp['used_count'] >= (int)$cp['usage_limit'];
                $effectiveActive = $cp['active'] && !$isExpired && !$isLimitReached;
            ?>
            <tr class="hover:bg-slate-50 transition">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-navy-700 font-mono text-base tracking-wider bg-navy-50 px-3 py-1 rounded-lg"><?php echo e($cp['code']); ?></span>
                    </div>
                    <?php if($cp['description']): ?><p class="text-xs text-slate-400 mt-1"><?php echo e($cp['description']); ?></p><?php endif; ?>
                </td>
                <td class="px-5 py-4">
                    <span class="<?php echo $cp['discount_type']==='percentage'?'bg-navy-100 text-navy-700':'bg-navy-100 text-navy-700'; ?> text-xs font-bold px-2.5 py-1 rounded-full">
                        <?php echo $cp['discount_type']==='percentage'?'%':'$'; ?> <?php echo ucfirst($cp['discount_type']); ?>
                    </span>
                </td>
                <td class="px-5 py-4 font-black text-slate-800">
                    <?php echo $cp['discount_type']==='percentage' ? (float)$cp['discount_value'].'%' : '$'.number_format((float)$cp['discount_value'],2); ?>
                    <?php if($cp['max_discount'] && $cp['discount_type']==='percentage'): ?>
                    <div class="text-xs text-slate-400">Max $<?php echo number_format((float)$cp['max_discount'],2); ?></div>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-4 text-slate-600">
                    <?php echo (float)$cp['min_order_amount'] > 0 ? '$'.number_format((float)$cp['min_order_amount'],2) : '<span class="text-slate-400">None</span>'; ?>
                </td>
                <td class="px-5 py-4">
                    <div class="text-sm font-bold text-slate-800"><?php echo (int)$cp['used_count']; ?> / <?php echo $cp['usage_limit'] ?? '∞'; ?></div>
                    <?php if($cp['usage_limit']): ?>
                    <div class="mt-1 h-1.5 bg-slate-100 rounded-full overflow-hidden w-20">
                        <div class="h-full bg-navy-500 rounded-full" style="width:<?php echo min(100, round((int)$cp['used_count'] / (int)$cp['usage_limit'] * 100)); ?>%"></div>
                    </div>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-4 text-xs text-slate-500">
                    <?php if($cp['start_date']): ?><div><span class="text-slate-400">From:</span> <?php echo e($cp['start_date']); ?></div><?php endif; ?>
                    <?php if($cp['end_date']): ?><div class="<?php echo $isExpired?'text-red-500 font-bold':''; ?>"><span class="text-slate-400">Exp:</span> <?php echo e($cp['end_date']); ?></div><?php endif; ?>
                    <?php if(!$cp['start_date'] && !$cp['end_date']): ?><span class="text-slate-400">No expiry</span><?php endif; ?>
                </td>
                <td class="px-5 py-4">
                    <?php if($isExpired): ?>
                        <span class="bg-red-100 text-red-600 text-xs font-bold px-2.5 py-1 rounded-full">Expired</span>
                    <?php elseif($isLimitReached): ?>
                        <span class="bg-slate-100 text-slate-500 text-xs font-bold px-2.5 py-1 rounded-full">Limit Reached</span>
                    <?php else: ?>
                        <span class="<?php echo (int)$cp['active']===1?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500'; ?> text-xs font-bold px-2.5 py-1 rounded-full">
                            <?php echo (int)$cp['active']===1?'Active':'Inactive'; ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <a href="?page=coupons&action=edit&id=<?php echo (int)$cp['id']; ?>" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center transition" title="Edit"><i class="ri-edit-line"></i></a>
                        <form method="POST">
                            <input type="hidden" name="form_action" value="toggle_coupon">
                            <input type="hidden" name="id" value="<?php echo (int)$cp['id']; ?>">
                            <button class="text-amber2-500 hover:bg-amber2-50 w-8 h-8 rounded-lg flex items-center justify-center transition" title="Toggle"><i class="ri-toggle-line"></i></button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Delete coupon <?php echo e($cp['code']); ?>?')">
                            <input type="hidden" name="form_action" value="delete_coupon">
                            <input type="hidden" name="id" value="<?php echo (int)$cp['id']; ?>">
                            <button class="text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center transition" title="Delete"><i class="ri-delete-bin-line"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php render_pagination($totalCoupons, $pagination); ?>
</div>
<?php else: ?>
<div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
    <i class="ri-coupon-3-line text-6xl text-slate-200 block mb-4"></i>
    <p class="text-slate-500 font-semibold mb-2">No coupons yet</p>
    <p class="text-slate-400 text-sm mb-4">Create your first discount coupon to offer deals to customers.</p>
    <a href="?page=coupons&action=add" class="inline-flex items-center gap-2 bg-navy-600 text-white font-bold px-6 py-2.5 rounded-xl text-sm"><i class="ri-add-line"></i> Create First Coupon</a>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
