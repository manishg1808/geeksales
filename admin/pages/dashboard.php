<?php
$stats = [
    'products' => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'orders' => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'revenue' => (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM orders WHERE status <> 'cancelled'")->fetchColumn(),
    'leads' => (int)$pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn(),
    'brands' => (int)$pdo->query('SELECT COUNT(*) FROM brands')->fetchColumn(),
    'categories' => (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'pending_orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
];
$recentOrders = $pdo->query('SELECT * FROM orders ORDER BY order_date DESC, id DESC LIMIT 5')->fetchAll();
$topBrands = $pdo->query('SELECT b.name, COUNT(p.id) total FROM brands b LEFT JOIN products p ON p.brand_id = b.id GROUP BY b.id ORDER BY total DESC, b.name LIMIT 5')->fetchAll();
$recentLeads = $pdo->query('SELECT * FROM leads ORDER BY created_at DESC LIMIT 5')->fetchAll();
$bestProducts = $pdo->query('SELECT p.name, COUNT(o.id) orders_count, COALESCE(SUM(o.amount),0) revenue FROM products p LEFT JOIN orders o ON o.product_name = p.name GROUP BY p.id ORDER BY orders_count DESC, revenue DESC LIMIT 5')->fetchAll();
$statusClasses = [
    'pending' => 'bg-amber2-100 text-amber2-700',
    'shipped' => 'bg-navy-100 text-navy-700',
    'delivered' => 'bg-emerald-100 text-emerald-700',
    'cancelled' => 'bg-red-100 text-red-700',
    'new' => 'bg-emerald-100 text-emerald-700',
    'contacted' => 'bg-navy-100 text-navy-700',
    'follow_up' => 'bg-amber2-100 text-amber2-700',
    'closed' => 'bg-slate-100 text-slate-500',
];
?>
<div class="animate-slide">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="card-hover bg-gradient-to-br from-navy-600 to-navy-700 rounded-2xl p-6 text-white shadow-lg"><div class="flex items-start justify-between mb-4"><div class="bg-white/20 rounded-xl w-12 h-12 flex items-center justify-center"><i class="ri-printer-line text-2xl"></i></div><span class="bg-emerald-400/20 text-emerald-300 text-xs font-bold px-2 py-1 rounded-lg">Live</span></div><div class="text-3xl font-black mb-1"><?php echo $stats['products']; ?></div><div class="text-navy-200 text-sm">Total Products</div></div>
        <div class="card-hover bg-gradient-to-br from-amber2-500 to-amber2-600 rounded-2xl p-6 text-white shadow-lg"><div class="flex items-start justify-between mb-4"><div class="bg-white/20 rounded-xl w-12 h-12 flex items-center justify-center"><i class="ri-shopping-bag-3-line text-2xl"></i></div><span class="bg-white/20 text-white text-xs font-bold px-2 py-1 rounded-lg">Live</span></div><div class="text-3xl font-black mb-1"><?php echo $stats['orders']; ?></div><div class="text-amber2-100 text-sm">Total Orders</div></div>
        <div class="card-hover bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg"><div class="flex items-start justify-between mb-4"><div class="bg-white/20 rounded-xl w-12 h-12 flex items-center justify-center"><i class="ri-money-dollar-circle-line text-2xl"></i></div><span class="bg-white/20 text-white text-xs font-bold px-2 py-1 rounded-lg">Live</span></div><div class="text-3xl font-black mb-1">$<?php echo number_format($stats['revenue'], 2); ?></div><div class="text-emerald-100 text-sm">Revenue</div></div>
        <div class="card-hover bg-gradient-to-br from-navy-500 to-navy-600 rounded-2xl p-6 text-white shadow-lg"><div class="flex items-start justify-between mb-4"><div class="bg-white/20 rounded-xl w-12 h-12 flex items-center justify-center"><i class="ri-contacts-line text-2xl"></i></div><span class="bg-white/20 text-white text-xs font-bold px-2 py-1 rounded-lg">Live</span></div><div class="text-3xl font-black mb-1"><?php echo $stats['leads']; ?></div><div class="text-navy-100 text-sm">Total Leads</div></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800 text-sm flex items-center gap-2"><i class="ri-shopping-bag-3-line text-navy-600"></i> Recent Orders</h3><a href="?page=orders" class="text-navy-600 text-xs font-semibold hover:underline">View All</a></div>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider"><tr><th class="px-6 py-3 text-left">Order</th><th class="px-6 py-3 text-left">Customer</th><th class="px-6 py-3 text-left">Product</th><th class="px-6 py-3 text-left">Amount</th><th class="px-6 py-3 text-left">Status</th></tr></thead><tbody class="divide-y divide-slate-100">
                <?php foreach($recentOrders as $order): ?><tr class="hover:bg-slate-50 transition"><td class="px-6 py-3 font-bold text-slate-700">#<?php echo e($order['order_no']); ?></td><td class="px-6 py-3 text-slate-600"><?php echo e($order['customer_name']); ?></td><td class="px-6 py-3 text-slate-500"><?php echo e($order['product_name']); ?></td><td class="px-6 py-3 font-bold text-slate-800">$<?php echo number_format((float)$order['amount'], 2); ?></td><td class="px-6 py-3"><span class="<?php echo $statusClasses[$order['status']]; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo e(ucfirst($order['status'])); ?></span></td></tr><?php endforeach; ?>
            </tbody></table></div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800 text-sm flex items-center gap-2"><i class="ri-award-line text-amber2-500"></i> Top Brands</h3><a href="?page=brands" class="text-navy-600 text-xs font-semibold hover:underline">View All</a></div>
            <div class="p-5 space-y-4">
                <?php $maxBrand = max(1, (int)($topBrands[0]['total'] ?? 1)); foreach($topBrands as $brand): $pct = (int)round(((int)$brand['total'] / $maxBrand) * 100); ?>
                <div><div class="flex items-center justify-between mb-1.5"><span class="text-sm font-semibold text-slate-700"><?php echo e($brand['name']); ?></span><span class="text-sm font-bold text-slate-800"><?php echo (int)$brand['total']; ?></span></div><div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-navy-600 rounded-full" style="width:<?php echo $pct; ?>%"></div></div></div>
                <?php endforeach; ?>
            </div>
            <div class="px-5 pb-5 grid grid-cols-2 gap-3"><div class="bg-navy-50 rounded-xl p-3 text-center"><div class="text-xl font-black text-navy-700"><?php echo $stats['brands']; ?></div><div class="text-xs text-slate-500">Brands</div></div><div class="bg-amber2-50 rounded-xl p-3 text-center"><div class="text-xl font-black text-amber2-600"><?php echo $stats['categories']; ?></div><div class="text-xs text-slate-500">Categories</div></div></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800 text-sm flex items-center gap-2"><i class="ri-contacts-line text-navy-500"></i> Recent Leads</h3><a href="?page=leads" class="text-navy-600 text-xs font-semibold hover:underline">View All</a></div>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider"><tr><th class="px-6 py-3 text-left">Name</th><th class="px-6 py-3 text-left">Email</th><th class="px-6 py-3 text-left">Phone</th><th class="px-6 py-3 text-left">Status</th></tr></thead><tbody class="divide-y divide-slate-100">
                <?php foreach($recentLeads as $lead): ?><tr class="hover:bg-slate-50 transition"><td class="px-6 py-3 font-semibold text-slate-700"><?php echo e($lead['name']); ?></td><td class="px-6 py-3 text-slate-500"><?php echo e($lead['email']); ?></td><td class="px-6 py-3 text-slate-500"><?php echo e($lead['phone']); ?></td><td class="px-6 py-3"><span class="<?php echo $statusClasses[$lead['status']]; ?> text-xs font-bold px-2.5 py-1 rounded-full"><?php echo e(ucwords(str_replace('_', ' ', $lead['status']))); ?></span></td></tr><?php endforeach; ?>
            </tbody></table></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2"><i class="ri-flashlight-line text-navy-600"></i> Quick Actions</h3>
            <div class="space-y-3">
                <a href="?page=products&action=add" class="flex items-center gap-3 p-3 bg-navy-50 hover:bg-navy-100 rounded-xl transition"><div class="bg-navy-600 rounded-lg w-9 h-9 flex items-center justify-center"><i class="ri-add-line text-white text-lg"></i></div><div><div class="text-sm font-bold text-slate-800">Add Product</div><div class="text-xs text-slate-400">Create new listing</div></div><i class="ri-arrow-right-line text-slate-400 ml-auto"></i></a>
                <a href="?page=categories&action=add" class="flex items-center gap-3 p-3 bg-amber2-50 hover:bg-amber2-100 rounded-xl transition"><div class="bg-amber2-500 rounded-lg w-9 h-9 flex items-center justify-center"><i class="ri-folder-add-line text-white text-lg"></i></div><div><div class="text-sm font-bold text-slate-800">Add Category</div><div class="text-xs text-slate-400">Organize products</div></div><i class="ri-arrow-right-line text-slate-400 ml-auto"></i></a>
                <a href="?page=brands&action=add" class="flex items-center gap-3 p-3 bg-navy-50 hover:bg-navy-100 rounded-xl transition"><div class="bg-navy-600 rounded-lg w-9 h-9 flex items-center justify-center"><i class="ri-award-line text-white text-lg"></i></div><div><div class="text-sm font-bold text-slate-800">Add Brand</div><div class="text-xs text-slate-400">Register brand</div></div><i class="ri-arrow-right-line text-slate-400 ml-auto"></i></a>
                <a href="?page=orders" class="flex items-center gap-3 p-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition"><div class="bg-emerald-600 rounded-lg w-9 h-9 flex items-center justify-center"><i class="ri-shopping-bag-3-line text-white text-lg"></i></div><div><div class="text-sm font-bold text-slate-800">View Orders</div><div class="text-xs text-slate-400"><?php echo $stats['pending_orders']; ?> pending orders</div></div><i class="ri-arrow-right-line text-slate-400 ml-auto"></i></a>
            </div>
        </div>
    </div>

    <div class="mt-5 bg-white rounded-2xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4"><h3 class="font-bold text-slate-800 text-base flex items-center gap-2"><i class="ri-trophy-line text-amber2-500"></i> Best Sellers</h3><a href="?page=sales_reports" class="text-xs text-navy-600 font-semibold hover:underline">Full Report</a></div>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <?php foreach($bestProducts as $product): ?><div class="bg-slate-50 rounded-xl p-3"><div class="text-xs font-bold text-slate-700 truncate"><?php echo e($product['name']); ?></div><div class="text-lg font-black text-slate-800 mt-1"><?php echo (int)$product['orders_count']; ?></div><div class="text-[10px] text-slate-400">$<?php echo number_format((float)$product['revenue'], 2); ?> revenue</div></div><?php endforeach; ?>
        </div>
    </div>
</div>
