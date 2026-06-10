<?php 
require_once __DIR__ . '/admin/includes/db.php'; 

// Fetch product dynamically for server-side SEO tags
$product = null;
$productId = 1; // Default fallback
$metaTitle = '';
$metaDesc = '';
$metaKeywords = '';

$db = db();
$idParam = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slugParam = isset($_GET['slug']) ? trim($_GET['slug']) : '';

try {
    if ($idParam > 0) {
        $stmt = $db->prepare('SELECT * FROM products WHERE id = ? AND status = "active"');
        $stmt->execute([$idParam]);
        $product = $stmt->fetch();
    } elseif ($slugParam !== '') {
        $stmt = $db->prepare('SELECT * FROM products WHERE slug = ? AND status = "active"');
        $stmt->execute([$slugParam]);
        $product = $stmt->fetch();
    }

    if ($product) {
        $productId = (int)$product['id'];
        $metaTitle = $product['meta_title'] ?: $product['name'] . ' - Geek Support LLc';
        $metaDesc = $product['meta_description'] ?: ($product['short_description'] ?: $product['description']);
        $metaKeywords = $product['meta_keywords'] ?: ($settings['default_meta_keywords'] ?? '');
    }
} catch (Throwable $e) {
    // ignore
}

// Fallback to page SEO if product not found
if (!$product) {
    $seo = get_page_seo('product-detail.php');
    $metaTitle = $seo['title'];
    $metaDesc = $seo['description'];
    $metaKeywords = $seo['keywords'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" type="image/svg+xml" href="IMAGE/geeksupport_unique_simple_icon.svg">
  <title><?php echo e($metaTitle); ?></title>
  <meta name="description" content="<?php echo e($metaDesc); ?>" />
  <meta name="keywords" content="<?php echo e($metaKeywords); ?>" />
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
            amber2:{ 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C',800:'#9A3412',900:'#7C2D12' },
          },
          fontFamily:{ sans:['Raleway','system-ui','sans-serif'] },
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
  <style>
    html{scroll-behavior:smooth}
    .ticker-wrap{overflow:hidden;white-space:nowrap}
    .ticker-inner{display:inline-block;animation:ticker 35s linear infinite}
    @keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
    .brand-gradient{background:#2563EB}
    .btn-gradient{background:#F97316;color:#fff}
    .btn-gradient:hover{filter:brightness(1.05);box-shadow:0 10px 24px rgba(249,115,22,.24)}
    .badge-pulse{animation:bpulse 2s ease-in-out infinite}
    @keyframes bpulse{0%,100%{opacity:1}50%{opacity:.55}}
    .tab-btn.active{color:#1e293b;border-bottom:2px solid #1e293b}
    .tab-content{display:none}.tab-content.active{display:block}
    .thumb-img{transition:all .15s;cursor:pointer;border:2px solid transparent}
    .thumb-img.active,.thumb-img:hover{border-color:#1e293b}
    .qty-btn{width:36px;height:36px;display:flex;align-items:center;justify-content:center;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer;font-size:1.1rem;font-weight:700;color:#334155;transition:all .15s}
    .qty-btn:hover{background:#f8fafc;border-color:#1e293b;color:#1e293b}
    .card-lift{transition:transform .2s ease,box-shadow .2s ease}
    .card-lift:hover{transform:translateY(-4px);box-shadow:0 14px 36px rgba(30,41,59,.13)}
    .thin-scroll::-webkit-scrollbar{width:4px}
    .thin-scroll::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}
    /* Image zoom */
    #main-img-wrap{overflow:hidden;border-radius:1.5rem}
    #main-img-wrap:hover #main-img{transform:scale(1.06)}
    #main-img{transition:transform .4s ease}
    /* Review stars input */
    .star-input{display:flex;flex-direction:row-reverse;gap:4px}
    .star-input input{display:none}
    .star-input label{font-size:1.5rem;color:#e2e8f0;cursor:pointer;transition:color .1s}
    .star-input input:checked ~ label,.star-input label:hover,.star-input label:hover ~ label{color:#F97316}
    /* Sticky buy box */
    @media(min-width:1024px){.sticky-buy{position:sticky;top:90px}}
  </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased">
<?php render_google_tag_manager_body(); ?>

<!-- TOP BAR -->
<div class="brand-gradient text-white hidden md:block">
  <div class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between text-xs">
    <div class="ticker-wrap flex-1 max-w-xl overflow-hidden">
      <div class="ticker-inner text-slate-300">
        &nbsp;&nbsp;<i class="ri-truck-line mr-1"></i>Free Shipping on orders over $99&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-tools-line mr-1"></i>Free Expert Setup on Every Printer&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-price-tag-3-line mr-1"></i>Best Price Guarantee&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-shield-check-line mr-1"></i>2-Year Warranty Included&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-truck-line mr-1"></i>Free Shipping on orders over $99&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-tools-line mr-1"></i>Free Expert Setup on Every Printer&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-price-tag-3-line mr-1"></i>Best Price Guarantee&nbsp;&nbsp;&nbsp;
      </div>
    </div>
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

<!-- NAVBAR -->
<header class="bg-white border-b border-slate-100 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 h-16 flex items-center gap-8">

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
    <div class="flex items-center gap-1 shrink-0">
      <button id="wl-btn" onclick="toggleWishlistDrawer()" class="relative w-9 h-9 flex items-center justify-center text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
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

<!-- BREADCRUMB -->
<div class="bg-white border-b border-slate-100">
  <div class="max-w-7xl mx-auto px-5 py-3 flex items-center gap-2 text-xs text-slate-400">
    <a href="index.php" class="hover:text-navy-600 transition">Home</a>
    <i class="ri-arrow-right-s-line"></i>
    <a href="products.php" class="hover:text-navy-600 transition">Products</a>
    <i class="ri-arrow-right-s-line"></i>
    <span id="bc-cat" class="hover:text-navy-600 transition cursor-pointer">Inkjet</span>
    <i class="ri-arrow-right-s-line"></i>
    <span id="bc-name" class="text-slate-600 font-semibold truncate max-w-xs">HP DeskJet 4155e</span>
  </div>
</div>

<!-- MAIN PRODUCT SECTION -->
<div class="max-w-7xl mx-auto px-5 py-8">
  <div class="flex flex-col lg:flex-row gap-10">

    <!-- LEFT: Image Gallery -->
    <div class="lg:w-[480px] shrink-0">
      <!-- Main image -->
      <div id="main-img-wrap" class="bg-white border border-slate-200 rounded-3xl flex items-center justify-center h-80 md:h-96 relative">
        <i id="main-img" class="ri-printer-fill" style="font-size:180px;line-height:1"></i>
        <span id="main-badge" class="absolute top-4 left-4 text-white text-xs font-bold px-3 py-1 rounded-lg badge-pulse hidden"></span>
        <button id="main-wl" onclick="toggleWishlist()" class="absolute top-4 right-4 w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center shadow hover:border-red-300 transition">
          <i class="ri-heart-3-line text-slate-400 text-lg"></i>
        </button>
        <!-- Zoom hint -->
        <div class="absolute bottom-3 right-3 bg-black/30 text-white text-[10px] px-2 py-1 rounded-lg flex items-center gap-1 pointer-events-none">
          <i class="ri-zoom-in-line"></i> Hover to zoom
        </div>
      </div>
      <!-- Thumbnails -->
      <div class="flex gap-3 mt-4" id="thumbs">
        <div class="thumb-img active flex-1 bg-white border rounded-xl flex items-center justify-center h-20 p-2">
          <i class="ri-printer-fill text-4xl" id="thumb-icon-1"></i>
        </div>
        <div class="thumb-img flex-1 bg-slate-50 border rounded-xl flex items-center justify-center h-20 p-2" onclick="switchThumb(2)">
          <i class="ri-printer-line text-4xl text-slate-400"></i>
        </div>
        <div class="thumb-img flex-1 bg-slate-50 border rounded-xl flex items-center justify-center h-20 p-2" onclick="switchThumb(3)">
          <i class="ri-ink-bottle-line text-4xl text-slate-400"></i>
        </div>
        <div class="thumb-img flex-1 bg-slate-50 border rounded-xl flex items-center justify-center h-20 p-2" onclick="switchThumb(4)">
          <i class="ri-wifi-line text-4xl text-slate-400"></i>
        </div>
      </div>
      <!-- Trust badges row -->
      <div class="mt-5 grid grid-cols-2 gap-3">
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2.5">
          <i class="ri-shield-check-line text-navy-600 text-lg"></i>
          <div><p class="text-xs font-bold text-slate-700">2-Year Warranty</p><p class="text-[10px] text-slate-400">Full coverage</p></div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2.5">
          <i class="ri-truck-line text-navy-600 text-lg"></i>
          <div><p class="text-xs font-bold text-slate-700">Free Shipping</p><p class="text-[10px] text-slate-400">Orders over $99</p></div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2.5">
          <i class="ri-refresh-line text-navy-600 text-lg"></i>
          <div><p class="text-xs font-bold text-slate-700">30-Day Returns</p><p class="text-[10px] text-slate-400">Hassle-free</p></div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2.5">
          <i class="ri-headphone-line text-navy-600 text-lg"></i>
          <div><p class="text-xs font-bold text-slate-700">Free Setup</p><p class="text-[10px] text-slate-400">Expert support</p></div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Product Info + Buy Box -->
    <div class="flex-1 min-w-0">
      <div class="flex flex-col lg:flex-row gap-8">

        <!-- Product Info -->
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-2">
            <span id="pd-brand" class="text-xs font-bold uppercase tracking-widest text-navy-600"></span>
            <span id="pd-badge" class="hidden text-white text-[10px] font-bold px-2 py-0.5 rounded-md"></span>
            <span id="pd-new" class="hidden bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-md">NEW</span>
          </div>
          <h1 id="pd-name" class="text-2xl md:text-3xl font-black text-slate-800 leading-tight"></h1>
          <p id="pd-cat" class="text-sm text-slate-400 mt-1"></p>

          <!-- Rating -->
          <div class="flex items-center gap-3 mt-3">
            <div id="pd-stars" class="flex items-center gap-0.5 text-amber2-400 text-base"></div>
            <span id="pd-rating-num" class="text-sm font-bold text-slate-700"></span>
            <span id="pd-reviews" class="text-sm text-slate-400"></span>
            <a href="#reviews" class="text-xs text-navy-600 hover:underline font-semibold">Read reviews</a>
          </div>

          <!-- Price -->
          <div class="flex items-baseline gap-3 mt-4">
            <span id="pd-price" class="text-3xl font-black text-slate-800"></span>
            <span id="pd-old-price" class="text-base text-slate-400 line-through hidden"></span>
            <span id="pd-save" class="hidden bg-emerald-100 text-emerald-700 text-sm font-bold px-2 py-0.5 rounded-lg"></span>
          </div>
          <p class="text-xs text-slate-400 mt-1">Inclusive of all taxes. Free shipping on this item.</p>

          <!-- Description -->
          <p id="pd-desc" class="text-slate-600 text-sm leading-relaxed mt-4 max-w-lg"></p>

          <!-- Key features -->
          <div id="pd-features" class="flex flex-wrap gap-2 mt-4"></div>

          <!-- PPM if applicable -->
          <div id="pd-ppm-wrap" class="mt-4 hidden">
            <p class="text-xs text-slate-500 font-semibold mb-2">Print Speed</p>
            <div class="flex items-center gap-3">
              <div class="flex-1 bg-slate-100 rounded-full h-2">
                <div id="pd-ppm-bar" class="bg-navy-600 h-2 rounded-full transition-all duration-700"></div>
              </div>
              <span id="pd-ppm-label" class="text-xs font-bold text-navy-600 whitespace-nowrap"></span>
            </div>
          </div>

          <!-- Tabs: Specs / In Box / Reviews -->
          <div class="mt-8">
            <div class="flex border-b border-slate-200 gap-6">
              <button class="tab-btn active pb-2 text-sm font-semibold text-slate-500 transition" onclick="switchTab('specs',this)">Specifications</button>
              <button class="tab-btn pb-2 text-sm font-semibold text-slate-500 transition" onclick="switchTab('inbox',this)">In the Box</button>
              <button class="tab-btn pb-2 text-sm font-semibold text-slate-500 transition" onclick="switchTab('reviews',this)" id="reviews-tab">Reviews</button>
            </div>

            <!-- Specs tab -->
            <div id="tab-specs" class="tab-content active pt-5">
              <div id="specs-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
            </div>

            <!-- In the Box tab -->
            <div id="tab-inbox" class="tab-content pt-5">
              <ul id="inbox-list" class="space-y-2 text-sm text-slate-600"></ul>
            </div>

            <!-- Reviews tab -->
            <div id="tab-reviews" class="tab-content pt-5" id="reviews">
              <div class="flex flex-col md:flex-row gap-8">
                <!-- Rating summary -->
                <div class="md:w-48 shrink-0 text-center">
                  <div id="rev-big" class="text-5xl font-black text-slate-800"></div>
                  <div id="rev-stars" class="flex justify-center text-amber2-400 text-lg mt-1"></div>
                  <p id="rev-count" class="text-xs text-slate-400 mt-1"></p>
                  <div class="mt-4 space-y-1.5" id="rev-bars"></div>
                </div>
                <!-- Review list -->
                <div class="flex-1 space-y-4" id="review-list"></div>
              </div>
              <!-- Write review -->
              <div class="mt-8 bg-slate-50 border border-slate-200 rounded-2xl p-5">
                <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2"><i class="ri-edit-2-line text-navy-600"></i> Write a Review</h4>
                <div class="star-input mb-3" id="star-input">
                  <input type="radio" name="rev-star" id="s5" value="5"/><label for="s5"><i class="ri-star-fill"></i></label>
                  <input type="radio" name="rev-star" id="s4" value="4"/><label for="s4"><i class="ri-star-fill"></i></label>
                  <input type="radio" name="rev-star" id="s3" value="3"/><label for="s3"><i class="ri-star-fill"></i></label>
                  <input type="radio" name="rev-star" id="s2" value="2"/><label for="s2"><i class="ri-star-fill"></i></label>
                  <input type="radio" name="rev-star" id="s1" value="1"/><label for="s1"><i class="ri-star-fill"></i></label>
                </div>
                <input type="text" placeholder="Your name" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm mb-3 outline-none focus:border-navy-500 bg-white" id="rev-name"/>
                <textarea rows="3" placeholder="Share your experience with this product…" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm mb-3 outline-none focus:border-navy-500 bg-white resize-none" id="rev-text"></textarea>
                <button onclick="submitReview()" class="btn-gradient text-white font-bold px-6 py-2.5 rounded-xl transition text-sm flex items-center gap-2">
                  <i class="ri-send-plane-line"></i> Submit Review
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- BUY BOX (sticky) -->
        <div class="lg:w-72 shrink-0">
          <div class="sticky-buy bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
              <i class="ri-checkbox-circle-fill text-emerald-500"></i>
              <span class="text-sm font-bold text-emerald-600">In Stock</span>
              <span class="text-xs text-slate-400 ml-auto">SKU: GSS-<span id="buy-sku"></span></span>
            </div>
            <div class="flex items-baseline gap-2 mt-2 mb-4">
              <span id="buy-price" class="text-2xl font-black text-slate-800"></span>
              <span id="buy-old" class="text-sm text-slate-400 line-through hidden"></span>
            </div>

            <!-- Qty -->
            <p class="text-xs font-semibold text-slate-500 mb-2">Quantity</p>
            <div class="flex items-center gap-3 mb-4">
              <button class="qty-btn" onclick="changeQty(-1)"><i class="ri-subtract-line"></i></button>
              <span id="qty-display" class="text-lg font-black text-slate-800 w-8 text-center">1</span>
              <button class="qty-btn" onclick="changeQty(1)"><i class="ri-add-line"></i></button>
              <span class="text-xs text-slate-400 ml-2">Max 10</span>
            </div>

            <!-- CTAs -->
            <button id="atc-btn" onclick="addToCartDetail()" class="w-full btn-gradient text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm mb-3">
              <i class="ri-shopping-cart-2-line"></i> Add to Cart
            </button>
            <button onclick="buyNow()" class="w-full btn-gradient text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm mb-4">
              <i class="ri-flashlight-line"></i> Buy Now
            </button>

            <!-- Delivery info -->
            <div class="space-y-2.5 text-xs text-slate-500 border-t border-slate-100 pt-4">
              <div class="flex items-center gap-2"><i class="ri-truck-line text-navy-500 text-base"></i><span><strong class="text-slate-700">Free delivery</strong> by <span id="delivery-date" class="text-navy-600 font-semibold"></span></span></div>
              <div class="flex items-center gap-2"><i class="ri-map-pin-2-line text-navy-500 text-base"></i><span>Deliver to <strong class="text-slate-700">Dallas, TX 75201</strong></span></div>
              <div class="flex items-center gap-2"><i class="ri-refresh-line text-navy-500 text-base"></i><span>Free 30-day returns</span></div>
              <div class="flex items-center gap-2"><i class="ri-lock-2-line text-navy-500 text-base"></i><span>Secure checkout</span></div>
            </div>

            <!-- Share -->
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-100">
              <span class="text-xs text-slate-400 font-semibold">Share:</span>
              <a href="#" class="w-7 h-7 bg-slate-100 hover:bg-navy-600 hover:text-white rounded-lg flex items-center justify-center text-slate-500 transition text-xs"><i class="ri-facebook-fill"></i></a>
              <a href="#" class="w-7 h-7 bg-slate-100 hover:bg-navy-600 hover:text-white rounded-lg flex items-center justify-center text-slate-500 transition text-xs"><i class="ri-twitter-x-line"></i></a>
              <a href="#" class="w-7 h-7 bg-slate-100 hover:bg-navy-600 hover:text-white rounded-lg flex items-center justify-center text-slate-500 transition text-xs"><i class="ri-link"></i></a>
            </div>
          </div>

          <!-- Support card -->
          <div class="mt-4 bg-navy-50 border border-navy-100 rounded-2xl p-4">
            <p class="text-xs font-bold text-navy-700 flex items-center gap-1.5 mb-2"><i class="ri-headphone-line"></i> Need Help Choosing?</p>
            <p class="text-xs text-slate-500 leading-relaxed">Our printer experts are available 24/7 to help you pick the right model.</p>
            <a href="contact.php" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-navy-600 hover:underline"><i class="ri-chat-3-line"></i> Chat with an Expert</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- RELATED PRODUCTS -->
<div class="max-w-7xl mx-auto px-5 py-10 border-t border-slate-200">
  <h2 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2"><i class="ri-layout-grid-line text-navy-600"></i> You May Also Like</h2>
  <div id="related-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
</div>

<!-- WISHLIST DRAWER -->
<div id="wl-drawer" class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
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

<!-- CART SIDEBAR -->
<div id="cart-sidebar" class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
  <div class="flex items-center justify-between p-5 border-b bg-navy-700 text-white">
    <h3 class="font-bold flex items-center gap-2"><i class="ri-shopping-cart-2-line text-lg"></i> Your Cart</h3>
    <button onclick="toggleCart()" class="text-white hover:text-amber2-400 transition text-xl"><i class="ri-close-line"></i></button>
  </div>
  <div id="cart-items" class="flex-1 overflow-y-auto p-4 space-y-3 thin-scroll">
    <div class="text-center mt-12"><i class="ri-shopping-cart-2-line text-slate-200 text-5xl"></i><p class="text-slate-400 text-sm mt-3">Your cart is empty</p></div>
  </div>
  <div class="p-5 border-t bg-slate-50">
    <div class="flex justify-between font-bold text-base mb-4 text-slate-800">
      <span>Total:</span><span id="cart-total" class="text-navy-600">$0.00</span>
    </div>
    <a href="checkout.php" id="checkout-link" class="w-full btn-gradient text-white font-bold py-3 rounded-xl transition mb-2 flex items-center justify-center gap-2 text-sm">
      <i class="ri-lock-2-line"></i> Proceed to Checkout
    </a>
    <button onclick="toggleCart()" class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-2 rounded-xl transition text-sm mt-2">Continue Shopping</button>
  </div>
</div>
<div id="cart-overlay" class="fixed inset-0 bg-black/40 z-40 hidden" onclick="toggleCart()"></div>

<!-- TOAST -->
<div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[70] bg-slate-800 text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-xl flex items-center gap-2 opacity-0 pointer-events-none transition-all duration-300 translate-y-4">
  <i class="ri-checkbox-circle-fill text-emerald-400 text-base"></i>
  <span id="toast-msg">Added to cart!</span>
</div>

<!-- BACK TO TOP -->
<button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="fixed bottom-6 right-6 btn-gradient text-white w-11 h-11 rounded-full shadow-lg transition z-30 flex items-center justify-center">
  <i class="ri-arrow-up-line text-lg"></i>
</button>

<script src="js/wishlist.js"></script>
<script src="js/taxonomy.js"></script>
<script>
  window.productId = <?php echo $productId; ?>;
</script>
<script src="js/product-detail.js"></script>



<!-- FOOTER -->
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
