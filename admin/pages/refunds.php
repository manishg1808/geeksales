<?php
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_action'] ?? '') === 'update_refund') {
    $status = $_POST['status'] ?? 'requested';
    if (in_array($status, ['requested', 'approved', 'rejected', 'completed'], true)) {
        $pdo->prepare('UPDATE refunds SET status = ? WHERE id = ?')->execute([$status, (int)$_POST['id']]);
        set_flash('Refund status updated.');
    }
    redirect_admin('refunds');
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_action'] ?? '') === 'delete_multiple') {
    $ids = $_POST['selected_ids'] ?? [];
    if (!empty($ids) && is_array($ids)) {
        $validIds = array_filter(array_map('intval', $ids), fn($id) => $id > 0);
        if (!empty($validIds)) {
            $inQuery = implode(',', array_fill(0, count($validIds), '?'));
            $pdo->prepare("DELETE FROM refunds WHERE id IN ($inQuery)")->execute(array_values($validIds));
            set_flash('Selected refunds deleted successfully.');
        }
    } else {
        set_flash('No refunds selected.', 'error');
    }
    redirect_admin('refunds');
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_action'] ?? '') === 'delete_refund') {
    admin_delete_record($pdo, 'refunds', (int)($_POST['id'] ?? 0), 'Refund');
    redirect_admin('refunds');
}
$q = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(refund_no LIKE ? OR order_no LIKE ? OR customer_name LIKE ? OR reason LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
}
if (in_array($statusFilter, ['requested', 'approved', 'rejected', 'completed'], true)) {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM refunds $sqlWhere");
$countStmt->execute($params);
$totalRefunds = (int)$countStmt->fetchColumn();
$pagination = pagination_state($totalRefunds, 10);
$stmt = $pdo->prepare("SELECT * FROM refunds $sqlWhere ORDER BY requested_at DESC, id DESC LIMIT 10 OFFSET " . (int)$pagination['offset']);
$stmt->execute($params);
$refunds = $stmt->fetchAll();
$cnt = ['requested'=>0,'approved'=>0,'rejected'=>0,'completed'=>0,'total_amount'=>0];
foreach ($pdo->query('SELECT status, amount FROM refunds') as $r) {
    $cnt[$r['status']]++;
    $cnt['total_amount'] += (float)$r['amount'];
}
$statusMap = [
    'requested'=>['bg-amber2-100 text-amber2-700','Requested'],
    'approved'=>['bg-emerald-100 text-emerald-700','Approved'],
    'rejected'=>['bg-red-100 text-red-600','Rejected'],
    'completed'=>['bg-navy-100 text-navy-700','Completed'],
];
?>
<div class="animate-slide">
<?php render_flash(); ?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <?php foreach([['Requested','requested','bg-amber2-500','ri-time-line'],['Approved','approved','bg-emerald-600','ri-checkbox-circle-line'],['Rejected','rejected','bg-red-500','ri-close-circle-line'],['Total Value','total_amount','bg-navy-600','ri-money-dollar-circle-line']] as $c): $val = $c[1]==='total_amount' ? '$'.number_format($cnt[$c[1]],2) : $cnt[$c[1]]; ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-4"><div class="flex items-center gap-3"><div class="<?php echo $c[2]; ?> rounded-xl w-11 h-11 flex items-center justify-center text-white"><i class="<?php echo $c[3]; ?> text-xl"></i></div><div><div class="text-2xl font-black text-slate-800"><?php echo $val; ?></div><div class="text-xs text-slate-400"><?php echo $c[0]; ?></div></div></div></div>
    <?php endforeach; ?>
</div>

<div class="flex items-center justify-between mb-4">
    <div><h3 class="text-lg font-black text-slate-800">All Refunds</h3><p class="text-sm text-slate-400"><?php echo $totalRefunds; ?> refunds, 10 per page</p></div>
    <div class="flex items-center gap-2">
        <label class="flex items-center gap-2 text-sm text-slate-600 font-semibold cursor-pointer mr-2">
            <input type="checkbox" id="selectAll" class="w-4 h-4 rounded text-indigo-600 border-slate-300"> Select All
        </label>
        <button type="button" onclick="deleteSelected()" class="bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl px-4 py-2 text-sm flex items-center gap-2"><i class="ri-delete-bin-line"></i> Delete Selected</button>
    </div>
</div>

<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="page" value="refunds">
    <div class="flex-1 min-w-[200px] relative">
        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input name="q" value="<?php echo e($q); ?>" placeholder="Search refunds..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-navy-600">
    </div>
    <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="">All Status</option>
        <?php foreach($statusMap as $key=>$item): ?><option value="<?php echo e($key); ?>" <?php echo $statusFilter===$key?'selected':''; ?>><?php echo e($item[1]); ?></option><?php endforeach; ?>
    </select>
    <button class="bg-navy-600 text-white font-bold rounded-xl px-4 py-2 text-sm">Filter</button>
    <?php if($q !== '' || $statusFilter !== ''): ?><a href="?page=refunds" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl px-4 py-2 text-sm">Clear</a><?php endif; ?>
</form>

<form id="bulkDeleteForm" method="POST">
<input type="hidden" name="form_action" value="delete_multiple">
<?php $adminView = $_COOKIE['admin_view'] ?? 'list'; ?>
<?php if($adminView === 'list'): ?>
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5 text-left w-10"><input type="checkbox" id="selectAllList" class="w-4 h-4 rounded text-indigo-600 border-slate-300"></th>
                    <th class="px-5 py-3.5 text-left">Refund</th>
                    <th class="px-5 py-3.5 text-left">Order</th>
                    <th class="px-5 py-3.5 text-left">Customer</th>
                    <th class="px-5 py-3.5 text-left">Amount</th>
                    <th class="px-5 py-3.5 text-left">Reason</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($refunds as $r): $s = $statusMap[$r['status']]; ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5"><input type="checkbox" name="selected_ids[]" value="<?php echo (int)$r['id']; ?>" class="item-checkbox w-4 h-4 rounded text-indigo-600 border-slate-300"></td>
                    <td class="px-5 py-3.5"><div class="font-black text-slate-800"><?php echo e($r['refund_no']); ?></div><div class="text-xs text-slate-400"><?php echo e($r['requested_at']); ?></div></td>
                    <td class="px-5 py-3.5 font-semibold text-navy-600">#<?php echo e($r['order_no']); ?></td>
                    <td class="px-5 py-3.5 text-slate-700 font-semibold"><?php echo e($r['customer_name']); ?></td>
                    <td class="px-5 py-3.5 font-black text-red-600">-$<?php echo number_format((float)$r['amount'],2); ?></td>
                    <td class="px-5 py-3.5 text-slate-500 max-w-xs truncate"><?php echo e($r['reason']); ?></td>
                    <td class="px-5 py-3.5"><span class="<?php echo $s[0]; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo $s[1]; ?></span></td>
                    <td class="px-5 py-3.5 text-right"><button type="button" onclick="deleteSingle(<?php echo (int)$r['id']; ?>)" class="text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg inline-flex items-center justify-center" title="Delete"><i class="ri-delete-bin-line"></i></button></td>
                </tr>
                <?php endforeach; ?>
                <?php if(!$refunds): ?><tr><td colspan="8" class="px-5 py-8 text-center text-slate-400">No refunds found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.getElementById('selectAllList')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
<?php else: ?>
<div class="view-wrapper space-y-4">
<?php foreach($refunds as $r): $s = $statusMap[$r['status']]; ?>
<div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-sm transition relative">
    <input type="checkbox" name="selected_ids[]" value="<?php echo (int)$r['id']; ?>" class="item-checkbox absolute top-4 right-4 w-4 h-4 rounded text-indigo-600 border-slate-300 z-10">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div class="flex items-center gap-3"><div class="bg-red-50 rounded-xl w-11 h-11 flex items-center justify-center shrink-0"><i class="ri-refund-2-line text-red-500 text-xl"></i></div><div><div class="flex items-center gap-2"><span class="font-black text-slate-800"><?php echo e($r['refund_no']); ?></span><span class="text-xs text-slate-400">-></span><span class="text-sm font-semibold text-navy-600">#<?php echo e($r['order_no']); ?></span></div><div class="text-xs text-slate-400 mt-0.5"><?php echo e($r['requested_at']); ?></div></div></div>
        <span class="<?php echo $s[0]; ?> text-xs font-bold px-3 py-1.5 rounded-full"><?php echo $s[1]; ?></span>
    </div>
    <div class="view-wrapper grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Customer</p><p class="font-bold text-slate-800 text-sm"><?php echo e($r['customer_name']); ?></p></div>
        <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Amount</p><p class="font-black text-red-600">-$<?php echo number_format((float)$r['amount'],2); ?></p></div>
        <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Reason</p><p class="text-xs text-slate-600"><?php echo e($r['reason']); ?></p></div>
    </div>
    <form method="POST" class="flex flex-wrap gap-2 pt-3 border-t border-slate-100">
        <input type="hidden" name="form_action" value="update_refund"><input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
        <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white outline-none focus:border-navy-600"><?php foreach($statusMap as $key=>$item): ?><option value="<?php echo e($key); ?>" <?php echo $r['status']===$key?'selected':''; ?>><?php echo e($item[1]); ?></option><?php endforeach; ?></select>
        <button class="bg-navy-600 hover:bg-navy-700 text-white text-xs font-bold px-5 py-2 rounded-xl">Save Status</button>
    </form>
    <button type="button" onclick="deleteSingle(<?php echo (int)$r['id']; ?>)" class="absolute bottom-5 right-5 text-red-500 hover:bg-red-50 w-9 h-9 rounded-xl flex items-center justify-center border border-red-200" title="Delete"><i class="ri-delete-bin-line"></i></button>
</div>
<?php endforeach; ?>
<?php if(!$refunds): ?><div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-400">No refunds found.</div><?php endif; ?>
</div>
<?php endif; ?>
</form>

<form method="POST" id="singleDeleteForm" class="hidden">
    <input type="hidden" name="form_action" value="delete_refund">
    <input type="hidden" name="id" id="singleDeleteId" value="">
</form>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});

function deleteSelected() {
    const selected = document.querySelectorAll('.item-checkbox:checked');
    if(selected.length === 0) {
        alert('Please select at least one refund to delete.');
        return;
    }
    if(confirm('Are you sure you want to delete the selected refunds?')) {
        document.getElementById('bulkDeleteForm').submit();
    }
}

function deleteSingle(id) {
    if(confirm('Delete this refund?')) {
        document.getElementById('singleDeleteId').value = id;
        document.getElementById('singleDeleteForm').submit();
    }
}
</script>
<?php render_pagination($totalRefunds, $pagination); ?>
</div>
