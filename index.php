<?php
require_once __DIR__ . '/admin/includes/db.php';

$heroPosterBanners = [];
$homeBannerImages = ['IMAGE/1.jpg', 'IMAGE/8.png', 'IMAGE/2.jpg', 'IMAGE/6.png'];
$homeBannerImage = $homeBannerImages[0];
$homeBannerSlides = [];
try {
    $homePdo = db();
    $stmt = $homePdo->query(
        "SELECT *
         FROM banners
         WHERE location = 'Homepage Hero'
           AND status = 'active'
           AND image_url IS NOT NULL
           AND image_url <> ''
           AND (start_date IS NULL OR start_date <= CURDATE())
           AND (end_date IS NULL OR end_date >= CURDATE())
         ORDER BY sort_order, id"
    );
    $heroPosterBanners = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    $dbBannerSlides = array_values(array_filter(array_map(static function (array $banner): string {
        return ltrim(trim((string)($banner['image_url'] ?? '')), '/');
    }, $heroPosterBanners)));
    $homeBannerSlides = $dbBannerSlides;
} catch (Throwable $e) {
    $heroPosterBanners = [];
    $homeBannerSlides = [];
}

$featuredProducts = [];
try {
    $stmt = $homePdo->query(
        "SELECT p.*, b.name as brand_name 
         FROM products p 
         LEFT JOIN brands b ON p.brand_id = b.id 
         WHERE p.status = 'active' AND p.featured = 1 
         ORDER BY p.id DESC LIMIT 4"
    );
    $featuredProducts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Throwable $e) {
    $featuredProducts = [];
}

$topPickProducts = [];
try {
    $stmt = $homePdo->query(
        "SELECT p.*, b.name as brand_name 
         FROM products p 
         LEFT JOIN brands b ON p.brand_id = b.id 
         WHERE p.status = 'active' AND p.top_pick = 1 
         ORDER BY p.id DESC LIMIT 4"
    );
    $topPickProducts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Throwable $e) {
    $topPickProducts = [];
}

function home_asset_url(string $path): string
{
    $path = trim($path);
    if ($path === '' || preg_match('/^(https?:)?\/\//i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }
    return ltrim($path, '/');
}

function home_rating_stars(float $rating): string
{
    $rating = max(0, min(5, $rating));
    $full = (int)floor($rating);
    $half = ($rating - $full) >= 0.5;
    $stars = str_repeat('<i class="ri-star-fill"></i>', $full);
    if ($half) {
        $stars .= '<i class="ri-star-half-fill"></i>';
    }
    return $stars . str_repeat('<i class="ri-star-line"></i>', 5 - $full - ($half ? 1 : 0));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/svg+xml" href="IMAGE/geeksupport_unique_simple_icon.svg">
  <?php 
  $seo = get_page_seo(); 
  ?>
  <title><?php echo e($seo['title']); ?></title>
  <meta name="description" content="<?php echo e($seo['description']); ?>" />
  <meta name="keywords" content="<?php echo e($seo['keywords']); ?>" />
  <?php
  render_google_site_verification();
  render_google_analytics();
  render_google_tag_manager_head();
  ?>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy:  { 50:'#F8FAFC',100:'#F1F5F9',200:'#E5E7EB',300:'#CBD5E1',400:'#6B7280',500:'#2563EB',600:'#2563EB',700:'#1D4ED8',800:'#0F172A',900:'#0F172A' },
            slate2:{ 50:'#F8FAFC', 100:'#F1F5F9', 200:'#E5E7EB', 700:'#6B7280', 800:'#111827', 900:'#0F172A' },
            amber2:{ 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C',800:'#9A3412',900:'#7C2D12' },
          },
          fontFamily: { sans: ['Raleway','system-ui','sans-serif'] },
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
  <style>
    html{scroll-behavior:smooth}
    .card-lift{transition:transform .22s ease,box-shadow .22s ease}
    .card-lift:hover{transform:translateY(-5px);box-shadow:0 16px 40px rgba(30,41,59,.13)}
    .badge-pulse{animation:bpulse 2.2s ease-in-out infinite}
    @keyframes bpulse{0%,100%{opacity:1}50%{opacity:.6}}
    .ticker-wrap{overflow:hidden;white-space:nowrap}
    .ticker-inner{display:inline-block;animation:ticker 35s linear infinite}
    @keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
    .brand-gradient,.hero-bg{background:#2563EB}
    .btn-gradient{background:#F97316;color:#fff}
    .btn-gradient:hover{filter:brightness(1.05);box-shadow:0 10px 24px rgba(249,115,22,.24)}
    .hero-slide{transition:opacity .7s ease}
    .section-label{letter-spacing:.12em;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#1e293b}
    .no-scrollbar{scrollbar-width:none;-ms-overflow-style:none}
    .no-scrollbar::-webkit-scrollbar{display:none}
    input:focus,textarea:focus{outline:none;border-color:#1e293b;box-shadow:0 0 0 3px rgba(37,99,235,.12)}
  </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased">
<?php render_google_tag_manager_body(); ?>

<!-- ======= TOP BAR ======= -->
<div class="brand-gradient text-white hidden md:block">
  <div class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between text-xs">
    <!-- Left: ticker -->
    <div class="ticker-wrap flex-1 max-w-xl overflow-hidden">
      <div class="ticker-inner text-slate-300">
        &nbsp;&nbsp;<i class="ri-truck-line mr-1"></i>Free Shipping on orders over $99&nbsp;&nbsp;Â·&nbsp;&nbsp;
        <i class="ri-tools-line mr-1"></i>Free Expert Setup on Every Printer&nbsp;&nbsp;Â·&nbsp;&nbsp;
        <i class="ri-price-tag-3-line mr-1"></i>Best Price Guarantee&nbsp;&nbsp;Â·&nbsp;&nbsp;
        <i class="ri-shield-check-line mr-1"></i>2-Year Warranty Included&nbsp;&nbsp;Â·&nbsp;&nbsp;
        <i class="ri-truck-line mr-1"></i>Free Shipping on orders over $99&nbsp;&nbsp;Â·&nbsp;&nbsp;
        <i class="ri-tools-line mr-1"></i>Free Expert Setup on Every Printer&nbsp;&nbsp;Â·&nbsp;&nbsp;
        <i class="ri-price-tag-3-line mr-1"></i>Best Price Guarantee&nbsp;&nbsp;Â·&nbsp;&nbsp;
        <i class="ri-shield-check-line mr-1"></i>2-Year Warranty Included&nbsp;&nbsp;&nbsp;
      </div>
    </div>
    <!-- Right: contact -->
    <div class="flex items-center gap-5 shrink-0 ml-6">
      <a href="tel:407-246-9887" class="flex items-center gap-1.5 text-slate-300 hover:text-white transition">
        <i class="ri-phone-fill text-amber2-400"></i>
        <span class="font-medium">407-246-9887</span>
      </a>
      <span class="w-px h-3 bg-slate-600"></span>
      <a href="mailto:support@geeksupportllc.com" class="flex items-center gap-1.5 text-slate-300 hover:text-white transition">
        <i class="ri-mail-fill text-amber2-400"></i>
        <span class="font-medium">support@geeksupportllc.com</span>
      </a>
    </div>
  </div>
</div>

<!-- ======= NAVBAR ======= -->
<header class="bg-white border-b border-slate-100 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center gap-3 lg:gap-8">

    <!-- Logo -->
    <a href="index.php" class="flex items-center gap-2.5 shrink-0">
      <div class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center shrink-0">
        <img src="IMAGE/geeksupport_unique_simple_icon.svg" alt="Geek Support LLc" class="w-6 h-6 sm:w-7 sm:h-7 object-contain">
      </div>
      <span class="flex flex-col justify-center leading-none">
        <span class="text-[13px] sm:text-[15px] font-black text-slate-800 whitespace-nowrap">Geek Support LLc</span>
        <span class="mt-1 text-[7px] sm:text-[9px] font-bold uppercase tracking-wide sm:tracking-widest text-slate-400 whitespace-nowrap">fast secure remote help</span>
      </span>
    </a>

    <!-- Nav Links -->
    <nav class="hidden lg:flex items-center gap-1">
      <a href="products.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Products</a>
      <a href="support.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Support</a>
      <a href="contact.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Contact</a>
    </nav>

    <!-- Search -->
    <form action="products.php" method="GET" class="hidden md:flex flex-1 max-w-sm ml-auto relative header-search-form">
      <div class="flex w-full h-9 rounded-lg border border-slate-200 bg-slate-50 hover:border-slate-300 focus-within:border-navy-400 focus-within:bg-white overflow-hidden transition">
        <input name="q" type="text" placeholder="Search printers, brand, model..." class="flex-1 px-3 text-sm bg-transparent outline-none text-slate-700 placeholder-slate-400 header-search-input" autocomplete="off"/>
        <button type="submit" class="px-3 text-slate-400 hover:text-navy-600 transition" aria-label="Search products">
          <i class="ri-search-2-line text-base"></i>
        </button>
      </div>
    </form>

    <!-- Divider -->
    <div class="hidden sm:block w-px h-6 bg-slate-200 shrink-0"></div>

    <!-- Actions -->
    <div class="flex items-center gap-1 shrink-0 ml-auto">
      <button onclick="toggleWishlistDrawer()" class="relative w-9 h-9 flex items-center justify-center text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
        <i class="ri-heart-3-line text-[18px]"></i>
        <span id="wl-count" class="absolute top-0.5 right-0.5 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-[14px] h-[14px] px-0.5 items-center justify-center hidden leading-none"></span>
      </button>
      <button onclick="toggleCart()" class="relative w-9 h-9 flex items-center justify-center text-slate-500 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">
        <i class="ri-shopping-bag-line text-[18px]"></i>
        <span id="cart-count" class="absolute top-0.5 right-0.5 bg-navy-600 text-white text-[9px] font-bold rounded-full min-w-[14px] h-[14px] px-0.5 flex items-center justify-center leading-none">0</span>
      </button>
      <a href="tel:407-246-9887" class="lg:hidden w-9 h-9 flex items-center justify-center text-slate-600 hover:text-navy-600 transition" aria-label="Call support">
        <i class="ri-phone-line text-xl"></i>
      </a>
      <button type="button" onclick="toggleMobileNav(true)" class="lg:hidden w-9 h-9 flex items-center justify-center text-slate-700 hover:text-navy-600 transition" aria-label="Open menu">
        <i class="ri-menu-3-line text-2xl"></i>
      </button>
    </div>

  </div>

  <!-- Sub-nav categories -->
  <div class="hidden lg:block border-t border-slate-100 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 py-2 flex gap-5 text-xs font-semibold text-slate-500 overflow-x-auto no-scrollbar" data-taxonomy-subnav></div>
  </div>
</header>
<?php include __DIR__ . '/includes/mobile_nav.php'; ?>

<!-- ======= HOME BANNER ======= -->
<?php if ($homeBannerSlides): ?>
<section id="hero-slider" class="relative w-full overflow-hidden bg-slate-900" style="height:clamp(240px,42vw,640px)">
  <?php foreach ($homeBannerSlides as $slideIndex => $imageUrl): ?>
  <div class="hero-slide absolute inset-0 transition-opacity duration-700 <?php echo $slideIndex === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none'; ?>" data-index="<?php echo (int)$slideIndex; ?>">
    <img src="<?php echo e(home_asset_url((string)$imageUrl)); ?>" alt="Homepage banner <?php echo (int)$slideIndex + 1; ?>" class="relative z-10 w-full h-full object-cover object-center">
  </div>
  <?php endforeach; ?>

  <?php if (count($homeBannerSlides) > 1): ?>
  <button type="button" onclick="sliderMove(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-black/30 hover:bg-black/50 backdrop-blur-sm border border-white/25 text-white rounded-full flex items-center justify-center transition" aria-label="Previous banner">
    <i class="ri-arrow-left-s-line text-xl"></i>
  </button>
  <button type="button" onclick="sliderMove(1)" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-black/30 hover:bg-black/50 backdrop-blur-sm border border-white/25 text-white rounded-full flex items-center justify-center transition" aria-label="Next banner">
    <i class="ri-arrow-right-s-line text-xl"></i>
  </button>
  <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
    <?php foreach ($homeBannerSlides as $dotIndex => $_imageUrl): ?>
    <button type="button" onclick="sliderGoto(<?php echo (int)$dotIndex; ?>)" class="slider-dot <?php echo $dotIndex === 0 ? 'w-7 bg-white' : 'w-2 bg-white/45'; ?> h-2 rounded-full transition-all" data-dot="<?php echo (int)$dotIndex; ?>" aria-label="Go to banner <?php echo (int)$dotIndex + 1; ?>"></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<script>
  let sliderCurrent = 0;
  let sliderTimer;

  function sliderGoto(n) {
    const slider = document.getElementById('hero-slider');
    const slides = slider ? slider.querySelectorAll('.hero-slide') : [];
    const dots = slider ? slider.querySelectorAll('.slider-dot') : [];
    if (!slides.length) return;
    slides[sliderCurrent].classList.add('opacity-0', 'pointer-events-none');
    slides[sliderCurrent].classList.remove('opacity-100');
    sliderCurrent = (n + slides.length) % slides.length;
    slides[sliderCurrent].classList.remove('opacity-0', 'pointer-events-none');
    slides[sliderCurrent].classList.add('opacity-100');
    dots.forEach((dot, index) => {
      dot.className = index === sliderCurrent
        ? 'slider-dot w-7 h-2 rounded-full bg-white transition-all'
        : 'slider-dot w-2 h-2 rounded-full bg-white/45 transition-all';
    });
    sliderReset();
  }

  function sliderMove(dir) { sliderGoto(sliderCurrent + dir); }

  function sliderReset() {
    clearInterval(sliderTimer);
    const slider = document.getElementById('hero-slider');
    const slides = slider ? slider.querySelectorAll('.hero-slide') : [];
    if (slides.length > 1) {
      sliderTimer = setInterval(() => sliderMove(1), 5000);
    }
  }

  sliderReset();
</script>
<?php endif; ?>


<!-- ======= TRUST STRIP ======= -->
<section class="bg-white border-b border-slate-200">
  <div class="max-w-7xl mx-auto px-5">
    <div class="grid grid-cols-2 lg:grid-cols-4 divide-x-0 lg:divide-x divide-slate-200">
      <div class="flex items-center justify-center gap-3 py-4 px-3">
        <i class="ri-truck-line text-navy-600 text-2xl shrink-0"></i>
        <div class="leading-tight">
          <p class="text-xs font-black text-slate-800">Free Shipping</p>
          <p class="text-[11px] text-slate-500 mt-0.5">On orders over $99</p>
        </div>
      </div>
      <div class="flex items-center justify-center gap-3 py-4 px-3">
        <i class="ri-refresh-line text-navy-600 text-2xl shrink-0"></i>
        <div class="leading-tight">
          <p class="text-xs font-black text-slate-800">7-Day Returns</p>
          <p class="text-[11px] text-slate-500 mt-0.5">Hassle-free returns</p>
        </div>
      </div>
      <div class="flex items-center justify-center gap-3 py-4 px-3">
        <i class="ri-shield-check-line text-navy-600 text-2xl shrink-0"></i>
        <div class="leading-tight">
          <p class="text-xs font-black text-slate-800">Secure Payments</p>
          <p class="text-[11px] text-slate-500 mt-0.5">100% protected</p>
        </div>
      </div>
      <div class="flex items-center justify-center gap-3 py-4 px-3">
        <i class="ri-customer-service-2-line text-navy-600 text-2xl shrink-0"></i>
        <div class="leading-tight">
          <p class="text-xs font-black text-slate-800">Expert Support</p>
          <p class="text-[11px] text-slate-500 mt-0.5">Here to help</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======= PRINTER SHOWCASE ======= -->
<section class="py-14 px-5 bg-white border-b border-slate-100">
  <div class="max-w-7xl mx-auto">
    <div class="flex items-end justify-between mb-8">
      <div>
        <p class="section-label mb-1">Featured Products</p>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800">Our Top Picks</h2>
        <p class="text-slate-500 text-sm mt-1">Authorized printer deals with setup support included.</p>
      </div>
      <a href="products.php" class="text-navy-600 text-sm font-semibold hover:underline flex items-center gap-1">View All <i class="ri-arrow-right-line"></i></a>
    </div>

    <?php if (count($featuredProducts) > 0): ?>
    <?php
      $featuredDisplay = array_slice($featuredProducts, 0, 3);
      $featuredPrimary = array_shift($featuredDisplay);
    ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:min-h-[570px]">
      <div onclick="window.location.href='product-detail.php?id=<?php echo (int)$featuredPrimary['id']; ?>'" class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden cursor-pointer h-full flex flex-col" data-featured-hp-printer>
        <div class="bg-navy-50 min-h-[260px] lg:flex-1 flex items-center justify-center p-8 relative">
          <?php if (!empty($featuredPrimary['badge'])): ?>
            <span class="absolute top-4 left-4 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md"><?php echo e($featuredPrimary['badge']); ?></span>
          <?php endif; ?>
          <?php if (!empty($featuredPrimary['image_url'])): ?>
            <img src="<?php echo e(home_asset_url($featuredPrimary['image_url'])); ?>" alt="<?php echo e($featuredPrimary['name']); ?>" class="w-full h-full max-h-72 object-contain">
          <?php else: ?>
            <i class="<?php echo e($featuredPrimary['image_icon'] ?: 'ri-printer-fill'); ?> text-navy-500" style="font-size:110px;line-height:1"></i>
          <?php endif; ?>
        </div>
        <div class="p-6 bg-white">
          <p class="text-[10px] text-navy-600 font-bold uppercase tracking-widest"><?php echo e($featuredPrimary['brand_name'] ?? ''); ?></p>
          <h3 class="font-black text-slate-800 mt-1 text-xl line-clamp-1" title="<?php echo e($featuredPrimary['name']); ?>"><?php echo e($featuredPrimary['name']); ?></h3>
          <p class="text-sm text-slate-500 mt-2 line-clamp-2"><?php echo e($featuredPrimary['short_description'] ?: $featuredPrimary['description']); ?></p>
          <div class="flex items-center gap-1 mt-3 text-amber2-400 text-xs">
            <?php echo home_rating_stars((float)$featuredPrimary['rating']); ?>
            <span class="text-slate-600 ml-1 font-bold"><?php echo number_format((float)$featuredPrimary['rating'], 1); ?></span>
          </div>
          <div class="flex items-baseline gap-2 mt-3">
            <span class="text-2xl font-black text-slate-800">$<?php echo number_format($featuredPrimary['price'], 2); ?></span>
            <?php if ($featuredPrimary['old_price'] > 0): ?>
              <span class="text-sm text-slate-400 line-through">$<?php echo number_format($featuredPrimary['old_price'], 2); ?></span>
            <?php endif; ?>
          </div>
          <div class="mt-5 grid grid-cols-[48px_1fr] gap-2 items-stretch">
            <button onclick="event.stopPropagation(); addToCart('<?php echo e(addslashes($featuredPrimary['name'])); ?>', <?php echo $featuredPrimary['price']; ?>, '<?php echo e(addslashes(home_asset_url($featuredPrimary['image_url'] ?? ''))); ?>')" class="h-11 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl" title="Add to Cart">
              <i class="ri-shopping-cart-2-line text-[22px] leading-none"></i>
            </button>
            <button onclick="event.stopPropagation(); buyNow('<?php echo e(addslashes($featuredPrimary['name'])); ?>', <?php echo $featuredPrimary['price']; ?>, '<?php echo e(addslashes(home_asset_url($featuredPrimary['image_url'] ?? ''))); ?>')" class="btn-gradient h-11 w-full rounded-xl transition font-bold text-xs flex items-center justify-center gap-1.5">Buy Now</button>
          </div>
        </div>
      </div>

      <?php if (count($featuredDisplay) > 0): ?>
      <div class="grid grid-cols-1 lg:grid-rows-2 gap-5 h-full">
        <?php foreach ($featuredDisplay as $product): ?>
          <div onclick="window.location.href='product-detail.php?id=<?php echo (int)$product['id']; ?>'" class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden cursor-pointer h-full grid grid-cols-1 sm:grid-cols-[42%_58%]">
            <div class="bg-navy-50 min-h-[190px] h-full flex items-center justify-center p-5 relative">
              <?php if (!empty($product['badge'])): ?>
                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md"><?php echo e($product['badge']); ?></span>
              <?php endif; ?>
              <?php if (!empty($product['image_url'])): ?>
                <img src="<?php echo e(home_asset_url($product['image_url'])); ?>" alt="<?php echo e($product['name']); ?>" class="w-full h-full max-h-44 object-contain">
              <?php else: ?>
                <i class="<?php echo e($product['image_icon'] ?: 'ri-printer-fill'); ?> text-navy-500" style="font-size:78px;line-height:1"></i>
              <?php endif; ?>
            </div>
            <div class="p-5 flex flex-col justify-between min-w-0">
              <div>
                <p class="text-[10px] text-navy-600 font-bold uppercase tracking-widest"><?php echo e($product['brand_name'] ?? ''); ?></p>
                <h3 class="font-black text-slate-800 mt-1 text-sm line-clamp-2" title="<?php echo e($product['name']); ?>"><?php echo e($product['name']); ?></h3>
                <div class="flex items-center gap-1 mt-2 text-amber2-400 text-[11px]">
                  <?php echo home_rating_stars((float)$product['rating']); ?>
                  <span class="text-slate-600 ml-1 font-bold"><?php echo number_format((float)$product['rating'], 1); ?></span>
                </div>
                <div class="flex items-baseline gap-2 mt-3">
                  <span class="text-lg font-black text-slate-800">$<?php echo number_format($product['price'], 2); ?></span>
                  <?php if ($product['old_price'] > 0): ?>
                    <span class="text-xs text-slate-400 line-through">$<?php echo number_format($product['old_price'], 2); ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="mt-4 grid grid-cols-[44px_1fr] gap-2 items-stretch">
                <button onclick="event.stopPropagation(); addToCart('<?php echo e(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>, '<?php echo e(addslashes(home_asset_url($product['image_url'] ?? ''))); ?>')" class="h-10 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl" title="Add to Cart">
                  <i class="ri-shopping-cart-2-line text-xl leading-none"></i>
                </button>
                <button onclick="event.stopPropagation(); buyNow('<?php echo e(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>, '<?php echo e(addslashes(home_asset_url($product['image_url'] ?? ''))); ?>')" class="btn-gradient h-10 w-full rounded-xl transition font-bold text-xs flex items-center justify-center">Buy Now</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-10 bg-slate-50 border border-slate-200 rounded-2xl">
      <p class="text-slate-500 font-semibold">Products coming soon. Stay tuned!</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ======= CATEGORIES ======= -->
<section class="py-14 px-5">
  <div class="max-w-7xl mx-auto">
    <p class="section-label text-center mb-2">Browse</p>
    <h2 class="text-2xl md:text-3xl font-black text-center text-slate-800 mb-2">Shop by Category</h2>
    <p class="text-center text-slate-500 text-sm mb-10">Find the right printer for every need</p>
    <div class="relative">
      <button type="button" onclick="document.querySelector('[data-home-category-slider]')?.scrollBy({left:-260,behavior:'smooth'})" class="flex absolute -left-3 md:-left-5 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm text-slate-500 hover:text-navy-600 items-center justify-center">
        <i class="ri-arrow-left-s-line text-xl"></i>
      </button>
      <div data-home-category-slider class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar px-1">
        <a href="products.php?cat=inkjet" class="card-lift relative overflow-hidden rounded-2xl border border-slate-200 text-white group shrink-0 w-[180px] sm:w-[190px] lg:w-[200px] h-40 bg-gradient-to-br from-navy-500 to-navy-800">
          <img src="https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=500&q=80" alt="Inkjet Printers" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition">
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/80 to-transparent p-4 text-left">
            <h3 class="font-bold text-white text-sm">Inkjet Printers</h3>
            <span class="text-white/80 text-xs font-semibold mt-1 inline-block">View products <i class="ri-arrow-right-line"></i></span>
          </div>
        </a>
      </div>
      <button type="button" onclick="document.querySelector('[data-home-category-slider]')?.scrollBy({left:260,behavior:'smooth'})" class="flex absolute -right-3 md:-right-5 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm text-slate-500 hover:text-navy-600 items-center justify-center">
        <i class="ri-arrow-right-s-line text-xl"></i>
      </button>
    </div>
  </div>
</section>

<!-- ======= FEATURED PRODUCTS ======= -->
<section id="products" class="py-14 px-5 bg-white border-y border-slate-100">
  <div class="max-w-7xl mx-auto">
    <div class="flex items-end justify-between mb-8">
      <div>
        <p class="section-label mb-1">Top Picks</p>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800">Featured Printers</h2>
        <p class="text-slate-500 text-sm mt-1">Best sellers & top-rated models</p>
      </div>
      <a href="products.php" class="text-navy-600 text-sm font-semibold hover:underline flex items-center gap-1">View All <i class="ri-arrow-right-line"></i></a>
    </div>

    <?php if (count($topPickProducts) > 0): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <?php foreach ($topPickProducts as $product): ?>
        <div onclick="window.location.href='product-detail.php?id=<?php echo (int)$product['id']; ?>'" class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden cursor-pointer" data-home-product-id="<?php echo (int)$product['id']; ?>">
          <div class="relative bg-navy-50 p-7 flex justify-center items-center h-44">
            <?php if (!empty($product['image_url'])): ?>
              <img src="<?php echo e(home_asset_url($product['image_url'])); ?>" alt="<?php echo e($product['name']); ?>" class="max-h-32 object-contain">
            <?php else: ?>
              <i class="<?php echo e($product['image_icon'] ?: 'ri-printer-fill'); ?> text-navy-400" style="font-size:90px;line-height:1"></i>
            <?php endif; ?>
            <?php if (!empty($product['badge'])): ?>
              <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md badge-pulse"><?php echo e($product['badge']); ?></span>
            <?php endif; ?>
            <button onclick="event.stopPropagation(); wlToggle(<?php echo (int)$product['id']; ?>)" data-wl-id="<?php echo (int)$product['id']; ?>" class="absolute top-3 right-3 text-slate-300 hover:text-red-400 transition text-lg"><i class="ri-heart-3-line"></i></button>
          </div>
          <div class="p-4 flex flex-col justify-between h-[210px]">
            <div>
              <p class="text-[10px] text-navy-600 font-bold uppercase tracking-widest"><?php echo e($product['brand_name'] ?? ''); ?></p>
              <h3 class="font-bold text-slate-800 mt-0.5 text-sm line-clamp-1" title="<?php echo e($product['name']); ?>"><?php echo e($product['name']); ?></h3>
              <p class="text-xs text-slate-400 mt-0.5 line-clamp-1"><?php echo e($product['short_description'] ?: $product['category'] ?? ''); ?></p>
              <div class="flex items-center gap-1 mt-2 text-amber2-400 text-xs">
                <?php echo home_rating_stars((float)$product['rating']); ?>
                <span class="text-slate-600 ml-1 font-bold"><?php echo number_format((float)$product['rating'], 1); ?></span>
              </div>
              <div class="flex items-baseline gap-2 mt-2">
                <span class="text-lg font-black text-slate-800">$<?php echo number_format($product['price'], 2); ?></span>
                <?php if ($product['old_price'] > 0): ?>
                  <span class="text-xs text-slate-400 line-through">$<?php echo number_format($product['old_price'], 2); ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div class="mt-3 grid grid-cols-[48px_1fr] gap-2 items-stretch">
              <button onclick="event.stopPropagation(); addToCart('<?php echo e(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>, '<?php echo e(addslashes(home_asset_url($product['image_url'] ?? ''))); ?>')" class="h-10 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl" title="Add to Cart">
                <i class="ri-shopping-cart-2-line text-[26px] leading-none"></i>
              </button>
              <button onclick="event.stopPropagation(); buyNow('<?php echo e(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>, '<?php echo e(addslashes(home_asset_url($product['image_url'] ?? ''))); ?>')" class="btn-gradient h-10 w-full rounded-xl transition text-xs font-bold flex items-center justify-center gap-1.5">
                <i class="ri-flashlight-line"></i> Buy Now
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-10 bg-slate-50 border border-slate-200 rounded-2xl">
      <p class="text-slate-500 font-semibold">Top picks coming soon. Stay tuned!</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ======= FLASH DEAL BANNER ======= -->
<section id="deals" class="py-14 px-5 brand-gradient text-white" data-home-product-id="5">
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12">
    <div class="flex-1">
      <span class="inline-flex items-center gap-1.5 bg-red-500/20 border border-red-400/30 text-red-300 text-xs font-bold px-3 py-1.5 rounded-full mb-5">
        <i class="ri-flashlight-fill"></i> Flash Deal â€” Today Only
      </span>
      <h2 class="text-3xl md:text-4xl font-black leading-tight">HP LaserJet Pro M404n</h2>
      <p class="text-slate-300 mt-3 text-sm leading-relaxed max-w-md">Professional monochrome laser printing at 40 ppm. Network-ready, duplex printing, built for serious business workloads.</p>
      <div class="flex items-baseline gap-3 mt-6">
        <span class="text-4xl font-black text-amber2-400">$249</span>
        <span class="text-lg text-slate-500 line-through">$399</span>
        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-lg">SAVE $150</span>
      </div>
      <!-- Countdown -->
      <div class="mt-6">
        <p class="text-xs text-slate-400 mb-2 flex items-center gap-1"><i class="ri-time-line"></i> Deal ends in:</p>
        <div class="flex gap-3">
          <div class="bg-white/10 rounded-xl px-4 py-2 text-center min-w-[56px]">
            <div class="text-xl font-black" id="hours">08</div>
            <div class="text-[10px] text-slate-400 uppercase tracking-wide">Hrs</div>
          </div>
          <div class="bg-white/10 rounded-xl px-4 py-2 text-center min-w-[56px]">
            <div class="text-xl font-black" id="mins">45</div>
            <div class="text-[10px] text-slate-400 uppercase tracking-wide">Min</div>
          </div>
          <div class="bg-white/10 rounded-xl px-4 py-2 text-center min-w-[56px]">
            <div class="text-xl font-black" id="secs">30</div>
            <div class="text-[10px] text-slate-400 uppercase tracking-wide">Sec</div>
          </div>
        </div>
      </div>
      <button onclick="addToCart('HP LaserJet Pro M404n',249)" class="mt-7 inline-flex items-center gap-2 btn-gradient text-white font-bold px-7 py-3 rounded-xl transition shadow-lg text-sm">
        <i class="ri-flashlight-line"></i> Grab This Deal
      </button>
    </div>
    <div class="flex-1 flex justify-center">
      <div class="bg-white/5 border border-white/10 rounded-3xl p-12">
        <i class="ri-printer-fill text-white/80" style="font-size:140px;line-height:1"></i>
      </div>
    </div>
  </div>
</section>



<!-- ======= SUPPORT BANNER ======= -->
<section id="scroll-expand-section" class="px-5 py-8 bg-slate-50 transition-all duration-300">
  <div id="scroll-expand-inner" class="max-w-7xl mx-auto overflow-hidden rounded-2xl bg-navy-700 text-white transition-all duration-300">
    <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr]">
      <div class="p-7 md:p-9 flex flex-col justify-center">
        <p class="text-[11px] font-black uppercase tracking-widest text-amber2-400 mb-3">Printer buying made easy</p>
        <h2 class="text-2xl md:text-4xl font-black leading-tight">Find the right printer, then get expert setup help.</h2>
        <p class="text-blue-100 text-sm md:text-base mt-4 max-w-2xl leading-relaxed">Shop trusted printer models, ink, and toner with fast guidance for wireless setup, drivers, and after-sales support.</p>
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="products.php" class="inline-flex items-center gap-2 bg-amber2-500 hover:bg-amber2-600 text-white font-bold px-5 py-3 rounded-xl transition text-sm">
            <i class="ri-printer-line"></i> Shop Printers
          </a>
          <a href="support.php" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 border border-white/15 text-white font-semibold px-5 py-3 rounded-xl transition text-sm">
            <i class="ri-customer-service-2-line"></i> Get Support
          </a>
        </div>
      </div>
      <div class="bg-navy-600 min-h-[220px] overflow-hidden">
        <img src="IMAGE/main.png" alt="Printer buying made easy" class="w-full h-full min-h-[220px] object-cover">
      </div>
    </div>
  </div>
</section>

<!-- ======= WHAT WE DO ======= -->
<section class="py-16 px-5 bg-white border-y border-slate-100">
  <div class="max-w-7xl mx-auto">
    <div class="max-w-3xl mx-auto text-center mb-11">
      <p class="section-label mb-2">What We Do</p>
      <h2 class="text-2xl md:text-4xl font-black text-slate-800 leading-tight">We make printer buying and setup simple.</h2>
      <p class="text-slate-500 text-sm md:text-base mt-4 leading-relaxed">
        At Geek Support LLc, we help customers find the right printer, ink, and toner for home, office, and business use. We provide trusted printer products, free expert setup support, warranty guidance, and ongoing technical assistance so you can start printing without stress.
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="bg-navy-600 rounded-xl w-11 h-11 flex items-center justify-center mb-4"><i class="ri-printer-line text-white text-xl"></i></div>
        <h3 class="font-bold text-slate-800">Printer Sales</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">Top-brand inkjet, laser, all-in-one, photo, and business printers.</p>
      </div>
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="bg-amber2-500 rounded-xl w-11 h-11 flex items-center justify-center mb-4"><i class="ri-ink-bottle-line text-white text-xl"></i></div>
        <h3 class="font-bold text-slate-800">Ink & Toner Supplies</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">Original and compatible ink cartridges and toner for major printer brands.</p>
      </div>
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="bg-emerald-600 rounded-xl w-11 h-11 flex items-center justify-center mb-4"><i class="ri-customer-service-2-line text-white text-xl"></i></div>
        <h3 class="font-bold text-slate-800">Free Printer Setup</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">Remote expert help for wireless setup, driver installation, and first-time configuration.</p>
      </div>
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="bg-slate-700 rounded-xl w-11 h-11 flex items-center justify-center mb-4"><i class="ri-tools-line text-white text-xl"></i></div>
        <h3 class="font-bold text-slate-800">Printer Troubleshooting</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">Support for printer offline errors, connection issues, driver problems, and print quality issues.</p>
      </div>
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="bg-blue-600 rounded-xl w-11 h-11 flex items-center justify-center mb-4"><i class="ri-building-2-line text-white text-xl"></i></div>
        <h3 class="font-bold text-slate-800">Business Printing Solutions</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">Reliable printers and support for offices, teams, and high-volume printing needs.</p>
      </div>
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="bg-indigo-600 rounded-xl w-11 h-11 flex items-center justify-center mb-4"><i class="ri-shield-check-line text-white text-xl"></i></div>
        <h3 class="font-bold text-slate-800">Warranty & After-Sales Support</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">2-year warranty support, returns help, and customer assistance after purchase.</p>
      </div>
    </div>

    <div class="mt-8 bg-slate-50 border border-slate-200 rounded-2xl p-6 flex flex-col lg:flex-row lg:items-center gap-5 justify-between">
      <p class="text-slate-600 text-sm leading-relaxed max-w-3xl">We sell printers, ink, and toner, and provide expert setup support to make printing easy for homes and businesses.</p>
      <div class="flex flex-wrap gap-3 shrink-0">
        <a href="#products" class="inline-flex items-center gap-2 btn-gradient text-white font-bold px-5 py-2.5 rounded-xl transition text-sm"><i class="ri-printer-line"></i> Shop Printers</a>
        <a href="support.php" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:border-navy-400 text-slate-700 hover:text-navy-600 font-semibold px-5 py-2.5 rounded-xl transition text-sm"><i class="ri-headphone-line"></i> Get Support</a>
      </div>
    </div>
  </div>
</section>

<!-- ======= SUPPORT INCLUDED ======= -->
<section class="py-14 px-5 bg-white border-y border-slate-100">
  <div class="max-w-7xl mx-auto">
    <p class="section-label text-center mb-2">Why Choose Us</p>
    <h2 class="text-2xl md:text-3xl font-black text-center text-slate-800 mb-2">More Than Just a Store</h2>
    <p class="text-center text-slate-500 text-sm mb-12">We support every printer we sell</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-7 text-center">
        <div class="bg-navy-600 rounded-2xl w-14 h-14 flex items-center justify-center mx-auto mb-4">
          <i class="ri-headphone-line text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-slate-800">Free Expert Setup</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">Every printer includes free remote setup by our certified tech team. We get you printing in under 20 minutes.</p>
      </div>

      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-7 text-center">
        <div class="bg-amber2-500 rounded-2xl w-14 h-14 flex items-center justify-center mx-auto mb-4">
          <i class="ri-shield-check-line text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-slate-800">2-Year Warranty</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">All printers come with an extended 2-year warranty. We fix it or replace it â€” no questions asked.</p>
      </div>

      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-7 text-center">
        <div class="bg-emerald-600 rounded-2xl w-14 h-14 flex items-center justify-center mx-auto mb-4">
          <i class="ri-price-tag-3-line text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-slate-800">Best Price Guarantee</h3>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">Found it cheaper? We'll match the price and give you an extra 5% off. Guaranteed best deal, every time.</p>
      </div>

    </div>

    <!-- Feature pills -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
        <i class="ri-truck-line text-navy-600 text-xl"></i>
        <span class="text-sm font-semibold text-slate-700">Free Shipping $99+</span>
      </div>
      <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
        <i class="ri-refresh-line text-navy-600 text-xl"></i>
        <span class="text-sm font-semibold text-slate-700">7-Day Returns</span>
      </div>
      <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
        <i class="ri-lock-2-line text-navy-600 text-xl"></i>
        <span class="text-sm font-semibold text-slate-700">Secure Checkout</span>
      </div>
      <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
        <i class="ri-phone-line text-navy-600 text-xl"></i>
        <span class="text-sm font-semibold text-slate-700">24/7 Phone Support</span>
      </div>
    </div>
  </div>
</section>

<!-- ======= BRAND SHOWCASE STRIP ======= -->
<section class="py-8 bg-[#071426] text-white">
  <div class="max-w-7xl mx-auto px-5">
    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 p-7 md:p-9">
      <div class="flex flex-col justify-center">
        <p class="text-xs font-black text-white/70 mb-3">Top Brands &bull; Best Prices</p>
        <h2 class="text-3xl md:text-4xl font-black leading-tight">World's Best Printer Brands</h2>
        <p class="text-blue-100 text-sm leading-relaxed mt-4">HP, Canon, Brother, Epson - all top models in stock with free expert setup on every order.</p>
        <a href="products.php" class="mt-6 inline-flex w-fit items-center gap-2 bg-navy-600 hover:bg-navy-700 text-white font-bold px-6 py-3 rounded-lg transition text-sm">Shop All Brands</a>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <a href="products.php?brand=HP" class="bg-white rounded-xl p-4 min-h-[150px] flex flex-col items-center justify-center text-center shadow-sm">
          <div class="text-5xl font-black text-sky-600 leading-none">hp</div>
          <p class="mt-5 text-sm font-bold text-slate-700">HP Printers</p>
          <span class="mt-4 text-xs font-bold text-navy-600">View Models</span>
        </a>
        <a href="products.php?brand=Canon" class="bg-white rounded-xl p-4 min-h-[150px] flex flex-col items-center justify-center text-center shadow-sm">
          <div class="text-3xl font-black text-red-600 leading-none">Canon</div>
          <p class="mt-6 text-sm font-bold text-slate-700">Canon Printers</p>
          <span class="mt-4 text-xs font-bold text-navy-600">View Models</span>
        </a>
        <a href="products.php?brand=Brother" class="bg-white rounded-xl p-4 min-h-[150px] flex flex-col items-center justify-center text-center shadow-sm">
          <div class="text-2xl font-black text-blue-700 leading-none">brother</div>
          <p class="mt-7 text-sm font-bold text-slate-700">Brother Printers</p>
          <span class="mt-4 text-xs font-bold text-navy-600">View Models</span>
        </a>
        <a href="products.php?brand=Epson" class="bg-white rounded-xl p-4 min-h-[150px] flex flex-col items-center justify-center text-center shadow-sm">
          <div class="text-2xl font-black text-blue-700 leading-none">EPSON</div>
          <p class="mt-7 text-sm font-bold text-slate-700">Epson Printers</p>
          <span class="mt-4 text-xs font-bold text-navy-600">View Models</span>
        </a>
        <a href="products.php?brand=Xerox" class="bg-white rounded-xl p-4 min-h-[150px] flex flex-col items-center justify-center text-center shadow-sm">
          <div class="text-2xl font-black text-red-600 leading-none">xerox</div>
          <p class="mt-7 text-sm font-bold text-slate-700">Xerox Printers</p>
          <span class="mt-4 text-xs font-bold text-navy-600">View Models</span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ======= SETUP SUPPORT ======= -->
<section class="py-16 px-5 bg-slate-50 border-b border-slate-100">
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
      <div class="brand-gradient rounded-3xl p-8 md:p-10 text-white relative overflow-hidden">
        <div class="absolute -right-10 -bottom-16 text-white/10">
          <i class="ri-customer-service-2-fill" style="font-size:220px;line-height:1"></i>
        </div>
        <div class="relative z-10">
          <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full mb-5">
            <i class="ri-tools-fill text-blue-200"></i> Support Included
          </span>
          <h2 class="text-3xl md:text-4xl font-black leading-tight">Printer Setup Support Included</h2>
          <p class="text-blue-100 text-sm md:text-base mt-4 leading-relaxed max-w-xl">
            Buy your printer with confidence. Our experts help you set it up, connect it, install the right drivers, and solve common printer issues after purchase.
          </p>
          <p class="mt-7 text-xs font-bold uppercase tracking-widest text-blue-200">We help with:</p>
          <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-blue-200"></i> Wireless printer setup</div>
            <div class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-blue-200"></i> Driver installation</div>
            <div class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-blue-200"></i> Printer offline issues</div>
            <div class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-blue-200"></i> Ink & toner guidance</div>
            <div class="flex items-center gap-2 sm:col-span-2"><i class="ri-checkbox-circle-fill text-blue-200"></i> Business printer configuration</div>
          </div>
          <div class="mt-8 flex flex-wrap gap-3">
            <a href="support.php" class="inline-flex items-center gap-2 bg-white text-navy-700 hover:bg-blue-50 font-bold px-6 py-3 rounded-xl transition text-sm">
              <i class="ri-headphone-line"></i> Get Setup Help
            </a>
            <a href="tel:407-246-9887" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
              <i class="ri-phone-fill"></i> Call Support
            </a>
          </div>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-3xl p-8 md:p-10">
        <p class="section-label mb-2">Expert Guidance</p>
        <h3 class="text-2xl md:text-3xl font-black text-slate-800 leading-tight">Need help choosing a printer?</h3>
        <p class="text-slate-500 text-sm md:text-base mt-4 leading-relaxed">
          Our experts can help you find the right model for your home or business.
        </p>
        <div class="mt-7 space-y-4">
          <div class="flex items-start gap-3">
            <div class="bg-navy-50 rounded-xl w-10 h-10 flex items-center justify-center shrink-0"><i class="ri-home-4-line text-navy-600 text-xl"></i></div>
            <div>
              <h4 class="font-bold text-slate-800 text-sm">Home & Office Matching</h4>
              <p class="text-slate-500 text-sm mt-1">We compare print volume, wireless needs, ink cost, and space before recommending a model.</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="bg-blue-50 rounded-xl w-10 h-10 flex items-center justify-center shrink-0"><i class="ri-building-4-line text-blue-600 text-xl"></i></div>
            <div>
              <h4 class="font-bold text-slate-800 text-sm">Business Printer Configuration</h4>
              <p class="text-slate-500 text-sm mt-1">Get help choosing reliable printers for teams, shared networks, and high-volume printing.</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="bg-emerald-50 rounded-xl w-10 h-10 flex items-center justify-center shrink-0"><i class="ri-shield-check-line text-emerald-600 text-xl"></i></div>
            <div>
              <h4 class="font-bold text-slate-800 text-sm">Warranty & After-Sales Help</h4>
              <p class="text-slate-500 text-sm mt-1">We guide you through warranty questions, returns, and ongoing support after purchase.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======= BRANDS ======= -->
<section class="py-10 px-5 bg-slate-50 border-b border-slate-100">
  <div class="max-w-7xl mx-auto">
    <p class="text-center text-xs text-slate-400 font-semibold uppercase tracking-widest mb-3">Authorized Dealer</p>
    <div class="flex justify-center mb-7">
      <span class="inline-flex items-center gap-2 bg-white border border-slate-200 rounded-full px-5 py-2 text-sm font-black text-navy-700 shadow-sm">
        <i class="ri-award-fill text-amber2-500"></i> Authorized Dealer HP Brand
      </span>
    </div>
    <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12">
      <span class="text-2xl font-black text-slate-400 hover:text-navy-600 transition cursor-pointer">HP</span>
      <span class="text-2xl font-black text-slate-400 hover:text-red-600 transition cursor-pointer">Canon</span>
      <span class="text-2xl font-black text-slate-400 hover:text-slate-700 transition cursor-pointer">Brother</span>
      <span class="text-2xl font-black text-slate-400 hover:text-blue-600 transition cursor-pointer">Epson</span>
      <span class="text-2xl font-black text-slate-400 hover:text-slate-800 transition cursor-pointer">Xerox</span>
      <span class="text-2xl font-black text-slate-400 hover:text-orange-500 transition cursor-pointer">Lexmark</span>
      <span class="text-2xl font-black text-slate-400 hover:text-green-600 transition cursor-pointer">Ricoh</span>
    </div>
  </div>
</section>

<!-- ======= BUSINESS SOLUTIONS STRIP ======= -->
<section class="px-5 py-8 bg-white">
  <div class="max-w-7xl mx-auto overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 via-white to-slate-100 shadow-sm">
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.1fr_0.8fr] gap-6 items-center">
      <div class="p-7 md:p-8">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900">Business Printing Solutions</h2>
        <p class="text-slate-600 text-sm leading-relaxed mt-3 max-w-md">Power your business with reliable printers, expert setup, and ongoing support.</p>
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="products.php?cat=business" class="inline-flex items-center justify-center bg-navy-600 hover:bg-navy-700 text-white font-bold px-6 py-3 rounded-lg text-sm transition">Explore Business Solutions</a>
          <a href="contact.php" class="inline-flex items-center justify-center bg-white border border-navy-300 text-navy-700 hover:bg-navy-50 font-bold px-6 py-3 rounded-lg text-sm transition">Talk to an Expert</a>
        </div>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 px-7 lg:px-0 py-2">
        <div class="text-center">
          <div class="mx-auto w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-navy-600 text-2xl"><i class="ri-coupon-3-line"></i></div>
          <p class="mt-2 text-xs font-bold text-slate-700">Volume<br/>Discounts</p>
        </div>
        <div class="text-center">
          <div class="mx-auto w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-navy-600 text-2xl"><i class="ri-customer-service-2-line"></i></div>
          <p class="mt-2 text-xs font-bold text-slate-700">Dedicated<br/>Support</p>
        </div>
        <div class="text-center">
          <div class="mx-auto w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-navy-600 text-2xl"><i class="ri-settings-4-line"></i></div>
          <p class="mt-2 text-xs font-bold text-slate-700">Flexible<br/>Leasing</p>
        </div>
        <div class="text-center">
          <div class="mx-auto w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-navy-600 text-2xl"><i class="ri-rocket-line"></i></div>
          <p class="mt-2 text-xs font-bold text-slate-700">Fast<br/>Deployment</p>
        </div>
      </div>
      <div class="hidden lg:flex min-h-[170px] items-center justify-center bg-white/55">
        <div class="relative w-full h-full min-h-[170px] flex items-center justify-center">
          <div class="absolute inset-0 bg-gradient-to-l from-white to-transparent"></div>
          <i class="ri-building-4-line text-slate-300 text-[110px]"></i>
          <i class="ri-printer-fill text-slate-500 text-[96px] -ml-8"></i>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======= SERVICE STANDARDS ======= -->
<section class="py-14 px-5 bg-white">
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-[0.9fr_1.1fr] gap-10 items-center">
      <div>
        <p class="section-label mb-2">Service Standards</p>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 leading-tight">A smoother printer purchase from cart to first print</h2>
        <p class="text-slate-500 text-sm mt-4 leading-relaxed">Every order is backed by practical setup guidance, clear delivery updates, and support that helps customers get their printer working without the usual guesswork.</p>
        <div class="mt-6 grid grid-cols-3 gap-3">
          <div class="border border-slate-200 rounded-xl p-4">
            <p class="text-2xl font-black text-navy-600">24/7</p>
            <p class="text-xs text-slate-500 mt-1">Support access</p>
          </div>
          <div class="border border-slate-200 rounded-xl p-4">
            <p class="text-2xl font-black text-navy-600">2 min</p>
            <p class="text-xs text-slate-500 mt-1">Avg. chat reply</p>
          </div>
          <div class="border border-slate-200 rounded-xl p-4">
            <p class="text-2xl font-black text-navy-600">Free</p>
            <p class="text-xs text-slate-500 mt-1">Setup help</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
          <div class="w-10 h-10 rounded-lg bg-navy-600 text-white flex items-center justify-center mb-4"><i class="ri-tools-line text-xl"></i></div>
          <h3 class="font-bold text-slate-800 text-sm">Guided Setup</h3>
          <p class="text-sm text-slate-500 mt-2 leading-relaxed">Driver installation, wireless pairing, and first-print checks handled step by step.</p>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
          <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center mb-4"><i class="ri-truck-line text-xl"></i></div>
          <h3 class="font-bold text-slate-800 text-sm">Order Clarity</h3>
          <p class="text-sm text-slate-500 mt-2 leading-relaxed">Straightforward delivery expectations with help available if anything changes.</p>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
          <div class="w-10 h-10 rounded-lg bg-amber2-500 text-white flex items-center justify-center mb-4"><i class="ri-shield-check-line text-xl"></i></div>
          <h3 class="font-bold text-slate-800 text-sm">Checked Products</h3>
          <p class="text-sm text-slate-500 mt-2 leading-relaxed">Popular printer models, ink, and toner organized for easier comparison.</p>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
          <div class="w-10 h-10 rounded-lg bg-slate-800 text-white flex items-center justify-center mb-4"><i class="ri-customer-service-2-line text-xl"></i></div>
          <h3 class="font-bold text-slate-800 text-sm">Real Support</h3>
          <p class="text-sm text-slate-500 mt-2 leading-relaxed">Phone, email, and chat options are kept visible throughout the buying flow.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======= CTA SECTION ======= -->
<section class="py-16 px-5 hero-bg text-white">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/25 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
      <i class="ri-customer-service-2-line text-amber2-400"></i> Expert Support Included Free
    </span>
    <h2 class="text-3xl md:text-4xl font-black leading-tight">
      Need Help Choosing<br/>
      <span class="text-amber2-400">a Printer?</span>
    </h2>
    <p class="mt-4 text-blue-100 text-base max-w-xl mx-auto leading-relaxed">
      Need help choosing a printer? Our experts can help you find the right model for your home or business.
    </p>
    <div class="mt-8 flex flex-wrap justify-center gap-4">
      <a href="#products" class="inline-flex items-center gap-2 btn-gradient text-white font-bold px-8 py-3.5 rounded-xl transition shadow-lg text-sm">
        <i class="ri-printer-line"></i> Shop Printers
      </a>
      <a href="contact.php" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-semibold px-8 py-3.5 rounded-xl transition text-sm">
        <i class="ri-chat-3-line"></i> Get Support
      </a>
    </div>
    <!-- Mini trust row -->
    <div class="mt-10 flex flex-wrap justify-center gap-6 text-xs text-blue-200">
      <span class="flex items-center gap-1.5"><i class="ri-shield-check-line text-amber2-400"></i> No-hassle returns</span>
      <span class="flex items-center gap-1.5"><i class="ri-truck-line text-amber2-400"></i> Fast free shipping</span>
      <span class="flex items-center gap-1.5"><i class="ri-lock-2-line text-amber2-400"></i> Secure payments</span>
      <span class="flex items-center gap-1.5"><i class="ri-star-fill text-amber2-400"></i> 4.9 rated service</span>
    </div>
  </div>
</section>
    </div>
  </div>
</section>

<!-- ======= FOOTER ======= -->
<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- ======= WISHLIST DRAWER ======= -->
<div id="wl-drawer" class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
  <div class="flex items-center justify-between p-5 border-b bg-red-500 text-white">
    <h3 class="font-bold flex items-center gap-2"><i class="ri-heart-3-line text-lg"></i> My Wishlist</h3>
    <button onclick="toggleWishlistDrawer()" class="hover:bg-red-600 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="ri-close-line text-lg"></i></button>
  </div>
  <div id="wl-items" class="flex-1 overflow-y-auto p-4 space-y-3"></div>
  <div class="p-4 border-t bg-slate-50">
    <a href="products.php" class="block w-full text-center btn-gradient text-white font-bold py-3 rounded-xl transition text-sm">
      <i class="ri-store-2-line mr-1"></i> Continue Shopping
    </a>
  </div>
</div>
<div id="wl-overlay" class="fixed inset-0 bg-black/40 z-40 hidden" onclick="toggleWishlistDrawer()"></div>

<!-- ======= CART SIDEBAR ======= -->
<div id="cart-sidebar" class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
  <div class="flex items-center justify-between p-5 border-b bg-navy-700 text-white">
    <h3 class="font-bold flex items-center gap-2"><i class="ri-shopping-cart-2-line text-lg"></i> Your Cart</h3>
    <button onclick="toggleCart()" class="text-white hover:text-amber2-400 transition text-xl"><i class="ri-close-line"></i></button>
  </div>
  <div id="cart-items" class="flex-1 overflow-y-auto p-4 space-y-3">
    <div class="text-center mt-12">
      <i class="ri-shopping-cart-2-line text-slate-200 text-5xl"></i>
      <p class="text-slate-400 text-sm mt-3">Your cart is empty</p>
    </div>
  </div>
  <div class="p-5 border-t bg-slate-50">
    <div class="flex justify-between font-bold text-base mb-4 text-slate-800">
      <span>Total:</span>
      <span id="cart-total" class="text-navy-600">$0.00</span>
    </div>
    <a href="checkout.php" class="w-full btn-gradient text-white font-bold py-3 rounded-xl transition mb-2 flex items-center justify-center gap-2 text-sm">
      <i class="ri-lock-2-line"></i> Secure Checkout
    </a>
    <button onclick="toggleCart()" class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-2 rounded-xl transition text-sm">
      Continue Shopping
    </button>
  </div>
</div>
<div id="cart-overlay" class="fixed inset-0 bg-black/40 z-40 hidden" onclick="toggleCart()"></div>

<!-- Back to top -->
<button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="fixed bottom-6 right-6 btn-gradient text-white w-11 h-11 rounded-full shadow-lg transition z-30 flex items-center justify-center">
  <i class="ri-arrow-up-line text-lg"></i>
</button>

<!-- ======= JS ======= -->
<script src="js/wishlist.js"></script>
<script src="js/taxonomy.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
  const tiles = [...document.querySelectorAll('[data-home-product-id]')];
  if (!tiles.length) return;

  try {
    const response = await fetch(`api/products.php?limit=100&_=${Date.now()}`, { cache: 'no-store' });
    const result = await response.json();
    const products = result?.data?.products || [];
    const activeIds = new Set(products.map(product => Number(product.id)));
    tiles.forEach(tile => {
      tile.hidden = !activeIds.has(Number(tile.dataset.homeProductId));
    });
  } catch (error) {
    console.warn('Could not sync homepage products:', error);
  }
});
</script>
<script src="js/index.js"></script>
</body>
</html>


