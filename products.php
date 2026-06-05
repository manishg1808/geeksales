<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>All Printers – GeekSupportSales</title>
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
    .card-lift{transition:transform .2s ease,box-shadow .2s ease}
    .card-lift:hover{transform:translateY(-5px);box-shadow:0 18px 40px rgba(30,41,59,.14)}
    .badge-pulse{animation:bpulse 2s ease-in-out infinite}
    @keyframes bpulse{0%,100%{opacity:1}50%{opacity:.55}}
    /* Range slider */
    input[type=range]{-webkit-appearance:none;width:100%;height:4px;border-radius:4px;background:#e2e8f0;outline:none}
    input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:18px;height:18px;border-radius:50%;background:#1e293b;cursor:pointer;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.2)}
    /* Checkbox custom */
    .filter-cb:checked{accent-color:#1e293b}
    /* Quick view modal */
    #qv-modal{transition:opacity .2s ease}
    #qv-inner{transition:transform .25s ease,opacity .25s ease}
    /* Star rating */
    .stars span{color:#F97316}
    /* List view card */
    .list-card{display:flex;gap:1rem;align-items:center}
    /* Skeleton */
    .skeleton{background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
    @keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
    /* Tooltip */
    [data-tip]{position:relative}
    [data-tip]:hover::after{content:attr(data-tip);position:absolute;bottom:110%;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;font-size:11px;padding:4px 8px;border-radius:6px;white-space:nowrap;pointer-events:none;z-index:99}
    /* Active filter chip */
    .chip{display:inline-flex;align-items:center;gap:4px;background:#f1f5f9;color:#1e293b;font-size:11px;font-weight:600;padding:3px 10px;border-radius:999px}
    /* Scrollbar thin */
    .thin-scroll::-webkit-scrollbar{width:4px}
    .thin-scroll::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}
  </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased">

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
        <i class="ri-price-tag-3-line mr-1"></i>Best Price Guarantee&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-shield-check-line mr-1"></i>2-Year Warranty Included&nbsp;&nbsp;&nbsp;
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
      <div class="bg-navy-600 rounded-lg w-8 h-8 flex items-center justify-center">
        <i class="ri-printer-fill text-white text-sm"></i>
      </div>
      <span class="text-[15px] font-black tracking-tight leading-none">
        <span class="text-navy-600">Geek</span><span class="text-amber2-500">Support</span><span class="text-slate-800">Sales</span>
      </span>
    </a>

    <!-- Nav Links -->
    <nav class="hidden lg:flex items-center gap-1">
      <a href="products.php" class="px-3 py-1.5 text-sm font-semibold text-navy-600 bg-navy-50 rounded-lg">Products</a>
      <a href="support.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Support</a>
      <a href="contact.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Contact</a>
    </nav>

    <!-- Search -->
    <form data-product-search-form class="hidden md:flex flex-1 max-w-sm ml-auto">
      <div class="flex w-full h-9 rounded-lg border border-slate-200 bg-slate-50 hover:border-slate-300 focus-within:border-navy-400 focus-within:bg-white overflow-hidden transition">
        <input id="search-input" type="text" placeholder="Search printers, brand, model…" oninput="applyFilters()" class="flex-1 px-3 text-sm bg-transparent outline-none text-slate-700 placeholder-slate-400"/>
        <button type="submit" class="px-3 text-slate-400 hover:text-navy-600 transition" aria-label="Search products">
          <i class="ri-search-2-line text-base"></i>
        </button>
      </div>
    </form>

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
      <a href="products.php" class="text-navy-600 whitespace-nowrap flex items-center gap-1"><i class="ri-printer-line"></i> All Printers</a>
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

<!-- PAGE HEADER -->
<div class="bg-white border-b border-slate-100">
  <div class="max-w-7xl mx-auto px-5 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
        <a href="index.php" class="hover:text-navy-600 transition">Home</a>
        <i class="ri-arrow-right-s-line"></i>
        <span class="text-slate-600 font-semibold" id="breadcrumb-cat">All Printers</span>
      </div>
      <h1 class="text-2xl font-black text-slate-800" id="page-title">All Printers</h1>
      <p class="text-slate-400 text-xs mt-0.5" id="result-count">Showing all products</p>
    </div>
    <!-- Sort + View toggle -->
    <div class="flex items-center gap-3">
      <select id="sort-select" onchange="applyFilters()" class="border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none focus:border-navy-500 cursor-pointer">
        <option value="featured">Featured</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="rating">Top Rated</option>
        <option value="newest">Newest First</option>
        <option value="discount">Biggest Discount</option>
      </select>
      <div class="flex border border-slate-200 rounded-xl overflow-hidden">
        <button id="btn-grid" onclick="setView('grid')" class="px-3 py-2 bg-navy-600 text-white transition" data-tip="Grid View">
          <i class="ri-grid-fill text-base"></i>
        </button>
        <button id="btn-list" onclick="setView('list')" class="px-3 py-2 bg-white text-slate-400 hover:text-navy-600 transition" data-tip="List View">
          <i class="ri-list-check-2 text-base"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ACTIVE FILTER CHIPS -->
<div id="active-chips" class="max-w-7xl mx-auto px-5 py-2 flex flex-wrap gap-2 hidden"></div>

<!-- MAIN LAYOUT -->
<div class="max-w-7xl mx-auto px-5 py-6 flex gap-6">

  <!-- ===== SIDEBAR FILTERS ===== -->
  <aside class="hidden lg:block w-60 shrink-0">
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden sticky top-28">

      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <span class="font-bold text-slate-800 text-sm flex items-center gap-1.5"><i class="ri-equalizer-2-line text-navy-600"></i> Filters</span>
        <button onclick="clearAllFilters()" class="text-xs text-navy-600 hover:underline font-semibold">Clear All</button>
      </div>

      <div class="p-5 space-y-6 thin-scroll overflow-y-auto max-h-[calc(100vh-180px)]">

        <!-- Category -->
        <div>
          <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Category</p>
          <div class="space-y-2" data-product-category-filters>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="cat" value="all" class="filter-cb accent-navy-600" checked onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">All Printers</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="cat" value="inkjet" class="filter-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Inkjet</span>
              <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md">6</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="cat" value="laser" class="filter-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Laser</span>
              <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md">5</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="cat" value="allinone" class="filter-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">All-in-One</span>
              <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md">5</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="cat" value="business" class="filter-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Business</span>
              <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md">4</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="radio" name="cat" value="ink" class="filter-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Ink & Toner</span>
              <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md">4</span>
            </label>
          </div>
        </div>

        <!-- Price Range -->
        <div>
          <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Price Range</p>
          <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
            <span>$<span id="price-min-label">0</span></span>
            <span>$<span id="price-max-label">600</span></span>
          </div>
          <input type="range" id="price-max" min="0" max="600" value="600" step="10" oninput="updatePriceLabel();applyFilters()" class="w-full"/>
          <div class="flex gap-2 mt-3">
            <input type="number" id="price-from" placeholder="Min" min="0" max="600" oninput="applyFilters()" class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-navy-500"/>
            <input type="number" id="price-to" placeholder="Max" min="0" max="600" oninput="applyFilters()" class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs outline-none focus:border-navy-500"/>
          </div>
        </div>

        <!-- Brand -->
        <div>
          <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Brand</p>
          <div class="space-y-2" data-product-brand-filters>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="HP" class="filter-cb brand-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">HP</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="Canon" class="filter-cb brand-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Canon</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="Brother" class="filter-cb brand-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Brother</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="Epson" class="filter-cb brand-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Epson</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="Xerox" class="filter-cb brand-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Xerox</span>
            </label>
          </div>
        </div>

        <!-- Rating -->
        <div>
          <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Min Rating</p>
          <div class="space-y-2">
            <label class="flex items-center gap-2 cursor-pointer group">
              <input type="radio" name="rating" value="0" class="filter-cb" checked onchange="applyFilters()"/>
              <span class="text-amber2-400 text-sm">★★★★★</span><span class="text-xs text-slate-400 ml-1">Any</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer group">
              <input type="radio" name="rating" value="4" class="filter-cb" onchange="applyFilters()"/>
              <span class="text-amber2-400 text-sm">★★★★</span><span class="text-xs text-slate-400 ml-1">& up</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer group">
              <input type="radio" name="rating" value="4.5" class="filter-cb" onchange="applyFilters()"/>
              <span class="text-amber2-400 text-sm">★★★★½</span><span class="text-xs text-slate-400 ml-1">& up</span>
            </label>
          </div>
        </div>

        <!-- Features -->
        <div>
          <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Features</p>
          <div class="space-y-2">
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="wireless" class="filter-cb feat-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Wireless / Wi-Fi</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="duplex" class="filter-cb feat-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Auto Duplex</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="color" class="filter-cb feat-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Color Printing</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer group">
              <input type="checkbox" value="mobile" class="filter-cb feat-cb" onchange="applyFilters()"/>
              <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">Mobile Print</span>
            </label>
          </div>
        </div>

        <!-- On Sale -->
        <div>
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" id="sale-only" class="filter-cb" onchange="applyFilters()"/>
            <span class="text-sm font-semibold text-slate-700">On Sale Only</span>
            <span class="ml-auto bg-red-100 text-red-600 text-[10px] font-bold px-1.5 py-0.5 rounded-md">SALE</span>
          </label>
        </div>

      </div>
    </div>
  </aside>

  <!-- ===== PRODUCT GRID ===== -->
  <div class="flex-1 min-w-0">
    <!-- Mobile filter bar -->
    <div class="lg:hidden flex gap-2 mb-4">
      <button onclick="toggleMobileFilter()" class="flex items-center gap-1.5 border border-slate-200 bg-white rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:border-navy-500 hover:text-navy-600 transition">
        <i class="ri-equalizer-2-line"></i> Filters
      </button>
      <select id="sort-select-mob" onchange="document.getElementById('sort-select').value=this.value;applyFilters()" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-600 bg-white outline-none">
        <option value="featured">Featured</option>
        <option value="price-asc">Price: Low → High</option>
        <option value="price-desc">Price: High → Low</option>
        <option value="rating">Top Rated</option>
        <option value="newest">Newest</option>
        <option value="discount">Biggest Discount</option>
      </select>
    </div>

    <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5"></div>
    <div id="pagination" class="mt-8 flex flex-wrap items-center justify-center gap-2"></div>

    <!-- Empty state -->
    <div id="empty-state" class="hidden text-center py-20">
      <i class="ri-search-eye-line text-slate-200 text-7xl mb-4"></i>
      <h3 class="text-xl font-bold text-slate-500">No products found</h3>
      <p class="text-slate-400 text-sm mt-2">Try adjusting your filters or search term.</p>
      <button onclick="clearAllFilters()" class="mt-5 bg-navy-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-navy-700 transition">Clear Filters</button>
    </div>
  </div>
</div>

<!-- MOBILE FILTER DRAWER -->
<div id="mob-filter-overlay" class="fixed inset-0 bg-black/40 z-40 hidden" onclick="toggleMobileFilter()"></div>
<div id="mob-filter-drawer" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl z-50 transform translate-y-full transition-transform duration-300 max-h-[85vh] overflow-y-auto thin-scroll">
  <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white">
    <span class="font-bold text-slate-800 flex items-center gap-1.5"><i class="ri-equalizer-2-line text-navy-600"></i> Filters</span>
    <button onclick="toggleMobileFilter()" class="text-slate-400 hover:text-slate-700"><i class="ri-close-line text-xl"></i></button>
  </div>
  <div class="p-5 pb-8 text-sm text-slate-500">
    <p class="text-center text-slate-400">Use the sidebar filters on desktop for full filter options.</p>
    <div class="mt-4 space-y-3">
      <p class="font-bold text-slate-700 text-xs uppercase tracking-widest">Category</p>
      <div class="flex flex-wrap gap-2">
        <button onclick="setCatMob('all')" class="mob-cat-btn px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-navy-600 hover:text-white hover:border-navy-600 transition">All</button>
        <button onclick="setCatMob('inkjet')" class="mob-cat-btn px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-navy-600 hover:text-white hover:border-navy-600 transition">Inkjet</button>
        <button onclick="setCatMob('laser')" class="mob-cat-btn px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-navy-600 hover:text-white hover:border-navy-600 transition">Laser</button>
        <button onclick="setCatMob('allinone')" class="mob-cat-btn px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-navy-600 hover:text-white hover:border-navy-600 transition">All-in-One</button>
        <button onclick="setCatMob('business')" class="mob-cat-btn px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-navy-600 hover:text-white hover:border-navy-600 transition">Business</button>
        <button onclick="setCatMob('ink')" class="mob-cat-btn px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-navy-600 hover:text-white hover:border-navy-600 transition">Ink & Toner</button>
      </div>
    </div>
    <button onclick="toggleMobileFilter()" class="mt-6 w-full bg-navy-600 text-white py-3 rounded-xl font-bold hover:bg-navy-700 transition">Apply Filters</button>
  </div>
</div>

<!-- QUICK VIEW MODAL -->
<div id="qv-modal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 opacity-0 pointer-events-none">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeQV()"></div>
  <div id="qv-inner" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto thin-scroll transform scale-95 opacity-0">
    <button onclick="closeQV()" class="absolute top-4 right-4 z-10 bg-slate-100 hover:bg-slate-200 w-9 h-9 rounded-full flex items-center justify-center transition">
      <i class="ri-close-line text-slate-600 text-lg"></i>
    </button>
    <div id="qv-content" class="p-6 md:p-8"></div>
  </div>
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
    <button class="w-full btn-gradient text-white font-bold py-3 rounded-xl transition mb-2 flex items-center justify-center gap-2 text-sm">
      <i class="ri-lock-2-line"></i> Secure Checkout
    </button>
    <button onclick="toggleCart()" class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-2 rounded-xl transition text-sm">Continue Shopping</button>
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
<script src="js/products.js"></script>





<!-- FOOTER -->
<footer class="bg-slate-900 text-slate-400 pt-14 pb-8 px-5 mt-10">
  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">
    <div>
      <div class="flex items-center gap-2.5 mb-4">
        <div class="bg-navy-600 rounded-xl w-9 h-9 flex items-center justify-center shrink-0"><i class="ri-printer-fill text-white text-base"></i></div>
        <div><span class="text-base font-black text-white">Geek</span><span class="text-base font-black text-amber2-400">Support</span><span class="text-base font-black text-slate-300">Sales</span></div>
      </div>
      <p class="text-sm leading-relaxed text-slate-500">Your trusted source for printers, ink, toner, and expert tech support.</p>
      <div class="flex gap-2 mt-5">
        <a href="#" class="bg-slate-800 hover:bg-navy-600 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="ri-facebook-fill text-sm"></i></a>
        <a href="#" class="bg-slate-800 hover:bg-navy-600 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="ri-twitter-x-line text-sm"></i></a>
        <a href="#" class="bg-slate-800 hover:bg-navy-600 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="ri-instagram-line text-sm"></i></a>
      </div>
    </div>
    <div>
      <h4 class="text-white font-bold text-sm mb-4">Categories</h4>
      <ul class="space-y-2 text-sm">
        <li><a href="products.php?cat=inkjet" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Inkjet Printers</a></li>
        <li><a href="products.php?cat=laser" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Laser Printers</a></li>
        <li><a href="products.php?cat=allinone" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> All-in-One</a></li>
        <li><a href="products.php?cat=business" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Business</a></li>
        <li><a href="products.php?cat=ink" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Ink & Toner</a></li>
      </ul>
    </div>
    <div>
      <h4 class="text-white font-bold text-sm mb-4">Support</h4>
      <ul class="space-y-2 text-sm">
        <li><a href="contact.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Setup Help</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Driver Downloads</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Track My Order</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Returns</a></li>
      </ul>
    </div>
    <div>
      <h4 class="text-white font-bold text-sm mb-4">Contact</h4>
      <ul class="space-y-2 text-sm">
        <li class="flex items-center gap-2"><i class="ri-phone-line text-navy-400"></i> 8019511533</li>
        <li class="flex items-center gap-2"><i class="ri-mail-line text-navy-400"></i> support@geeksupportsales.com</li>
        <li class="flex items-center gap-2"><i class="ri-time-line text-navy-400"></i> Mon–Fri 8am–8pm EST</li>
      </ul>
    </div>
  </div>
  <div class="max-w-7xl mx-auto pt-6 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-600">
    <p>© 2025 GeekSupportSales LLC. All rights reserved.</p>
    <div class="flex items-center gap-3 text-slate-500 text-xl">
      <i class="ri-visa-line"></i><i class="ri-mastercard-line"></i><i class="ri-paypal-line"></i><i class="ri-apple-line"></i>
    </div>
  </div>
</footer>
</body>
</html>
