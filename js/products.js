// ===== PRODUCT DATA =====
let PRODUCTS = [];

// ===== STATE =====
let cart = JSON.parse(localStorage.getItem('gss_cart') || '[]');
let currentView = 'grid';
let wishlist = wlLoad(); // load from localStorage via wishlist.js
let currentPage = 1;
const PRODUCTS_PER_PAGE = 12;

// ===== UTILS =====
const debounce = window.debounce || function (func, delay) {
  let timeoutId;
  return function (...args) {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      func.apply(this, args);
    }, delay);
  };
};

// ===== INIT =====
document.addEventListener('DOMContentLoaded', async () => {
  const params = new URLSearchParams(window.location.search);
  const cat = params.get('cat') || 'all';
  const brand = params.get('brand') || '';
  const search = params.get('q') || params.get('search') || '';
  if (cat !== 'all') {
    const radio = document.querySelector(`input[name="cat"][value="${cat}"]`);
    if (radio) radio.checked = true;
  }
  if (brand !== '') {
    document.querySelectorAll('.brand-cb').forEach(cb => {
      cb.checked = cb.value.toLowerCase() === brand.toLowerCase();
    });
  }
  const searchInput = document.getElementById('search-input');
  if (searchInput) {
    if (search !== '') {
      searchInput.value = search;
    }
    // Add debounced input event listener
    searchInput.addEventListener('input', debounce(() => {
      applyFilters();
    }, 300));
  }
  document.querySelectorAll('[data-product-search-form]').forEach(form => {
    form.addEventListener('submit', event => {
      event.preventDefault();
      applyFilters();
    });
  });
  await loadProductsFromApi();
  applyFilters();
});

async function loadProductsFromApi() {
  try {
    const response = await fetch(`api/products.php?limit=100&sort=newest&_=${Date.now()}`, { cache: 'no-store' });
    const result = await response.json();
    const apiProducts = result?.data?.products;
    if (!response.ok || !result.success || !Array.isArray(apiProducts)) {
      throw new Error('Invalid products response');
    }
    PRODUCTS = apiProducts.map(normalizeProductForList);
  } catch (error) {
    PRODUCTS = [];
    console.warn('Products API failed; no stale products rendered:', error);
  }
}

function normalizeProductForList(p) {
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
    desc: p.desc || p.description || '',
    ppm: p.ppm ? Number(p.ppm) : null,
    newest: Boolean(p.newest),
    image_url: p.image_url || p.imageUrl || '',
    discount: Number(p.discount) || (oldPrice > price && oldPrice > 0 ? Math.round(((oldPrice - price) / oldPrice) * 100) : 0),
  };
}

// ===== FILTER & RENDER =====
function applyFilters() {
  currentPage = 1;
  const cat = document.querySelector('input[name="cat"]:checked')?.value || 'all';
  const maxPrice = parseFloat(document.getElementById('price-max').value) || 600;
  const fromPrice = parseFloat(document.getElementById('price-from').value) || 0;
  const toPrice = parseFloat(document.getElementById('price-to').value) || maxPrice;
  const minRating = parseFloat(document.querySelector('input[name="rating"]:checked')?.value) || 0;
  const brands = [...document.querySelectorAll('.brand-cb:checked')].map(c => c.value);
  const feats = [...document.querySelectorAll('.feat-cb:checked')].map(c => c.value);
  const saleOnly = document.getElementById('sale-only').checked;
  const search = document.getElementById('search-input').value.toLowerCase().trim();
  const sort = document.getElementById('sort-select').value;

  let filtered = PRODUCTS.filter(p => {
    if (cat !== 'all' && p.cat !== cat) return false;
    if (p.price > Math.min(maxPrice, toPrice)) return false;
    if (p.price < fromPrice) return false;
    if (p.rating < minRating) return false;
    if (brands.length && !brands.includes(p.brand)) return false;
    if (feats.length && !feats.every(f => p.features.includes(f))) return false;
    if (saleOnly && !p.badge.includes('SALE') && !p.badge.includes('DEAL')) return false;
    if (search && !productSearchHaystack(p).includes(search)) return false;
    return true;
  });

  // Sort
  if (sort === 'price-asc') filtered.sort((a, b) => a.price - b.price);
  else if (sort === 'price-desc') filtered.sort((a, b) => b.price - a.price);
  else if (sort === 'rating') filtered.sort((a, b) => b.rating - a.rating);
  else if (sort === 'newest') filtered.sort((a, b) => b.newest - a.newest);
  else if (sort === 'discount') filtered.sort((a, b) => b.discount - a.discount);

  // Update title
  const dynamicCatLabels = Object.fromEntries((window.GSS_TAXONOMY?.categories || []).map(category => [category.frontend_key || category.slug, category.name]));
  const catLabels = { all: 'All Printers', inkjet: 'Inkjet Printers', laser: 'Laser Printers', allinone: 'All-in-One Printers', business: 'Business Printers', ink: 'Ink & Toner', deals: 'Flash Deals', ...dynamicCatLabels };
  document.getElementById('page-title').textContent = catLabels[cat] || 'All Printers';
  document.getElementById('breadcrumb-cat').textContent = catLabels[cat] || 'All Printers';
  renderProducts(filtered);
  renderChips(cat, brands, feats, saleOnly, search);
  syncSearchUrl(search, cat, brands);
}

function productSearchHaystack(p) {
  return [
    p.name,
    p.brand,
    p.brand_slug,
    p.cat,
    p.category_name,
    p.category_slug,
    p.model,
    p.badge,
    p.desc,
    p.description,
    ...(p.features || []),
    ...(p.specs || []),
  ].filter(Boolean).join(' ').toLowerCase();
}

function syncSearchUrl(search, cat, brands) {
  const params = new URLSearchParams(window.location.search);
  if (search) params.set('q', search); else params.delete('q');
  if (cat && cat !== 'all') params.set('cat', cat); else params.delete('cat');
  if (brands.length === 1) params.set('brand', brands[0].toLowerCase()); else params.delete('brand');
  const query = params.toString();
  const nextUrl = `${window.location.pathname}${query ? `?${query}` : ''}`;
  window.history.replaceState({}, '', nextUrl);
}

function renderProducts(list) {
  const grid = document.getElementById('product-grid');
  const empty = document.getElementById('empty-state');
  const pagination = document.getElementById('pagination');
  if (!list.length) {
    grid.innerHTML = '';
    pagination.innerHTML = '';
    document.getElementById('result-count').textContent = 'Showing 0 products';
    empty.classList.remove('hidden');
    return;
  }
  empty.classList.add('hidden');

  const totalPages = Math.ceil(list.length / PRODUCTS_PER_PAGE);
  currentPage = Math.min(Math.max(currentPage, 1), totalPages);
  const start = (currentPage - 1) * PRODUCTS_PER_PAGE;
  const pageItems = list.slice(start, start + PRODUCTS_PER_PAGE);
  const from = start + 1;
  const to = Math.min(start + PRODUCTS_PER_PAGE, list.length);
  document.getElementById('result-count').textContent = `Showing ${from}-${to} of ${list.length} product${list.length !== 1 ? 's' : ''}`;

  if (currentView === 'grid') {
    grid.className = 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5';
    grid.innerHTML = pageItems.map(p => gridCard(p)).join('');
  } else {
    grid.className = 'flex flex-col gap-4';
    grid.innerHTML = pageItems.map(p => listCard(p)).join('');
  }
  renderPagination(list, totalPages);
}

function renderPagination(list, totalPages) {
  const pagination = document.getElementById('pagination');
  if (totalPages <= 1) {
    pagination.innerHTML = '';
    return;
  }

  const pageBtn = page => `
    <button onclick="goToPage(${page})" class="w-10 h-10 rounded-xl text-sm font-bold transition ${page === currentPage ? 'bg-navy-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:border-navy-400 hover:text-navy-600'}">
      ${page}
    </button>`;

  pagination.innerHTML = `
    <button onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-600 hover:border-navy-400 hover:text-navy-600 transition disabled:opacity-40 disabled:pointer-events-none flex items-center gap-1.5">
      <i class="ri-arrow-left-s-line"></i> Prev
    </button>
    ${Array.from({ length: totalPages }, (_, i) => pageBtn(i + 1)).join('')}
    <button onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-600 hover:border-navy-400 hover:text-navy-600 transition disabled:opacity-40 disabled:pointer-events-none flex items-center gap-1.5">
      Next <i class="ri-arrow-right-s-line"></i>
    </button>`;
}

function goToPage(page) {
  const cat = document.querySelector('input[name="cat"]:checked')?.value || 'all';
  const maxPrice = parseFloat(document.getElementById('price-max').value) || 600;
  const fromPrice = parseFloat(document.getElementById('price-from').value) || 0;
  const toPrice = parseFloat(document.getElementById('price-to').value) || maxPrice;
  const minRating = parseFloat(document.querySelector('input[name="rating"]:checked')?.value) || 0;
  const brands = [...document.querySelectorAll('.brand-cb:checked')].map(c => c.value);
  const feats = [...document.querySelectorAll('.feat-cb:checked')].map(c => c.value);
  const saleOnly = document.getElementById('sale-only').checked;
  const search = document.getElementById('search-input').value.toLowerCase().trim();
  const sort = document.getElementById('sort-select').value;

  let filtered = PRODUCTS.filter(p => {
    if (cat !== 'all' && p.cat !== cat) return false;
    if (p.price > Math.min(maxPrice, toPrice)) return false;
    if (p.price < fromPrice) return false;
    if (p.rating < minRating) return false;
    if (brands.length && !brands.includes(p.brand)) return false;
    if (feats.length && !feats.every(f => p.features.includes(f))) return false;
    if (saleOnly && !p.badge.includes('SALE') && !p.badge.includes('DEAL')) return false;
    if (feats.length && !(p.features && feats.every(f => p.features.includes(f)))) return false;
    if (saleOnly && !(p.badge && (p.badge.includes('SALE') || p.badge.includes('DEAL')))) return false;
    if (search && !productSearchHaystack(p).includes(search)) return false;
    return true;
  });

  if (sort === 'price-asc') filtered.sort((a, b) => a.price - b.price);
  else if (sort === 'price-desc') filtered.sort((a, b) => b.price - a.price);
  else if (sort === 'rating') filtered.sort((a, b) => b.rating - a.rating);
  else if (sort === 'newest') filtered.sort((a, b) => b.newest - a.newest);
  else if (sort === 'discount') filtered.sort((a, b) => b.discount - a.discount);

  currentPage = page;
  renderProducts(filtered);
  document.getElementById('product-grid').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function starsHtml(r) {
  const full = Math.floor(r);
  const half = r % 1 >= 0.5;
  let s = '';
  for (let i = 0; i < full; i++) s += '<i class="ri-star-fill"></i>';
  if (half) s += '<i class="ri-star-half-fill"></i>';
  for (let i = full + (half ? 1 : 0); i < 5; i++) s += '<i class="ri-star-line"></i>';
  return s;
}

function badgeCls(c) {
  return { red: 'bg-red-500', green: 'bg-emerald-500', amber: 'bg-amber2-500', navy: 'bg-navy-600' }[c] || 'bg-slate-500';
}

function gridCard(p) {
  const saved = p.oldPrice ? (p.oldPrice - p.price).toFixed(2) : null;
  const wl = wlLoad().has(p.id);
  return `
  <div class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden group relative">
    <!-- Image area -->
    <div class="relative h-48 flex items-center justify-center p-6" style="background:${p.color}">
      ${p.image_url ? `<img src="${p.image_url}" class="transition-transform duration-300 group-hover:scale-110 w-full h-full object-contain">` : `<i class="ri-${p.cat === 'ink' ? 'ink-bottle' : 'printer'}-fill transition-transform duration-300 group-hover:scale-110" style="font-size:90px;color:${p.iconColor};line-height:1"></i>`}
      ${p.badge ? `<span class="absolute top-3 left-3 ${badgeCls(p.badgeColor)} text-white text-[10px] font-bold px-2 py-0.5 rounded-md ${p.badgeColor === 'red' ? 'badge-pulse' : ''}">${p.badge}</span>` : ''}
      <button onclick="toggleWishlist(${p.id},this)" data-wl-id="${p.id}" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 hover:bg-white flex items-center justify-center shadow transition">
        <i class="${wl ? 'ri-heart-fill text-red-500' : 'ri-heart-3-line text-slate-400 hover:text-red-400'} text-base"></i>
      </button>
      <!-- Quick view on hover -->
      <div class="absolute inset-x-0 bottom-0 flex justify-center pb-3 opacity-0 group-hover:opacity-100 transition-all duration-200 translate-y-2 group-hover:translate-y-0">
        <button onclick="openQV(${p.id})" class="bg-white/90 backdrop-blur-sm text-navy-700 text-xs font-bold px-4 py-1.5 rounded-full shadow-lg hover:bg-navy-600 hover:text-white transition flex items-center gap-1.5">
          <i class="ri-eye-line"></i> Quick View
        </button>
      </div>
    </div>
    <!-- Info -->
    <div class="p-4">
      <div class="flex items-center justify-between mb-0.5">
        <p class="text-[10px] font-bold uppercase tracking-widest" style="color:${p.iconColor}">${p.brand}</p>
        ${p.newest ? '<span class="text-[10px] bg-navy-50 text-navy-600 font-bold px-1.5 py-0.5 rounded">NEW</span>' : ''}
      </div>
      <h3 class="font-bold text-slate-800 text-sm leading-snug">${p.name}</h3>
      <p class="text-xs text-slate-400 mt-0.5 capitalize">${p.cat === 'allinone' ? 'All-in-One' : p.cat === 'ink' ? 'Ink & Toner' : p.cat.charAt(0).toUpperCase() + p.cat.slice(1)}</p>
      <!-- Stars -->
      <div class="flex items-center gap-1 mt-2 text-amber2-400 text-xs">
        ${starsHtml(p.rating)}
        <span class="text-slate-500 ml-1 font-semibold">${p.rating.toFixed(1)}</span>
        <span class="text-slate-400">(${p.reviews})</span>
      </div>
      <!-- Features -->
      <div class="flex flex-wrap gap-1 mt-2">
        ${p.features.slice(0, 3).map(f => `<span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-md font-medium capitalize">${f}</span>`).join('')}
      </div>
      <!-- Price -->
      <div class="flex items-baseline gap-2 mt-3">
        <span class="text-lg font-black text-slate-800">$${p.price.toFixed(2)}</span>
        ${p.oldPrice ? `<span class="text-xs text-slate-400 line-through">$${p.oldPrice.toFixed(2)}</span>` : ''}
        ${saved ? `<span class="text-xs text-emerald-600 font-bold">Save $${saved}</span>` : ''}
      </div>
      <!-- CTA -->
      <div class="grid grid-cols-[48px_1fr] gap-2 mt-3 items-stretch">
        <button onclick="addToCart('${p.name.replace(/'/g, "\\'")}',${p.price},'${(p.image_url || '').replace(/'/g, "\\'")}')" class="h-10 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add ${p.name.replace(/"/g, '&quot;')} to cart">
          <i class="ri-shopping-cart-2-line text-[26px] leading-none"></i>
        </button>
        <button onclick="buyNow('${p.name.replace(/'/g, "\\'")}',${p.price},'${(p.image_url || '').replace(/'/g, "\\'")}')" class="btn-gradient h-10 w-full text-white font-bold rounded-xl transition text-xs flex items-center justify-center gap-1.5">
          <i class="ri-flashlight-line"></i> Buy Now
        </button>
      </div>
      <a href="product-detail.php?id=${p.id}" class="block text-center text-xs text-navy-600 hover:underline mt-2 font-semibold">View Full Details →</a>
    </div>
  </div>`;
}

function listCard(p) {
  const saved = p.oldPrice ? (p.oldPrice - p.price).toFixed(2) : null;
  const wl = wlLoad().has(p.id);
  return `
  <div class="card-lift bg-white border border-slate-200 rounded-2xl overflow-hidden flex gap-0">
    <div class="w-40 sm:w-52 shrink-0 flex items-center justify-center p-5 relative" style="background:${p.color}">
      ${p.image_url ? `<img src="${p.image_url}" class="w-full h-full object-contain p-2">` : `<i class="ri-${p.cat === 'ink' ? 'ink-bottle' : 'printer'}-fill" style="font-size:70px;color:${p.iconColor};line-height:1"></i>`}
      ${p.badge ? `<span class="absolute top-2 left-2 ${badgeCls(p.badgeColor)} text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md">${p.badge}</span>` : ''}
    </div>
    <div class="flex-1 p-5 flex flex-col justify-between">
      <div>
        <div class="flex items-start justify-between gap-2">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest mb-0.5" style="color:${p.iconColor}">${p.brand}</p>
            <h3 class="font-bold text-slate-800">${p.name}</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed max-w-lg">${p.desc}</p>
          </div>
          <button onclick="toggleWishlist(${p.id},this)" data-wl-id="${p.id}" class="shrink-0 w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center hover:border-red-300 transition">
            <i class="${wl ? 'ri-heart-fill text-red-500' : 'ri-heart-3-line text-slate-400'} text-sm"></i>
          </button>
        </div>
        <div class="flex items-center gap-1 mt-2 text-amber2-400 text-xs">
          ${starsHtml(p.rating)}<span class="text-slate-500 ml-1 font-semibold">${p.rating.toFixed(1)}</span><span class="text-slate-400">(${p.reviews})</span>
        </div>
        <div class="flex flex-wrap gap-1 mt-2">
          ${p.features.map(f => `<span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-md font-medium capitalize">${f}</span>`).join('')}
        </div>
      </div>
      <div class="flex items-center justify-between mt-4 flex-wrap gap-3">
        <div class="flex items-baseline gap-2">
          <span class="text-xl font-black text-slate-800">$${p.price.toFixed(2)}</span>
          ${p.oldPrice ? `<span class="text-sm text-slate-400 line-through">$${p.oldPrice.toFixed(2)}</span>` : ''}
          ${saved ? `<span class="text-xs text-emerald-600 font-bold">Save $${saved}</span>` : ''}
        </div>
        <div class="flex gap-2">
          <button onclick="openQV(${p.id})" class="border border-slate-200 hover:border-navy-500 text-slate-500 hover:text-navy-600 font-semibold px-4 py-2 rounded-xl transition text-xs flex items-center gap-1.5">
            <i class="ri-eye-line"></i> Quick View
          </button>
          <a href="product-detail.php?id=${p.id}" class="border border-navy-200 bg-navy-50 hover:bg-navy-100 text-navy-700 font-semibold px-4 py-2 rounded-xl transition text-xs flex items-center gap-1.5">
            <i class="ri-information-line"></i> Details
          </a>
          <button onclick="addToCart('${p.name.replace(/'/g, "\\'")}',${p.price},'${(p.image_url || '').replace(/'/g, "\\'")}')" class="text-navy-700 hover:text-blue-700 transition w-12 h-10 flex items-center justify-center" title="Add to Cart" aria-label="Add ${p.name.replace(/"/g, '&quot;')} to cart">
            <i class="ri-shopping-cart-2-line text-[26px] leading-none"></i>
          </button>
          <button onclick="buyNow('${p.name.replace(/'/g, "\\'")}',${p.price},'${(p.image_url || '').replace(/'/g, "\\'")}')" class="btn-gradient text-white font-bold px-5 py-2 rounded-xl transition text-xs flex items-center gap-1.5">
            <i class="ri-flashlight-line"></i> Buy Now
          </button>
        </div>
      </div>
    </div>
  </div>`;
}

// ===== QUICK VIEW =====
function openQV(id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  const saved = p.oldPrice ? (p.oldPrice - p.price).toFixed(2) : null;
  document.getElementById('qv-content').innerHTML = `
    <div class="flex flex-col md:flex-row gap-8">
      <div class="md:w-64 shrink-0 rounded-2xl flex items-center justify-center p-8 h-56 md:h-auto" style="background:${p.color}">
        ${p.image_url ? `<img src="${p.image_url}" class="w-full h-full object-contain p-4">` : `<i class="ri-${p.cat === 'ink' ? 'ink-bottle' : 'printer'}-fill" style="font-size:110px;color:${p.iconColor};line-height:1"></i>`}
      </div>
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <span class="text-xs font-bold uppercase tracking-widest" style="color:${p.iconColor}">${p.brand}</span>
          ${p.badge ? `<span class="${badgeCls(p.badgeColor)} text-white text-[10px] font-bold px-2 py-0.5 rounded-md">${p.badge}</span>` : ''}
        </div>
        <h2 class="text-2xl font-black text-slate-800">${p.name}</h2>
        <div class="flex items-center gap-1 mt-2 text-amber2-400 text-sm">
          ${starsHtml(p.rating)}<span class="text-slate-400 text-xs ml-1">${p.rating.toFixed(1)} · ${p.reviews} reviews</span>
        </div>
        <p class="text-slate-500 text-sm mt-3 leading-relaxed">${p.desc}</p>
        <!-- Specs -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
          ${p.specs.map(s => `<div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 rounded-lg px-3 py-1.5"><i class="ri-checkbox-circle-line text-navy-500"></i>${s}</div>`).join('')}
        </div>
        <!-- Features -->
        <div class="flex flex-wrap gap-1.5 mt-3">
          ${p.features.map(f => `<span class="text-xs bg-navy-50 text-navy-700 font-semibold px-2.5 py-1 rounded-full capitalize flex items-center gap-1"><i class="ri-check-line"></i>${f}</span>`).join('')}
        </div>
        <!-- Price + CTA -->
        <div class="mt-5 flex items-center gap-3 flex-wrap">
          <span class="text-3xl font-black text-slate-800">$${p.price.toFixed(2)}</span>
          ${p.oldPrice ? `<span class="text-base text-slate-400 line-through">$${p.oldPrice.toFixed(2)}</span>` : ''}
          ${saved ? `<span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-1 rounded-lg">Save $${saved}</span>` : ''}
        </div>
        <div class="grid grid-cols-[56px_1fr] gap-3 mt-4 items-stretch">
          <button onclick="addToCart('${p.name.replace(/'/g, "\\'")}',${p.price},'${(p.image_url || '').replace(/'/g, "\\'")}');closeQV()" class="h-12 w-full text-navy-700 hover:text-blue-700 transition flex items-center justify-center" title="Add to Cart" aria-label="Add ${p.name.replace(/"/g, '&quot;')} to cart">
            <i class="ri-shopping-cart-2-line text-[30px] leading-none"></i>
          </button>
          <button onclick="buyNow('${p.name.replace(/'/g, "\\'")}',${p.price},'${(p.image_url || '').replace(/'/g, "\\'")}')" class="btn-gradient h-12 w-full text-white font-bold rounded-xl transition flex items-center justify-center gap-2 text-sm">
            <i class="ri-flashlight-line"></i> Buy Now
          </button>
        </div>
        <div class="mt-4 flex flex-wrap gap-4 text-xs text-slate-500">
          <span class="flex items-center gap-1"><i class="ri-truck-line text-navy-500"></i> Free shipping $99+</span>
          <span class="flex items-center gap-1"><i class="ri-shield-check-line text-navy-500"></i> 2-Year Warranty</span>
          <span class="flex items-center gap-1"><i class="ri-refresh-line text-navy-500"></i> 7-Day Returns</span>
        </div>
      </div>
    </div>`;
  const modal = document.getElementById('qv-modal');
  const inner = document.getElementById('qv-inner');
  modal.classList.remove('opacity-0', 'pointer-events-none');
  setTimeout(() => { inner.classList.remove('scale-95', 'opacity-0'); }, 10);
  document.body.style.overflow = 'hidden';
}

function closeQV() {
  const modal = document.getElementById('qv-modal');
  const inner = document.getElementById('qv-inner');
  inner.classList.add('scale-95', 'opacity-0');
  setTimeout(() => { modal.classList.add('opacity-0', 'pointer-events-none'); document.body.style.overflow = ''; }, 220);
}

// ===== WISHLIST =====
function toggleWishlist(id, btn) {
  const icon = btn.querySelector('i');
  wlToggle(id);
  const isAdded = wlLoad().has(id);
  if (icon) {
    icon.className = isAdded ? 'ri-heart-fill text-red-500 text-base' : 'ri-heart-3-line text-slate-400 hover:text-red-400 text-base';
  }
  if (typeof wlRefreshAll === 'function') wlRefreshAll();
}

// ===== VIEW TOGGLE =====
function setView(v) {
  currentView = v;
  document.getElementById('btn-grid').className = v === 'grid' ? 'px-3 py-2 bg-navy-600 text-white transition' : 'px-3 py-2 bg-white text-slate-400 hover:text-navy-600 transition';
  document.getElementById('btn-list').className = v === 'list' ? 'px-3 py-2 bg-navy-600 text-white transition' : 'px-3 py-2 bg-white text-slate-400 hover:text-navy-600 transition';
  applyFilters();
}

// ===== PRICE LABEL =====
function updatePriceLabel() {
  document.getElementById('price-max-label').textContent = document.getElementById('price-max').value;
}

// ===== CLEAR FILTERS =====
function clearAllFilters() {
  document.querySelector('input[name="cat"][value="all"]').checked = true;
  document.getElementById('price-max').value = 600;
  document.getElementById('price-from').value = '';
  document.getElementById('price-to').value = '';
  document.getElementById('price-max-label').textContent = '600';
  document.querySelectorAll('.brand-cb').forEach(c => c.checked = false);
  document.querySelectorAll('.feat-cb').forEach(c => c.checked = false);
  document.querySelector('input[name="rating"][value="0"]').checked = true;
  document.getElementById('sale-only').checked = false;
  document.getElementById('search-input').value = '';
  applyFilters();
}

// ===== ACTIVE CHIPS =====
function renderChips(cat, brands, feats, saleOnly, search) {
  const wrap = document.getElementById('active-chips');
  const chips = [];
  if (cat !== 'all') chips.push(`<span class="chip">${cat} <button onclick="document.querySelector('input[name=cat][value=all]').checked=true;applyFilters()" class="ml-1 hover:text-red-500"><i class="ri-close-line"></i></button></span>`);
  brands.forEach(b => chips.push(`<span class="chip">${b} <button onclick="document.querySelector('.brand-cb[value=${b}]').checked=false;applyFilters()" class="ml-1 hover:text-red-500"><i class="ri-close-line"></i></button></span>`));
  feats.forEach(f => chips.push(`<span class="chip">${f} <button onclick="document.querySelector('.feat-cb[value=${f}]').checked=false;applyFilters()" class="ml-1 hover:text-red-500"><i class="ri-close-line"></i></button></span>`));
  if (saleOnly) chips.push(`<span class="chip">On Sale <button onclick="document.getElementById('sale-only').checked=false;applyFilters()" class="ml-1 hover:text-red-500"><i class="ri-close-line"></i></button></span>`);
  if (search) chips.push(`<span class="chip">"${search}" <button onclick="document.getElementById('search-input').value='';applyFilters()" class="ml-1 hover:text-red-500"><i class="ri-close-line"></i></button></span>`);
  if (chips.length) {
    wrap.innerHTML = chips.join('') + `<button onclick="clearAllFilters()" class="text-xs text-red-500 font-semibold hover:underline ml-1">Clear all</button>`;
    wrap.classList.remove('hidden');
  } else {
    wrap.classList.add('hidden');
  }
}

// ===== MOBILE FILTER =====
function toggleMobileFilter() {
  const drawer = document.getElementById('mob-filter-drawer');
  const overlay = document.getElementById('mob-filter-overlay');
  const open = !drawer.classList.contains('translate-y-full');
  drawer.classList.toggle('translate-y-full', open);
  overlay.classList.toggle('hidden', open);
}

function setCatMob(val) {
  const radio = document.querySelector(`input[name="cat"][value="${val}"]`);
  if (radio) { radio.checked = true; applyFilters(); }
}

// ===== CART =====
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
  localStorage.setItem('gss_cart', JSON.stringify(cart));
  renderCart();
  showToast(`${name.split(' ').slice(0, 3).join(' ')} added to cart!`);
}

function buyNow(name, price, imageUrl = '') {
  const ex = cart.find(i => i.name === name);
  ex ? ex.qty++ : cart.push({ name, price, qty: 1, image_url: imageUrl });
  if (ex && imageUrl && !ex.image_url) ex.image_url = imageUrl;
  localStorage.setItem('gss_cart', JSON.stringify(cart));
  window.location.href = 'checkout.php';
}

function removeFromCart(name) {
  cart = cart.filter(i => i.name !== name);
  localStorage.setItem('gss_cart', JSON.stringify(cart));
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

// ===== TOAST =====
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.remove('opacity-0', 'translate-y-4');
  t.classList.add('opacity-100', 'translate-y-0');
  setTimeout(() => { t.classList.add('opacity-0', 'translate-y-4'); t.classList.remove('opacity-100', 'translate-y-0'); }, 2500);
}

// Close QV on Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeQV(); });
