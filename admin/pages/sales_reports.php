<?php
$summary = $pdo->query("SELECT COUNT(*) orders_count, COALESCE(SUM(amount),0) revenue, COALESCE(AVG(amount),0) avg_order FROM orders WHERE status <> 'cancelled'")->fetch();
$daily = $pdo->query("SELECT order_date, COUNT(*) orders_count, COALESCE(SUM(amount),0) revenue FROM orders WHERE status <> 'cancelled' GROUP BY order_date ORDER BY order_date DESC LIMIT 14")->fetchAll();
$categoryRevenue = $pdo->query("
    SELECT COALESCE(c.name, 'Uncategorized') category, COALESCE(SUM(o.amount),0) revenue, COUNT(o.id) orders_count
    FROM orders o
    LEFT JOIN products p ON p.name = o.product_name
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE o.status <> 'cancelled'
    GROUP BY c.id, c.name
    ORDER BY revenue DESC
")->fetchAll();
$topProducts = $pdo->query("
    SELECT product_name, COUNT(*) orders_count, COALESCE(SUM(amount),0) revenue
    FROM orders
    WHERE status <> 'cancelled'
    GROUP BY product_name
    ORDER BY orders_count DESC, revenue DESC
    LIMIT 8
")->fetchAll();
$maxDaily = max(1, ...array_map(fn($row) => (float)$row['revenue'], $daily ?: [['revenue'=>1]]));
$totalRevenue = max(1, (float)$summary['revenue']);
?>
<div class="animate-slide">
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div><h2 class="text-xl font-black text-slate-800 flex items-center gap-2"><i class="ri-bar-chart-2-line text-navy-600"></i> Sales Reports</h2><p class="text-sm text-slate-400">Revenue and order analytics from geeksales.orders</p></div>
    <a href="?page=orders" class="flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold px-4 py-2 rounded-xl text-sm"><i class="ri-shopping-bag-3-line"></i> View Orders</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-navy-600 to-navy-800 rounded-2xl p-5 text-white"><div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-money-dollar-circle-line text-xl"></i></div><div class="text-2xl font-black">$<?php echo number_format((float)$summary['revenue'],2); ?></div><div class="text-navy-200 text-xs mt-0.5">Total Revenue</div></div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 text-white"><div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-shopping-bag-3-line text-xl"></i></div><div class="text-2xl font-black"><?php echo (int)$summary['orders_count']; ?></div><div class="text-emerald-100 text-xs mt-0.5">Total Orders</div></div>
    <div class="bg-gradient-to-br from-amber2-500 to-amber2-600 rounded-2xl p-5 text-white"><div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-shopping-cart-line text-xl"></i></div><div class="text-2xl font-black">$<?php echo number_format((float)$summary['avg_order'],2); ?></div><div class="text-amber2-100 text-xs mt-0.5">Avg. Order Value</div></div>
    <div class="bg-gradient-to-br from-navy-500 to-navy-700 rounded-2xl p-5 text-white"><div class="bg-white/20 rounded-xl w-10 h-10 flex items-center justify-center mb-3"><i class="ri-printer-line text-xl"></i></div><div class="text-2xl font-black"><?php echo count($topProducts); ?></div><div class="text-navy-100 text-xs mt-0.5">Products Sold</div></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5"><h3 class="font-bold text-slate-800 text-sm">Revenue by Day</h3><span class="text-xs text-slate-400 bg-slate-100 px-3 py-1.5 rounded-lg">Latest 14 days</span></div>
        <div class="flex items-end gap-2 h-48">
            <?php foreach(array_reverse($daily) as $row): $pct = round(((float)$row['revenue'] / $maxDaily) * 100); ?>
            <div class="flex-1 flex flex-col items-center gap-1 group relative"><div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-2 py-1 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100">$<?php echo number_format((float)$row['revenue'],2); ?></div><div class="w-full rounded-t-lg bg-navy-500 hover:bg-navy-600 transition" style="height:<?php echo max(8,$pct); ?>%"></div><span class="text-[9px] text-slate-400"><?php echo e(date('M d', strtotime($row['order_date']))); ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <h3 class="font-bold text-slate-800 text-sm mb-5">Revenue by Category</h3>
        <div class="space-y-4">
            <?php foreach($categoryRevenue as $row): $pct = round(((float)$row['revenue'] / $totalRevenue) * 100); ?>
            <div><div class="flex items-center justify-between text-xs mb-1.5"><span class="font-semibold text-slate-700"><?php echo e($row['category']); ?></span><span class="text-slate-500">$<?php echo number_format((float)$row['revenue'],2); ?> <span class="text-slate-400">(<?php echo $pct; ?>%)</span></span></div><div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-navy-600 rounded-full" style="width:<?php echo $pct; ?>%"></div></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800 text-sm flex items-center gap-2"><i class="ri-trophy-line text-amber2-500"></i> Top Selling Products</h3></div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
        <?php foreach($topProducts as $i => $product): ?>
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition"><span class="font-black text-slate-400 w-6 text-center"><?php echo $i + 1; ?></span><div class="bg-navy-100 rounded-xl w-9 h-9 flex items-center justify-center shrink-0"><i class="ri-printer-fill text-navy-600"></i></div><div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-xs truncate"><?php echo e($product['product_name']); ?></div><div class="text-[10px] text-slate-400"><?php echo (int)$product['orders_count']; ?> orders</div></div><div class="font-black text-slate-800 text-sm shrink-0">$<?php echo number_format((float)$product['revenue'],2); ?></div></div>
        <?php endforeach; ?>
    </div>
</div>
</div>
