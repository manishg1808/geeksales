<?php
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';
    if ($postAction === 'update_lead') {
        $status = $_POST['status'] ?? 'new';
        if (in_array($status, ['new', 'contacted', 'follow_up', 'closed'], true)) {
            $pdo->prepare('UPDATE leads SET status = ? WHERE id = ?')->execute([$status, (int)$_POST['id']]);
            set_flash('Lead status updated.');
        }
        redirect_admin('leads');
    }
    if ($postAction === 'delete_lead') {
        admin_delete_record($pdo, 'leads', (int)($_POST['id'] ?? 0), 'Lead');
        redirect_admin('leads');
    }
    if ($postAction === 'delete_multiple') {
        $ids = $_POST['selected_ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $validIds = array_filter(array_map('intval', $ids), fn($id) => $id > 0);
            if (!empty($validIds)) {
                $inQuery = implode(',', array_fill(0, count($validIds), '?'));
                $pdo->prepare("DELETE FROM leads WHERE id IN ($inQuery)")->execute(array_values($validIds));
                set_flash('Selected leads deleted successfully.');
            }
        } else {
            set_flash('No leads selected.', 'error');
        }
        redirect_admin('leads');
    }
}

$statusFilter = $_GET['status'] ?? '';
$q = trim($_GET['q'] ?? '');
$where = [];
$params = [];
if (in_array($statusFilter, ['new', 'contacted', 'follow_up', 'closed'], true)) {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
}
if ($q !== '') {
    $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR subject LIKE ? OR message LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%", "%$q%");
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM leads $sqlWhere");
$countStmt->execute($params);
$totalLeads = (int)$countStmt->fetchColumn();
$pagination = pagination_state($totalLeads, 10);
$stmt = $pdo->prepare("SELECT * FROM leads $sqlWhere ORDER BY created_at DESC LIMIT 10 OFFSET " . (int)$pagination['offset']);
$stmt->execute($params);
$leads = $stmt->fetchAll();

$counts = ['new'=>0,'contacted'=>0,'follow_up'=>0,'closed'=>0];
foreach ($pdo->query('SELECT status, COUNT(*) total FROM leads GROUP BY status') as $row) {
    $counts[$row['status']] = (int)$row['total'];
}
$statusMap = [
    'new' => ['class'=>'bg-emerald-100 text-emerald-700', 'label'=>'New'],
    'contacted' => ['class'=>'bg-navy-100 text-navy-700', 'label'=>'Contacted'],
    'follow_up' => ['class'=>'bg-amber2-100 text-amber2-700', 'label'=>'Follow Up'],
    'closed' => ['class'=>'bg-slate-100 text-slate-500', 'label'=>'Closed'],
];
?>
<div class="animate-slide">
<?php render_flash(); ?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <?php foreach([['New Leads','new','bg-emerald-600','ri-user-add-line'],['Contacted','contacted','bg-navy-600','ri-phone-line'],['Follow Up','follow_up','bg-amber2-500','ri-repeat-line'],['Closed','closed','bg-slate-500','ri-checkbox-circle-line']] as $card): ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center gap-4">
        <div class="<?php echo $card[2]; ?> rounded-xl w-11 h-11 flex items-center justify-center text-white"><i class="<?php echo $card[3]; ?> text-lg"></i></div>
        <div><div class="text-2xl font-black text-slate-800"><?php echo $counts[$card[1]]; ?></div><div class="text-xs text-slate-400"><?php echo $card[0]; ?></div></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="flex items-center justify-between mb-5">
    <div><h2 class="text-xl font-black text-slate-800">All Leads</h2><p class="text-sm text-slate-400"><?php echo $totalLeads; ?> inquiries, 10 per page</p></div>
    <div class="flex gap-2 items-center">
        <label class="flex items-center gap-2 text-sm text-slate-600 font-semibold cursor-pointer mr-2">
            <input type="checkbox" id="selectAll" class="w-4 h-4 rounded text-indigo-600 border-slate-300"> Select All
        </label>
        <button type="button" onclick="deleteSelected()" class="bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl px-4 py-2 text-sm flex items-center gap-2"><i class="ri-delete-bin-line"></i> Delete Selected</button>
    </div>
</div>

<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="page" value="leads">
    <div class="flex-1 min-w-[200px] relative">
        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input name="q" value="<?php echo e($q); ?>" placeholder="Search leads..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-navy-600">
    </div>
    <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="">All Status</option>
        <?php foreach($statusMap as $key=>$s): ?><option value="<?php echo e($key); ?>" <?php echo $statusFilter===$key?'selected':''; ?>><?php echo e($s['label']); ?></option><?php endforeach; ?>
    </select>
    <button class="bg-navy-600 text-white font-bold rounded-xl px-4 py-2 text-sm">Filter</button>
    <?php if($q !== '' || $statusFilter !== ''): ?><a href="?page=leads" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl px-4 py-2 text-sm">Clear</a><?php endif; ?>
</form>

<form id="bulkDeleteForm" method="POST" action="">
    <input type="hidden" name="form_action" value="delete_multiple">

<?php $adminView = $_COOKIE['admin_view'] ?? 'list'; ?>
<?php if($adminView === 'list'): ?>
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5 text-left w-10"><input type="checkbox" id="selectAllList" class="w-4 h-4 rounded text-indigo-600 border-slate-300"></th>
                    <th class="px-5 py-3.5 text-left">Lead</th>
                    <th class="px-5 py-3.5 text-left">Subject</th>
                    <th class="px-5 py-3.5 text-left">Contact</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($leads as $l): $s = $statusMap[$l['status']]; ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5"><input type="checkbox" name="selected_ids[]" value="<?php echo (int)$l['id']; ?>" class="item-checkbox w-4 h-4 rounded text-indigo-600 border-slate-300"></td>
                    <td class="px-5 py-3.5"><div class="font-bold text-slate-800"><?php echo e($l['name']); ?></div><div class="text-xs text-slate-400"><?php echo e(date('Y-m-d', strtotime($l['created_at']))); ?></div></td>
                    <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate"><?php echo e($l['subject']); ?></td>
                    <td class="px-5 py-3.5 text-xs text-slate-500"><div><?php echo e($l['email']); ?></div><div><?php echo e($l['phone']); ?></div></td>
                    <td class="px-5 py-3.5"><span class="<?php echo $s['class']; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo $s['label']; ?></span></td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center btn-view-lead" data-lead="<?php echo e(json_encode($l)); ?>" title="View"><i class="ri-eye-line"></i></button>
                            <a href="mailto:<?php echo e($l['email']); ?>" class="text-navy-600 hover:bg-navy-50 w-8 h-8 rounded-lg flex items-center justify-center" title="Reply"><i class="ri-mail-send-line"></i></a>
                            <button type="button" onclick="deleteSingle(<?php echo (int)$l['id']; ?>)" class="text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center" title="Delete"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(!$leads): ?><tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">No leads found.</td></tr><?php endif; ?>
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
<div class="view-wrapper grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach($leads as $l): $s = $statusMap[$l['status']]; ?>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-md transition relative">
        <input type="checkbox" name="selected_ids[]" value="<?php echo (int)$l['id']; ?>" class="item-checkbox absolute top-4 right-4 w-4 h-4 rounded text-indigo-600 border-slate-300 z-10">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="bg-navy-100 rounded-full w-10 h-10 flex items-center justify-center text-navy-700 font-black text-sm"><?php echo e(strtoupper(substr($l['name'],0,1))); ?></div>
                <div><div class="font-bold text-slate-800 text-sm"><?php echo e($l['name']); ?></div><div class="text-xs text-slate-400"><?php echo e(date('Y-m-d', strtotime($l['created_at']))); ?></div></div>
            </div>
            <span class="<?php echo $s['class']; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo $s['label']; ?></span>
        </div>
        <h4 class="font-semibold text-slate-700 text-sm mb-1"><?php echo e($l['subject']); ?></h4>
        <p class="text-xs text-slate-500 mb-3 leading-relaxed"><?php echo e($l['message']); ?></p>
        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mb-4">
            <span class="flex items-center gap-1"><i class="ri-mail-line text-navy-600"></i><?php echo e($l['email']); ?></span>
            <span class="flex items-center gap-1"><i class="ri-phone-line text-navy-600"></i><?php echo e($l['phone']); ?></span>
        </div>
        <div class="flex gap-2">
            <button class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 w-9 h-9 rounded-xl flex items-center justify-center border border-indigo-200 btn-view-lead" data-lead="<?php echo e(json_encode($l)); ?>" title="View Details"><i class="ri-eye-line text-sm"></i></button>
            <a href="mailto:<?php echo e($l['email']); ?>" class="flex-1 flex items-center justify-center gap-1.5 bg-navy-600 hover:bg-navy-700 text-white text-xs font-semibold py-2 rounded-xl"><i class="ri-mail-send-line"></i> Reply</a>
            <form method="POST" class="flex-1 flex gap-2"><input type="hidden" name="form_action" value="update_lead"><input type="hidden" name="id" value="<?php echo (int)$l['id']; ?>"><select name="status" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs text-slate-600 bg-white outline-none focus:border-navy-600"><?php foreach($statusMap as $key=>$item): ?><option value="<?php echo e($key); ?>" <?php echo $l['status']===$key?'selected':''; ?>><?php echo e($item['label']); ?></option><?php endforeach; ?></select><button class="bg-slate-100 text-slate-700 px-3 rounded-xl text-xs font-bold">Save</button></form>
            <button type="button" onclick="deleteSingle(<?php echo (int)$l['id']; ?>)" class="text-red-500 hover:bg-red-50 w-9 h-9 rounded-xl flex items-center justify-center border border-slate-200" title="Delete"><i class="ri-delete-bin-line text-sm"></i></button>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(!$leads): ?><div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-400">No leads found.</div><?php endif; ?>
</div>
<?php endif; ?>
</form>

<form method="POST" id="singleDeleteForm" class="hidden">
    <input type="hidden" name="form_action" value="delete_lead">
    <input type="hidden" name="id" id="singleDeleteId" value="">
</form>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
});

function deleteSelected() {
    const selected = document.querySelectorAll('.item-checkbox:checked');
    if(selected.length === 0) {
        alert('Please select at least one lead to delete.');
        return;
    }
    if(confirm('Are you sure you want to delete the selected leads?')) {
        document.getElementById('bulkDeleteForm').submit();
    }
}

function deleteSingle(id) {
    if(confirm('Delete this lead?')) {
        document.getElementById('singleDeleteId').value = id;
        document.getElementById('singleDeleteForm').submit();
    }
}
</script>
<?php render_pagination($totalLeads, $pagination); ?>
</div>

<!-- Lead Detail Modal -->
<div id="leadModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl max-w-lg w-full mx-4 shadow-xl border border-slate-200 overflow-hidden transform scale-95 transition-transform duration-300" id="leadModalContent">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="ri-contacts-line text-indigo-600"></i> Lead Details</h3>
            <button onclick="closeLeadModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition"><i class="ri-close-line text-lg"></i></button>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div id="leadModalAvatar" class="bg-indigo-100 text-indigo-700 rounded-full w-12 h-12 flex items-center justify-center font-black text-lg"></div>
                <div>
                    <h4 id="leadModalName" class="font-bold text-slate-800 text-base"></h4>
                    <p id="leadModalDate" class="text-xs text-slate-400"></p>
                </div>
                <span id="leadModalStatus" class="ml-auto text-xs font-bold px-2.5 py-1 rounded-full"></span>
            </div>
            <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl text-sm border border-slate-100">
                <div>
                    <span class="block text-[10px] uppercase font-bold text-slate-400 mb-0.5">Email</span>
                    <a id="leadModalEmail" href="" class="text-indigo-600 font-semibold hover:underline flex items-center gap-1"><i class="ri-mail-line"></i> <span></span></a>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-bold text-slate-400 mb-0.5">Phone</span>
                    <span id="leadModalPhone" class="text-slate-700 font-semibold flex items-center gap-1"><i class="ri-phone-line"></i> <span></span></span>
                </div>
            </div>
            <div>
                <span class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Subject</span>
                <p id="leadModalSubject" class="text-sm font-bold text-slate-800 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100/50"></p>
            </div>
            <div>
                <span class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Message</span>
                <div id="leadModalMessage" class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 max-h-60 overflow-y-auto whitespace-pre-wrap"></div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeLeadModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Close</button>
            <a id="leadModalReplyBtn" href="" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold flex items-center gap-1.5 transition"><i class="ri-mail-send-line"></i> Reply Email</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(event) {
    const btn = event.target.closest('.btn-view-lead');
    if (btn) {
        try {
            const lead = JSON.parse(btn.getAttribute('data-lead'));
            showLeadModal(lead);
        } catch (e) {
            console.error(e);
        }
    }
});

function showLeadModal(lead) {
    const modal = document.getElementById('leadModal');
    const content = document.getElementById('leadModalContent');
    
    // Fill data
    document.getElementById('leadModalAvatar').textContent = lead.name.charAt(0).toUpperCase();
    document.getElementById('leadModalName').textContent = lead.name;
    
    // Date formatting
    const d = new Date(lead.created_at);
    document.getElementById('leadModalDate').textContent = d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    
    // Status mapping
    const statusMap = {
        'new': { class: 'bg-emerald-100 text-emerald-700', label: 'New' },
        'contacted': { class: 'bg-navy-100 text-navy-700', label: 'Contacted' },
        'follow_up': { class: 'bg-amber2-100 text-amber2-700', label: 'Follow Up' },
        'closed': { class: 'bg-slate-100 text-slate-500', label: 'Closed' }
    };
    const s = statusMap[lead.status] || { class: 'bg-slate-100 text-slate-500', label: lead.status };
    const statusEl = document.getElementById('leadModalStatus');
    statusEl.className = 'ml-auto text-xs font-bold px-2.5 py-1 rounded-full ' + s.class;
    statusEl.textContent = s.label;
    
    // Contact
    const emailLink = document.getElementById('leadModalEmail');
    emailLink.href = 'mailto:' + lead.email;
    emailLink.querySelector('span').textContent = lead.email;
    
    document.getElementById('leadModalPhone').querySelector('span').textContent = lead.phone || 'N/A';
    
    // Subject & Message
    document.getElementById('leadModalSubject').textContent = lead.subject;
    document.getElementById('leadModalMessage').textContent = lead.message;
    
    // Reply button
    document.getElementById('leadModalReplyBtn').href = 'mailto:' + lead.email;
    
    // Show modal
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-95');
    }, 10);
}

function closeLeadModal() {
    const modal = document.getElementById('leadModal');
    const content = document.getElementById('leadModalContent');
    modal.classList.add('opacity-0');
    content.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>
