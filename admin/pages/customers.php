<?php
// ─── Customers page ───────────────────────────────────────────────────────────
// Customers are derived from the orders table (unique email+name combinations)
// plus a dedicated customers table if it exists.

// Handle actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';

    if ($postAction === 'toggle_customer') {
        $pdo->prepare("UPDATE customers SET status = IF(status='active','blocked','active') WHERE id=?")
            ->execute([(int)$_POST['id']]);
        set_flash('Customer status updated.');
        redirect_admin('customers');
    }

    if ($postAction === 'bulk_delete_customers') {
        $ids = array_values(array_filter(array_map('intval', $_POST['customer_ids'] ?? [])));
        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $pdo->prepare("DELETE FROM customers WHERE id IN ($placeholders)")->execute($ids);
            set_flash(count($ids) . ' customer(s) deleted.');
        } else {
            set_flash('Select at least one customer to delete.');
        }
        $_SESSION['customers_synced_at'] = time();
        redirect_admin('customers');
    }

    if ($postAction === 'save_customer_note') {
        $pdo->prepare("UPDATE customers SET notes=?, phone=?, address=?, city=?, state=? WHERE id=?")
            ->execute([
                trim($_POST['notes'] ?? ''),
                trim($_POST['phone'] ?? ''),
                trim($_POST['address'] ?? ''),
                trim($_POST['city'] ?? ''),
                trim($_POST['state'] ?? ''),
                (int)$_POST['id'],
            ]);
        set_flash('Customer details updated.');
        redirect_admin('customers');
    }
}

// Auto-sync only occasionally; doing it on every click makes the page feel slow.
if ((time() - (int)($_SESSION['customers_synced_at'] ?? 0)) > 30) {
    $orderCustomerCount = (int)$pdo->query("SELECT COUNT(DISTINCT email) FROM orders WHERE email <> '' AND email IS NOT NULL")->fetchColumn();
    if ($orderCustomerCount > table_count($pdo, 'customers')) {
        $pdo->exec("INSERT IGNORE INTO customers (name, email, phone)
            SELECT customer_name, email, phone FROM orders
            WHERE email <> '' AND email IS NOT NULL
            GROUP BY email");
    }
    $_SESSION['customers_synced_at'] = time();
}

// Search & filter
$q = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(c.name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%");
}
if (in_array($statusFilter, ['active','blocked'], true)) {
    $where[] = 'c.status = ?';
    $params[] = $statusFilter;
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM customers c $sqlWhere");
$countStmt->execute($params);
$totalCustomers = (int)$countStmt->fetchColumn();

$pagination = pagination_state($totalCustomers, 12);

$stmt = $pdo->prepare("
    SELECT c.*,
        COUNT(DISTINCT o.id) AS total_orders,
        COALESCE(SUM(o.amount),0) AS total_spent,
        MAX(o.order_date) AS last_order
    FROM customers c
    LEFT JOIN orders o ON o.email = c.email AND o.status <> 'cancelled'
    $sqlWhere
    GROUP BY c.id
    ORDER BY total_orders DESC, c.created_at DESC
    LIMIT 12 OFFSET " . (int)$pagination['offset']);
$stmt->execute($params);
$customers = $stmt->fetchAll();

// Stats
$stats = $pdo->query("
    SELECT 
        COUNT(*) total,
        SUM(status='active') active,
        SUM(status='blocked') blocked
    FROM customers
")->fetch();
$topSpenderRow = $pdo->query("
    SELECT c.name, c.email, COALESCE(SUM(o.amount),0) spent
    FROM customers c
    LEFT JOIN orders o ON o.email = c.email AND o.status <> 'cancelled'
    GROUP BY c.id ORDER BY spent DESC LIMIT 1
")->fetch();

// View specific customer
$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewCustomer = null;
$viewOrders = [];
if ($viewId > 0) {
    $stmtV = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
    $stmtV->execute([$viewId]);
    $viewCustomer = $stmtV->fetch();
    if ($viewCustomer) {
        $viewOrders = $pdo->prepare("SELECT * FROM orders WHERE email = ? ORDER BY order_date DESC, id DESC LIMIT 20");
        $viewOrders->execute([$viewCustomer['email']]);
        $viewOrders = $viewOrders->fetchAll();
    }
}
?>
<div class="animate-slide">
<?php render_flash(); ?>

<?php if ($viewCustomer): ?>
<!-- ── Customer Detail View ─────────────────────────── -->
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=customers" class="text-slate-500 hover:text-slate-800 p-2 rounded-xl hover:bg-slate-100 transition"><i class="ri-arrow-left-line text-xl"></i></a>
        <div>
            <h2 class="text-xl font-black text-slate-800">Customer Profile</h2>
            <p class="text-sm text-slate-400">Full order history and contact info</p>
        </div>
    </div>

    <div class="view-wrapper grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <!-- Profile Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-navy-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-black mx-auto mb-3">
                <?php echo e(strtoupper(substr($viewCustomer['name'], 0, 1))); ?>
            </div>
            <h3 class="font-black text-slate-800 text-lg"><?php echo e($viewCustomer['name']); ?></h3>
            <p class="text-sm text-slate-400"><?php echo e($viewCustomer['email']); ?></p>
            <p class="text-sm text-slate-500 mt-1"><?php echo e($viewCustomer['phone'] ?: 'No phone'); ?></p>
            <span class="mt-3 inline-block text-xs font-bold px-3 py-1 rounded-full <?php echo $viewCustomer['status']==='active'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-600'; ?>">
                <?php echo ucfirst($viewCustomer['status']); ?>
            </span>
            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-3 text-center">
                <div class="bg-navy-50 rounded-xl p-3">
                    <div class="text-xl font-black text-navy-700"><?php echo count($viewOrders); ?></div>
                    <div class="text-xs text-slate-500">Orders</div>
                </div>
                <div class="bg-emerald-50 rounded-xl p-3">
                    <div class="text-xl font-black text-emerald-700">$<?php echo number_format(array_sum(array_column($viewOrders, 'amount')), 2); ?></div>
                    <div class="text-xs text-slate-500">Total Spent</div>
                </div>
            </div>
        </div>

        <!-- Edit Notes -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6">
            <h4 class="font-bold text-slate-700 text-sm mb-4 flex items-center gap-2"><i class="ri-edit-2-line text-navy-600"></i> Customer Info & Notes</h4>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="form_action" value="save_customer_note">
                <input type="hidden" name="id" value="<?php echo (int)$viewCustomer['id']; ?>">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Phone</label><input name="phone" value="<?php echo e($viewCustomer['phone']); ?>" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">City</label><input name="city" value="<?php echo e($viewCustomer['city']); ?>" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600"></div>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Address</label><input name="address" value="<?php echo e($viewCustomer['address']); ?>" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600"></div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">State</label><input name="state" value="<?php echo e($viewCustomer['state']); ?>" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600"></div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Internal Notes</label><textarea name="notes" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600 resize-none" placeholder="Private notes about this customer..."><?php echo e($viewCustomer['notes']); ?></textarea></div>
                <div class="flex gap-3 pt-1">
                    <button class="bg-navy-600 hover:bg-navy-700 text-white font-bold px-6 py-2 rounded-xl text-sm flex items-center gap-2"><i class="ri-save-line"></i> Save</button>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="form_action" value="toggle_customer">
                        <input type="hidden" name="id" value="<?php echo (int)$viewCustomer['id']; ?>">
                        <button class="<?php echo $viewCustomer['status']==='active'?'bg-red-50 text-red-600 hover:bg-red-100':'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'; ?> font-semibold px-6 py-2 rounded-xl text-sm">
                            <?php echo $viewCustomer['status']==='active'?'Block Customer':'Unblock'; ?>
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    <!-- Order History -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2"><i class="ri-shopping-bag-3-line text-navy-600"></i> Order History</h4>
        </div>
        <?php if($viewOrders): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wider">
                    <tr><th class="px-5 py-3 text-left">Order</th><th class="px-5 py-3 text-left">Product</th><th class="px-5 py-3 text-left">Amount</th><th class="px-5 py-3 text-left">Date</th><th class="px-5 py-3 text-left">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                <?php 
                $orderStatusClasses = ['pending'=>'bg-amber2-100 text-amber2-700','shipped'=>'bg-navy-100 text-navy-700','delivered'=>'bg-emerald-100 text-emerald-700','cancelled'=>'bg-red-100 text-red-700'];
                foreach($viewOrders as $o): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-bold text-navy-700">#<?php echo e($o['order_no']); ?></td>
                    <td class="px-5 py-3 text-slate-600 max-w-[200px] truncate"><?php echo e($o['product_name']); ?></td>
                    <td class="px-5 py-3 font-black text-slate-800">$<?php echo number_format((float)$o['amount'],2); ?></td>
                    <td class="px-5 py-3 text-slate-400 text-xs"><?php echo e($o['order_date']); ?></td>
                    <td class="px-5 py-3"><span class="<?php echo $orderStatusClasses[$o['status']] ?? 'bg-slate-100 text-slate-500'; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo ucfirst($o['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-8 text-center text-slate-400">No orders found for this customer.</div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- ── Customers List ──────────────────────────────── -->
<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-navy-600 to-navy-700 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-group-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo (int)($stats['total'] ?? 0); ?></div>
        <div class="text-navy-200 text-xs mt-0.5">Total Customers</div>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-user-follow-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo (int)($stats['active'] ?? 0); ?></div>
        <div class="text-emerald-100 text-xs mt-0.5">Active</div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-user-forbid-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo (int)($stats['blocked'] ?? 0); ?></div>
        <div class="text-red-100 text-xs mt-0.5">Blocked</div>
    </div>
    <div class="bg-gradient-to-br from-amber2-500 to-amber2-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-trophy-line text-xl"></i></div>
        <div class="text-sm font-black truncate"><?php echo e($topSpenderRow['name'] ?? '-'); ?></div>
        <div class="text-amber2-100 text-xs mt-0.5">Top Spender</div>
    </div>
</div>

<!-- Header + Search -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-5">
    <div>
        <h2 class="text-xl font-black text-slate-800">Customers</h2>
        <p class="text-sm text-slate-400"><?php echo $totalCustomers; ?> registered customers (synced from orders)</p>
    </div>
    <form id="customer-bulk-delete" method="POST" onsubmit="return confirm('Delete selected customers?');">
        <input type="hidden" name="form_action" value="bulk_delete_customers">
        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl px-4 py-2 text-sm flex items-center gap-2">
            <i class="ri-delete-bin-line"></i> Delete Selected
        </button>
    </form>
</div>

<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="page" value="customers">
    <div class="flex-1 min-w-[200px] relative">
        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input name="q" value="<?php echo e($q); ?>" placeholder="Search by name, email, phone..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-navy-600">
    </div>
    <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="">All Status</option>
        <option value="active" <?php echo $statusFilter==='active'?'selected':''; ?>>Active</option>
        <option value="blocked" <?php echo $statusFilter==='blocked'?'selected':''; ?>>Blocked</option>
    </select>
    <button class="bg-navy-600 text-white font-bold rounded-xl px-4 py-2 text-sm">Filter</button>
    <?php if($q !== '' || $statusFilter !== ''): ?><a href="?page=customers" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl px-4 py-2 text-sm">Clear</a><?php endif; ?>
</form>

<?php $adminView = $_COOKIE['admin_view'] ?? 'list'; ?>
<?php if($adminView === 'list'): ?>
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5 text-left w-10">
                        <input type="checkbox" id="select-all-customers" class="w-4 h-4 rounded border-slate-300 text-navy-600">
                    </th>
                    <th class="px-5 py-3.5 text-left">Customer</th>
                    <th class="px-5 py-3.5 text-left">Contact</th>
                    <th class="px-5 py-3.5 text-left">Orders</th>
                    <th class="px-5 py-3.5 text-left">Spent</th>
                    <th class="px-5 py-3.5 text-left">Last Order</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($customers as $c): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5">
                        <input form="customer-bulk-delete" type="checkbox" name="customer_ids[]" value="<?php echo (int)$c['id']; ?>" class="customer-select w-4 h-4 rounded border-slate-300 text-navy-600">
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-black shrink-0" style="background: linear-gradient(135deg, #2563EB, #0F172A)"><?php echo e(strtoupper(substr($c['name'], 0, 1))); ?></div>
                            <div><div class="font-bold text-slate-800"><?php echo e($c['name']); ?></div><div class="text-xs text-slate-400">ID: <?php echo (int)$c['id']; ?></div></div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-slate-500"><div><?php echo e($c['email']); ?></div><div><?php echo e($c['phone'] ?: 'No phone'); ?></div></td>
                    <td class="px-5 py-3.5 font-black text-slate-800"><?php echo (int)$c['total_orders']; ?></td>
                    <td class="px-5 py-3.5 font-bold text-emerald-700">$<?php echo number_format((float)$c['total_spent'], 2); ?></td>
                    <td class="px-5 py-3.5 text-slate-500 text-xs"><?php echo $c['last_order'] ? e($c['last_order']) : '-'; ?></td>
                    <td class="px-5 py-3.5"><span class="text-xs font-bold px-2.5 py-1 rounded-full <?php echo $c['status']==='active'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-600'; ?>"><?php echo ucfirst($c['status']); ?></span></td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="?page=customers&view=<?php echo (int)$c['id']; ?>" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center" title="View"><i class="ri-eye-line"></i></a>
                            <a href="mailto:<?php echo e($c['email']); ?>" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center" title="Email"><i class="ri-mail-line"></i></a>
                            <form method="POST">
                                <input type="hidden" name="form_action" value="toggle_customer">
                                <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                                <button class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $c['status']==='active'?'text-red-500 hover:bg-red-50':'text-emerald-600 hover:bg-emerald-50'; ?>" title="<?php echo $c['status']==='active'?'Block':'Unblock'; ?>"><i class="<?php echo $c['status']==='active'?'ri-user-forbid-line':'ri-user-follow-line'; ?>"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$customers): ?><tr><td colspan="8" class="px-5 py-8 text-center text-slate-400">No customers found. Customers are auto-synced from orders.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<!-- Customer Cards Grid -->
<div class="view-wrapper grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <?php foreach($customers as $c): ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-md transition card-hover">
        <div class="flex items-start gap-3 mb-4">
            <input form="customer-bulk-delete" type="checkbox" name="customer_ids[]" value="<?php echo (int)$c['id']; ?>" class="customer-select mt-3 w-4 h-4 rounded border-slate-300 text-navy-600">
            <div class="w-11 h-11 rounded-full flex items-center justify-center text-white font-black text-lg shrink-0"
                 style="background: linear-gradient(135deg, #2563EB, #0F172A)">
                <?php echo e(strtoupper(substr($c['name'], 0, 1))); ?>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-slate-800 text-sm truncate"><?php echo e($c['name']); ?></h4>
                <p class="text-xs text-slate-400 truncate"><?php echo e($c['email']); ?></p>
                <p class="text-xs text-slate-400"><?php echo e($c['phone'] ?: 'No phone'); ?></p>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 <?php echo $c['status']==='active'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-600'; ?>">
                <?php echo ucfirst($c['status']); ?>
            </span>
        </div>

        <div class="grid grid-cols-3 gap-2 mb-4">
            <div class="bg-slate-50 rounded-xl p-2 text-center">
                <div class="text-base font-black text-slate-800"><?php echo (int)$c['total_orders']; ?></div>
                <div class="text-[10px] text-slate-400">Orders</div>
            </div>
            <div class="bg-emerald-50 rounded-xl p-2 text-center">
                <div class="text-base font-black text-emerald-700">$<?php echo number_format((float)$c['total_spent'], 0); ?></div>
                <div class="text-[10px] text-slate-400">Spent</div>
            </div>
            <div class="bg-navy-50 rounded-xl p-2 text-center">
                <div class="text-[10px] font-black text-navy-700"><?php echo $c['last_order'] ? date('M d', strtotime($c['last_order'])) : '-'; ?></div>
                <div class="text-[10px] text-slate-400">Last Order</div>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="?page=customers&view=<?php echo (int)$c['id']; ?>" class="flex-1 flex items-center justify-center gap-1.5 bg-navy-600 hover:bg-navy-700 text-white text-xs font-bold py-2 rounded-xl transition">
                <i class="ri-eye-line"></i> View Profile
            </a>
            <a href="mailto:<?php echo e($c['email']); ?>" class="w-9 h-9 flex items-center justify-center border border-slate-200 rounded-xl text-slate-500 hover:text-navy-600 hover:border-navy-300 transition" title="Email Customer">
                <i class="ri-mail-line"></i>
            </a>
            <form method="POST">
                <input type="hidden" name="form_action" value="toggle_customer">
                <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                <button class="w-9 h-9 flex items-center justify-center border border-slate-200 rounded-xl transition <?php echo $c['status']==='active'?'text-red-400 hover:bg-red-50 hover:border-red-300':'text-emerald-500 hover:bg-emerald-50 hover:border-emerald-300'; ?>"
                    title="<?php echo $c['status']==='active'?'Block':'Unblock'; ?>">
                    <i class="<?php echo $c['status']==='active'?'ri-user-forbid-line':'ri-user-follow-line'; ?>"></i>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (!$customers): ?>
    <div class="md:col-span-3 bg-white border border-slate-200 rounded-2xl p-10 text-center text-slate-400">
        <i class="ri-group-line text-5xl mb-3 block text-slate-200"></i>
        No customers found. Customers are auto-synced from orders.
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php render_pagination($totalCustomers, $pagination); ?>
<?php endif; ?>
</div>
<script>
document.getElementById('select-all-customers')?.addEventListener('change', function () {
  document.querySelectorAll('.customer-select').forEach(input => { input.checked = this.checked; });
});
</script>
