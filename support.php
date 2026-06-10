<?php require_once __DIR__ . '/admin/includes/db.php'; ?>
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
    html { scroll-behavior: smooth }
    .card-lift { transition: transform .22s ease, box-shadow .22s ease }
    .card-lift:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(30,41,59,.13) }
    .section-label { letter-spacing: .12em; font-size: .7rem; font-weight: 700; text-transform: uppercase; color: #1e293b }
    .ticker-wrap { overflow: hidden; white-space: nowrap }
    .ticker-inner { display: inline-block; animation: ticker 35s linear infinite }
    @keyframes ticker { 0% { transform: translateX(0) } 100% { transform: translateX(-50%) } }
    .brand-gradient {
      background: #1D4ED8;
    }
    .support-hero { background: linear-gradient(90deg, rgba(15,23,42,.82), rgba(29,78,216,.62)), url('IMAGE/support.jpg') center/cover no-repeat; }
    .btn-gradient { background:#F97316; color:#fff }
    .btn-gradient:hover { filter: brightness(1.05); box-shadow: 0 10px 24px rgba(249,115,22,.24) }
    input:focus, textarea:focus { outline: none; border-color: #1e293b; box-shadow: 0 0 0 3px rgba(37,99,235,.12) }
    .faq-body { display: none }
    .faq-item.open .faq-body { display: block }
    .faq-item.open .faq-icon { transform: rotate(45deg) }
    .faq-icon { transition: transform .2s ease }
  </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased">
<?php render_google_tag_manager_body(); ?>

<!-- ======= TOP BAR ======= -->
<div class="brand-gradient text-white hidden md:block">
  <div class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between text-xs">
    <div class="ticker-wrap flex-1 max-w-xl overflow-hidden">
      <div class="ticker-inner text-slate-300">
        &nbsp;&nbsp;<i class="ri-truck-line mr-1"></i>Free Shipping on orders over $99&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-tools-line mr-1"></i>Free Expert Setup on Every Printer&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-price-tag-3-line mr-1"></i>Best Price Guarantee&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-shield-check-line mr-1"></i>2-Year Warranty Included&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-truck-line mr-1"></i>Free Shipping on orders over $99&nbsp;&nbsp;·&nbsp;&nbsp;
        <i class="ri-tools-line mr-1"></i>Free Expert Setup on Every Printer&nbsp;&nbsp;&nbsp;
      </div>
    </div>
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

    <!-- Nav Links -->
    <nav class="hidden lg:flex items-center gap-1">
      <a href="products.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Products</a>
      <a href="support.php" class="px-3 py-1.5 text-sm font-semibold text-navy-600 bg-navy-50 rounded-lg">Support</a>
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

<!-- ======= HERO ======= -->
<section class="support-hero text-white py-16 px-5">
  <div class="max-w-3xl mx-auto text-center">
    <p class="section-label text-navy-200 mb-3"><i class="ri-headphone-line mr-1"></i> Printer Support Center</p>
    <h1 class="text-4xl md:text-5xl font-black mb-4 leading-tight">We're Here to Help<br/>With Your Printer</h1>
    <p class="text-navy-200 text-lg mb-8 max-w-xl mx-auto">Setup issues, driver downloads, troubleshooting — our experts have you covered 24/7.</p>
    <!-- Search bar -->
    <div class="flex w-full max-w-xl mx-auto rounded-2xl border border-white/20 overflow-hidden bg-white/10 backdrop-blur focus-within:bg-white/20 transition">
      <input id="support-search" type="text" placeholder="Search your issue (e.g. printer not connecting…)" class="flex-1 px-5 py-3.5 text-sm bg-transparent outline-none text-white placeholder-navy-200"/>
      <button class="btn-gradient px-5 text-white font-semibold text-sm transition">Search</button>
    </div>
    <!-- Quick stats -->
    <div class="flex items-center justify-center gap-8 mt-8 text-sm text-navy-200">
      <div class="flex items-center gap-2"><i class="ri-time-line text-amber2-400"></i> Avg. reply: &lt; 2 min</div>
      <div class="flex items-center gap-2"><i class="ri-star-fill text-amber2-400"></i> 4.9/5 Support Rating</div>
      <div class="flex items-center gap-2"><i class="ri-shield-check-line text-amber2-400"></i> Free for All Customers</div>
    </div>
  </div>
</section>

<!-- ======= CONTACT OPTIONS ======= -->
<section class="py-14 px-5 bg-white">
  <div class="max-w-5xl mx-auto">
    <p class="section-label text-center mb-2">Get Help Fast</p>
    <h2 class="text-2xl font-black text-center mb-8 text-slate-800">Choose How You Want Support</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

      <!-- Live Chat -->
      <div class="card-lift bg-navy-50 border border-navy-100 rounded-2xl p-6 text-center cursor-pointer hover:border-navy-300">
        <div class="flex items-center justify-center mx-auto mb-4">
          <i class="ri-chat-3-line text-navy-600 text-4xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">Live Chat</h3>
        <p class="text-sm text-slate-500 mb-4">Chat with an expert in real-time</p>
        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 border border-green-200 rounded-full px-3 py-1">
          <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Online Now
        </span>
      </div>

      <!-- Phone -->
      <div class="card-lift bg-amber2-50 border border-amber2-100 rounded-2xl p-6 text-center">
        <div class="flex items-center justify-center mx-auto mb-4">
          <i class="ri-phone-line text-amber2-500 text-4xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">Call Us</h3>
        <p class="text-sm text-slate-500 mb-4">Talk to a tech specialist now</p>
        <a href="tel:407-246-9887" class="inline-block text-sm font-bold text-amber2-600 hover:underline">407-246-9887</a>
      </div>

      <!-- Email -->
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center">
        <div class="flex items-center justify-center mx-auto mb-4">
          <i class="ri-mail-line text-slate-700 text-4xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">Email Support</h3>
        <p class="text-sm text-slate-500 mb-4">Response within 2 hours</p>
        <a href="contact.php" class="inline-block text-sm font-semibold text-navy-600 hover:underline">Send a Message</a>
      </div>

      <!-- Remote Help -->
      <div class="card-lift bg-navy-50 border border-navy-100 rounded-2xl p-6 text-center">
        <div class="flex items-center justify-center mx-auto mb-4">
          <i class="ri-remote-control-line text-navy-600 text-4xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">Remote Setup</h3>
        <p class="text-sm text-slate-500 mb-4">We connect & fix it for you</p>
        <span class="inline-block text-xs font-semibold text-navy-700 bg-navy-100 rounded-full px-3 py-1">Schedule Session</span>
      </div>

    </div>
  </div>
</section>

<!-- ======= POPULAR TOPICS ======= -->
<section class="py-14 px-5 bg-slate-50">
  <div class="max-w-5xl mx-auto">
    <p class="section-label text-center mb-2">Browse Topics</p>
    <h2 class="text-2xl font-black text-center mb-8 text-slate-800">Common Printer Issues</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">

      <?php
      $topics = [
        ['ri-wifi-line',       'navy-600',  'Wireless Setup',    '#wireless'],
        ['ri-download-line',   'green-600', 'Driver Download',   '#drivers'],
        ['ri-printer-line',    'blue-600',  'Not Printing',      '#not-printing'],
        ['ri-ink-bottle-line', 'amber2-500','Ink / Toner',       '#ink'],
        ['ri-file-damage-line','red-600',   'Paper Jam',         '#paper-jam'],
        ['ri-scan-line',       'navy-600','Scanner Issues',    '#scanner'],
      ];
      foreach ($topics as [$icon, $color, $label, $anchor]):
      ?>
      <a href="<?= $anchor ?>" class="card-lift bg-white border border-slate-200 rounded-2xl p-5 flex flex-col items-center gap-2 text-center hover:border-navy-300">
        <i class="<?= $icon ?> text-<?= $color ?> text-3xl"></i>
        <span class="text-xs font-semibold text-slate-700"><?= $label ?></span>
      </a>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<!-- ======= STEP-BY-STEP GUIDES ======= -->
<section id="wireless" class="py-14 px-5 bg-white scroll-mt-20">
  <div class="max-w-5xl mx-auto">
    <p class="section-label mb-2">Step-by-Step</p>
    <h2 class="text-2xl font-black mb-2 text-slate-800">Wireless Printer Setup</h2>
    <p class="text-slate-500 text-sm mb-8 max-w-xl">Follow these steps to connect your printer to Wi-Fi on Windows or Mac.</p>
    <div class="grid md:grid-cols-2 gap-6">
      <!-- Windows -->
      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2"><i class="ri-windows-line text-navy-600 text-lg"></i> Windows</h3>
        <ol class="space-y-3 text-sm text-slate-600">
          <li class="flex gap-3"><span class="bg-navy-600 text-white rounded-full w-6 h-6 flex items-center justify-center shrink-0 text-xs font-bold">1</span> Open <strong>Settings → Devices → Printers & Scanners</strong></li>
          <li class="flex gap-3"><span class="bg-navy-600 text-white rounded-full w-6 h-6 flex items-center justify-center shrink-0 text-xs font-bold">2</span> Click <strong>"Add a printer or scanner"</strong></li>
          <li class="flex gap-3"><span class="bg-navy-600 text-white rounded-full w-6 h-6 flex items-center justify-center shrink-0 text-xs font-bold">3</span> Select your printer from the list and follow on-screen instructions</li>
          <li class="flex gap-3"><span class="bg-navy-600 text-white rounded-full w-6 h-6 flex items-center justify-center shrink-0 text-xs font-bold">4</span> Make sure printer and computer are on the <strong>same Wi-Fi network</strong></li>
        </ol>
      </div>
      <!-- Mac -->
      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2"><i class="ri-apple-line text-slate-700 text-lg"></i> macOS</h3>
        <ol class="space-y-3 text-sm text-slate-600">
          <li class="flex gap-3"><span class="bg-navy-600 text-white rounded-full w-6 h-6 flex items-center justify-center shrink-0 text-xs font-bold">1</span> Go to <strong>System Settings → Printers & Scanners</strong></li>
          <li class="flex gap-3"><span class="bg-navy-600 text-white rounded-full w-6 h-6 flex items-center justify-center shrink-0 text-xs font-bold">2</span> Click <strong>"+"</strong> to add a new printer</li>
          <li class="flex gap-3"><span class="bg-navy-600 text-white rounded-full w-6 h-6 flex items-center justify-center shrink-0 text-xs font-bold">3</span> Select your printer and click <strong>Add</strong></li>
          <li class="flex gap-3"><span class="bg-navy-600 text-white rounded-full w-6 h-6 flex items-center justify-center shrink-0 text-xs font-bold">4</span> macOS auto-installs most drivers. If not, download from manufacturer site.</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<!-- ======= DRIVER DOWNLOADS ======= -->
<section id="drivers" class="py-14 px-5 bg-slate-50 scroll-mt-20">
  <div class="max-w-5xl mx-auto">
    <p class="section-label mb-2">Downloads</p>
    <h2 class="text-2xl font-black mb-2 text-slate-800">Driver & Software Downloads</h2>
    <p class="text-slate-500 text-sm mb-8">Select your printer brand and our support team will help with the right driver.</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      <?php
      $brands = [
        ['HP',       '#1A56DB'],
        ['Canon',    '#CC0000'],
        ['Epson',    '#007DB8'],
        ['Brother',  '#004B8D'],
        ['Lexmark',  '#E31837'],
        ['Xerox',    '#EE3124'],
        ['Ricoh',    '#008C99'],
        ['Samsung',  '#1428A0'],
      ];
      foreach ($brands as [$name, $color]):
      ?>
      <a href="contact.php"
         class="card-lift bg-white border border-slate-200 rounded-2xl p-5 flex flex-col items-center gap-3 hover:border-navy-300 text-center">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-black"
             style="background:<?= $color ?>">
          <?= strtoupper(substr($name,0,2)) ?>
        </div>
        <span class="text-sm font-semibold text-slate-700"><?= $name ?></span>
        <span class="text-xs text-navy-600 font-medium flex items-center gap-1"><i class="ri-arrow-right-line"></i> Get Help</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======= NOT PRINTING FIX ======= -->
<section id="not-printing" class="py-14 px-5 bg-white scroll-mt-20">
  <div class="max-w-5xl mx-auto">
    <p class="section-label mb-2">Troubleshoot</p>
    <h2 class="text-2xl font-black mb-2 text-slate-800">Printer Not Printing?</h2>
    <p class="text-slate-500 text-sm mb-8 max-w-xl">Try these quick fixes before calling support.</p>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php
      $fixes = [
        ['ri-refresh-line',    'Restart Printer',       'Turn off, unplug for 30 seconds, then turn back on.'],
        ['ri-wifi-off-line',   'Check Connection',      'Ensure the printer is connected to the same Wi-Fi as your computer.'],
        ['ri-printer-line',    'Set as Default',        'Go to Printers & Scanners and set this as your default printer.'],
        ['ri-file-list-line',  'Clear Print Queue',     'Open the print queue and cancel any stuck or pending jobs.'],
        ['ri-download-2-line', 'Update Drivers',        'Outdated drivers can block printing. Download the latest version.'],
        ['ri-shield-line',     'Check Firewall',        'Security software may be blocking the printer. Temporarily disable and test.'],
      ];
      foreach ($fixes as [$icon, $title, $desc]):
      ?>
      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
        <div class="flex items-center gap-3 mb-2">
          <i class="<?= $icon ?> text-navy-600 text-xl"></i>
          <h4 class="font-bold text-slate-800 text-sm"><?= $title ?></h4>
        </div>
        <p class="text-xs text-slate-500 leading-relaxed"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======= PAPER JAM ======= -->
<section id="paper-jam" class="py-14 px-5 bg-slate-50 scroll-mt-20">
  <div class="max-w-4xl mx-auto">
    <p class="section-label mb-2">Paper Jam</p>
    <h2 class="text-2xl font-black mb-8 text-slate-800">How to Clear a Paper Jam</h2>
    <div class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8">
      <ol class="space-y-4 text-sm text-slate-600">
        <li class="flex gap-4 items-start">
          <span class="bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center shrink-0 font-bold text-xs">1</span>
          <p><strong>Turn off the printer</strong> immediately. Never pull paper while the printer is on.</p>
        </li>
        <li class="flex gap-4 items-start">
          <span class="bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center shrink-0 font-bold text-xs">2</span>
          <p><strong>Open all access panels</strong> — front, rear, and top (varies by model).</p>
        </li>
        <li class="flex gap-4 items-start">
          <span class="bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center shrink-0 font-bold text-xs">3</span>
          <p><strong>Gently pull out the jammed paper</strong> in the direction of the paper path. Avoid tearing.</p>
        </li>
        <li class="flex gap-4 items-start">
          <span class="bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center shrink-0 font-bold text-xs">4</span>
          <p><strong>Check for small torn pieces</strong> inside the printer. Remove any debris carefully.</p>
        </li>
        <li class="flex gap-4 items-start">
          <span class="bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center shrink-0 font-bold text-xs">5</span>
          <p><strong>Reload paper correctly</strong> — make sure it's aligned with the guides and not overfilled.</p>
        </li>
        <li class="flex gap-4 items-start">
          <span class="bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center shrink-0 font-bold text-xs">6</span>
          <p><strong>Turn the printer back on</strong> and try a test print.</p>
        </li>
      </ol>
    </div>
  </div>
</section>

<!-- ======= FAQ ======= -->
<section class="py-14 px-5 bg-white">
  <div class="max-w-3xl mx-auto">
    <p class="section-label text-center mb-2">FAQ</p>
    <h2 class="text-2xl font-black text-center mb-8 text-slate-800">Frequently Asked Questions</h2>
    <div class="space-y-3" id="faq-list">

      <?php
      $faqs = [
        ['Why is my printer offline even though it is turned on?',
         'Most common cause is a lost Wi-Fi connection or the printer is set to "Use Printer Offline" mode. Go to Devices > Printers & Scanners, right-click your printer, and uncheck "Use Printer Offline". Then restart the printer and your router.'],
        ['How do I reset my printer to factory settings?',
         'The exact steps vary by brand. Generally: hold the Wi-Fi or Reset button for 5-10 seconds. For HP, try pressing the Wireless + Cancel buttons simultaneously. Check your printer manual or contact us for model-specific steps.'],
        ['Why is my print quality poor or streaky?',
         'This is usually caused by low ink/toner levels or clogged print heads. Run the "Clean Print Heads" utility from your printer software. If that does not help, replace the ink or toner cartridge.'],
        ['Can I use third-party ink cartridges?',
         'Yes, but with caution. Third-party cartridges may void your warranty and can sometimes cause quality issues or printer errors. We recommend OEM (original) cartridges for reliable results. We stock genuine cartridges in our store.'],
        ['How do I install my printer without the CD?',
         'Contact our support team with your printer model and we will guide you to the correct driver. Most modern printers can also be set up via Windows Update or macOS auto-detection without any disc.'],
        ['My printer says "low ink" but I just installed a new cartridge. What do I do?',
         'Try removing the cartridge and reinserting it firmly. Make sure the protective tape was fully removed. Some printers also need a cartridge reset — check your model\'s manual for the exact steps.'],
      ];
      foreach ($faqs as $i => [$q, $a]):
      ?>
      <div class="faq-item border border-slate-200 rounded-2xl overflow-hidden" data-index="<?= $i ?>">
        <button class="w-full text-left px-5 py-4 flex items-center justify-between gap-3 font-semibold text-slate-700 text-sm hover:bg-slate-50 transition" onclick="toggleFaq(this)">
          <?= $q ?>
          <i class="ri-add-line faq-icon text-navy-600 text-lg shrink-0"></i>
        </button>
        <div class="faq-body px-5 pb-4 text-sm text-slate-500 leading-relaxed border-t border-slate-100">
          <p class="pt-3"><?= $a ?></p>
        </div>
      </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<!-- ======= CTA BANNER ======= -->
<section class="py-14 px-5 brand-gradient text-white">
  <div class="max-w-3xl mx-auto text-center">
    <i class="ri-customer-service-2-line text-5xl text-amber2-400 mb-4 block"></i>
    <h2 class="text-3xl font-black mb-3">Still Need Help?</h2>
    <p class="text-navy-200 text-base mb-8">Our certified printer technicians are available 24/7. Don't struggle alone.</p>
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <a href="contact.php" class="inline-flex items-center gap-2 btn-gradient text-white font-bold px-7 py-3 rounded-2xl transition text-sm">
        <i class="ri-chat-3-line"></i> Start Live Chat
      </a>
      <a href="tel:407-246-9887" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold px-7 py-3 rounded-2xl transition text-sm">
        <i class="ri-phone-line"></i> Call 407-246-9887
      </a>
    </div>
  </div>
</section>

<!-- ======= FOOTER ======= -->
<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
  function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const supportSearchInput = document.getElementById('support-search');
    const supportSearchBtn = supportSearchInput ? supportSearchInput.nextElementSibling : null;

    if (supportSearchInput) {
      const faqItems = document.querySelectorAll('.faq-item');
      
      const performSupportSearch = () => {
        const query = supportSearchInput.value.toLowerCase().trim();
        
        // 1. FAQ Accordion Filtering
        if (query === '') {
          faqItems.forEach(item => {
            item.style.display = '';
            item.classList.remove('open');
          });
        } else {
          let firstMatch = true;
          faqItems.forEach(item => {
            const questionText = item.querySelector('button').textContent.toLowerCase();
            const answerText = item.querySelector('.faq-body').textContent.toLowerCase();
            
            if (questionText.includes(query) || answerText.includes(query)) {
              item.style.display = '';
              if (firstMatch) {
                item.classList.add('open');
                firstMatch = false;
              } else {
                item.classList.remove('open');
              }
            } else {
              item.style.display = 'none';
              item.classList.remove('open');
            }
          });
        }

        // 2. Auto-scroll to sections based on search keywords
        if (query.length >= 3) {
          let targetSection = null;
          if (query.includes('wifi') || query.includes('wireless') || query.includes('connect')) {
            targetSection = document.getElementById('wireless');
          } else if (query.includes('driver') || query.includes('download') || query.includes('software') || 
                     ['hp', 'canon', 'epson', 'brother', 'lexmark', 'xerox', 'ricoh', 'samsung'].some(brand => query.includes(brand))) {
            targetSection = document.getElementById('drivers');
          } else if (query.includes('not printing') || query.includes('offline') || query.includes('troubleshoot') || 
                     query.includes('restart') || query.includes('queue')) {
            targetSection = document.getElementById('not-printing');
          } else if (query.includes('jam') || query.includes('stuck') || query.includes('paper')) {
            targetSection = document.getElementById('paper-jam');
          }

          if (targetSection) {
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      };

      // Listen to input event (debounced using the global window.debounce)
      supportSearchInput.addEventListener('input', window.debounce(performSupportSearch, 300));

      // Listen to keydown Enter
      supportSearchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          performSupportSearch();
        }
      });

      // Listen to button click
      if (supportSearchBtn) {
        supportSearchBtn.addEventListener('click', performSupportSearch);
      }
    }
  });
</script>

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

<script src="js/wishlist.js"></script>
<script src="js/taxonomy.js"></script>
</body>
</html>


