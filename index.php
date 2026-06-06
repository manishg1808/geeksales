<?php
require_once __DIR__ . '/admin/includes/db.php';

$heroPosterBanners = [];
try {
    $homePdo = db();
    $stmt = $homePdo->query(
        "SELECT *
         FROM banners
         WHERE location = 'Homepage Hero'
           AND status = 'active'
           AND poster_style <> 'standard'
           AND (start_date IS NULL OR start_date <= CURDATE())
           AND (end_date IS NULL OR end_date >= CURDATE())
         ORDER BY sort_order, id"
    );
    $heroPosterBanners = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Throwable $e) {
    $heroPosterBanners = [];
}

function home_asset_url(string $path): string
{
    $path = trim($path);
    if ($path === '' || preg_match('/^(https?:)?\/\//i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }
    return ltrim($path, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GeekSupportSales – Printers, Ink & Toner</title>
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

<!-- ======= TOP BAR ======= -->
<div class="brand-gradient text-white hidden md:block">
  <div class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between text-xs">
    <!-- Left: ticker -->
    <div class="ticker-wrap flex-1 max-w-xl overflow-hidden">
      <div class="ticker-inner text-slate-300">
        &nbsp;&nbsp;<i class="ri-truck-line mr-1"></i>Free Shipping on orders over $99&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-tools-line mr-1"></i>Free Expert Setup on Every Printer&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-price-tag-3-line mr-1"></i>Best Price Guarantee&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-shield-check-line mr-1"></i>2-Year Warranty Included&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-truck-line mr-1"></i>Free Shipping on orders over $99&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-tools-line mr-1"></i>Free Expert Setup on Every Printer&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-price-tag-3-line mr-1"></i>Best Price Guarantee&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-shield-check-line mr-1"></i>2-Year Warranty Included&nbsp;&nbsp;&nbsp;
      </div>
    </div>
    <!-- Right: contact -->
    <div class="flex items-center gap-5 shrink-0 ml-6">
      <a href="tel:8019511533" class="flex items-center gap-1.5 text-slate-300 hover:text-white transition">
        <i class="ri-phone-fill text-amber2-400"></i>
        <span class="font-medium">8019511533</span>
      </a>
      <span class="w-px h-3 bg-slate-600"></span>
      <a href="mailto:support@geeksupportsales.com" class="flex items-center gap-1.5 text-slate-300 hover:text-white transition">
        <i class="ri-mail-fill text-amber2-400"></i>
        <span class="font-medium">support@geeksupportsales.com</span>
      </a>
    </div>
  </div>
</div>

<!-- ======= NAVBAR ======= -->
<header class="bg-white border-b border-slate-100 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 h-16 flex items-center gap-8">

    <!-- Logo -->
    <a href="index.php" class="flex items-center gap-2.5 shrink-0">
      <div class="bg-navy-600 rounded-lg w-8 h-8 flex items-center justify-center">
        <i class="ri-printer-fill text-white text-sm"></i>
      </div>
      <span class="text-[15px] font-black tracking-tight leading-none">
        <span class="text-navy-600">Geek</span><span class="text-amber2-500">Support</span><span class="text-slate-800">Sales</span>
      </span>
    </a>

    <!-- Nav Links -->
    <nav class="hidden lg:flex items-center gap-1">
      <a href="products.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Products</a>
      <a href="support.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Support</a>
      <a href="contact.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Contact</a>
    </nav>

    <!-- Search -->
    <div class="hidden md:flex flex-1 max-w-sm ml-auto">
      <div class="flex w-full h-9 rounded-lg border border-slate-200 bg-slate-50 hover:border-slate-300 focus-within:border-navy-400 focus-within:bg-white overflow-hidden transition">
        <input type="text" placeholder="Search printers, ink, toner…" class="flex-1 px-3 text-sm bg-transparent outline-none text-slate-700 placeholder-slate-400"/>
        <button class="px-3 text-slate-400 hover:text-navy-600 transition">
          <i class="ri-search-2-line text-base"></i>
        </button>
      </div>
    </div>

    <!-- Divider -->
    <div class="hidden sm:block w-px h-6 bg-slate-200 shrink-0"></div>

    <!-- Actions -->
    <div class="flex items-center gap-1 shrink-0">
      <button onclick="toggleWishlistDrawer()" class="relative w-9 h-9 flex items-center justify-center text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
        <i class="ri-heart-3-line text-[18px]"></i>
        <span id="wl-count" class="absolute top-0.5 right-0.5 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-[14px] h-[14px] px-0.5 items-center justify-center hidden leading-none"></span>
      </button>
      <button onclick="toggleCart()" class="relative w-9 h-9 flex items-center justify-center text-slate-500 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">
        <i class="ri-shopping-bag-line text-[18px]"></i>
        <span id="cart-count" class="absolute top-0.5 right-0.5 bg-navy-600 text-white text-[9px] font-bold rounded-full min-w-[14px] h-[14px] px-0.5 flex items-center justify-center leading-none">0</span>
      </button>
    </div>

  </div>

  <!-- Sub-nav categories -->
  <div class="border-t border-slate-100 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 py-2 flex gap-5 text-xs font-semibold text-slate-500 overflow-x-auto" data-taxonomy-subnav>
      <a href="products.php" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-printer-line"></i> All Printers</a>
      <a href="products.php?cat=inkjet" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-drop-line"></i> Inkjet</a>
      <a href="products.php?cat=laser" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-fire-line"></i> Laser</a>
      <a href="products.php?cat=allinone" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-file-copy-2-line"></i> All-in-One</a>
      <a href="products.php?cat=business" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-building-2-line"></i> Business</a>
      <a href="products.php?cat=ink" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-ink-bottle-line"></i> Ink & Toner</a>
      <a href="products.php?cat=deals" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-flashlight-line"></i> Flash Deals</a>
      <a href="contact.php" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-headphone-line"></i> Tech Support</a>
    </div>
  </div>
</header>

<!-- ======= HERO BANNER SLIDER ======= -->
<section id="hero-slider" class="relative overflow-hidden brand-gradient" style="height:580px">

  <!-- ── SLIDE 1 : Video ── -->
  <div class="hero-slide absolute inset-0 transition-opacity duration-700" data-index="0">
    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover">
      <source src="https://videos.pexels.com/video-files/3195394/3195394-uhd_2560_1440_25fps.mp4" type="video/mp4"/>
    </video>
    <div class="absolute inset-0" style="background:linear-gradient(100deg,rgba(25,38,82,.88) 0%,rgba(50,65,118,.48) 60%,transparent 100%)"></div>
    <div class="relative z-10 h-full max-w-7xl mx-auto px-8 flex items-center">
      <div class="max-w-xl text-white">
        <span class="inline-flex items-center gap-1.5 bg-amber2-500/90 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">
          <i class="ri-flashlight-fill"></i> Summer Sale — Up to 40% Off
        </span>
        <h1 class="text-5xl lg:text-6xl font-black leading-[1.08] tracking-tight">
          Print Smarter.<br/><span class="text-amber2-400">Save Bigger.</span>
        </h1>
        <p class="mt-4 text-blue-100 text-base leading-relaxed max-w-md">
          Top-brand printers with free expert setup on every order. Home, office, or enterprise.
        </p>
        <div class="mt-7 flex flex-wrap gap-3">
          <a href="#products" class="inline-flex items-center gap-2 btn-gradient text-white font-bold px-7 py-3 rounded-xl transition shadow-lg text-sm">
            Shop Now <i class="ri-arrow-right-line"></i>
          </a>
          <a href="contact.php" class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 backdrop-blur-sm text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
            <i class="ri-headphone-line"></i> Free Support
          </a>
        </div>
        <div class="mt-8 flex gap-6 text-xs text-blue-200">
          <span class="flex items-center gap-1"><i class="ri-shield-check-line text-amber2-400"></i> 2-Year Warranty</span>
          <span class="flex items-center gap-1"><i class="ri-truck-line text-amber2-400"></i> Free Shipping $99+</span>
          <span class="flex items-center gap-1"><i class="ri-star-fill text-amber2-400"></i> 4.9 / 5 Rating</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ── SLIDE 2 : Brand Showcase Poster — HP, Canon, Brother, Epson ── -->
  <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0 pointer-events-none" data-index="1">
    <!-- Background: deep navy gradient -->
    <div class="absolute inset-0" style="background:linear-gradient(135deg, #0F172A 0%, #2563EB 55%, #2563EB 100%)"></div>

    <!-- Subtle dot pattern overlay -->
    <div class="absolute inset-0 opacity-10"
         style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:28px 28px"></div>

    <div class="relative z-10 h-full max-w-7xl mx-auto px-8 flex items-center gap-10">

      <!-- LEFT: Text -->
      <div class="flex-1 text-white min-w-0">
        <span class="inline-flex items-center gap-1.5 bg-amber2-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-5">
          <i class="ri-award-line"></i> Top Brands · Best Prices
        </span>
        <h2 class="text-5xl lg:text-6xl font-black leading-[1.06] tracking-tight">
          World's Best<br/>
          <span class="text-amber2-400">Printer Brands</span>
        </h2>
        <p class="mt-4 text-blue-200 text-base leading-relaxed max-w-sm">
          HP, Canon, Brother, Epson — all top models in stock with free expert setup on every order.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="products.php" class="inline-flex items-center gap-2 btn-gradient text-white font-bold px-6 py-2.5 rounded-xl transition shadow-lg text-sm">
            Shop All Brands <i class="ri-arrow-right-line"></i>
          </a>
          <a href="contact.php" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 text-white font-semibold px-5 py-2.5 rounded-xl transition text-sm">
            Get Expert Advice
          </a>
        </div>
        <!-- Brand logos row -->
        <div class="mt-8 flex items-center gap-4 flex-wrap">
          <a href="products.php?brand=HP"     class="bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-black px-4 py-2 rounded-lg transition tracking-widest">HP</a>
          <a href="products.php?brand=Canon"  class="bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-black px-4 py-2 rounded-lg transition tracking-widest">CANON</a>
          <a href="products.php?brand=Brother"class="bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-black px-4 py-2 rounded-lg transition tracking-widest">BROTHER</a>
          <a href="products.php?brand=Epson"  class="bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-black px-4 py-2 rounded-lg transition tracking-widest">EPSON</a>
        </div>
      </div>

      <!-- RIGHT: 4 Product Cards Grid -->
      <div class="hidden md:grid grid-cols-2 gap-3 shrink-0 w-[460px]">

        <!-- HP DeskJet 4155e -->
        <a href="product-detail.php?id=1"
           class="group bg-white/10 hover:bg-white/18 backdrop-blur-sm border border-white/20 hover:border-amber2-400/60 rounded-2xl p-4 flex flex-col items-center text-center transition">
          <div class="w-full h-24 rounded-xl flex items-center justify-center mb-3" style="background:#f1f5f922">
            <i class="ri-printer-fill text-[56px] leading-none" style="color:#94a3b8"></i>
          </div>
          <div class="text-[10px] font-black text-amber2-400 tracking-widest mb-0.5">HP</div>
          <div class="text-white text-xs font-bold leading-snug">DeskJet 4155e</div>
          <div class="text-blue-300 text-[10px] mt-0.5">Inkjet · All-in-One</div>
          <div class="mt-2 flex items-center gap-1.5">
            <span class="text-white font-black text-sm">$89.99</span>
            <span class="text-white/40 line-through text-[10px]">$119.99</span>
          </div>
          <span class="mt-2 bg-red-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">SALE 25% OFF</span>
        </a>

        <!-- Canon PIXMA TR8620 -->
        <a href="product-detail.php?id=2"
           class="group bg-white/10 hover:bg-white/18 backdrop-blur-sm border border-white/20 hover:border-amber2-400/60 rounded-2xl p-4 flex flex-col items-center text-center transition">
          <div class="w-full h-24 rounded-xl flex items-center justify-center mb-3" style="background:#f1f5f922">
            <i class="ri-printer-fill text-[56px] leading-none" style="color:#94a3b8"></i>
          </div>
          <div class="text-[10px] font-black text-amber2-400 tracking-widest mb-0.5">CANON</div>
          <div class="text-white text-xs font-bold leading-snug">PIXMA TR8620</div>
          <div class="text-blue-300 text-[10px] mt-0.5">All-in-One · Photo</div>
          <div class="mt-2 flex items-center gap-1.5">
            <span class="text-white font-black text-sm">$149.99</span>
            <span class="text-white/40 line-through text-[10px]">$179.99</span>
          </div>
          <span class="mt-2 bg-emerald-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">NEW</span>
        </a>

        <!-- Brother HL-L2350DW -->
        <a href="product-detail.php?id=3"
           class="group bg-white/10 hover:bg-white/18 backdrop-blur-sm border border-white/20 hover:border-amber2-400/60 rounded-2xl p-4 flex flex-col items-center text-center transition">
          <div class="w-full h-24 rounded-xl flex items-center justify-center mb-3" style="background:#fffbeb22">
            <i class="ri-printer-fill text-[56px] leading-none" style="color:#F97316"></i>
          </div>
          <div class="text-[10px] font-black text-amber2-400 tracking-widest mb-0.5">BROTHER</div>
          <div class="text-white text-xs font-bold leading-snug">HL-L2350DW</div>
          <div class="text-blue-300 text-[10px] mt-0.5">Laser · Duplex</div>
          <div class="mt-2 flex items-center gap-1.5">
            <span class="text-white font-black text-sm">$109.99</span>
            <span class="text-white/40 line-through text-[10px]">$139.99</span>
          </div>
          <span class="mt-2 bg-amber2-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">BEST SELLER</span>
        </a>

        <!-- Epson EcoTank ET-2800 -->
        <a href="product-detail.php?id=4"
           class="group bg-white/10 hover:bg-white/18 backdrop-blur-sm border border-white/20 hover:border-amber2-400/60 rounded-2xl p-4 flex flex-col items-center text-center transition">
          <div class="w-full h-24 rounded-xl flex items-center justify-center mb-3" style="background:#ecfdf522">
            <i class="ri-printer-fill text-[56px] leading-none" style="color:#34d399"></i>
          </div>
          <div class="text-[10px] font-black text-amber2-400 tracking-widest mb-0.5">EPSON</div>
          <div class="text-white text-xs font-bold leading-snug">EcoTank ET-2800</div>
          <div class="text-blue-300 text-[10px] mt-0.5">EcoTank · Cartridge-Free</div>
          <div class="mt-2 flex items-center gap-1.5">
            <span class="text-white font-black text-sm">$174.99</span>
            <span class="text-white/40 line-through text-[10px]">$249.99</span>
          </div>
          <span class="mt-2 bg-red-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full badge-pulse">SALE 30% OFF</span>
        </a>

      </div>
    </div>
  </div>

  <!-- ── SLIDE 3 : Image Poster — Business Printers ── -->
  <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0 pointer-events-none" data-index="2">
    <img src="https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=1600" alt="Business printers banner"
         class="absolute inset-0 w-full h-full object-cover"/>
    <div class="absolute inset-0" style="background:linear-gradient(105deg,rgba(25,38,82,.92) 0%,rgba(50,65,118,.58) 55%,transparent 100%)"></div>
    <div class="relative z-10 h-full max-w-7xl mx-auto px-8 flex items-center">
      <div class="max-w-xl text-white">
        <span class="inline-flex items-center gap-1.5 bg-amber2-500/90 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">
          <i class="ri-building-2-line"></i> Business Solutions
        </span>
        <h2 class="text-5xl lg:text-6xl font-black leading-[1.08] tracking-tight">
          Power Your<br/>Office<span class="text-amber2-400">.</span>
        </h2>
        <p class="mt-4 text-blue-100 text-base leading-relaxed max-w-md">
          High-speed laser and color printers built for teams. Network-ready, duplex printing, enterprise reliability.
        </p>
        <div class="mt-7 flex flex-wrap gap-3">
          <a href="products.php?cat=business" class="inline-flex items-center gap-2 btn-gradient text-white font-bold px-7 py-3 rounded-xl transition shadow-lg text-sm">
            Business Printers <i class="ri-arrow-right-line"></i>
          </a>
          <a href="contact.php" class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 backdrop-blur-sm text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
            <i class="ri-phone-line"></i> Talk to an Expert
          </a>
        </div>
        <div class="mt-8 flex gap-6 text-xs text-blue-200">
          <span class="flex items-center gap-1"><i class="ri-wifi-line text-amber2-400"></i> Network Ready</span>
          <span class="flex items-center gap-1"><i class="ri-file-copy-2-line text-amber2-400"></i> Auto Duplex</span>
          <span class="flex items-center gap-1"><i class="ri-customer-service-2-line text-amber2-400"></i> Dedicated Support</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Prev / Next Arrows ── -->
  <?php foreach ($heroPosterBanners as $posterIndex => $banner):
    $slideIndex = 3 + $posterIndex;
    $style = (string)($banner['poster_style'] ?? 'poster_light');
    $isDark = $style === 'poster_dark';
    $isTeal = $style === 'poster_teal';
    $wrapClass = $isDark
      ? 'bg-[#071426] text-white'
      : ($isTeal ? 'bg-gradient-to-br from-emerald-50 via-white to-cyan-50 text-slate-900' : 'bg-gradient-to-br from-blue-50 via-white to-slate-100 text-slate-900');
    $primaryClass = $isDark ? 'bg-amber2-500 hover:bg-amber2-600 text-white' : ($isTeal ? 'bg-teal-700 hover:bg-teal-800 text-white' : 'bg-navy-600 hover:bg-navy-700 text-white');
    $secondaryClass = $isDark ? 'border-white/50 text-white hover:bg-white/10' : ($isTeal ? 'border-teal-600 text-teal-800 hover:bg-teal-50' : 'border-amber2-500 text-amber2-700 hover:bg-amber2-50');
    $imageUrl = home_asset_url((string)($banner['image_url'] ?? ''));
  ?>
  <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0 pointer-events-none" data-index="<?php echo $slideIndex; ?>">
    <div class="absolute inset-0 <?php echo $wrapClass; ?>"></div>
    <div class="relative z-10 h-full max-w-7xl mx-auto px-8 flex items-center">
      <div class="grid grid-cols-1 md:grid-cols-[0.9fr_1.1fr] gap-8 items-center w-full">
        <div class="<?php echo $isDark ? 'text-white' : 'text-slate-900'; ?>">
          <p class="text-[11px] font-black uppercase tracking-widest <?php echo $isDark ? 'text-white/70' : 'text-slate-500'; ?> mb-4"><?php echo $isDark ? 'Professional Printing' : ($isTeal ? 'Smart Solutions' : 'Printer Deals'); ?></p>
          <h2 class="text-4xl lg:text-5xl font-black leading-[1.06] tracking-tight max-w-lg"><?php echo e($banner['title']); ?></h2>
          <p class="mt-4 <?php echo $isDark ? 'text-blue-100' : 'text-slate-600'; ?> text-base leading-relaxed max-w-md"><?php echo e($banner['subtitle']); ?></p>
          <div class="mt-7 flex flex-wrap gap-3">
            <?php if(!empty($banner['button_text'])): ?><a href="<?php echo e($banner['link_url'] ?: 'products.php'); ?>" class="inline-flex items-center gap-2 <?php echo $primaryClass; ?> font-bold px-6 py-3 rounded-lg transition shadow-sm text-sm"><?php echo e($banner['button_text']); ?></a><?php endif; ?>
            <?php if(!empty($banner['secondary_button_text'])): ?><a href="<?php echo e($banner['secondary_link_url'] ?: 'products.php?cat=deals'); ?>" class="inline-flex items-center gap-2 bg-transparent border <?php echo $secondaryClass; ?> font-bold px-6 py-3 rounded-lg transition text-sm"><?php echo e($banner['secondary_button_text']); ?></a><?php endif; ?>
          </div>
        </div>
        <div class="relative hidden md:flex h-[360px] items-center justify-center">
          <div class="absolute inset-x-8 bottom-10 h-24 rounded-full <?php echo $isDark ? 'bg-blue-400/10' : 'bg-slate-300/40'; ?> blur-xl"></div>
          <div class="absolute right-6 top-10 bottom-10 w-72 rounded-full <?php echo $isDark ? 'bg-white/5' : 'bg-white/55'; ?>"></div>
          <?php if($imageUrl !== ''): ?>
            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($banner['title']); ?>" class="relative z-10 max-h-[320px] w-full object-contain drop-shadow-2xl">
          <?php else: ?>
            <i class="ri-printer-fill relative z-10 text-[220px] <?php echo $isDark ? 'text-amber2-400' : 'text-navy-700'; ?>"></i>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <button onclick="sliderMove(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-white/15 hover:bg-white/30 backdrop-blur-sm border border-white/25 text-white rounded-full flex items-center justify-center transition">
    <i class="ri-arrow-left-s-line text-xl"></i>
  </button>
  <button onclick="sliderMove(1)" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-white/15 hover:bg-white/30 backdrop-blur-sm border border-white/25 text-white rounded-full flex items-center justify-center transition">
    <i class="ri-arrow-right-s-line text-xl"></i>
  </button>

  <!-- ── Dot indicators ── -->
  <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
    <button onclick="sliderGoto(0)" class="slider-dot w-7 h-2 rounded-full bg-white transition-all" data-dot="0"></button>
    <button onclick="sliderGoto(1)" class="slider-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-dot="1"></button>
    <button onclick="sliderGoto(2)" class="slider-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-dot="2"></button>
    <?php foreach ($heroPosterBanners as $posterIndex => $_banner): $dotIndex = 3 + $posterIndex; ?>
    <button onclick="sliderGoto(<?php echo $dotIndex; ?>)" class="slider-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-dot="<?php echo $dotIndex; ?>"></button>
    <?php endforeach; ?>
  </div>

  <!-- ── Slide counter ── -->
  <div class="absolute bottom-5 right-6 z-20 text-white/60 text-xs font-semibold" id="slide-counter">1 / <?php echo 3 + count($heroPosterBanners); ?></div>

  <!-- ── Video mute toggle (only shown on slide 0) ── -->
  <button id="vid-toggle" onclick="toggleVidSound()" title="Toggle sound"
    class="absolute bottom-5 right-16 z-20 bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white w-8 h-8 rounded-full flex items-center justify-center transition border border-white/20">
    <i class="ri-volume-mute-line text-sm" id="vid-icon"></i>
  </button>

</section>

<script>
  // ===== HERO SLIDER =====
  let sliderCurrent = 0;
  const sliderTotal = <?php echo 3 + count($heroPosterBanners); ?>;
  let sliderTimer;

  function sliderGoto(n) {
    const slides = document.querySelectorAll('.hero-slide');
    const dots   = document.querySelectorAll('.slider-dot');
    slides[sliderCurrent].classList.add('opacity-0','pointer-events-none');
    slides[sliderCurrent].classList.remove('opacity-100');
    sliderCurrent = (n + sliderTotal) % sliderTotal;
    slides[sliderCurrent].classList.remove('opacity-0','pointer-events-none');
    slides[sliderCurrent].classList.add('opacity-100');
    dots.forEach((d, i) => {
      d.className = i === sliderCurrent
        ? 'slider-dot w-7 h-2 rounded-full bg-white transition-all'
        : 'slider-dot w-2 h-2 rounded-full bg-white/40 transition-all';
    });
    document.getElementById('slide-counter').textContent = (sliderCurrent + 1) + ' / ' + sliderTotal;
    document.getElementById('vid-toggle').style.display = sliderCurrent === 0 ? '' : 'none';
    sliderReset();
  }
  function sliderMove(dir) { sliderGoto(sliderCurrent + dir); }
  function sliderReset() {
    clearInterval(sliderTimer);
    sliderTimer = setInterval(() => sliderMove(1), 6000);
  }

  // Keyboard arrows
  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowLeft')  sliderMove(-1);
    if (e.key === 'ArrowRight') sliderMove(1);
  });

  // Touch swipe
  let _tx = 0;
  const _sl = document.getElementById('hero-slider');
  _sl.addEventListener('touchstart', e => { _tx = e.touches[0].clientX; }, { passive:true });
  _sl.addEventListener('touchend',   e => {
    const diff = _tx - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) sliderMove(diff > 0 ? 1 : -1);
  });

  sliderReset();
</script>

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
          <p class="text-xs font-black text-slate-800">30-Day Returns</p>
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
        <p class="section-label mb-1">Best Seller HP Authorized</p>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800">Choose Your HP Printer</h2>
        <p class="text-slate-500 text-sm mt-1">Authorized HP printer deals with setup support included.</p>
      </div>
      <a href="products.php" class="text-navy-600 text-sm font-semibold hover:underline flex items-center gap-1">View All <i class="ri-arrow-right-line"></i></a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Grid 1: One printer -->
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-3xl overflow-hidden" data-featured-hp-printer>
        <div class="grid grid-cols-1 md:grid-cols-2 h-full">
          <div class="bg-navy-50 min-h-[280px] flex items-center justify-center p-8">
            <i class="ri-printer-fill text-navy-500" style="font-size:150px;line-height:1"></i>
          </div>
          <div class="p-7 flex flex-col justify-center">
            <span class="inline-flex w-fit bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md mb-4">SALE 25% OFF</span>
            <p class="text-[10px] text-navy-600 font-bold uppercase tracking-widest">HP</p>
            <h3 class="text-2xl font-black text-slate-800 mt-1">HP DeskJet 4155e</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Wireless all-in-one inkjet printer for home printing, scanning, and copying.</p>
            <div class="flex items-baseline gap-2 mt-4">
              <span class="text-3xl font-black text-slate-800">$89.99</span>
              <span class="text-sm text-slate-400 line-through">$119.99</span>
            </div>
            <div class="mt-5 grid grid-cols-[56px_1fr] gap-3 items-stretch">
              <button onclick="addToCart('HP DeskJet 4155e',89.99)" class="h-12 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add HP DeskJet 4155e to cart">
                <i class="ri-shopping-cart-2-line text-[30px] leading-none"></i>
              </button>
              <button onclick="buyNow('HP DeskJet 4155e',89.99)" class="btn-gradient h-12 w-full rounded-xl transition font-bold text-sm flex items-center justify-center gap-2">
                <i class="ri-flashlight-line"></i> Buy Now
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid 2: Two HP printers -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="card-lift bg-white border border-slate-200 rounded-3xl overflow-hidden" data-featured-hp-printer>
          <div class="bg-navy-50 h-40 flex items-center justify-center p-5">
            <i class="ri-printer-fill text-navy-500" style="font-size:86px;line-height:1"></i>
          </div>
          <div class="p-5">
            <p class="text-[10px] text-navy-600 font-bold uppercase tracking-widest">HP</p>
            <h3 class="font-black text-slate-800 mt-1">HP LaserJet Pro M404n</h3>
            <p class="text-xs text-slate-400 mt-1">Authorized HP Laser Printer</p>
            <div class="flex items-baseline gap-2 mt-3">
              <span class="text-xl font-black text-slate-800">$249.00</span>
              <span class="text-xs text-slate-400 line-through">$299.00</span>
            </div>
            <div class="mt-4 grid grid-cols-[52px_1fr] gap-2 items-stretch">
              <button onclick="addToCart('HP LaserJet Pro M404n',249)" class="h-11 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add HP LaserJet Pro M404n to cart">
                <i class="ri-shopping-cart-2-line text-[28px] leading-none"></i>
              </button>
              <button onclick="buyNow('HP LaserJet Pro M404n',249)" class="btn-gradient h-11 w-full rounded-xl transition font-bold text-xs flex items-center justify-center gap-1.5">
                Buy Now
              </button>
            </div>
          </div>
        </div>

        <div class="card-lift bg-white border border-slate-200 rounded-3xl overflow-hidden" data-featured-hp-printer>
          <div class="bg-blue-50 h-40 flex items-center justify-center p-5">
            <i class="ri-printer-fill text-blue-600" style="font-size:86px;line-height:1"></i>
          </div>
          <div class="p-5">
            <p class="text-[10px] text-navy-600 font-bold uppercase tracking-widest">HP</p>
            <h3 class="font-black text-slate-800 mt-1">HP OfficeJet Pro 9015e</h3>
            <p class="text-xs text-slate-400 mt-1">Authorized HP All-in-One</p>
            <div class="flex items-baseline gap-2 mt-3">
              <span class="text-xl font-black text-slate-800">$199.99</span>
              <span class="text-xs text-slate-400 line-through">$249.99</span>
            </div>
            <div class="mt-4 grid grid-cols-[52px_1fr] gap-2 items-stretch">
              <button onclick="addToCart('HP OfficeJet Pro 9015e',199.99)" class="h-11 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add HP OfficeJet Pro 9015e to cart">
                <i class="ri-shopping-cart-2-line text-[28px] leading-none"></i>
              </button>
              <button onclick="buyNow('HP OfficeJet Pro 9015e',199.99)" class="btn-gradient h-11 w-full rounded-xl transition font-bold text-xs flex items-center justify-center gap-1.5">
                Buy Now
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

      <!-- Card 1 -->
      <div class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden" data-home-product-id="1">
        <div class="relative bg-navy-50 p-7 flex justify-center items-center h-44">
          <i class="ri-printer-fill text-navy-400" style="font-size:90px;line-height:1"></i>
          <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md badge-pulse">−25%</span>
          <button onclick="wlToggle(1)" data-wl-id="1" class="absolute top-3 right-3 text-slate-300 hover:text-red-400 transition text-lg"><i class="ri-heart-3-line"></i></button>
        </div>
        <div class="p-4">
          <p class="text-[10px] text-navy-600 font-bold uppercase tracking-widest">HP</p>
          <h3 class="font-bold text-slate-800 mt-0.5 text-sm">HP DeskJet 4155e</h3>
          <p class="text-xs text-slate-400 mt-0.5">Wireless All-in-One Inkjet</p>
          <div class="flex items-center gap-1 mt-2">
            <span class="text-amber2-400 text-xs">★★★★★</span>
            <span class="text-xs text-slate-400">(312)</span>
          </div>
          <div class="flex items-baseline gap-2 mt-2">
            <span class="text-lg font-black text-slate-800">$89.99</span>
            <span class="text-xs text-slate-400 line-through">$119.99</span>
          </div>
          <div class="mt-3 grid grid-cols-[48px_1fr] gap-2 items-stretch">
            <button onclick="addToCart('HP DeskJet 4155e',89.99)" class="h-10 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add HP DeskJet 4155e to cart">
              <i class="ri-shopping-cart-2-line text-[26px] leading-none"></i>
            </button>
            <button onclick="buyNow('HP DeskJet 4155e',89.99)" class="btn-gradient h-10 w-full rounded-xl transition text-xs font-bold flex items-center justify-center gap-1.5">
              <i class="ri-flashlight-line"></i> Buy Now
            </button>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden" data-home-product-id="2">
        <div class="relative bg-slate-100 p-7 flex justify-center items-center h-44">
          <i class="ri-printer-fill text-slate-500" style="font-size:90px;line-height:1"></i>
          <span class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">NEW</span>
          <button onclick="wlToggle(2)" data-wl-id="2" class="absolute top-3 right-3 text-slate-300 hover:text-red-400 transition text-lg"><i class="ri-heart-3-line"></i></button>
        </div>
        <div class="p-4">
          <p class="text-[10px] text-slate-600 font-bold uppercase tracking-widest">Canon</p>
          <h3 class="font-bold text-slate-800 mt-0.5 text-sm">Canon PIXMA TR8620</h3>
          <p class="text-xs text-slate-400 mt-0.5">Home Office All-in-One</p>
          <div class="flex items-center gap-1 mt-2">
            <span class="text-amber2-400 text-xs">★★★★★</span>
            <span class="text-xs text-slate-400">(198)</span>
          </div>
          <div class="flex items-baseline gap-2 mt-2">
            <span class="text-lg font-black text-slate-800">$149.99</span>
            <span class="text-xs text-slate-400 line-through">$179.99</span>
          </div>
          <div class="mt-3 grid grid-cols-[48px_1fr] gap-2 items-stretch">
            <button onclick="addToCart('Canon PIXMA TR8620',149.99)" class="h-10 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add Canon PIXMA TR8620 to cart">
              <i class="ri-shopping-cart-2-line text-[26px] leading-none"></i>
            </button>
            <button onclick="buyNow('Canon PIXMA TR8620',149.99)" class="btn-gradient h-10 w-full rounded-xl transition text-xs font-bold flex items-center justify-center gap-1.5">
              <i class="ri-flashlight-line"></i> Buy Now
            </button>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden" data-home-product-id="3">
        <div class="relative bg-amber2-50 p-7 flex justify-center items-center h-44">
          <i class="ri-printer-fill text-amber2-400" style="font-size:90px;line-height:1"></i>
          <span class="absolute top-3 left-3 bg-amber2-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">BEST SELLER</span>
          <button onclick="wlToggle(3)" data-wl-id="3" class="absolute top-3 right-3 text-slate-300 hover:text-red-400 transition text-lg"><i class="ri-heart-3-line"></i></button>
        </div>
        <div class="p-4">
          <p class="text-[10px] text-amber2-600 font-bold uppercase tracking-widest">Brother</p>
          <h3 class="font-bold text-slate-800 mt-0.5 text-sm">Brother HL-L2350DW</h3>
          <p class="text-xs text-slate-400 mt-0.5">Compact Laser Printer</p>
          <div class="flex items-center gap-1 mt-2">
            <span class="text-amber2-400 text-xs">★★★★☆</span>
            <span class="text-xs text-slate-400">(541)</span>
          </div>
          <div class="flex items-baseline gap-2 mt-2">
            <span class="text-lg font-black text-slate-800">$109.99</span>
            <span class="text-xs text-slate-400 line-through">$139.99</span>
          </div>
          <div class="mt-3 grid grid-cols-[48px_1fr] gap-2 items-stretch">
            <button onclick="addToCart('Brother HL-L2350DW',109.99)" class="h-10 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add Brother HL-L2350DW to cart">
              <i class="ri-shopping-cart-2-line text-[26px] leading-none"></i>
            </button>
            <button onclick="buyNow('Brother HL-L2350DW',109.99)" class="btn-gradient h-10 w-full rounded-xl transition text-xs font-bold flex items-center justify-center gap-1.5">
              <i class="ri-flashlight-line"></i> Buy Now
            </button>
          </div>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden" data-home-product-id="4">
        <div class="relative bg-emerald-50 p-7 flex justify-center items-center h-44">
          <i class="ri-printer-fill text-emerald-500" style="font-size:90px;line-height:1"></i>
          <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md badge-pulse">−30%</span>
          <button onclick="wlToggle(4)" data-wl-id="4" class="absolute top-3 right-3 text-slate-300 hover:text-red-400 transition text-lg"><i class="ri-heart-3-line"></i></button>
        </div>
        <div class="p-4">
          <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest">Epson</p>
          <h3 class="font-bold text-slate-800 mt-0.5 text-sm">Epson EcoTank ET-2800</h3>
          <p class="text-xs text-slate-400 mt-0.5">Supertank Inkjet Printer</p>
          <div class="flex items-center gap-1 mt-2">
            <span class="text-amber2-400 text-xs">★★★★★</span>
            <span class="text-xs text-slate-400">(427)</span>
          </div>
          <div class="flex items-baseline gap-2 mt-2">
            <span class="text-lg font-black text-slate-800">$174.99</span>
            <span class="text-xs text-slate-400 line-through">$249.99</span>
          </div>
          <div class="mt-3 grid grid-cols-[48px_1fr] gap-2 items-stretch">
            <button onclick="addToCart('Epson EcoTank ET-2800',174.99)" class="h-10 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add Epson EcoTank ET-2800 to cart">
              <i class="ri-shopping-cart-2-line text-[26px] leading-none"></i>
            </button>
            <button onclick="buyNow('Epson EcoTank ET-2800',174.99)" class="btn-gradient h-10 w-full rounded-xl transition text-xs font-bold flex items-center justify-center gap-1.5">
              <i class="ri-flashlight-line"></i> Buy Now
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ======= FLASH DEAL BANNER ======= -->
<section id="deals" class="py-14 px-5 brand-gradient text-white" data-home-product-id="5">
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12">
    <div class="flex-1">
      <span class="inline-flex items-center gap-1.5 bg-red-500/20 border border-red-400/30 text-red-300 text-xs font-bold px-3 py-1.5 rounded-full mb-5">
        <i class="ri-flashlight-fill"></i> Flash Deal — Today Only
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

<!-- ======= INK & TONER ======= -->
<section class="py-14 px-5 bg-slate-50">
  <div class="max-w-7xl mx-auto">
    <div class="flex items-end justify-between mb-8">
      <div>
        <p class="section-label mb-1">Supplies</p>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800">Ink & Toner</h2>
        <p class="text-slate-500 text-sm mt-1">OEM & compatible cartridges for all major brands</p>
      </div>
      <a href="#" class="text-navy-600 text-sm font-semibold hover:underline flex items-center gap-1">View All <i class="ri-arrow-right-line"></i></a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

      <div class="card-lift bg-white border border-slate-200 rounded-2xl p-5 text-center">
        <div class="bg-navy-50 rounded-xl p-4 mb-3 flex justify-center">
          <i class="ri-ink-bottle-line text-navy-600 text-4xl"></i>
        </div>
        <p class="text-[10px] text-navy-600 font-bold uppercase tracking-widest">HP</p>
        <h4 class="font-bold text-slate-800 text-sm mt-0.5">HP 65XL Black Ink</h4>
        <p class="text-xs text-slate-400">High Yield</p>
        <p class="font-black text-slate-800 mt-2">$24.99</p>
        <button onclick="addToCart('HP 65XL Black Ink',24.99)" class="w-full mt-3 btn-gradient text-white text-xs font-semibold py-2 rounded-lg transition">Add to Cart</button>
      </div>

      <div class="card-lift bg-white border border-slate-200 rounded-2xl p-5 text-center">
        <div class="bg-red-50 rounded-xl p-4 mb-3 flex justify-center">
          <i class="ri-ink-bottle-line text-red-500 text-4xl"></i>
        </div>
        <p class="text-[10px] text-red-500 font-bold uppercase tracking-widest">Canon</p>
        <h4 class="font-bold text-slate-800 text-sm mt-0.5">Canon PG-245XL</h4>
        <p class="text-xs text-slate-400">Black Ink Cartridge</p>
        <p class="font-black text-slate-800 mt-2">$19.99</p>
        <button onclick="addToCart('Canon PG-245XL',19.99)" class="w-full mt-3 btn-gradient text-white text-xs font-semibold py-2 rounded-lg transition">Add to Cart</button>
      </div>

      <div class="card-lift bg-white border border-slate-200 rounded-2xl p-5 text-center">
        <div class="bg-slate-100 rounded-xl p-4 mb-3 flex justify-center">
          <i class="ri-archive-2-line text-slate-600 text-4xl"></i>
        </div>
        <p class="text-[10px] text-slate-600 font-bold uppercase tracking-widest">Brother</p>
        <h4 class="font-bold text-slate-800 text-sm mt-0.5">Brother TN760 Toner</h4>
        <p class="text-xs text-slate-400">High Yield Black</p>
        <p class="font-black text-slate-800 mt-2">$49.99</p>
        <button onclick="addToCart('Brother TN760 Toner',49.99)" class="w-full mt-3 btn-gradient text-white text-xs font-semibold py-2 rounded-lg transition">Add to Cart</button>
      </div>

      <div class="card-lift bg-white border border-slate-200 rounded-2xl p-5 text-center">
        <div class="bg-emerald-50 rounded-xl p-4 mb-3 flex justify-center">
          <i class="ri-ink-bottle-line text-emerald-600 text-4xl"></i>
        </div>
        <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest">Epson</p>
        <h4 class="font-bold text-slate-800 text-sm mt-0.5">Epson 502XL Color</h4>
        <p class="text-xs text-slate-400">Multipack Ink Set</p>
        <p class="font-black text-slate-800 mt-2">$39.99</p>
        <button onclick="addToCart('Epson 502XL Color',39.99)" class="w-full mt-3 btn-gradient text-white text-xs font-semibold py-2 rounded-lg transition">Add to Cart</button>
      </div>

    </div>
  </div>
</section>

<!-- ======= SUPPORT BANNER ======= -->
<section class="px-5 py-8 bg-slate-50">
  <div class="max-w-7xl mx-auto overflow-hidden rounded-2xl bg-navy-700 text-white">
    <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr]">
      <div class="p-7 md:p-9">
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
      <div class="bg-navy-600 min-h-[220px] flex items-center justify-center p-8">
        <i class="ri-printer-fill text-white/90" style="font-size:150px;line-height:1"></i>
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

<!-- ======= WHAT WE DO ======= -->
<section class="py-16 px-5 bg-white border-y border-slate-100">
  <div class="max-w-7xl mx-auto">
    <div class="max-w-3xl mx-auto text-center mb-11">
      <p class="section-label mb-2">What We Do</p>
      <h2 class="text-2xl md:text-4xl font-black text-slate-800 leading-tight">We make printer buying and setup simple.</h2>
      <p class="text-slate-500 text-sm md:text-base mt-4 leading-relaxed">
        At GeekSupportSales, we help customers find the right printer, ink, and toner for home, office, and business use. We provide trusted printer products, free expert setup support, warranty guidance, and ongoing technical assistance so you can start printing without stress.
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
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">All printers come with an extended 2-year warranty. We fix it or replace it — no questions asked.</p>
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
        <span class="text-sm font-semibold text-slate-700">30-Day Returns</span>
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
            <a href="tel:8019511533" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
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

<!-- ======= TESTIMONIALS ======= -->
<section class="py-14 px-5 bg-white">
  <div class="max-w-7xl mx-auto">
    <p class="section-label text-center mb-2">Reviews</p>
    <h2 class="text-2xl md:text-3xl font-black text-center text-slate-800 mb-2">What Customers Say</h2>
    <p class="text-center text-slate-500 text-sm mb-10">Trusted by thousands of home & business users</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="flex text-amber2-400 text-sm mb-3">★★★★★</div>
        <p class="text-slate-600 text-sm leading-relaxed">"Ordered the HP DeskJet and had it set up in 20 minutes with help from their tech team. Incredible service — will definitely buy again!"</p>
        <div class="flex items-center gap-3 mt-5">
          <div class="bg-navy-600 rounded-full w-9 h-9 flex items-center justify-center text-white text-xs font-bold shrink-0">JM</div>
          <div><p class="font-semibold text-sm text-slate-800">James Mitchell</p><p class="text-xs text-slate-400">Home User · Texas</p></div>
        </div>
      </div>

      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="flex text-amber2-400 text-sm mb-3">★★★★★</div>
        <p class="text-slate-600 text-sm leading-relaxed">"Best prices I found online. The Brother laser printer is perfect for our small office. Fast shipping and great packaging too."</p>
        <div class="flex items-center gap-3 mt-5">
          <div class="bg-emerald-600 rounded-full w-9 h-9 flex items-center justify-center text-white text-xs font-bold shrink-0">SR</div>
          <div><p class="font-semibold text-sm text-slate-800">Sarah Rodriguez</p><p class="text-xs text-slate-400">Small Business · Florida</p></div>
        </div>
      </div>

      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <div class="flex text-amber2-400 text-sm mb-3">★★★★★</div>
        <p class="text-slate-600 text-sm leading-relaxed">"The Epson EcoTank is a game changer. GeekSupportSales had the best deal and their support team helped me set it up wirelessly."</p>
        <div class="flex items-center gap-3 mt-5">
          <div class="bg-amber2-500 rounded-full w-9 h-9 flex items-center justify-center text-white text-xs font-bold shrink-0">DK</div>
          <div><p class="font-semibold text-sm text-slate-800">David Kim</p><p class="text-xs text-slate-400">Photographer · California</p></div>
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
      <span class="flex items-center gap-1.5"><i class="ri-star-fill text-amber2-400"></i> 4.9★ rated service</span>
    </div>
  </div>
</section>

<!-- ======= FOOTER ======= -->
<footer class="bg-slate-900 text-slate-400 pt-14 pb-8 px-5">
  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">

    <!-- Brand -->
    <div>
      <div class="flex items-center gap-2.5 mb-4">
        <div class="bg-navy-600 rounded-xl w-9 h-9 flex items-center justify-center shrink-0">
          <i class="ri-printer-fill text-white text-base"></i>
        </div>
        <div class="leading-tight">
          <span class="text-base font-black text-white">Geek</span><span class="text-base font-black text-amber2-400">Support</span><span class="text-base font-black text-slate-300">Sales</span>
        </div>
      </div>
      <p class="text-sm leading-relaxed text-slate-500">Your trusted source for printers, ink, toner, and expert tech support. We make printing easy.</p>
      <div class="flex gap-2 mt-5">
        <a href="#" class="bg-slate-800 hover:bg-navy-600 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="ri-facebook-fill text-sm"></i></a>
        <a href="#" class="bg-slate-800 hover:bg-navy-600 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="ri-twitter-x-line text-sm"></i></a>
        <a href="#" class="bg-slate-800 hover:bg-navy-600 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="ri-instagram-line text-sm"></i></a>
        <a href="#" class="bg-slate-800 hover:bg-navy-600 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="ri-youtube-line text-sm"></i></a>
      </div>
    </div>

    <!-- Products -->
    <div>
      <h4 class="text-white font-bold text-sm mb-4">Products</h4>
      <ul class="space-y-2.5 text-sm">
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Inkjet Printers</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Laser Printers</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> All-in-One Printers</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Business Printers</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Ink Cartridges</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Toner Cartridges</a></li>
      </ul>
    </div>

    <!-- Support -->
    <div>
      <h4 class="text-white font-bold text-sm mb-4">Support</h4>
      <ul class="space-y-2.5 text-sm">
        <li><a href="contact.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Printer Setup Help</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Driver Downloads</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Troubleshooting</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Warranty Claims</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Track My Order</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Returns & Refunds</a></li>
      </ul>
    </div>

    <!-- Company -->
    <div>
      <h4 class="text-white font-bold text-sm mb-4">Company</h4>
      <ul class="space-y-2.5 text-sm">
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> About Us</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Blog</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Careers</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Privacy Policy</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Terms of Service</a></li>
        <li><a href="contact.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Contact Us</a></li>
      </ul>
    </div>

  </div>

  <!-- Footer bottom -->
  <div class="max-w-7xl mx-auto pt-6 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-600">
    <p>© 2025 GeekSupportSales LLC. All rights reserved.</p>
    <div class="flex items-center gap-3 text-slate-500 text-xl">
      <i class="ri-visa-line" title="Visa"></i>
      <i class="ri-mastercard-line" title="Mastercard"></i>
      <i class="ri-paypal-line" title="PayPal"></i>
      <i class="ri-apple-line" title="Apple Pay"></i>
      <i class="ri-secure-payment-line" title="Secure"></i>
    </div>
  </div>
</footer>

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
    <button class="w-full btn-gradient text-white font-bold py-3 rounded-xl transition mb-2 flex items-center justify-center gap-2 text-sm">
      <i class="ri-lock-2-line"></i> Secure Checkout
    </button>
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
