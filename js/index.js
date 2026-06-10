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

  function addToCart(name, price, imageUrl = '') {
    const ex = cart.find(i => i.name === name);
    ex ? ex.qty++ : cart.push({ name, price, qty: 1, image_url: imageUrl });
    if (ex && imageUrl && !ex.image_url) ex.image_url = imageUrl;
    saveCart();
    renderCart();
    document.getElementById('cart-sidebar').classList.remove('translate-x-full');
    document.getElementById('cart-overlay').classList.remove('hidden');
  }

  function buyNow(name, price, imageUrl = '') {
    const ex = cart.find(i => i.name === name);
    ex ? ex.qty++ : cart.push({ name, price, qty: 1, image_url: imageUrl });
    if (ex && imageUrl && !ex.image_url) ex.image_url = imageUrl;
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
        <div class="bg-navy-50 rounded-lg w-11 h-11 flex items-center justify-center shrink-0 overflow-hidden">
          ${item.image_url ? `<img src="${item.image_url}" alt="${item.name}" class="w-full h-full object-contain p-1">` : `<i class="ri-printer-fill text-navy-600 text-lg"></i>`}
        </div>
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

  // Scroll-expand banner animation
  const scrollSection = document.getElementById('scroll-expand-section');
  const scrollInner = document.getElementById('scroll-expand-inner');

  if (scrollSection && scrollInner) {
    const handleScrollExpand = () => {
      const rect = scrollSection.getBoundingClientRect();
      const viewportHeight = window.innerHeight;
      
      const triggerStart = viewportHeight; // Enters from bottom
      const triggerEnd = viewportHeight * 0.35; // past center of viewport
      
      if (rect.top < triggerStart && rect.bottom > 0) {
        const totalDistance = triggerStart - triggerEnd;
        const currentDistance = triggerStart - rect.top;
        const progress = Math.min(1, Math.max(0, currentDistance / totalDistance));
        
        const borderRadius = 16 * (1 - progress);
        const paddingX = 20 * (1 - progress); 
        const paddingY = 32 * (1 - progress / 2); 
        
        if (window.innerWidth > 1280) {
          const maxWidth = 1280 + (window.innerWidth - 1280) * progress;
          scrollInner.style.maxWidth = `${maxWidth}px`;
        } else {
          scrollInner.style.maxWidth = '100%';
        }
        
        scrollInner.style.borderRadius = `${borderRadius}px`;
        scrollSection.style.paddingLeft = `${paddingX}px`;
        scrollSection.style.paddingRight = `${paddingX}px`;
        scrollSection.style.paddingTop = `${paddingY}px`;
        scrollSection.style.paddingBottom = `${paddingY}px`;
      } else if (rect.top >= triggerStart) {
        scrollInner.style.maxWidth = '1280px';
        scrollInner.style.borderRadius = '16px';
        scrollSection.style.paddingLeft = '20px';
        scrollSection.style.paddingRight = '20px';
        scrollSection.style.paddingTop = '32px';
        scrollSection.style.paddingBottom = '32px';
      } else if (rect.bottom <= 0) {
        scrollInner.style.maxWidth = '100%';
        scrollInner.style.borderRadius = '0px';
        scrollSection.style.paddingLeft = '0px';
        scrollSection.style.paddingRight = '0px';
        scrollSection.style.paddingTop = '16px';
        scrollSection.style.paddingBottom = '16px';
      }
    };

    const throttledScroll = window.throttle ? window.throttle(handleScrollExpand, 10) : handleScrollExpand;
    window.addEventListener('scroll', throttledScroll);
    window.addEventListener('resize', throttledScroll);
    setTimeout(handleScrollExpand, 100);
  }
