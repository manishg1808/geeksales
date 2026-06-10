<?php
$mobilePolicies = [];
$mobileCategories = [];
try {
    if (!function_exists('db')) {
        require_once __DIR__ . '/../admin/includes/db.php';
    }
    if (!function_exists('e')) {
        function e($value): string
        {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }
    $mobilePolicies = db()
        ->query("SELECT title, slug FROM policies ORDER BY id ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
    $mobileCategories = db()
        ->query("SELECT name, slug, icon FROM categories WHERE active = 1 ORDER BY name ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $mobilePolicies = [];
    $mobileCategories = [];
}
?>
<div id="mobile-nav-overlay" class="fixed inset-0 bg-slate-950/45 z-[60] hidden lg:hidden" onclick="toggleMobileNav(false)"></div>
<aside id="mobile-nav-drawer" class="fixed inset-y-0 left-0 z-[70] w-80 max-w-[86vw] bg-white shadow-2xl -translate-x-full transition-transform duration-300 lg:hidden flex flex-col">
  <div class="h-16 px-5 border-b border-slate-100 flex items-center justify-between">
    <a href="index.php" class="flex items-center gap-2.5">
      <img src="IMAGE/geeksupport_unique_simple_icon.svg" alt="Geek Support LLc" class="w-8 h-8 object-contain shrink-0">
      <span class="flex flex-col justify-center leading-none">
        <span class="font-black text-slate-900">Geek Support LLc</span>
        <span class="mt-1 text-[9px] font-bold uppercase tracking-widest text-slate-400">fast secure remote help</span>
      </span>
    </a>
    <button type="button" onclick="toggleMobileNav(false)" class="w-9 h-9 flex items-center justify-center text-slate-500 hover:text-slate-900 transition" aria-label="Close menu">
      <i class="ri-close-line text-2xl"></i>
    </button>
  </div>

  <!-- Search for mobile -->
  <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
    <form action="products.php" method="GET" class="flex w-full h-10 rounded-xl border border-slate-200 bg-white hover:border-slate-300 focus-within:border-navy-400 overflow-hidden relative header-search-form transition">
      <input name="q" type="text" placeholder="Search products..." class="flex-1 px-3.5 text-sm bg-transparent outline-none text-slate-700 placeholder-slate-400 header-search-input" autocomplete="off" />
      <button type="submit" class="px-3 text-slate-400 hover:text-navy-600 transition" aria-label="Search products">
        <i class="ri-search-2-line text-base"></i>
      </button>
    </form>
  </div>

  <nav class="flex-1 overflow-y-auto p-4 space-y-1">
    <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-navy-50 hover:text-navy-700 transition"><i class="ri-home-5-line text-lg"></i> Home</a>
    <a href="products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-navy-50 hover:text-navy-700 transition"><i class="ri-printer-line text-lg"></i> Products</a>
    <a href="support.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-navy-50 hover:text-navy-700 transition"><i class="ri-customer-service-2-line text-lg"></i> Support</a>
    <a href="contact.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-navy-50 hover:text-navy-700 transition"><i class="ri-phone-line text-lg"></i> Contact</a>
    <a href="checkout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-navy-50 hover:text-navy-700 transition"><i class="ri-shopping-bag-3-line text-lg"></i> Checkout</a>

    <?php if ($mobileCategories): ?>
    <div class="pt-4 mt-3 border-t border-slate-100">
      <p class="px-4 pb-2 text-[11px] font-black uppercase tracking-widest text-slate-400">Product Categories</p>
      <?php foreach ($mobileCategories as $mobileCategory): ?>
      <?php $mobileCategoryIcon = $mobileCategory['icon'] ?: 'ri-printer-line'; ?>
      <a href="products.php?cat=<?php echo e($mobileCategory['slug']); ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-navy-700 transition"><i class="<?php echo e($mobileCategoryIcon); ?> text-lg"></i> <?php echo e($mobileCategory['name']); ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($mobilePolicies): ?>
    <div class="pt-4 mt-3 border-t border-slate-100">
      <p class="px-4 pb-2 text-[11px] font-black uppercase tracking-widest text-slate-400">Policies</p>
      <?php foreach ($mobilePolicies as $mobilePolicy): ?>
      <a href="policy.php?slug=<?php echo e($mobilePolicy['slug']); ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-navy-700 transition"><i class="ri-file-list-3-line text-lg"></i> <?php echo e($mobilePolicy['title']); ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </nav>

  <div class="p-4 border-t border-slate-100">
    <a href="tel:407-246-9887" class="flex items-center justify-center gap-2 text-navy-700 font-black py-3 rounded-xl border border-navy-100 hover:border-navy-300 transition">
      <i class="ri-phone-line text-xl"></i> 407-246-9887
    </a>
  </div>
</aside>
<script>
function toggleMobileNav(force) {
  const drawer = document.getElementById('mobile-nav-drawer');
  const overlay = document.getElementById('mobile-nav-overlay');
  if (!drawer || !overlay) return;
  const shouldOpen = typeof force === 'boolean' ? force : drawer.classList.contains('-translate-x-full');
  drawer.classList.toggle('-translate-x-full', !shouldOpen);
  overlay.classList.toggle('hidden', !shouldOpen);
  document.documentElement.classList.toggle('overflow-hidden', shouldOpen);
}
document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') toggleMobileNav(false);
});
</script>

