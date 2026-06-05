<?php
// ─── Product Reviews Management ──────────────────────────────────────────────

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postAction = $_POST['form_action'] ?? '';

    if ($postAction === 'update_review_status') {
        $status = $_POST['status'] ?? 'pending';
        if (in_array($status, ['pending','approved','rejected'], true)) {
            $pdo->prepare('UPDATE reviews SET status=? WHERE id=?')->execute([$status, (int)$_POST['id']]);
            set_flash('Review status updated.');
        }
        redirect_admin('reviews');
    }

    if ($postAction === 'save_reply') {
        $reply = trim($_POST['admin_reply'] ?? '');
        $pdo->prepare('UPDATE reviews SET admin_reply=? WHERE id=?')->execute([$reply, (int)$_POST['id']]);
        set_flash('Reply saved.');
        redirect_admin('reviews');
    }

    if ($postAction === 'delete_review') {
        admin_delete_record($pdo, 'reviews', (int)($_POST['id'] ?? 0), 'Review');
        redirect_admin('reviews');
    }

    if ($postAction === 'add_review') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $productName = '';
        if ($productId > 0) {
            $pRow = $pdo->prepare('SELECT name FROM products WHERE id=?');
            $pRow->execute([$productId]);
            $pRow = $pRow->fetch();
            $productName = $pRow ? $pRow['name'] : '';
        }
        $pdo->prepare('INSERT INTO reviews (product_id, product_name, customer_name, customer_email, rating, title, body, status) VALUES (?,?,?,?,?,?,?,?)')
            ->execute([
                $productId ?: null,
                $productName ?: trim($_POST['product_name'] ?? ''),
                trim($_POST['customer_name'] ?? 'Anonymous'),
                trim($_POST['customer_email'] ?? ''),
                max(1, min(5, (int)($_POST['rating'] ?? 5))),
                trim($_POST['title'] ?? ''),
                trim($_POST['body'] ?? ''),
                in_array($_POST['status'] ?? '', ['pending','approved','rejected'],true) ? $_POST['status'] : 'pending',
            ]);
        set_flash('Review added successfully.');
        redirect_admin('reviews');
    }
}

// Filters
$statusFilter = $_GET['status'] ?? '';
$ratingFilter = (int)($_GET['rating'] ?? 0);
$q = trim($_GET['q'] ?? '');
$where = [];
$params = [];

if (in_array($statusFilter, ['pending','approved','rejected'], true)) {
    $where[] = 'r.status = ?';
    $params[] = $statusFilter;
}
if ($ratingFilter >= 1 && $ratingFilter <= 5) {
    $where[] = 'r.rating = ?';
    $params[] = $ratingFilter;
}
if ($q !== '') {
    $where[] = '(r.customer_name LIKE ? OR r.product_name LIKE ? OR r.title LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%");
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM reviews r $sqlWhere");
$countStmt->execute($params);
$totalReviews = (int)$countStmt->fetchColumn();
$pagination = pagination_state($totalReviews, 10);

$stmt = $pdo->prepare("
    SELECT r.*, p.name AS p_name
    FROM reviews r
    LEFT JOIN products p ON p.id = r.product_id
    $sqlWhere
    ORDER BY FIELD(r.status,'pending','approved','rejected'), r.created_at DESC
    LIMIT 10 OFFSET " . (int)$pagination['offset']);
$stmt->execute($params);
$reviews = $stmt->fetchAll();

// Stats
$counts = ['pending'=>0,'approved'=>0,'rejected'=>0,'avg_rating'=>0];
$cRow = $pdo->query("SELECT status, COUNT(*) total FROM reviews GROUP BY status")->fetchAll();
foreach ($cRow as $r) { $counts[$r['status']] = (int)$r['total']; }
$avgRating = (float)($pdo->query("SELECT COALESCE(AVG(rating),0) FROM reviews WHERE status='approved'")->fetchColumn());

$statusMap = [
    'pending'  => ['bg-amber2-100 text-amber2-700', 'Pending'],
    'approved' => ['bg-emerald-100 text-emerald-700', 'Approved'],
    'rejected' => ['bg-red-100 text-red-600', 'Rejected'],
];

// All products for add form
$allProducts = $pdo->query("SELECT id, name FROM products WHERE status='active' ORDER BY name")->fetchAll();

// View single review
$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewReview = null;
if ($viewId > 0) {
    $stmtV = $pdo->prepare('SELECT r.*, p.name AS p_name FROM reviews r LEFT JOIN products p ON p.id = r.product_id WHERE r.id = ?');
    $stmtV->execute([$viewId]);
    $viewReview = $stmtV->fetch();
}
?>
<div class="animate-slide">
<?php render_flash(); ?>

<?php if ($viewReview): ?>
<!-- ── Single Review Detail ─────────────────────────────── -->
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="?page=reviews" class="text-slate-500 hover:text-slate-800 p-2 rounded-xl hover:bg-slate-100 transition"><i class="ri-arrow-left-line text-xl"></i></a>
        <div>
            <h2 class="text-xl font-black text-slate-800">Review Detail</h2>
            <p class="text-sm text-slate-400">Manage and reply to this review</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-5">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-black text-lg">
                    <?php echo e(strtoupper(substr($viewReview['customer_name'],0,1))); ?>
                </div>
                <div>
                    <p class="font-bold text-slate-800"><?php echo e($viewReview['customer_name']); ?></p>
                    <p class="text-xs text-slate-400"><?php echo e($viewReview['customer_email']); ?> · <?php echo e(date('M d, Y', strtotime($viewReview['created_at']))); ?></p>
                </div>
            </div>
            <span class="<?php echo $statusMap[$viewReview['status']][0]; ?> text-xs font-bold px-3 py-1 rounded-full">
                <?php echo $statusMap[$viewReview['status']][1]; ?>
            </span>
        </div>

        <div class="flex gap-0.5 mb-3">
            <?php for($i=1;$i<=5;$i++): ?>
            <i class="<?php echo $i<=(int)$viewReview['rating']?'ri-star-fill text-amber2-400':'ri-star-line text-slate-300'; ?> text-lg"></i>
            <?php endfor; ?>
            <span class="text-sm text-slate-500 ml-2 font-semibold"><?php echo (int)$viewReview['rating']; ?>.0</span>
        </div>

        <?php if($viewReview['product_name'] || $viewReview['p_name']): ?>
        <div class="bg-navy-50 rounded-xl px-3 py-2 text-xs text-navy-700 font-semibold inline-flex items-center gap-1 mb-3">
            <i class="ri-printer-line"></i> <?php echo e($viewReview['p_name'] ?: $viewReview['product_name']); ?>
        </div>
        <?php endif; ?>

        <?php if($viewReview['title']): ?>
        <h4 class="font-bold text-slate-800 mb-2"><?php echo e($viewReview['title']); ?></h4>
        <?php endif; ?>
        <p class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl"><?php echo e($viewReview['body']); ?></p>

        <!-- Update Status -->
        <form method="POST" class="flex gap-2 mt-4 pt-4 border-t border-slate-100">
            <input type="hidden" name="form_action" value="update_review_status">
            <input type="hidden" name="id" value="<?php echo (int)$viewReview['id']; ?>">
            <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600">
                <?php foreach($statusMap as $key=>$s): ?><option value="<?php echo e($key); ?>" <?php echo $viewReview['status']===$key?'selected':''; ?>><?php echo e($s[1]); ?></option><?php endforeach; ?>
            </select>
            <button class="bg-navy-600 text-white font-bold px-5 py-2 rounded-xl text-sm">Update Status</button>
        </form>
    </div>

    <!-- Admin Reply -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <h4 class="font-bold text-slate-700 text-sm mb-3 flex items-center gap-2"><i class="ri-reply-line text-navy-600"></i> Admin Reply</h4>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="form_action" value="save_reply">
            <input type="hidden" name="id" value="<?php echo (int)$viewReview['id']; ?>">
            <textarea name="admin_reply" rows="4" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-600 resize-none" placeholder="Write a reply visible to the customer..."><?php echo e($viewReview['admin_reply']); ?></textarea>
            <button class="bg-navy-600 hover:bg-navy-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm flex items-center gap-2"><i class="ri-send-plane-line"></i> Save Reply</button>
        </form>
    </div>
</div>

<?php else: ?>
<!-- ── Reviews List ─────────────────────────────────── -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-navy-600 to-navy-700 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-star-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo $totalReviews; ?></div>
        <div class="text-navy-200 text-xs mt-0.5">Total Reviews</div>
    </div>
    <div class="bg-gradient-to-br from-amber2-500 to-amber2-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-time-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo $counts['pending']; ?></div>
        <div class="text-amber2-100 text-xs mt-0.5">Pending</div>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-checkbox-circle-line text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo $counts['approved']; ?></div>
        <div class="text-emerald-100 text-xs mt-0.5">Approved</div>
    </div>
    <div class="bg-gradient-to-br from-navy-500 to-navy-600 rounded-2xl p-5 text-white">
        <div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-star-fill text-xl"></i></div>
        <div class="text-2xl font-black"><?php echo number_format($avgRating,1); ?></div>
        <div class="text-navy-100 text-xs mt-0.5">Avg Rating</div>
    </div>
</div>

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-5">
    <div>
        <h2 class="text-xl font-black text-slate-800">Product Reviews</h2>
        <p class="text-sm text-slate-400"><?php echo $totalReviews; ?> reviews in total</p>
    </div>
    <button onclick="document.getElementById('addReviewModal').classList.remove('hidden')" 
        class="flex items-center gap-2 bg-navy-600 hover:bg-navy-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm">
        <i class="ri-add-line text-lg"></i> Add Review
    </button>
</div>

<!-- Filter Bar -->
<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="page" value="reviews">
    <div class="flex-1 min-w-[180px] relative">
        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input name="q" value="<?php echo e($q); ?>" placeholder="Search reviews..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-navy-600">
    </div>
    <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="">All Status</option>
        <?php foreach($statusMap as $key=>$s): ?><option value="<?php echo e($key); ?>" <?php echo $statusFilter===$key?'selected':''; ?>><?php echo e($s[1]); ?></option><?php endforeach; ?>
    </select>
    <select name="rating" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-600">
        <option value="0">All Ratings</option>
        <?php for($r=5;$r>=1;$r--): ?><option value="<?php echo $r; ?>" <?php echo $ratingFilter===$r?'selected':''; ?>><?php echo $r; ?> Star</option><?php endfor; ?>
    </select>
    <button class="bg-navy-600 text-white font-bold rounded-xl px-4 py-2 text-sm">Filter</button>
</form>

<!-- Reviews List -->
<div class="space-y-4">
<?php foreach($reviews as $rev): $s = $statusMap[$rev['status']]; ?>
<div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-sm transition">
    <div class="flex items-start justify-between gap-3 mb-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-black shrink-0">
                <?php echo e(strtoupper(substr($rev['customer_name'],0,1))); ?>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm"><?php echo e($rev['customer_name']); ?></p>
                <p class="text-xs text-slate-400"><?php echo e($rev['customer_email']); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="<?php echo $s[0]; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo $s[1]; ?></span>
            <span class="text-xs text-slate-400"><?php echo e(date('M d, Y', strtotime($rev['created_at']))); ?></span>
        </div>
    </div>

    <!-- Stars -->
    <div class="flex items-center gap-1 mb-2">
        <?php for($i=1;$i<=5;$i++): ?>
        <i class="<?php echo $i<=(int)$rev['rating']?'ri-star-fill text-amber2-400':'ri-star-line text-slate-200'; ?> text-sm"></i>
        <?php endfor; ?>
        <?php if($rev['p_name'] || $rev['product_name']): ?>
        <span class="ml-2 text-xs bg-navy-50 text-navy-700 font-semibold px-2 py-0.5 rounded-lg"><?php echo e($rev['p_name'] ?: $rev['product_name']); ?></span>
        <?php endif; ?>
    </div>

    <?php if($rev['title']): ?><p class="font-bold text-slate-800 text-sm mb-1"><?php echo e($rev['title']); ?></p><?php endif; ?>
    <p class="text-sm text-slate-600 leading-relaxed mb-3"><?php echo e(mb_substr($rev['body'],0,200)); ?><?php echo mb_strlen($rev['body'])>200?'...':''; ?></p>

    <?php if($rev['admin_reply']): ?>
    <div class="bg-navy-50 border-l-4 border-navy-400 rounded-r-xl p-3 mb-3 text-sm text-navy-800">
        <span class="font-bold text-xs block mb-1 text-navy-600"><i class="ri-shield-user-line"></i> Admin Reply</span>
        <?php echo e(mb_substr($rev['admin_reply'],0,150)); ?>
    </div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2 pt-3 border-t border-slate-100">
        <a href="?page=reviews&view=<?php echo (int)$rev['id']; ?>" class="flex items-center gap-1.5 bg-navy-50 hover:bg-navy-100 text-navy-700 text-xs font-semibold px-3 py-2 rounded-xl transition">
            <i class="ri-eye-line"></i> View & Reply
        </a>
        <form method="POST" class="flex gap-2">
            <input type="hidden" name="form_action" value="update_review_status">
            <input type="hidden" name="id" value="<?php echo (int)$rev['id']; ?>">
            <select name="status" class="border border-slate-200 rounded-xl px-2 py-1.5 text-xs bg-white outline-none focus:border-navy-600">
                <?php foreach($statusMap as $key=>$item): ?><option value="<?php echo e($key); ?>" <?php echo $rev['status']===$key?'selected':''; ?>><?php echo e($item[1]); ?></option><?php endforeach; ?>
            </select>
            <button class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-xl">Save</button>
        </form>
        <form method="POST" class="ml-auto" onsubmit="return confirm('Delete this review?')">
            <input type="hidden" name="form_action" value="delete_review">
            <input type="hidden" name="id" value="<?php echo (int)$rev['id']; ?>">
            <button class="text-red-500 hover:bg-red-50 px-3 py-1.5 rounded-xl text-xs font-semibold border border-slate-200 transition"><i class="ri-delete-bin-line"></i> Delete</button>
        </form>
    </div>
</div>
<?php endforeach; ?>
<?php if(!$reviews): ?>
<div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
    <i class="ri-star-line text-6xl text-slate-200 block mb-4"></i>
    <p class="text-slate-500 font-semibold">No reviews found</p>
    <p class="text-slate-400 text-sm mt-1">Reviews submitted from frontend or manually added will appear here.</p>
</div>
<?php endif; ?>
</div>
<?php render_pagination($totalReviews, $pagination); ?>

<!-- Add Review Modal -->
<div id="addReviewModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full mx-4 shadow-xl border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="ri-star-line text-amber2-500"></i> Add Review</h3>
            <button onclick="document.getElementById('addReviewModal').classList.add('hidden')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-slate-200 text-slate-400 transition"><i class="ri-close-line text-lg"></i></button>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="form_action" value="add_review">
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Customer Name</label><input name="customer_name" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600"></div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Customer Email</label><input name="customer_email" type="email" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600"></div>
            </div>
            <div><label class="block text-xs font-semibold text-slate-600 mb-1">Product</label>
                <select name="product_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600">
                    <option value="">-- Select Product --</option>
                    <?php foreach($allProducts as $p): ?><option value="<?php echo (int)$p['id']; ?>"><?php echo e($p['name']); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Rating</label>
                    <select name="rating" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600">
                        <?php for($r=5;$r>=1;$r--): ?><option value="<?php echo $r; ?>"><?php echo $r; ?> Star<?php echo $r>1?'s':''; ?></option><?php endfor; ?>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-navy-600">
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
            <div><label class="block text-xs font-semibold text-slate-600 mb-1">Review Title</label><input name="title" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600" placeholder="Great printer!"></div>
            <div><label class="block text-xs font-semibold text-slate-600 mb-1">Review Body</label><textarea name="body" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-navy-600 resize-none" placeholder="Write review content..."></textarea></div>
            <div class="flex gap-3 pt-2">
                <button class="flex-1 bg-navy-600 hover:bg-navy-700 text-white font-bold py-2.5 rounded-xl text-sm flex items-center justify-center gap-2"><i class="ri-save-line"></i> Add Review</button>
                <button type="button" onclick="document.getElementById('addReviewModal').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 rounded-xl text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
</div>
