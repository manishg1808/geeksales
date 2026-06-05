<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

// Simple authentication check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$pdo = db();
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    unset($_SESSION['admin_badge_counts']);
}

if (!function_exists('admin_badge_counts')) {
    function admin_badge_counts(PDO $pdo): array
    {
        $cached = $_SESSION['admin_badge_counts'] ?? null;
        if (is_array($cached) && (time() - (int)($cached['cached_at'] ?? 0)) < 30) {
            return $cached;
        }

        $counts = [
            'pending_orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
            'requested_refunds' => (int)$pdo->query("SELECT COUNT(*) FROM refunds WHERE status = 'requested'")->fetchColumn(),
            'new_leads' => (int)$pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn(),
            'pending_reviews' => 0,
            'active_coupons' => 0,
            'cached_at' => time(),
        ];

        try { $counts['pending_reviews'] = (int)$pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn(); } catch(Throwable $e) {}
        try { $counts['active_coupons'] = (int)$pdo->query("SELECT COUNT(*) FROM coupons WHERE active=1 AND (end_date IS NULL OR end_date >= CURDATE())")->fetchColumn(); } catch(Throwable $e) {}

        $_SESSION['admin_badge_counts'] = $counts;
        return $counts;
    }
}

$badgeCounts = admin_badge_counts($pdo);
$pendingOrders = $badgeCounts['pending_orders'];
$requestedRefunds = $badgeCounts['requested_refunds'];
$newLeads = $badgeCounts['new_leads'];
$pendingReviews = $badgeCounts['pending_reviews'];
$activeCoupons = $badgeCounts['active_coupons'];

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - GeekSupportSales</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#EFF6FF',100:'#DBEAFE',200:'#BFDBFE',300:'#93C5FD',400:'#60A5FA',500:'#2563EB',600:'#2563EB',700:'#1D4ED8',800:'#0F172A',900:'#0F172A' },
                        navy:  { 50:'#F8FAFC',100:'#F1F5F9',200:'#E5E7EB',300:'#CBD5E1',400:'#6B7280',500:'#2563EB',600:'#2563EB',700:'#1D4ED8',800:'#0F172A',900:'#0F172A' },
                        amber2:{ 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C',800:'#9A3412',900:'#7C2D12' }
                    },
                    fontFamily:{ sans:['Inter','system-ui','sans-serif'] },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        :root {
            --primary: #2563EB;
            --primary-light: #EFF6FF;
            --primary-mid: #DBEAFE;
            --primary-text: #1D4ED8;
        }
        html{scroll-behavior:smooth}

        /* Sidebar Links */
        .sidebar-link{transition:all .2s ease; color:#64748b;}
        .sidebar-link:hover{
            background:var(--primary-light);
            color:var(--primary);
            transform:translateX(4px);
        }
        .sidebar-link:hover i { color:var(--primary); }
        .sidebar-link.active{
            background:var(--primary);
            color:white;
            font-weight:600;
            box-shadow:0 4px 14px rgba(79,70,229,0.35);
        }
        .sidebar-link.active i { color:white; }

        /* Cards */
        .card-hover{transition:all .2s ease;}
        .card-hover:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(79,70,229,.12);}

        /* Animations */
        @keyframes slideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
        .animate-slide{animation:slideIn .3s ease;}

        /* Sidebar brand */
        .brand-icon {
            background: linear-gradient(135deg, #2563EB 0%, #0F172A 100%);
            border-radius: 12px;
            width:40px; height:40px;
            display:flex; align-items:center; justify-content:center;
        }

        /* Header icon hover */
        .header-icon:hover { color: var(--primary) !important; }

        /* Scrollbar */
        ::-webkit-scrollbar{width:4px;}
        ::-webkit-scrollbar-track{background:#f8fafc;}
        ::-webkit-scrollbar-thumb{background:#BFDBFE;border-radius:4px;}
        ::-webkit-scrollbar-thumb:hover{background:var(--primary);}
    </style>
    
</head>
<body class="bg-slate-50 font-sans antialiased">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-100 flex flex-col shadow-sm">
            <!-- Logo -->
            <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-100">
                <div class="brand-icon">
                    <i class="ri-printer-fill text-white text-xl"></i>
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-black">
                        <span style="color:#2563EB">Geek</span><span style="color:#0F172A">Admin</span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium">GeekSupportSales</div>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <a href="?page=dashboard" class="sidebar-link <?php echo $page === 'dashboard' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-dashboard-3-line text-lg"></i>
                    <span>Dashboard</span>
                </a>
                <a href="?page=products" class="sidebar-link <?php echo $page === 'products' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-printer-line text-lg"></i>
                    <span>Products</span>
                </a>
                <a href="?page=import_export" class="sidebar-link <?php echo $page === 'import_export' ? 'active' : ''; ?> flex items-center gap-2 pl-10 pr-4 py-2 rounded-xl text-slate-500 text-xs">
                    <i class="ri-upload-cloud-2-line text-sm"></i>
                    <span>Import / Export</span>
                </a>
                <a href="?page=categories" class="sidebar-link <?php echo $page === 'categories' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-folder-3-line text-lg"></i>
                    <span>Categories</span>
                </a>
                <a href="?page=brands" class="sidebar-link <?php echo $page === 'brands' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-award-line text-lg"></i>
                    <span>Brands</span>
                </a>
                <a href="?page=orders" class="sidebar-link <?php echo $page === 'orders' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-shopping-bag-3-line text-lg"></i>
                    <span>Orders</span>
                    <?php if($pendingOrders > 0): ?><span class="ml-auto bg-red-505 text-white text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:#ef4444;"><?php echo $pendingOrders; ?></span><?php endif; ?>
                </a>
                <a href="?page=leads" class="sidebar-link <?php echo $page === 'leads' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-contacts-line text-lg"></i>
                    <span>Leads</span>
                    <?php if($newLeads > 0): ?><span class="ml-auto bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:#16A34A;"><?php echo $newLeads; ?></span><?php endif; ?>
                </a>
                <a href="?page=customers" class="sidebar-link <?php echo $page === 'customers' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-group-line text-lg"></i>
                    <span>Customers</span>
                </a>
                <a href="?page=reviews" class="sidebar-link <?php echo $page === 'reviews' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-star-line text-lg"></i>
                    <span>Reviews</span>
                    <?php if($pendingReviews > 0): ?><span class="ml-auto bg-amber2-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo $pendingReviews; ?></span><?php endif; ?>
                </a>

                <!-- Divider -->
                <div class="pt-3 pb-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4">Marketing</p>
                </div>

                <a href="?page=seo" class="sidebar-link <?php echo $page === 'seo' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-search-eye-line text-lg"></i>
                    <span>SEO / Meta</span>
                </a>
                <a href="?page=banners" class="sidebar-link <?php echo $page === 'banners' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-image-2-line text-lg"></i>
                    <span>Banner Manager</span>
                </a>
                <a href="?page=coupons" class="sidebar-link <?php echo $page === 'coupons' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-coupon-3-line text-lg"></i>
                    <span>Coupons</span>
                    <?php if($activeCoupons > 0): ?><span class="ml-auto bg-navy-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo $activeCoupons; ?></span><?php endif; ?>
                </a>

                <!-- Divider -->
                <div class="pt-3 pb-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4">Reports</p>
                </div>

                <a href="?page=sales_reports" class="sidebar-link <?php echo $page === 'sales_reports' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-bar-chart-2-line text-lg"></i>
                    <span>Sales Reports</span>
                </a>
                <a href="?page=refunds" class="sidebar-link <?php echo $page === 'refunds' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-refund-2-line text-lg"></i>
                    <span>Refunds</span>
                    <?php if($requestedRefunds > 0): ?><span class="ml-auto bg-amber2-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:#F97316;"><?php echo $requestedRefunds; ?></span><?php endif; ?>
                </a>

                <!-- Divider -->
                <div class="pt-3 pb-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4">System</p>
                </div>

                <a href="?page=settings" class="sidebar-link <?php echo $page === 'settings' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-600 text-sm">
                    <i class="ri-settings-3-line text-lg"></i>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-slate-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#EFF6FF">
                        <i class="ri-user-3-fill text-lg" style="color:#2563EB"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-slate-800 truncate">Admin User</div>
                        <div class="text-xs text-slate-400">admin@geek.com</div>
                    </div>
                </div>
                <a href="../index.php" class="flex items-center justify-center gap-2 w-full text-sm font-semibold py-2 rounded-xl transition" style="background:#EFF6FF;color:#2563EB;" onmouseover="this.style.background='#DBEAFE'" onmouseout="this.style.background='#EFF6FF'">
                    <i class="ri-external-link-line"></i> View Frontend
                </a>
                <a href="logout.php" class="flex items-center justify-center gap-2 w-full bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold py-2 rounded-xl transition mt-2">
                    <i class="ri-logout-box-line"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6" style="box-shadow:0 1px 4px rgba(79,70,229,0.06)">
                <div>
                    <h1 class="text-xl font-black text-slate-800 capitalize"><?php echo ucfirst(str_replace('_', ' ', $page)); ?></h1>
                    <p class="text-xs text-slate-400">Welcome back, Admin! 👋</p>
                </div>
                                <div class="flex items-center gap-3">
                    <?php if(in_array($page, ['products','categories','brands','orders','leads','customers','refunds'])): ?>
                    <div class="flex border border-slate-200 rounded-lg overflow-hidden h-8 mr-2">
                        <button onclick="setAdminView('grid')" id="view-grid-btn" class="px-2.5 bg-white text-slate-400 hover:text-navy-600 transition" title="Grid View"><i class="ri-grid-fill"></i></button>
                        <button onclick="setAdminView('list')" id="view-list-btn" class="px-2.5 bg-navy-600 text-white transition" title="List View"><i class="ri-list-check-2"></i></button>
                    </div>
                    <?php endif; ?>
                    <a href="?page=leads" class="relative text-slate-400 header-icon transition">
                        <i class="ri-notification-3-line text-xl"></i>
                        <?php if($newLeads > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center"><?php echo $newLeads; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="?page=settings" class="text-slate-400 header-icon transition">
                        <i class="ri-settings-3-line text-xl"></i>
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <?php
                switch($page) {
                    case 'dashboard':
                        include 'pages/dashboard.php';
                        break;
                    case 'products':
                        include 'pages/products.php';
                        break;
                    case 'categories':
                        include 'pages/categories.php';
                        break;
                    case 'brands':
                        include 'pages/brands.php';
                        break;
                    case 'orders':
                        include 'pages/orders.php';
                        break;
                    case 'leads':
                        include 'pages/leads.php';
                        break;
                    case 'seo':
                        include 'pages/seo.php';
                        break;
                    case 'banners':
                        include 'pages/banners.php';
                        break;
                    case 'sales_reports':
                        include 'pages/sales_reports.php';
                        break;
                    case 'refunds':
                        include 'pages/refunds.php';
                        break;
                    case 'import_export':
                        include 'pages/import_export.php';
                        break;
                    case 'settings':
                        include is_file(__DIR__ . '/pages/settings.php') ? 'pages/settings.php' : 'pages/dashboard.php';
                        break;
                    case 'customers':
                        include 'pages/customers.php';
                        break;
                    case 'coupons':
                        include 'pages/coupons.php';
                        break;
                    case 'reviews':
                        include 'pages/reviews.php';
                        break;
                    default:
                        include 'pages/dashboard.php';
                }
                ?>
            </main>

        </div>
    </div>

    <script>
        function getAdminView() {
            const match = document.cookie.match(/(?:^|; )admin_view=([^;]+)/);
            return match ? decodeURIComponent(match[1]) : 'list';
        }

        function paintAdminViewButtons() {
            const view = getAdminView();
            const grid = document.getElementById('view-grid-btn');
            const list = document.getElementById('view-list-btn');
            if (grid) {
                grid.className = view === 'grid'
                    ? 'px-2.5 bg-navy-600 text-white transition'
                    : 'px-2.5 bg-white text-slate-400 hover:text-navy-600 transition';
            }
            if (list) {
                list.className = view === 'list'
                    ? 'px-2.5 bg-navy-600 text-white transition'
                    : 'px-2.5 bg-white text-slate-400 hover:text-navy-600 transition';
            }
        }

        function setAdminView(view) {
            document.cookie = 'admin_view=' + encodeURIComponent(view) + '; path=/; max-age=31536000; samesite=lax';
            paintAdminViewButtons();
            window.location.reload();
        }

        document.addEventListener('DOMContentLoaded', paintAdminViewButtons);
    </script>
</body>
</html>
