let cart = JSON.parse(localStorage.getItem('gss_cart') || '[]');
let qty = 1;
let currentProduct = null;
window.currentProduct = null;

function saveCart() {
  localStorage.setItem('gss_cart', JSON.stringify(cart));
}

document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const id = window.productId || parseInt(params.get('id') || '1', 10);
  loadProduct(Number.isFinite(id) ? id : 1);
  renderCart();
  setDeliveryDate();
});

function setDeliveryDate() {
  const d = new Date();
  d.setDate(d.getDate() + 3);
  const el = document.getElementById('delivery-date');
  if (el) {
    el.textContent = d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
  }
}

function starsHtml(rating) {
  const r = Number(rating) || 0;
  const full = Math.floor(r);
  const half = r % 1 >= 0.5;
  let stars = '';
  for (let i = 0; i < full; i++) stars += '<i class="ri-star-fill"></i>';
  if (half) stars += '<i class="ri-star-half-fill"></i>';
  for (let i = full + (half ? 1 : 0); i < 5; i++) stars += '<i class="ri-star-line"></i>';
  return stars;
}

function badgeCls(color) {
  return { red: 'bg-red-500', green: 'bg-emerald-500', amber: 'bg-amber2-500', navy: 'bg-navy-600' }[color] || 'bg-slate-500';
}

function catLabel(cat) {
  return { inkjet: 'Inkjet', laser: 'Laser', allinone: 'All-in-One', business: 'Business', ink: 'Ink & Toner' }[cat] || cat || 'Printer';
}

function productType(cat) {
  return { inkjet: 'Inkjet Printer', laser: 'Laser Printer', allinone: 'All-in-One Printer', business: 'Business Printer', ink: 'Ink & Toner' }[cat] || 'Printer';
}

function normalizeProduct(p) {
  const price = Number(p.price) || 0;
  const oldPrice = Number(p.oldPrice ?? p.old_price ?? 0) || 0;
  const rating = Number(p.rating);
  const cat = p.cat || (p.category_slug === 'all-in-one' ? 'allinone' : p.category_slug === 'ink-toner' ? 'ink' : p.category_slug) || 'inkjet';

  return {
    ...p,
    id: Number(p.id),
    name: p.name || 'Product',
    brand: p.brand || p.brand_name || '',
    cat,
    price,
    oldPrice,
    rating: Number.isFinite(rating) ? Math.max(0, Math.min(5, rating)) : 0,
    reviews: Number(p.reviews) || 120,
    badge: p.badge || '',
    badgeColor: p.badgeColor || 'navy',
    color: p.color || '#f1f5f9',
    iconColor: p.iconColor || '#1e293b',
    features: Array.isArray(p.features) ? p.features : [],
    specs: Array.isArray(p.specs) ? p.specs : [],
    inbox: Array.isArray(p.inbox) ? p.inbox : [],
    desc: p.desc || p.description || '',
    newest: Boolean(p.newest),
    image_url: p.image_url || p.imageUrl || '',
  };
}

async function loadProduct(id) {
  try {
    const response = await fetch(`api/products.php?id=${encodeURIComponent(id)}`);
    const result = await response.json();

    if (!response.ok || !result.success || !result.data.product) {
      showProductError('Product not found.');
      return;
    }

    const p = normalizeProduct(result.data.product);
    currentProduct = p;
    window.currentProduct = p;
    qty = 1;
    document.title = `${p.name} - Geek Support LLc`;

    setText('bc-name', p.name);
    const bcCat = document.getElementById('bc-cat');
    if (bcCat) {
      bcCat.textContent = catLabel(p.cat);
      bcCat.onclick = () => { window.location.href = `products.php?cat=${encodeURIComponent(p.cat)}`; };
    }

    renderMainProduct(p);
    renderRelated((result.data.related || []).map(normalizeProduct));

    if (typeof wlRefreshAll === 'function') wlRefreshAll();
  } catch (error) {
    console.error('Failed to load product:', error);
    showProductError('Product could not be loaded.');
  }
}

function showProductError(message) {
  const wrap = document.querySelector('.max-w-7xl.mx-auto.px-5.py-8');
  if (wrap) {
    wrap.innerHTML = `<p class="text-center py-20 text-slate-400">${message}</p>`;
  }
}

function setText(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value;
}

function renderMainProduct(p) {
  const mainIcon = document.getElementById('main-img');
  if (mainIcon) {
    mainIcon.className = `ri-${p.cat === 'ink' ? 'ink-bottle' : 'printer'}-fill`;
    mainIcon.style.cssText = `font-size:180px;line-height:1;color:${p.iconColor}`;
  }

  const mainWrap = document.getElementById('main-img-wrap');
  if (p.image_url && mainWrap) {
      mainWrap.style.background = 'transparent';
      mainWrap.innerHTML = `<img src="${p.image_url}" class="w-full h-full object-contain p-4 transition-transform duration-400" id="main-img" alt="${escapeAttr(p.name)}" />` +
        (p.badge ? `<span id="main-badge" class="absolute top-4 left-4 text-white text-xs font-bold px-3 py-1 rounded-lg badge-pulse ${badgeCls(p.badgeColor)}">${p.badge}</span>` : '') +
        `<button id="main-wl" onclick="toggleWishlist()" class="absolute top-4 right-4 w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center shadow hover:border-red-300 transition"><i class="ri-heart-3-line text-slate-400 text-lg"></i></button>` +
        `<div class="absolute bottom-3 right-3 bg-black/30 text-white text-[10px] px-2 py-1 rounded-lg flex items-center gap-1 pointer-events-none"><i class="ri-zoom-in-line"></i> Hover to zoom</div>`;
  } else {
      if (mainWrap) mainWrap.style.background = p.color;
  }

  const thumbIcon = document.getElementById('thumb-icon-1');
  if (thumbIcon) {
    thumbIcon.style.color = p.iconColor;
    thumbIcon.className = `ri-${p.cat === 'ink' ? 'ink-bottle' : 'printer'}-fill text-4xl`;
  }

  const mainBadge = document.getElementById('main-badge');
  if (mainBadge) {
    if (p.badge) {
      mainBadge.textContent = p.badge;
      mainBadge.className = `absolute top-4 left-4 text-white text-xs font-bold px-3 py-1 rounded-lg ${badgeCls(p.badgeColor)} ${p.badgeColor === 'red' ? 'badge-pulse' : ''}`;
    } else {
      mainBadge.classList.add('hidden');
    }
  }

  setText('pd-brand', p.brand);
  const brand = document.getElementById('pd-brand');
  if (brand) brand.style.color = p.iconColor;
  setText('pd-name', p.name);
  setText('pd-cat', productType(p.cat));
  setText('pd-rating-num', p.rating.toFixed(1));
  setText('pd-reviews', `(${p.reviews} reviews)`);
  setText('pd-price', formatMoney(p.price));
  setText('buy-sku', String(1000 + p.id));
  setText('pd-desc', p.desc);

  const stars = document.getElementById('pd-stars');
  if (stars) stars.innerHTML = starsHtml(p.rating);

  const pdBadge = document.getElementById('pd-badge');
  if (pdBadge) {
    if (p.badge) {
      pdBadge.textContent = p.badge;
      pdBadge.className = `text-white text-[10px] font-bold px-2 py-0.5 rounded-md ${badgeCls(p.badgeColor)}`;
    } else {
      pdBadge.classList.add('hidden');
    }
  }

  const pdNew = document.getElementById('pd-new');
  if (pdNew) pdNew.classList.toggle('hidden', !p.newest);

  const oldPrice = document.getElementById('pd-old-price');
  const buyOld = document.getElementById('buy-old');
  const save = document.getElementById('pd-save');
  if (p.oldPrice > p.price) {
    const saved = (p.oldPrice - p.price).toFixed(2);
    if (oldPrice) {
      oldPrice.textContent = formatMoney(p.oldPrice);
      oldPrice.classList.remove('hidden');
    }
    if (save) {
      save.textContent = `Save $${saved}`;
      save.classList.remove('hidden');
    }
  }
  updateQuantityPricing();

  const features = document.getElementById('pd-features');
  if (features) {
    features.innerHTML = p.features.map(feature => (
      `<span class="inline-flex items-center gap-1 text-xs bg-navy-50 text-navy-700 font-semibold px-3 py-1.5 rounded-full"><i class="ri-check-line"></i>${escapeHtml(titleCase(feature))}</span>`
    )).join('');
  }

  const specsGrid = document.getElementById('specs-grid');
  if (specsGrid) {
    specsGrid.innerHTML = p.specs.map(spec => {
      const [key, ...rest] = String(spec).split(':');
      const value = rest.join(':').trim();
      return `<div class="flex gap-2 bg-slate-50 rounded-xl px-3 py-2.5 text-xs"><span class="font-bold text-slate-600 shrink-0">${escapeHtml(key)}:</span><span class="text-slate-500">${escapeHtml(value)}</span></div>`;
    }).join('');
  }

  const inbox = document.getElementById('inbox-list');
  if (inbox) {
    inbox.innerHTML = p.inbox.map(item => `<li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-emerald-500 shrink-0"></i>${escapeHtml(item)}</li>`).join('');
  }
}

function renderReviews(p) {
  setText('rev-big', p.rating.toFixed(1));
  setText('rev-count', `${p.reviews} reviews`);
  const revStars = document.getElementById('rev-stars');
  if (revStars) revStars.innerHTML = starsHtml(p.rating);

  const dist = [
    Math.round(p.reviews * 0.65),
    Math.round(p.reviews * 0.20),
    Math.round(p.reviews * 0.10),
    Math.round(p.reviews * 0.03),
    Math.round(p.reviews * 0.02),
  ];

  const bars = document.getElementById('rev-bars');
  if (bars) {
    bars.innerHTML = [5, 4, 3, 2, 1].map((star, i) => `
      <div class="flex items-center gap-2">
        <span class="text-[10px] text-slate-400 w-6">${star}</span>
        <div class="flex-1 bg-slate-100 rounded-full h-1.5">
          <div class="bg-amber2-400 h-1.5 rounded-full" style="width:${Math.max(8, (dist[i] / p.reviews) * 100)}%"></div>
        </div>
        <span class="text-[10px] text-slate-400 w-8 text-right">${dist[i]}</span>
      </div>`).join('');
  }

  const sampleReviews = [
    { name: 'James M.', rating: 5, date: 'May 28, 2025', text: 'Setup was simple and the printer has been reliable every day.', helpful: 24 },
    { name: 'Sarah R.', rating: 5, date: 'May 15, 2025', text: 'Fast delivery, clean print quality, and the support team helped with wireless setup.', helpful: 18 },
    { name: 'David K.', rating: 4, date: 'Apr 30, 2025', text: 'Good value for the price. The setup support made the first print painless.', helpful: 11 },
  ];

  const reviewList = document.getElementById('review-list');
  if (reviewList) {
    reviewList.innerHTML = sampleReviews.map(review => `
      <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <div class="flex items-start justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-navy-600 flex items-center justify-center text-white text-xs font-bold shrink-0">${review.name.split(' ').map(n => n[0]).join('')}</div>
            <div>
              <p class="font-bold text-sm text-slate-800">${escapeHtml(review.name)}</p>
              <p class="text-xs text-slate-400">${escapeHtml(review.date)}</p>
            </div>
          </div>
          <div class="flex text-amber2-400 text-xs">${starsHtml(review.rating)}</div>
        </div>
        <p class="text-sm text-slate-600 mt-3 leading-relaxed">${escapeHtml(review.text)}</p>
        <div class="flex items-center gap-3 mt-3">
          <span class="text-xs text-slate-400">Helpful?</span>
          <button class="text-xs text-navy-600 hover:underline font-semibold flex items-center gap-1"><i class="ri-thumb-up-line"></i> Yes (${review.helpful})</button>
          <button class="text-xs text-slate-400 hover:underline flex items-center gap-1"><i class="ri-thumb-down-line"></i> No</button>
        </div>
      </div>`).join('');
  }
}

function renderRelated(related) {
  const grid = document.getElementById('related-grid');
  if (!grid) return;

  if (!related.length) {
    grid.innerHTML = '<p class="text-sm text-slate-400 col-span-full">No related products found.</p>';
    return;
  }

  grid.innerHTML = related.map(p => `
    <a href="product-detail.php?id=${p.id}" class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden group block">
      <div class="h-32 flex items-center justify-center p-4" style="background:${p.color}">
        ${p.image_url ? `<img src="${escapeAttr(p.image_url)}" alt="${escapeAttr(p.name)}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">` : `<i class="ri-${p.cat === 'ink' ? 'ink-bottle' : 'printer'}-fill group-hover:scale-110 transition-transform duration-300" style="font-size:60px;color:${p.iconColor};line-height:1"></i>`}
      </div>
      <div class="p-3">
        <p class="text-[10px] font-bold uppercase tracking-widest" style="color:${p.iconColor}">${escapeHtml(p.brand)}</p>
        <h4 class="font-bold text-slate-800 text-xs mt-0.5 leading-snug">${escapeHtml(p.name)}</h4>
        <div class="flex items-center gap-1 mt-1 text-amber2-400 text-[10px]">${starsHtml(p.rating)}<span class="text-slate-500 ml-1 font-semibold">${p.rating.toFixed(1)}</span><span class="text-slate-400">(${p.reviews})</span></div>
        <div class="flex items-baseline gap-1.5 mt-1.5">
          <span class="font-black text-slate-800 text-sm">$${p.price.toFixed(2)}</span>
          ${p.oldPrice > p.price ? `<span class="text-[10px] text-slate-400 line-through">$${p.oldPrice.toFixed(2)}</span>` : ''}
        </div>
      </div>
    </a>`).join('');
}

function switchThumb(n) {
  document.querySelectorAll('.thumb-img').forEach((thumb, index) => {
    thumb.classList.toggle('active', index + 1 === n);
  });
}

function switchTab(name, btn) {
  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(button => button.classList.remove('active'));
  const tab = document.getElementById(`tab-${name}`);
  if (tab) tab.classList.add('active');
  if (btn) btn.classList.add('active');
}

function changeQty(delta) {
  qty = Math.max(1, Math.min(10, qty + delta));
  setText('qty-display', String(qty));
  updateQuantityPricing();
}

function updateQuantityPricing() {
  if (!window.currentProduct) return;
  const total = window.currentProduct.price * qty;
  const oldTotal = window.currentProduct.oldPrice * qty;
  
  // Update Buy Box Price
  setText('buy-price', formatMoney(total));
  const buyOld = document.getElementById('buy-old');
  if (buyOld) {
    if (window.currentProduct.oldPrice > window.currentProduct.price) {
      buyOld.textContent = formatMoney(oldTotal);
      buyOld.classList.remove('hidden');
    } else {
      buyOld.textContent = '';
      buyOld.classList.add('hidden');
    }
  }

  // Update Main Details Price
  setText('pd-price', formatMoney(total));
  const pdOldPrice = document.getElementById('pd-old-price');
  if (pdOldPrice) {
    if (window.currentProduct.oldPrice > window.currentProduct.price) {
      pdOldPrice.textContent = formatMoney(oldTotal);
      pdOldPrice.classList.remove('hidden');
    } else {
      pdOldPrice.textContent = '';
      pdOldPrice.classList.add('hidden');
    }
  }

  // Update Savings
  const pdSave = document.getElementById('pd-save');
  if (pdSave) {
    if (window.currentProduct.oldPrice > window.currentProduct.price) {
      const saved = (oldTotal - total).toFixed(2);
      pdSave.textContent = `Save $${saved}`;
      pdSave.classList.remove('hidden');
    } else {
      pdSave.classList.add('hidden');
    }
  }
}

function toggleWishlist() {
  if (!window.currentProduct || typeof wlToggle !== 'function') return;
  wlToggle(window.currentProduct.id);
}

function buyNow() {
  if (!window.currentProduct) return;
  addToCartDetail();
  setTimeout(() => { window.location.href = 'checkout.php'; }, 300);
}

function addToCartDetail() {
  if (!window.currentProduct) return;
  for (let i = 0; i < qty; i++) {
    const existing = cart.find(item => item.name === window.currentProduct.name);
    existing ? existing.qty++ : cart.push({ name: window.currentProduct.name, price: window.currentProduct.price, qty: 1, image_url: window.currentProduct.image_url || '' });
    if (existing && window.currentProduct.image_url && !existing.image_url) existing.image_url = window.currentProduct.image_url;
  }
  saveCart();
  renderCart();
  showToast(`${qty}x ${window.currentProduct.name.split(' ').slice(0, 3).join(' ')} added!`);
  document.getElementById('cart-sidebar')?.classList.remove('translate-x-full');
  document.getElementById('cart-overlay')?.classList.remove('hidden');
}

function submitReview() {
  const name = document.getElementById('rev-name')?.value.trim();
  const text = document.getElementById('rev-text')?.value.trim();
  const star = document.querySelector('input[name="rev-star"]:checked')?.value;
  if (!name || !text || !star) {
    showToast('Please fill all fields and select a rating');
    return;
  }

  const list = document.getElementById('review-list');
  if (!list) return;

  const div = document.createElement('div');
  div.className = 'bg-emerald-50 border border-emerald-200 rounded-2xl p-5';
  div.innerHTML = `
    <div class="flex items-center gap-3 mb-2">
      <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-bold">${escapeHtml(name[0].toUpperCase())}</div>
      <div><p class="font-bold text-sm text-slate-800">${escapeHtml(name)}</p><p class="text-xs text-slate-400">Just now</p></div>
      <div class="ml-auto flex text-amber2-400 text-xs">${starsHtml(parseInt(star, 10))}</div>
    </div>
    <p class="text-sm text-slate-600">${escapeHtml(text)}</p>`;
  list.prepend(div);
  document.getElementById('rev-name').value = '';
  document.getElementById('rev-text').value = '';
  document.querySelectorAll('input[name="rev-star"]').forEach(input => { input.checked = false; });
  showToast('Review submitted! Thank you.');
}

function toggleCart() {
  const sidebar = document.getElementById('cart-sidebar');
  const overlay = document.getElementById('cart-overlay');
  if (!sidebar || !overlay) return;
  const open = !sidebar.classList.contains('translate-x-full');
  sidebar.classList.toggle('translate-x-full', open);
  overlay.classList.toggle('hidden', open);
}

function removeFromCart(name) {
  cart = cart.filter(item => item.name !== name);
  saveCart();
  renderCart();
}

function renderCart() {
  const el = document.getElementById('cart-items');
  const countEl = document.getElementById('cart-count');
  const totalEl = document.getElementById('cart-total');
  if (!el || !countEl || !totalEl) return;

  const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
  const count = cart.reduce((sum, item) => sum + item.qty, 0);
  countEl.textContent = count;
  totalEl.textContent = `$${total.toFixed(2)}`;

  if (!cart.length) {
    el.innerHTML = '<div class="text-center mt-12"><i class="ri-shopping-cart-2-line text-slate-200 text-5xl"></i><p class="text-slate-400 text-sm mt-3">Your cart is empty</p></div>';
    return;
  }

  el.innerHTML = cart.map(item => `
    <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-3">
      <div class="bg-navy-50 rounded-lg w-11 h-11 flex items-center justify-center shrink-0 overflow-hidden">
        ${item.image_url ? `<img src="${escapeAttr(item.image_url)}" alt="${escapeAttr(item.name)}" class="w-full h-full object-contain p-1">` : `<i class="ri-printer-fill text-navy-600 text-lg"></i>`}
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-slate-800 truncate">${escapeHtml(item.name)}</p>
        <p class="text-xs text-slate-400">Qty: ${item.qty} x $${item.price.toFixed(2)}</p>
      </div>
      <div class="text-right shrink-0">
        <p class="text-sm font-bold text-navy-600">$${(item.price * item.qty).toFixed(2)}</p>
        <button onclick="removeFromCart('${escapeAttr(item.name)}')" class="text-red-400 hover:text-red-600 text-xs mt-0.5">Remove</button>
      </div>
    </div>`).join('');
}

function showToast(msg) {
  const toast = document.getElementById('toast');
  const toastMsg = document.getElementById('toast-msg');
  if (!toast || !toastMsg) return;

  toastMsg.textContent = msg;
  toast.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
  toast.classList.add('opacity-100', 'translate-y-0');
  clearTimeout(window._pdToastTimer);
  window._pdToastTimer = setTimeout(() => {
    toast.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
    toast.classList.remove('opacity-100', 'translate-y-0');
  }, 2500);
}

function titleCase(value) {
  return String(value).replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, char => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }[char]));
}

function escapeAttr(value) {
  return escapeHtml(value).replace(/`/g, '&#096;');
}

function formatMoney(value) {
  return `$${(Number(value) || 0).toFixed(2)}`;
}
