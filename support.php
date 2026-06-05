<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Printer Support – GeekSupportSales</title>
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
    .brand-gradient { background:#2563EB }
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
      <a href="support.php" class="px-3 py-1.5 text-sm font-semibold text-navy-600 bg-navy-50 rounded-lg">Support</a>
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
    </div>

  </div>

  <!-- Sub-nav categories -->
  <div class="border-t border-slate-100 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 py-2 flex gap-5 text-xs font-semibold text-slate-500 overflow-x-auto">
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

<!-- ======= HERO ======= -->
<section class="brand-gradient text-white py-16 px-5">
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
        <div class="bg-navy-600 w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <i class="ri-chat-3-line text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">Live Chat</h3>
        <p class="text-sm text-slate-500 mb-4">Chat with an expert in real-time</p>
        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 border border-green-200 rounded-full px-3 py-1">
          <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Online Now
        </span>
      </div>

      <!-- Phone -->
      <div class="card-lift bg-amber2-50 border border-amber2-100 rounded-2xl p-6 text-center">
        <div class="bg-amber2-500 w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <i class="ri-phone-line text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">Call Us</h3>
        <p class="text-sm text-slate-500 mb-4">Talk to a tech specialist now</p>
        <a href="tel:8019511533" class="inline-block text-sm font-bold text-amber2-600 hover:underline">8019511533</a>
      </div>

      <!-- Email -->
      <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center">
        <div class="bg-slate-700 w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <i class="ri-mail-line text-white text-2xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">Email Support</h3>
        <p class="text-sm text-slate-500 mb-4">Response within 2 hours</p>
        <a href="contact.php" class="inline-block text-sm font-semibold text-navy-600 hover:underline">Send a Message</a>
      </div>

      <!-- Remote Help -->
      <div class="card-lift bg-navy-50 border border-navy-100 rounded-2xl p-6 text-center">
        <div class="bg-navy-600 w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <i class="ri-remote-control-line text-white text-2xl"></i>
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
    <p class="text-slate-500 text-sm mb-8">Select your printer brand to find the latest drivers.</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      <?php
      $brands = [
        ['HP',       'https://support.hp.com/us-en/drivers/printers',            '#1A56DB'],
        ['Canon',    'https://www.usa.canon.com/support/consumer/products/printers', '#CC0000'],
        ['Epson',    'https://epson.com/Support/sl/s',                           '#007DB8'],
        ['Brother',  'https://support.brother.com',                              '#004B8D'],
        ['Lexmark',  'https://www.lexmark.com/en_US/support/downloads.html',     '#E31837'],
        ['Xerox',    'https://www.support.xerox.com/en-US',                      '#EE3124'],
        ['Ricoh',    'https://www.ricoh-usa.com/en/support-and-download',        '#008C99'],
        ['Samsung',  'https://www.samsung.com/us/support/',                      '#1428A0'],
      ];
      foreach ($brands as [$name, $url, $color]):
      ?>
      <a href="<?= $url ?>" target="_blank" rel="noopener"
         class="card-lift bg-white border border-slate-200 rounded-2xl p-5 flex flex-col items-center gap-3 hover:border-navy-300 text-center">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-black"
             style="background:<?= $color ?>">
          <?= strtoupper(substr($name,0,2)) ?>
        </div>
        <span class="text-sm font-semibold text-slate-700"><?= $name ?></span>
        <span class="text-xs text-navy-600 font-medium flex items-center gap-1"><i class="ri-external-link-line"></i> Drivers</span>
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
         'Download the driver directly from the manufacturer\'s website (see our Downloads section above). Most modern printers can also be set up via Windows Update or macOS auto-detection without any disc.'],
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
      <a href="tel:8019511533" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold px-7 py-3 rounded-2xl transition text-sm">
        <i class="ri-phone-line"></i> Call 8019511533
      </a>
    </div>
  </div>
</section>

<!-- ======= FOOTER ======= -->
<footer class="bg-slate-900 text-slate-400 py-10 px-5">
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 text-sm">
    <div class="flex items-center gap-2.5">
      <div class="bg-navy-600 rounded-xl w-8 h-8 flex items-center justify-center">
        <i class="ri-printer-fill text-white"></i>
      </div>
      <span class="font-bold text-white">GeekSupportSales</span>
    </div>
    <div class="flex gap-6 text-xs">
      <a href="index.php" class="hover:text-white transition">Home</a>
      <a href="products.php" class="hover:text-white transition">Products</a>
      <a href="support.php" class="text-white font-semibold">Support</a>
      <a href="contact.php" class="hover:text-white transition">Contact</a>
    </div>
    <p class="text-xs">© <?= date('Y') ?> GeekSupportSales. All rights reserved.</p>
  </div>
</footer>

<script>
  function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    // close all
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  }
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
</body>
</html>
