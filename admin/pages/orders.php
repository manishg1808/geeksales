<?php
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_action'] ?? '') === 'update_order') {
    $status = $_POST['status'] ?? 'pending';
    if (in_array($status, ['pending', 'shipped', 'delivered', 'cancelled'], true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, (int)$_POST['id']]);
        set_flash('Order status updated.');
    }
    if (isset($_POST['redirect_to_view']) && $_POST['redirect_to_view'] === '1') {
        redirect_admin('orders&action=view&id=' . (int)$_POST['id']);
    } else {
        redirect_admin('orders');
    }
} elseif (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_action'] ?? '') === 'delete_multiple') {
    $ids = $_POST['selected_ids'] ?? [];
    if (!empty($ids) && is_array($ids)) {
        $validIds = array_filter(array_map('intval', $ids), fn($id) => $id > 0);
        if (!empty($validIds)) {
            $inQuery = implode(',', array_fill(0, count($validIds), '?'));
            $pdo->prepare("DELETE FROM orders WHERE id IN ($inQuery)")->execute(array_values($validIds));
            set_flash('Selected orders deleted successfully.');
        }
    } else {
        set_flash('No orders selected.', 'error');
    }
    redirect_admin('orders');
} elseif (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_action'] ?? '') === 'delete_order') {
    admin_delete_record($pdo, 'orders', (int)($_POST['id'] ?? 0), 'Order');
    redirect_admin('orders');
}

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'view' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    $o = $stmt->fetch();
    
    if (!$o) {
        set_flash('Order not found.', 'error');
        redirect_admin('orders');
    }
    
    $customerName = $o['billing_name'] ?? $o['customer_name'] ?? 'N/A';
    $totalAmount = $o['total_amount'] ?? $o['amount'] ?? 0;
    $paymentMethod = $o['payment_method'] ?? 'manual';
    $orderDate = $o['created_at'] ?? $o['order_date'] ?? 'N/A';
    $status = $o['status'] ?? 'pending';
    
    $statusMap = [
        'pending' => ['class'=>'bg-amber2-100 text-amber2-700 border-amber2-200', 'label'=>'Pending'],
        'shipped' => ['class'=>'bg-navy-100 text-navy-700 border-navy-200', 'label'=>'Shipped'],
        'delivered' => ['class'=>'bg-emerald-100 text-emerald-700 border-emerald-200', 'label'=>'Delivered'],
        'cancelled' => ['class'=>'bg-red-100 text-red-700 border-red-200', 'label'=>'Cancelled'],
    ];
    $s = $statusMap[$status] ?? $statusMap['pending'];
    ?>
    <div class="animate-slide">
        <!-- Back button & Title -->
        <div class="flex items-center gap-4 mb-6">
            <a href="?page=orders" class="bg-white border border-slate-200 hover:border-slate-300 text-slate-600 w-10 h-10 rounded-xl flex items-center justify-center transition shadow-sm"><i class="ri-arrow-left-line text-lg"></i></a>
            <div>
                <h1 class="text-2xl font-black text-slate-800">Order #<?php echo e($o['order_no']); ?></h1>
                <p class="text-xs text-slate-400 mt-0.5">Placed on <?php echo e($orderDate); ?></p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Order Details & Products -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Products Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-base font-black text-slate-800 mb-4 flex items-center gap-2"><i class="ri-printer-line text-navy-600"></i> Ordered Items</h3>
                    <div class="divide-y divide-slate-100">
                        <?php 
                        $items = explode(', ', $o['product_name']);
                        foreach ($items as $item): 
                            if (trim($item) === '') continue;
                            $qty = 1;
                            $name = $item;
                            if (preg_match('/^(.*?)\s*\(x(\d+)\)$/', $item, $matches)) {
                                $name = $matches[1];
                                $qty = (int)$matches[2];
                            }
                        ?>
                            <div class="py-3 flex items-center gap-4">
                                <div class="bg-navy-50 rounded-xl w-12 h-12 flex items-center justify-center shrink-0 text-navy-600"><i class="ri-printer-fill text-xl"></i></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 leading-normal"><?php echo e($name); ?></p>
                                    <p class="text-xs text-slate-400 mt-0.5">Quantity: <span class="font-bold text-slate-700"><?php echo $qty; ?></span></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="border-t border-slate-100 pt-4 mt-4 flex justify-between items-center text-slate-800">
                        <span class="text-sm font-semibold text-slate-500">Grand Total:</span>
                        <span class="text-xl font-black text-navy-600">$<?php echo number_format((float)$totalAmount, 2); ?></span>
                    </div>
                </div>

                <!-- Customer Details Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-base font-black text-slate-800 mb-4 flex items-center gap-2"><i class="ri-user-line text-navy-600"></i> Customer Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Full Name</p>
                            <p class="font-bold text-slate-800"><?php echo e($customerName); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Email Address</p>
                            <p class="font-bold text-slate-800"><?php echo e($o['email'] ?: 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Phone Number</p>
                            <p class="font-bold text-slate-800"><?php echo e($o['phone'] ?: 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Payment Method</p>
                            <span class="inline-block px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700 rounded-lg mt-0.5"><?php echo e(ucfirst($paymentMethod)); ?></span>
                        </div>
                        <div class="md:col-span-2 border-t border-slate-100 pt-4 mt-2">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5"><i class="ri-map-pin-line text-navy-600"></i> Shipping Address</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-slate-600">
                                <div>
                                    <p class="font-semibold text-slate-400 mb-0.5">Street Address</p>
                                    <p class="text-slate-800 font-bold text-sm"><?php echo e($o['address'] ?: 'N/A'); ?></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-400 mb-0.5">City, State, ZIP</p>
                                    <p class="text-slate-800 font-bold text-sm">
                                        <?php 
                                        $cityStateZip = [];
                                        if (!empty($o['city'])) $cityStateZip[] = $o['city'];
                                        if (!empty($o['state'])) $cityStateZip[] = $o['state'];
                                        if (!empty($o['zip'])) $cityStateZip[] = $o['zip'];
                                        echo $cityStateZip ? implode(', ', $cityStateZip) : 'N/A';
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Order Status Management -->
            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-base font-black text-slate-800 mb-4 flex items-center gap-2"><i class="ri-settings-4-line text-navy-600"></i> Manage Status</h3>
                    
                    <div class="mb-5">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Current Status</p>
                        <span class="inline-block px-3 py-1 text-sm font-bold rounded-full border <?php echo $s['class']; ?>"><?php echo $s['label']; ?></span>
                    </div>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="form_action" value="update_order">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="redirect_to_view" value="1">
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Change Status</label>
                            <select name="status" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 bg-white outline-none focus:border-navy-600">
                                <option value="pending" <?php echo $status==='pending'?'selected':''; ?>>Pending</option>
                                <option value="shipped" <?php echo $status==='shipped'?'selected':''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $status==='delivered'?'selected':''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $status==='cancelled'?'selected':''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-navy-600 hover:bg-navy-700 text-white font-bold py-2 px-4 rounded-xl text-sm transition">Update Status</button>
                    </form>
                </div>
                
                <!-- Danger Zone -->
                <div class="bg-white border border-red-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-base font-black text-red-600 mb-4 flex items-center gap-2"><i class="ri-error-warning-line"></i> Danger Zone</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Deleting this order will remove all customer details and invoice data permanently. This cannot be undone.</p>
                    <button type="button" onclick="deleteSingle(<?php echo $id; ?>)" class="w-full border border-red-200 hover:bg-red-50 text-red-600 font-bold py-2 px-4 rounded-xl text-sm transition"><i class="ri-delete-bin-line"></i> Delete Order</button>
                </div>
            </div>
        </div>
    </div>
    
    <form method="POST" id="singleDeleteForm" class="hidden">
        <input type="hidden" name="form_action" value="delete_order">
        <input type="hidden" name="id" id="singleDeleteId" value="">
    </form>
    
    <script>
    function deleteSingle(id) {
        if(confirm('Are you sure you want to permanently delete this order?')) {
            document.getElementById('singleDeleteId').value = id;
            document.getElementById('singleDeleteForm').submit();
        }
    }
    </script>
    <?php
    return;
}


$statusFilter = $_GET['status'] ?? '';
$q = trim($_GET['q'] ?? '');
$where = [];
$params = [];
if (in_array($statusFilter, ['pending', 'shipped', 'delivered', 'cancelled'], true)) {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
}
if ($q !== '') {
    $where[] = '(order_no LIKE ? OR customer_name LIKE ? OR email LIKE ? OR phone LIKE ? OR product_name LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%", "%$q%");
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders $sqlWhere");
$countStmt->execute($params);
$totalOrders = (int)$countStmt->fetchColumn();
$pagination = pagination_state($totalOrders, 10);
$stmt = $pdo->prepare("SELECT * FROM orders $sqlWhere ORDER BY order_date DESC, id DESC LIMIT 10 OFFSET " . (int)$pagination['offset']);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$counts = ['pending'=>0,'shipped'=>0,'delivered'=>0,'cancelled'=>0];
foreach ($pdo->query('SELECT status, COUNT(*) total FROM orders GROUP BY status') as $row) {
    $counts[$row['status']] = (int)$row['total'];
}
$statusMap = [
    'pending' => ['class'=>'bg-amber2-100 text-amber2-700', 'label'=>'Pending'],
    'shipped' => ['class'=>'bg-navy-100 text-navy-700', 'label'=>'Shipped'],
    'delivered' => ['class'=>'bg-emerald-100 text-emerald-700', 'label'=>'Delivered'],
    'cancelled' => ['class'=>'bg-red-100 text-red-700', 'label'=>'Cancelled'],
];
?>
<div class="animate-slide">
<?php render_flash(); ?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <?php foreach([['Pending','pending','amber','ri-time-line'],['Shipped','shipped','blue','ri-truck-line'],['Delivered','delivered','emerald','ri-checkbox-circle-line'],['Cancelled','cancelled','red','ri-close-circle-line']] as $card):
        $iconColors=['amber'=>'bg-amber2-500','blue'=>'bg-navy-600','emerald'=>'bg-emerald-600','red'=>'bg-red-500']; ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center gap-4">
        <div class="<?php echo $iconColors[$card[2]]; ?> rounded-xl w-11 h-11 flex items-center justify-center text-white"><i class="<?php echo $card[3]; ?> text-lg"></i></div>
        <div><div class="text-2xl font-black text-slate-800"><?php echo $counts[$card[1]]; ?></div><div class="text-xs text-slate-400"><?php echo $card[0]; ?></div></div>
    </div>
    <?php endforeach; ?>
</div>

<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex flex-wrap gap-3 items-center">
    <input type="hidden" name="page" value="orders">
    <div class="flex-1 min-w-[180px] relative"><i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i><input name="q" value="<?php echo e($q); ?>" placeholder="Search orders..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-navy-600"></div>
    <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600"><option value="">All Status</option><?php foreach($statusMap as $key=>$s): ?><option value="<?php echo e($key); ?>" <?php echo $statusFilter===$key?'selected':''; ?>><?php echo e($s['label']); ?></option><?php endforeach; ?></select>
    <button class="bg-navy-600 text-white font-bold rounded-xl px-4 py-2 text-sm">Filter</button>
    <button type="button" onclick="deleteSelected()" class="bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl px-4 py-2 text-sm flex items-center gap-2 ml-auto"><i class="ri-delete-bin-line"></i> Delete Selected</button>
</form>

<form id="bulkDeleteForm" method="POST" action="">
    <input type="hidden" name="form_action" value="delete_multiple">

<?php $adminView = $_COOKIE['admin_view'] ?? 'list'; ?>
<?php if($adminView === 'grid'): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    <div class="col-span-full mb-2">
        <label class="flex items-center gap-2 text-sm text-slate-600 font-semibold cursor-pointer w-fit bg-white px-3 py-1.5 rounded-lg border border-slate-200">
            <input type="checkbox" id="selectAllGrid" class="w-4 h-4 rounded text-indigo-600 border-slate-300"> Select All
        </label>
    </div>
    <?php foreach($orders as $o):
        $s = $statusMap[$o['status']] ?? $statusMap['pending'];
        $customerName = $o['billing_name'] ?? $o['customer_name'] ?? '';
        $totalAmount = $o['total_amount'] ?? $o['amount'] ?? 0;
        $paymentMethod = $o['payment_method'] ?? 'manual';
        $orderDate = $o['created_at'] ?? $o['order_date'] ?? '';
    ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-md transition relative group">
        <input type="checkbox" name="selected_ids[]" value="<?php echo (int)$o['id']; ?>" class="item-checkbox absolute top-4 right-4 w-4 h-4 rounded text-indigo-600 border-slate-300 z-10">
        <div class="flex items-start gap-3 mb-4">
            <div class="bg-navy-50 rounded-xl w-12 h-12 flex items-center justify-center shrink-0"><i class="ri-shopping-bag-3-line text-navy-600 text-xl"></i></div>
            <div class="pr-6">
                <h4 class="font-black text-slate-800 text-base">#<?php echo e($o['order_no']); ?></h4>
                <div class="text-xs text-slate-400 mt-0.5"><?php echo e($orderDate); ?></div>
            </div>
        </div>
        <div class="mb-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Customer</p>
            <p class="text-sm text-slate-800 font-bold"><?php echo e($customerName); ?></p>
        </div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total</p>
                <div class="font-black text-slate-800 text-lg">$<?php echo number_format((float)$totalAmount,2); ?></div>
            </div>
            <div class="text-right">
                <span class="<?php echo $s['class']; ?> text-[10px] font-bold px-2 py-0.5 rounded-full block mb-1"><?php echo $s['label']; ?></span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600"><?php echo e(ucfirst($paymentMethod)); ?></span>
            </div>
        </div>
        <div class="flex gap-2 border-t border-slate-100 pt-4">
            <a href="?page=orders&action=view&id=<?php echo (int)$o['id']; ?>" class="flex-1 border border-navy-200 hover:bg-navy-50 text-navy-600 font-semibold py-1.5 rounded-lg text-center text-xs transition"><i class="ri-eye-line"></i> View</a>
            <button type="button" onclick="deleteSingle(<?php echo (int)$o['id']; ?>)" class="border border-red-200 hover:bg-red-50 text-red-500 font-semibold py-1.5 px-3 rounded-lg text-center text-xs transition"><i class="ri-delete-bin-line"></i></button>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(!$orders): ?><div class="col-span-full py-8 text-center text-slate-400 bg-white border border-slate-200 rounded-2xl">No orders found.</div><?php endif; ?>
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
                    <th class="px-5 py-3.5 text-left">Order</th>
                    <th class="px-5 py-3.5 text-left">Date</th>
                    <th class="px-5 py-3.5 text-left">Customer</th>
                    <th class="px-5 py-3.5 text-left">Total</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5 text-left">Payment</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($orders as $o):
                    $s = $statusMap[$o['status']] ?? $statusMap['pending'];
                    $customerName = $o['billing_name'] ?? $o['customer_name'] ?? '';
                    $totalAmount = $o['total_amount'] ?? $o['amount'] ?? 0;
                    $paymentMethod = $o['payment_method'] ?? 'manual';
                    $orderDate = $o['created_at'] ?? $o['order_date'] ?? '';
                ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5"><input type="checkbox" name="selected_ids[]" value="<?php echo (int)$o['id']; ?>" class="item-checkbox w-4 h-4 rounded text-indigo-600 border-slate-300"></td>
                    <td class="px-5 py-3.5 font-black text-slate-800">#<?php echo e($o['order_no']); ?></td>
                    <td class="px-5 py-3.5 text-slate-500 text-xs whitespace-nowrap"><?php echo e($orderDate); ?></td>
                    <td class="px-5 py-3.5 font-semibold text-slate-700"><?php echo e($customerName); ?></td>
                    <td class="px-5 py-3.5 font-bold text-slate-800">$<?php echo number_format((float)$totalAmount,2); ?></td>
                    <td class="px-5 py-3.5"><span class="<?php echo $s['class']; ?> text-[10px] font-bold px-2 py-1 rounded-full"><?php echo $s['label']; ?></span></td>
                    <td class="px-5 py-3.5"><span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded-full"><?php echo e(ucfirst($paymentMethod)); ?></span></td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="?page=orders&action=view&id=<?php echo (int)$o['id']; ?>" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center" title="View"><i class="ri-eye-line"></i></a>
                            <button type="button" onclick="deleteSingle(<?php echo (int)$o['id']; ?>)" class="text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center" title="Delete"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(!$orders): ?><tr><td colspan="8" class="px-5 py-8 text-center text-slate-400">No orders found.</td></tr><?php endif; ?>
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
    <?php render_pagination($totalOrders, $pagination); ?>
</div>
</form>

<form method="POST" id="singleDeleteForm" class="hidden">
    <input type="hidden" name="form_action" value="delete_order">
    <input type="hidden" name="id" id="singleDeleteId" value="">
</form>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});

function deleteSelected() {
    const selected = document.querySelectorAll('.item-checkbox:checked');
    if(selected.length === 0) {
        alert('Please select at least one order to delete.');
        return;
    }
    if(confirm('Are you sure you want to delete the selected orders?')) {
        document.getElementById('bulkDeleteForm').submit();
    }
}

function deleteSingle(id) {
    if(confirm('Delete this order?')) {
        document.getElementById('singleDeleteId').value = id;
        document.getElementById('singleDeleteForm').submit();
    }
}
</script>
</div>
