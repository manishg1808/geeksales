  // Video sound toggle
  function toggleVidSound() {
    const vid = document.querySelector('#hero-slider video');
    const icon = document.getElementById('vid-icon');
    if (!vid) return;
    vid.muted = !vid.muted;
    icon.className = vid.muted ? 'ri-volume-mute-line text-sm' : 'ri-volume-up-line text-sm';
  }

  let cart = JSON.parse(localStorage.getItem('gss_cart') || '[]');

  function saveCart() {
    localStorage.setItem('gss_cart', JSON.stringify(cart));
  }

  function toggleCart() {
    const s = document.getElementById('cart-sidebar');
    const o = document.getElementById('cart-overlay');
    const open = !s.classList.contains('translate-x-full');
    s.classList.toggle('translate-x-full', open);
    o.classList.toggle('hidden', open);
  }

  function addToCart(name, price) {
    const ex = cart.find(i => i.name === name);
    ex ? ex.qty++ : cart.push({ name, price, qty: 1 });
    saveCart();
    renderCart();
    document.getElementById('cart-sidebar').classList.remove('translate-x-full');
    document.getElementById('cart-overlay').classList.remove('hidden');
  }

  function buyNow(name, price) {
    const ex = cart.find(i => i.name === name);
    ex ? ex.qty++ : cart.push({ name, price, qty: 1 });
    saveCart();
    window.location.href = 'checkout.php';
  }

  function removeFromCart(name) {
    cart = cart.filter(i => i.name !== name);
    saveCart();
    renderCart();
  }

  function renderCart() {
    const el = document.getElementById('cart-items');
    const cnt = document.getElementById('cart-count');
    const tot = document.getElementById('cart-total');
    const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const count = cart.reduce((s, i) => s + i.qty, 0);
    cnt.textContent = count;
    tot.textContent = '$' + total.toFixed(2);
    if (!cart.length) {
      el.innerHTML = `<div class="text-center mt-12"><i class="ri-shopping-cart-2-line text-slate-200 text-5xl"></i><p class="text-slate-400 text-sm mt-3">Your cart is empty</p></div>`;
      return;
    }
    el.innerHTML = cart.map(item => `
      <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-3">
        <div class="bg-navy-50 rounded-lg p-2 shrink-0"><i class="ri-printer-fill text-navy-600 text-lg"></i></div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-slate-800 truncate">${item.name}</p>
          <p class="text-xs text-slate-400">Qty: ${item.qty} × $${item.price.toFixed(2)}</p>
        </div>
        <div class="text-right shrink-0">
          <p class="text-sm font-bold text-navy-600">$${(item.price * item.qty).toFixed(2)}</p>
          <button onclick="removeFromCart('${item.name}')" class="text-red-400 hover:text-red-600 text-xs mt-0.5">Remove</button>
        </div>
    </div>`).join('');
  }

  document.addEventListener('DOMContentLoaded', renderCart);

  // Countdown
  let countdown = 8 * 3600 + 45 * 60 + 30;
  setInterval(() => {
    if (countdown <= 0) return;
    countdown--;
    const h = Math.floor(countdown / 3600);
    const m = Math.floor((countdown % 3600) / 60);
    const s = countdown % 60;
    document.getElementById('hours').textContent = String(h).padStart(2,'0');
    document.getElementById('mins').textContent = String(m).padStart(2,'0');
    document.getElementById('secs').textContent = String(s).padStart(2,'0');
  }, 1000);
