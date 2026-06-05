<?php
// Backend logic has been moved to api/leads.php
// This file now serves only the frontend HTML.
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us – GeekSupportSales</title>
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
    .brand-gradient,.hero-bg{background:#2563EB}
    .btn-gradient{background:#F97316;color:#fff}
    .btn-gradient:hover{filter:brightness(1.05);box-shadow:0 10px 24px rgba(249,115,22,.24)}
    .section-label{letter-spacing:.12em;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#1e293b}
    .card-lift{transition:transform .22s ease,box-shadow .22s ease}
    .card-lift:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(30,41,59,.12)}
    input:focus,textarea:focus,select:focus{outline:none;border-color:#1e293b;box-shadow:0 0 0 3px rgba(37,99,235,.12)}
    .ticker-wrap{overflow:hidden;white-space:nowrap}
    .ticker-inner{display:inline-block;animation:ticker 35s linear infinite}
    @keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
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
      <a href="support.php" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-navy-600 hover:bg-navy-50 rounded-lg transition">Support</a>
      <a href="contact.php" class="px-3 py-1.5 text-sm font-semibold text-navy-600 bg-navy-50 rounded-lg">Contact</a>
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
      <a href="contact.php" class="text-navy-600 whitespace-nowrap flex items-center gap-1"><i class="ri-headphone-line"></i> Tech Support</a>
    </div>
  </div>
</header>

<!-- ======= PAGE HERO ======= -->
<section class="hero-bg text-white py-14 px-5">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/25 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-5">
      <i class="ri-customer-service-2-line text-amber2-400"></i> We're Here to Help
    </span>
    <h1 class="text-3xl md:text-5xl font-black leading-tight">
      Contact <span class="text-amber2-400">GeekSupportSales</span>
    </h1>
    <p class="mt-4 text-blue-100 text-base max-w-xl mx-auto leading-relaxed">
      Questions about a printer? Need setup help? Want to track an order? Our expert team is ready — reach us any way you prefer.
    </p>
    <!-- Breadcrumb -->
    <div class="mt-6 flex items-center justify-center gap-2 text-xs text-blue-200">
      <a href="index.php" class="hover:text-white transition">Home</a>
      <i class="ri-arrow-right-s-line"></i>
      <span class="text-white font-semibold">Contact Us</span>
    </div>
  </div>
</section>

<!-- ======= CONTACT CHANNELS ======= -->
<section class="py-12 px-5 bg-white border-b border-slate-100">
  <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

    <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center">
      <div class="bg-navy-600 rounded-2xl w-14 h-14 flex items-center justify-center mx-auto mb-4">
        <i class="ri-phone-line text-white text-2xl"></i>
      </div>
      <h3 class="font-bold text-slate-800">Call Us</h3>
      <p class="text-navy-600 font-bold mt-2 text-sm">8019511533</p>
      <p class="text-xs text-slate-400 mt-1">Mon–Fri · 8am–8pm EST</p>
      <a href="tel:8019511533" class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-navy-600 hover:underline">
        <i class="ri-phone-fill"></i> Call Now
      </a>
    </div>

    <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center">
      <div class="bg-emerald-600 rounded-2xl w-14 h-14 flex items-center justify-center mx-auto mb-4">
        <i class="ri-chat-3-line text-white text-2xl"></i>
      </div>
      <h3 class="font-bold text-slate-800">Live Chat</h3>
      <p class="text-emerald-600 font-bold mt-2 text-sm">Available 24 / 7</p>
      <p class="text-xs text-slate-400 mt-1">Avg. response: under 2 min</p>
      <button class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:underline">
        <i class="ri-chat-smile-3-line"></i> Start Chat
      </button>
    </div>

    <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center">
      <div class="bg-amber2-500 rounded-2xl w-14 h-14 flex items-center justify-center mx-auto mb-4">
        <i class="ri-mail-send-line text-white text-2xl"></i>
      </div>
      <h3 class="font-bold text-slate-800">Email Us</h3>
      <p class="text-amber2-600 font-bold mt-2 text-sm">support@geeksupportsales.com</p>
      <p class="text-xs text-slate-400 mt-1">Reply within 2 hours</p>
      <a href="mailto:support@geeksupportsales.com" class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-amber2-600 hover:underline">
        <i class="ri-mail-line"></i> Send Email
      </a>
    </div>

    <div class="card-lift bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center">
      <div class="bg-slate-700 rounded-2xl w-14 h-14 flex items-center justify-center mx-auto mb-4">
        <i class="ri-map-pin-2-line text-white text-2xl"></i>
      </div>
      <h3 class="font-bold text-slate-800">Visit Us</h3>
      <p class="text-slate-700 font-bold mt-2 text-sm">123 Printer Ave, Suite 400</p>
      <p class="text-xs text-slate-400 mt-1">Dallas, TX 75201</p>
      <a href="#map" class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:underline">
        <i class="ri-map-2-line"></i> Get Directions
      </a>
    </div>

  </div>
</section>

<!-- ======= CONTACT FORM + INFO ======= -->
<section id="contact-form" class="py-14 px-5">
  <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-10">

    <!-- Form — 3 cols -->
    <div class="lg:col-span-3 bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
      <p class="section-label mb-2">Send a Message</p>
      <h2 class="text-2xl font-black text-slate-800 mb-1">How Can We Help?</h2>
      <p class="text-slate-500 text-sm mb-7">Fill out the form and our team will get back to you within 2 hours.</p>

      <form id="contact-form-el" onsubmit="handleSubmit(event)" class="space-y-5">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">First Name <span class="text-red-400">*</span></label>
            <input type="text" name="first_name" required placeholder="John" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 bg-slate-50 transition"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Last Name <span class="text-red-400">*</span></label>
            <input type="text" name="last_name" required placeholder="Smith" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 bg-slate-50 transition"/>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email Address <span class="text-red-400">*</span></label>
            <input type="email" name="email" required placeholder="john@example.com" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 bg-slate-50 transition"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Phone Number</label>
            <input type="tel" name="phone" placeholder="+1 (555) 000-0000" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 bg-slate-50 transition"/>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Topic <span class="text-red-400">*</span></label>
          <select name="topic" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-slate-50 transition">
            <option value="" disabled selected>Select a topic…</option>
            <option>Printer Purchase Inquiry</option>
            <option>Order Status / Tracking</option>
            <option>Printer Setup & Installation</option>
            <option>Technical Troubleshooting</option>
            <option>Ink & Toner Questions</option>
            <option>Warranty & Returns</option>
            <option>Billing & Payments</option>
            <option>Business / Bulk Orders</option>
            <option>Other</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Order Number <span class="text-slate-400 font-normal">(optional)</span></label>
          <input type="text" name="order_no" placeholder="e.g. GSS-20250001" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 bg-slate-50 transition"/>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Message <span class="text-red-400">*</span></label>
          <textarea name="message" required rows="5" placeholder="Describe your issue or question in detail…" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 bg-slate-50 transition resize-none"></textarea>
        </div>

        <div class="flex items-start gap-2.5">
          <input type="checkbox" id="consent" required class="mt-0.5 accent-navy-600"/>
          <label for="consent" class="text-xs text-slate-500 leading-relaxed">I agree to the <a href="#" class="text-navy-600 hover:underline">Privacy Policy</a> and consent to being contacted by GeekSupportSales regarding my inquiry.</label>
        </div>

        <button type="submit" class="w-full btn-gradient text-white font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2 text-sm">
          <i class="ri-send-plane-line"></i> Send Message
        </button>
      </form>

      <!-- Success message (hidden) -->
      <div id="success-msg" class="hidden mt-6 bg-emerald-50 border border-emerald-200 rounded-xl p-5 text-center">
        <i class="ri-checkbox-circle-fill text-emerald-500 text-4xl mb-2"></i>
        <h4 class="font-bold text-emerald-800">Message Sent!</h4>
        <p class="text-emerald-600 text-sm mt-1">We'll get back to you within 2 hours. Check your email for confirmation.</p>
      </div>
    </div>

    <!-- Info sidebar — 2 cols -->
    <div class="lg:col-span-2 space-y-5">

      <!-- Hours -->
      <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <h3 class="font-bold text-slate-800 flex items-center gap-2 mb-4">
          <i class="ri-time-line text-navy-600 text-lg"></i> Business Hours
        </h3>
        <div class="space-y-2.5 text-sm">
          <div class="flex justify-between">
            <span class="text-slate-600">Monday – Friday</span>
            <span class="font-semibold text-slate-800">8:00 AM – 8:00 PM</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600">Saturday</span>
            <span class="font-semibold text-slate-800">9:00 AM – 6:00 PM</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600">Sunday</span>
            <span class="font-semibold text-slate-800">10:00 AM – 4:00 PM</span>
          </div>
          <div class="pt-2 border-t border-slate-100 flex justify-between">
            <span class="text-slate-600">Live Chat</span>
            <span class="font-semibold text-emerald-600">24 / 7</span>
          </div>
        </div>
      </div>

      <!-- Quick support -->
      <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <h3 class="font-bold text-slate-800 flex items-center gap-2 mb-4">
          <i class="ri-tools-line text-navy-600 text-lg"></i> Quick Support
        </h3>
        <div class="space-y-3">
          <a href="#" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-navy-50 transition group">
            <div class="bg-navy-100 rounded-lg w-9 h-9 flex items-center justify-center shrink-0 group-hover:bg-navy-600 transition">
              <i class="ri-download-2-line text-navy-600 group-hover:text-white text-sm transition"></i>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-800">Driver Downloads</p>
              <p class="text-xs text-slate-400">Get the latest printer drivers</p>
            </div>
            <i class="ri-arrow-right-s-line text-slate-300 ml-auto"></i>
          </a>
          <a href="#" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-navy-50 transition group">
            <div class="bg-navy-100 rounded-lg w-9 h-9 flex items-center justify-center shrink-0 group-hover:bg-navy-600 transition">
              <i class="ri-question-answer-line text-navy-600 group-hover:text-white text-sm transition"></i>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-800">FAQ & Troubleshooting</p>
              <p class="text-xs text-slate-400">Common issues & solutions</p>
            </div>
            <i class="ri-arrow-right-s-line text-slate-300 ml-auto"></i>
          </a>
          <a href="#" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-navy-50 transition group">
            <div class="bg-navy-100 rounded-lg w-9 h-9 flex items-center justify-center shrink-0 group-hover:bg-navy-600 transition">
              <i class="ri-map-pin-2-line text-navy-600 group-hover:text-white text-sm transition"></i>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-800">Track My Order</p>
              <p class="text-xs text-slate-400">Real-time shipping updates</p>
            </div>
            <i class="ri-arrow-right-s-line text-slate-300 ml-auto"></i>
          </a>
          <a href="#" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-navy-50 transition group">
            <div class="bg-navy-100 rounded-lg w-9 h-9 flex items-center justify-center shrink-0 group-hover:bg-navy-600 transition">
              <i class="ri-shield-check-line text-navy-600 group-hover:text-white text-sm transition"></i>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-800">Warranty Claims</p>
              <p class="text-xs text-slate-400">Start a warranty request</p>
            </div>
            <i class="ri-arrow-right-s-line text-slate-300 ml-auto"></i>
          </a>
        </div>
      </div>

      <!-- Social -->
      <div class="bg-navy-600 rounded-2xl p-6 text-white">
        <h3 class="font-bold flex items-center gap-2 mb-3">
          <i class="ri-share-line text-amber2-400"></i> Follow Us
        </h3>
        <p class="text-blue-100 text-xs mb-4">Stay updated with deals, tips & printer news.</p>
        <div class="flex gap-2">
          <a href="#" class="bg-white/15 hover:bg-white/25 w-9 h-9 rounded-lg flex items-center justify-center transition"><i class="ri-facebook-fill"></i></a>
          <a href="#" class="bg-white/15 hover:bg-white/25 w-9 h-9 rounded-lg flex items-center justify-center transition"><i class="ri-twitter-x-line"></i></a>
          <a href="#" class="bg-white/15 hover:bg-white/25 w-9 h-9 rounded-lg flex items-center justify-center transition"><i class="ri-instagram-line"></i></a>
          <a href="#" class="bg-white/15 hover:bg-white/25 w-9 h-9 rounded-lg flex items-center justify-center transition"><i class="ri-youtube-line"></i></a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ======= MAP PLACEHOLDER ======= -->
<section id="map" class="px-5 pb-14">
  <div class="max-w-7xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="bg-slate-100 h-64 flex flex-col items-center justify-center text-slate-400">
        <i class="ri-map-2-line text-5xl mb-3 text-slate-300"></i>
        <p class="font-semibold text-slate-500">123 Printer Ave, Suite 400 · Dallas, TX 75201</p>
        <a href="https://maps.google.com" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-600 hover:underline">
          <i class="ri-external-link-line"></i> Open in Google Maps
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ======= FAQ ======= -->
<section class="py-14 px-5 bg-white border-y border-slate-100">
  <div class="max-w-3xl mx-auto">
    <p class="section-label text-center mb-2">FAQ</p>
    <h2 class="text-2xl md:text-3xl font-black text-center text-slate-800 mb-10">Frequently Asked Questions</h2>

    <div class="space-y-3" id="faq-list">

      <div class="faq-item border border-slate-200 rounded-xl overflow-hidden">
        <button onclick="toggleFaq(this)" class="w-full flex items-center justify-between px-5 py-4 text-left bg-white hover:bg-slate-50 transition">
          <span class="font-semibold text-slate-800 text-sm">How does the free printer setup work?</span>
          <i class="ri-add-line text-navy-600 text-lg shrink-0 faq-icon transition-transform"></i>
        </button>
        <div class="faq-body hidden px-5 pb-4 text-sm text-slate-500 leading-relaxed">
          After your printer arrives, call or chat with our tech team. A certified specialist will remotely guide you through unboxing, driver installation, Wi-Fi setup, and a test print — completely free, no matter what printer you bought.
        </div>
      </div>

      <div class="faq-item border border-slate-200 rounded-xl overflow-hidden">
        <button onclick="toggleFaq(this)" class="w-full flex items-center justify-between px-5 py-4 text-left bg-white hover:bg-slate-50 transition">
          <span class="font-semibold text-slate-800 text-sm">What is your return policy?</span>
          <i class="ri-add-line text-navy-600 text-lg shrink-0 faq-icon transition-transform"></i>
        </button>
        <div class="faq-body hidden px-5 pb-4 text-sm text-slate-500 leading-relaxed">
          We offer a 30-day hassle-free return policy. If you're not satisfied, contact us within 30 days of delivery and we'll arrange a free return pickup and full refund.
        </div>
      </div>

      <div class="faq-item border border-slate-200 rounded-xl overflow-hidden">
        <button onclick="toggleFaq(this)" class="w-full flex items-center justify-between px-5 py-4 text-left bg-white hover:bg-slate-50 transition">
          <span class="font-semibold text-slate-800 text-sm">Do you sell compatible (non-OEM) ink cartridges?</span>
          <i class="ri-add-line text-navy-600 text-lg shrink-0 faq-icon transition-transform"></i>
        </button>
        <div class="faq-body hidden px-5 pb-4 text-sm text-slate-500 leading-relaxed">
          Yes. We carry both OEM (original manufacturer) and high-quality compatible cartridges. Compatible cartridges are clearly labeled and come with a quality guarantee — they won't void your printer warranty.
        </div>
      </div>

      <div class="faq-item border border-slate-200 rounded-xl overflow-hidden">
        <button onclick="toggleFaq(this)" class="w-full flex items-center justify-between px-5 py-4 text-left bg-white hover:bg-slate-50 transition">
          <span class="font-semibold text-slate-800 text-sm">How long does shipping take?</span>
          <i class="ri-add-line text-navy-600 text-lg shrink-0 faq-icon transition-transform"></i>
        </button>
        <div class="faq-body hidden px-5 pb-4 text-sm text-slate-500 leading-relaxed">
          Standard shipping takes 3–5 business days. Expedited (2-day) and overnight options are available at checkout. Orders over $99 qualify for free standard shipping.
        </div>
      </div>

      <div class="faq-item border border-slate-200 rounded-xl overflow-hidden">
        <button onclick="toggleFaq(this)" class="w-full flex items-center justify-between px-5 py-4 text-left bg-white hover:bg-slate-50 transition">
          <span class="font-semibold text-slate-800 text-sm">Can I get a bulk / business discount?</span>
          <i class="ri-add-line text-navy-600 text-lg shrink-0 faq-icon transition-transform"></i>
        </button>
        <div class="faq-body hidden px-5 pb-4 text-sm text-slate-500 leading-relaxed">
          Absolutely. We offer volume pricing for businesses ordering 5+ units. Contact our business sales team at business@geeksupportsales.com or call 8019511533 for a custom quote.
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ======= CTA SECTION ======= -->
<section class="py-16 px-5 hero-bg text-white">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/25 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
      <i class="ri-printer-line text-amber2-400"></i> 500+ Printer Models In Stock
    </span>
    <h2 class="text-3xl md:text-4xl font-black leading-tight">
      Still Looking for the<br/>
      <span class="text-amber2-400">Right Printer?</span>
    </h2>
    <p class="mt-4 text-blue-100 text-base max-w-xl mx-auto leading-relaxed">
      Our experts can recommend the perfect printer for your budget and needs — home, office, or enterprise. No pressure, just honest advice.
    </p>
    <div class="mt-8 flex flex-wrap justify-center gap-4">
      <a href="index.php#products" class="inline-flex items-center gap-2 btn-gradient text-white font-bold px-8 py-3.5 rounded-xl transition shadow-lg text-sm">
        <i class="ri-printer-line"></i> Browse All Printers
      </a>
      <a href="#contact-form" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-semibold px-8 py-3.5 rounded-xl transition text-sm">
        <i class="ri-chat-3-line"></i> Ask an Expert
      </a>
    </div>
  </div>
</section>

<!-- ======= FOOTER ======= -->
<footer class="bg-slate-900 text-slate-400 pt-14 pb-8 px-5">
  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">

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

    <div>
      <h4 class="text-white font-bold text-sm mb-4">Products</h4>
      <ul class="space-y-2.5 text-sm">
        <li><a href="index.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Inkjet Printers</a></li>
        <li><a href="index.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Laser Printers</a></li>
        <li><a href="index.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> All-in-One Printers</a></li>
        <li><a href="index.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Business Printers</a></li>
        <li><a href="index.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Ink Cartridges</a></li>
        <li><a href="index.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Toner Cartridges</a></li>
      </ul>
    </div>

    <div>
      <h4 class="text-white font-bold text-sm mb-4">Support</h4>
      <ul class="space-y-2.5 text-sm">
        <li><a href="contact.php" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Printer Setup Help</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Driver Downloads</a></li>
        <li><a href="#faq-list" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Troubleshooting FAQ</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Warranty Claims</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Track My Order</a></li>
        <li><a href="#" class="hover:text-white transition flex items-center gap-1.5"><i class="ri-arrow-right-s-line"></i> Returns & Refunds</a></li>
      </ul>
    </div>

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

<!-- Back to top -->
<button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="fixed bottom-6 right-6 btn-gradient text-white w-11 h-11 rounded-full shadow-lg transition z-30 flex items-center justify-center">
  <i class="ri-arrow-up-line text-lg"></i>
</button>

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
<script src="js/contact.js"></script>
</body>
</html>
