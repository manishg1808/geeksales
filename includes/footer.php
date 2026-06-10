<?php
require_once __DIR__ . '/../admin/includes/db.php';

$footerCategories = [];
$footerPolicies = [];

try {
    $footerPdo = db();
    $footerCategories = $footerPdo
        ->query("SELECT name, slug FROM categories WHERE active = 1 ORDER BY id ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
    $footerPolicies = $footerPdo
        ->query("SELECT title, slug FROM policies ORDER BY id ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $footerCategories = [];
    $footerPolicies = [];
}

$footerCurrentPolicySlug = isset($slug) ? (string)$slug : '';
?>
<style>
  html, body { max-width: 100%; overflow-x: hidden; }
  img, video, iframe { max-width: 100%; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
<footer class="bg-slate-900 text-slate-400 pt-14 pb-8 px-5 mt-10">
  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-5 gap-10 mb-10">
    <div>
      <div class="flex items-center gap-2.5 mb-4">
        <div class="w-9 h-9 flex items-center justify-center shrink-0">
          <img src="IMAGE/geeksupport_unique_simple_icon.svg" alt="Geek Support LLc" class="w-8 h-8 object-contain">
        </div>
        <span class="flex flex-col justify-center leading-none">
          <span class="text-base font-black text-white">Geek Support LLc</span>
          <span class="mt-1 text-[9px] font-bold uppercase tracking-widest text-slate-500">fast secure remote help</span>
        </span>
      </div>
      <p class="text-sm leading-relaxed text-slate-500">Your trusted source for printers, ink, toner, and expert tech support.</p>
    </div>

    <div>
      <h4 class="text-white font-bold text-sm mb-4">Products</h4>
      <ul class="space-y-2.5 text-sm">
        <?php if ($footerCategories): ?>
          <?php foreach ($footerCategories as $category): ?>
            <li>
              <a href="products.php?cat=<?php echo e($category['slug']); ?>" class="hover:text-white transition flex items-center gap-1.5">
                <i class="ri-arrow-right-s-line"></i> <?php echo e($category['name']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li><a href="products.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> All Products</a></li>
        <?php endif; ?>
      </ul>
    </div>

    <div>
      <h4 class="text-white font-bold text-sm mb-4">Support</h4>
      <ul class="space-y-2.5 text-sm">
        <li><a href="support.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Support Center</a></li>
        <li><a href="contact.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Contact Us</a></li>
        <li><a href="support.php#drivers" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Driver Downloads</a></li>
      </ul>
    </div>

    <div>
      <h4 class="text-white font-bold text-sm mb-4">Policies</h4>
      <ul class="space-y-2.5 text-sm">
        <?php foreach ($footerPolicies as $policyLink): ?>
          <?php $activeClass = $policyLink['slug'] === $footerCurrentPolicySlug ? 'text-white font-semibold' : 'hover:text-white transition'; ?>
          <li>
            <a href="policy.php?slug=<?php echo e($policyLink['slug']); ?>" class="<?php echo $activeClass; ?> flex items-center gap-1.5">
              <i class="ri-arrow-right-s-line"></i> <?php echo e($policyLink['title']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div>
      <h4 class="text-white font-bold text-sm mb-4">Contact Info</h4>
      <ul class="space-y-2.5 text-sm">
        <li class="flex items-start gap-2"><i class="ri-phone-line text-blue-400 mt-0.5"></i> <a href="tel:407-246-9887" class="hover:text-white transition">407-246-9887</a></li>
        <li class="flex items-start gap-2"><i class="ri-mail-line text-blue-400 mt-0.5"></i> <a href="mailto:support@geeksupportllc.com" class="hover:text-white transition break-all">support@geeksupportllc.com</a></li>
        <li class="flex items-start gap-2"><i class="ri-map-pin-line text-blue-400 mt-0.5"></i> <span>4307 Vineland Road, Suite H-12, Orlando, FL 32811</span></li>
      </ul>
    </div>
  </div>

  <div class="max-w-7xl mx-auto pt-6 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-600">
    <p>&copy; <?php echo date('Y'); ?> Geek Support LLc. All rights reserved.</p>
    <?php if (($footerPaymentMode ?? '') === 'cod'): ?>
      <div class="flex items-center gap-2 text-emerald-500 text-sm font-semibold">
        <i class="ri-truck-line text-xl"></i>
        Cash on Delivery
      </div>
    <?php else: ?>
      <div class="flex items-center gap-3 text-slate-500 text-xl">
        <i class="ri-visa-line" title="Visa"></i>
        <i class="ri-mastercard-line" title="Mastercard"></i>
        <i class="ri-paypal-line" title="PayPal"></i>
        <i class="ri-apple-line" title="Apple Pay"></i>
      </div>
    <?php endif; ?>
  </div>
</footer>

