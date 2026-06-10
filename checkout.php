<?php
// Backend logic has been moved to api/orders.php
// This file now serves only the frontend HTML.
require_once __DIR__ . '/admin/includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
            amber2:{ 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C',800:'#9A3412',900:'#7C2D12' },
          },
          fontFamily:{ sans:['Raleway','system-ui','sans-serif'] },
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
  <style>
    html{scroll-behavior:smooth}
    input:focus,select:focus,textarea:focus{outline:none;border-color:#1e293b;box-shadow:0 0 0 3px rgba(30,41,59,.1)}
    .step-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.8rem;transition:all .2s}
    .step-dot.done{background:#1e293b;color:white}
    .step-dot.active{background:#1e293b;color:white;box-shadow:0 0 0 4px rgba(37,99,235,.18)}
    .step-dot.idle{background:#e2e8f0;color:#94a3b8}
    .step-line{flex:1;height:2px;background:#e2e8f0;margin:0 4px;transition:background .3s}
    .step-line.done{background:#1e293b}
    .form-field{width:100%;border:1px solid #e2e8f0;border-radius:.75rem;padding:.65rem 1rem;font-size:.875rem;background:#f8fafc;color:#1e293b;transition:all .15s}
    .form-field:focus{background:#fff;border-color:#1e293b;box-shadow:0 0 0 3px rgba(30,41,59,.1)}
    .pay-method{border:2px solid #e2e8f0;border-radius:1rem;padding:1rem;cursor:pointer;transition:all .15s}
    .pay-method.selected{border-color:#1e293b;background:#f8fafc}
    .thin-scroll::-webkit-scrollbar{width:4px}
    .thin-scroll::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}
    #success-overlay{transition:opacity .3s}
    .brand-gradient{background:#2563EB}
    .btn-gradient{background:#F97316;color:#fff}
    .btn-gradient:hover{filter:brightness(1.05);box-shadow:0 10px 24px rgba(249,115,22,.24)}
  </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased">
<?php render_google_tag_manager_body(); ?>

<!-- NAVBAR (minimal) -->
<header class="brand-gradient text-white shadow-sm">
  <div class="max-w-6xl mx-auto px-5 py-4 flex items-center justify-between">
    <a href="index.php" class="flex items-center gap-2.5">
      <div class="bg-white/15 border border-white/20 rounded-xl w-9 h-9 flex items-center justify-center"><i class="ri-printer-fill text-white"></i></div>
      <div><span class="font-black text-white">Geek</span><span class="font-black text-blue-200">Support</span><span class="font-black text-white">Sales</span></div>
    </a>
    <div class="flex items-center gap-2 text-xs text-white/80">
      <i class="ri-lock-2-fill text-emerald-500 text-base"></i>
      <span class="font-semibold text-white/90">Secure Checkout</span>
    </div>
    <div class="flex items-center gap-2 text-xs text-white/80">
      <i class="ri-phone-line text-blue-200"></i>
      <span>8019511533</span>
    </div>
  </div>
</header>

<!-- PROGRESS STEPS -->
<div class="bg-white border-b border-slate-100">
  <div class="max-w-6xl mx-auto px-5 py-4">
    <div class="flex items-center max-w-lg mx-auto">
      <div class="flex flex-col items-center">
        <div class="step-dot active" id="step1-dot">1</div>
        <span class="text-[10px] font-semibold text-navy-600 mt-1 whitespace-nowrap">Info</span>
      </div>
      <div class="step-line" id="line1"></div>
      <div class="flex flex-col items-center">
        <div class="step-dot idle" id="step2-dot">2</div>
        <span class="text-[10px] font-semibold text-slate-400 mt-1 whitespace-nowrap">Shipping</span>
      </div>
      <div class="step-line" id="line2"></div>
      <div class="flex flex-col items-center">
        <div class="step-dot idle" id="step3-dot">3</div>
        <span class="text-[10px] font-semibold text-slate-400 mt-1 whitespace-nowrap">Payment</span>
      </div>
      <div class="step-line" id="line3"></div>
      <div class="flex flex-col items-center">
        <div class="step-dot idle" id="step4-dot"><i class="ri-check-line text-xs"></i></div>
        <span class="text-[10px] font-semibold text-slate-400 mt-1 whitespace-nowrap">Confirm</span>
      </div>
    </div>
  </div>
</div>

<!-- MAIN CHECKOUT LAYOUT -->
<div class="max-w-6xl mx-auto px-5 py-8 flex flex-col lg:flex-row gap-8">

  <!-- LEFT: Form Steps -->
  <div class="flex-1 min-w-0">

    <!-- STEP 1: Contact Info -->
    <div id="step1" class="bg-white border border-slate-200 rounded-2xl p-6 mb-5">
      <h2 class="font-black text-slate-800 text-lg flex items-center gap-2 mb-5">
        <span class="bg-navy-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-black">1</span>
        Contact Information
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">First Name *</label>
          <input type="text" id="f-fname" class="form-field" placeholder="John"/>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Last Name *</label>
          <input type="text" id="f-lname" class="form-field" placeholder="Smith"/>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Email Address *</label>
          <input type="email" id="f-email" class="form-field" placeholder="john@example.com"/>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Phone Number *</label>
          <input type="tel" id="f-phone" class="form-field" placeholder="+1 (555) 000-0000"/>
        </div>
      </div>

      <button onclick="goStep(2)" class="mt-5 btn-gradient text-white font-bold px-8 py-3 rounded-xl transition text-sm flex items-center gap-2">
        Continue to Shipping <i class="ri-arrow-right-line"></i>
      </button>
    </div>

    <!-- STEP 2: Shipping -->
    <div id="step2" class="bg-white border border-slate-200 rounded-2xl p-6 mb-5 opacity-50 pointer-events-none">
      <h2 class="font-black text-slate-800 text-lg flex items-center gap-2 mb-5">
        <span class="bg-slate-200 text-slate-500 w-7 h-7 rounded-full flex items-center justify-center text-xs font-black" id="step2-num">2</span>
        Shipping Address
      </h2>
      <div class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Street Address *</label>
          <input type="text" id="f-addr" class="form-field" placeholder="123 Main Street, Apt 4B"/>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">City *</label>
            <input type="text" id="f-city" class="form-field" placeholder="Dallas"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">State *</label>
            <select id="f-state" class="form-field">
              <option value="">Select…</option>
              <option>TX</option><option>CA</option><option>NY</option><option>FL</option><option>IL</option><option>WA</option><option>Other</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">ZIP Code *</label>
            <input type="text" id="f-zip" class="form-field" placeholder="75201"/>
          </div>
        </div>
      </div>



      <div class="flex gap-3 mt-5">
        <button onclick="goStep(1)" class="border border-slate-200 text-slate-600 font-semibold px-6 py-3 rounded-xl transition text-sm hover:border-navy-500 hover:text-navy-600 flex items-center gap-2">
          <i class="ri-arrow-left-line"></i> Back
        </button>
        <button onclick="goStep(3)" class="btn-gradient text-white font-bold px-8 py-3 rounded-xl transition text-sm flex items-center gap-2">
          Continue to Payment <i class="ri-arrow-right-line"></i>
        </button>
      </div>
    </div>

    <!-- STEP 3: Payment -->
    <div id="step3" class="bg-white border border-slate-200 rounded-2xl p-6 mb-5 opacity-50 pointer-events-none">
      <h2 class="font-black text-slate-800 text-lg flex items-center gap-2 mb-5">
        <span class="bg-slate-200 text-slate-500 w-7 h-7 rounded-full flex items-center justify-center text-xs font-black" id="step3-num">3</span>
        Payment Method
      </h2>

      <!-- Cash on Delivery Option -->
      <div class="pay-method selected flex items-center gap-4 cursor-default mb-5">
        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
          <i class="ri-truck-line text-xl"></i>
        </div>
        <div class="flex-1">
          <p class="font-bold text-slate-800 text-sm">Cash on Delivery (COD)</p>
          <p class="text-xs text-slate-400">Pay with cash upon delivery of your order.</p>
        </div>
        <i class="ri-checkbox-circle-fill text-emerald-500 text-xl"></i>
      </div>

      <!-- Promo code -->
      <div class="mt-5 flex gap-2">
        <input type="text" id="promo-input" class="form-field flex-1" placeholder="Promo / Gift code"/>
        <button onclick="applyPromo()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-xl transition text-sm">Apply</button>
      </div>
      <p id="promo-msg" class="text-xs mt-1.5 hidden"></p>

      <div class="flex gap-3 mt-5">
        <button onclick="goStep(2)" class="border border-slate-200 text-slate-600 font-semibold px-6 py-3 rounded-xl transition text-sm hover:border-navy-500 hover:text-navy-600 flex items-center gap-2">
          <i class="ri-arrow-left-line"></i> Back
        </button>
        <button onclick="placeOrder()" class="flex-1 btn-gradient text-white font-black py-3 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg">
          <i class="ri-lock-2-line"></i> Place Order Securely
        </button>
      </div>
      <p class="text-xs text-slate-400 text-center mt-3 flex items-center justify-center gap-1">
        <i class="ri-shield-check-line text-emerald-500"></i> 256-bit SSL encryption · Your data is safe
      </p>
    </div>

  </div>

  <!-- RIGHT: Order Summary -->
  <div class="lg:w-96 shrink-0">
    <div class="bg-white border border-slate-200 rounded-2xl p-5 sticky top-6">
      <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2">
        <i class="ri-shopping-bag-3-line text-navy-600"></i> Order Summary
        <span id="summary-count" class="ml-auto text-xs text-slate-400 font-normal"></span>
      </h3>

      <!-- Items -->
      <div id="summary-items" class="space-y-3 max-h-64 overflow-y-auto thin-scroll mb-4"></div>

      <!-- Coupon applied -->
      <div id="discount-row" class="hidden flex items-center justify-between text-sm py-2 border-t border-slate-100">
        <span class="text-emerald-600 font-semibold flex items-center gap-1"><i class="ri-coupon-3-line"></i> Promo Applied</span>
        <span id="discount-amt" class="text-emerald-600 font-bold"></span>
      </div>

      <!-- Totals -->
      <div class="border-t border-slate-100 pt-4 space-y-2.5 text-sm">
        <div class="flex justify-between text-slate-600"><span>Subtotal</span><span id="sum-subtotal" class="font-semibold">$0.00</span></div>
        <div class="flex justify-between text-slate-600"><span>Shipping</span><span id="sum-shipping" class="font-semibold text-emerald-600">FREE</span></div>
        <div class="flex justify-between text-slate-600"><span>Tax (8.25%)</span><span id="sum-tax" class="font-semibold">$0.00</span></div>
        <div class="flex justify-between text-slate-800 font-black text-base border-t border-slate-200 pt-3">
          <span>Total</span><span id="sum-total" class="text-navy-600">$0.00</span>
        </div>
      </div>

      <!-- Trust -->
      <div class="mt-5 space-y-2">
        <div class="flex items-center gap-2 text-xs text-slate-500"><i class="ri-shield-check-line text-emerald-500 text-base"></i> Secure 256-bit SSL checkout</div>
        <div class="flex items-center gap-2 text-xs text-slate-500"><i class="ri-refresh-line text-navy-500 text-base"></i> 30-day hassle-free returns</div>
        <div class="flex items-center gap-2 text-xs text-slate-500"><i class="ri-headphone-line text-navy-500 text-base"></i> Free expert setup support</div>
        <div class="flex items-center gap-2 text-xs text-slate-500"><i class="ri-truck-line text-navy-500 text-base"></i> Free shipping on this order</div>
      </div>

      <!-- Payment icons -->
      <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-100 text-emerald-600 text-sm font-semibold">
        <i class="ri-truck-line text-lg"></i>
        <span>Cash on Delivery (COD) Enabled</span>
      </div>
    </div>
  </div>
</div>

<!-- SUCCESS OVERLAY -->
<div id="success-overlay" class="fixed inset-0 bg-white z-50 flex items-center justify-center opacity-0 pointer-events-none">
  <div class="text-center max-w-md px-6">
    <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
      <i class="ri-checkbox-circle-fill text-emerald-500 text-5xl"></i>
    </div>
    <h2 class="text-3xl font-black text-slate-800">Order Placed!</h2>
    <p class="text-slate-500 mt-3 leading-relaxed">Thank you for your order. You'll receive a confirmation email shortly. Our tech team will contact you to schedule your free printer setup.</p>
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 mt-6 text-left space-y-2">
      <div class="flex justify-between text-sm"><span class="text-slate-500">Order Number</span><span class="font-black text-navy-600" id="order-num">#GSS-00000</span></div>
      <div class="flex justify-between text-sm"><span class="text-slate-500">Estimated Delivery</span><span class="font-semibold text-slate-700" id="order-delivery"></span></div>
      <div class="flex justify-between text-sm"><span class="text-slate-500">Total Charged</span><span class="font-black text-slate-800" id="order-total"></span></div>
    </div>
    <div class="flex gap-3 mt-6">
      <a href="products.php" class="flex-1 border border-slate-200 text-slate-600 font-semibold py-3 rounded-xl hover:border-navy-500 hover:text-navy-600 transition text-sm text-center">Continue Shopping</a>
      <a href="contact.php" class="flex-1 btn-gradient text-white font-bold py-3 rounded-xl transition text-sm text-center flex items-center justify-center gap-2">
        <i class="ri-headphone-line"></i> Schedule Setup
      </a>
    </div>
  </div>
</div>

<script src="js/checkout.js"></script>
</body>
</html>
