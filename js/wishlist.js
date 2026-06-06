// ===== SHARED WISHLIST SYSTEM =====
// Uses localStorage so wishlist persists across all pages

const WL_KEY = 'gss_wishlist';

// Product data (same as products.js / product-detail.js)
let WL_PRODUCTS = [
  { id:1,  name:'HP DeskJet 4155e',          brand:'HP',     cat:'inkjet',   price:89.99,  oldPrice:119.99, color:'#f1f5f9', iconColor:'#1e293b' },
  { id:2,  name:'Canon PIXMA TR8620',         brand:'Canon',  cat:'allinone', price:149.99, oldPrice:179.99, color:'#f1f5f9', iconColor:'#475569' },
  { id:3,  name:'Brother HL-L2350DW',         brand:'Brother',cat:'laser',    price:109.99, oldPrice:139.99, color:'#fffbeb', iconColor:'#1d4ed8' },
  { id:4,  name:'Epson EcoTank ET-2800',      brand:'Epson',  cat:'inkjet',   price:174.99, oldPrice:249.99, color:'#ecfdf5', iconColor:'#059669' },
  { id:5,  name:'HP LaserJet Pro M404n',      brand:'HP',     cat:'business', price:249.99, oldPrice:399.99, color:'#f1f5f9', iconColor:'#1e293b' },
  { id:6,  name:'Canon imageCLASS MF743Cdw',  brand:'Canon',  cat:'business', price:449.99, oldPrice:549.99, color:'#f1f5f9', iconColor:'#475569' },
  { id:7,  name:'Brother MFC-J995DW',         brand:'Brother',cat:'allinone', price:199.99, oldPrice:249.99, color:'#fffbeb', iconColor:'#1d4ed8' },
  { id:8,  name:'Epson WorkForce WF-7840',    brand:'Epson',  cat:'business', price:299.99, oldPrice:379.99, color:'#ecfdf5', iconColor:'#059669' },
  { id:9,  name:'HP OfficeJet Pro 9015e',     brand:'HP',     cat:'allinone', price:219.99, oldPrice:279.99, color:'#f1f5f9', iconColor:'#1e293b' },
  { id:10, name:'Brother HL-L3270CDW',        brand:'Brother',cat:'laser',    price:279.99, oldPrice:329.99, color:'#fffbeb', iconColor:'#1d4ed8' },
  { id:11, name:'Xerox B210 Monochrome',      brand:'Xerox',  cat:'laser',    price:129.99, oldPrice:159.99, color:'#f8fafc', iconColor:'#64748b' },
  { id:12, name:'Canon PIXMA G620',           brand:'Canon',  cat:'inkjet',   price:199.99, oldPrice:229.99, color:'#f1f5f9', iconColor:'#475569' },
  { id:13, name:'HP 65XL Black Ink',          brand:'HP',     cat:'ink',      price:24.99,  oldPrice:29.99,  color:'#f1f5f9', iconColor:'#1e293b' },
  { id:14, name:'Canon PG-245XL Black',       brand:'Canon',  cat:'ink',      price:19.99,  oldPrice:24.99,  color:'#f1f5f9', iconColor:'#475569' },
  { id:15, name:'Brother TN760 Toner',        brand:'Brother',cat:'ink',      price:49.99,  oldPrice:64.99,  color:'#fffbeb', iconColor:'#1d4ed8' },
  { id:16, name:'Epson 502XL Color Set',      brand:'Epson',  cat:'ink',      price:39.99,  oldPrice:49.99,  color:'#ecfdf5', iconColor:'#059669' },
  { id:17, name:'Epson EcoTank ET-4850',      brand:'Epson',  cat:'allinone', price:349.99, oldPrice:449.99, color:'#ecfdf5', iconColor:'#059669' },
  { id:18, name:'HP Color LaserJet Pro M255dw',brand:'HP',    cat:'laser',    price:319.99, oldPrice:399.99, color:'#f1f5f9', iconColor:'#1e293b' },
];

// ---- Core helpers ----
function wlLoad() {
  try { return new Set(JSON.parse(localStorage.getItem(WL_KEY) || '[]')); }
  catch(e) { return new Set(); }
}
function wlSave(set) {
  localStorage.setItem(WL_KEY, JSON.stringify([...set]));
}

async function wlSyncProductsFromApi() {
  try {
    const response = await fetch(`api/products.php?limit=100&sort=newest&_=${Date.now()}`, { cache: 'no-store' });
    const result = await response.json();
    const apiProducts = result?.data?.products;
    if (!response.ok || !result.success || !Array.isArray(apiProducts)) {
      throw new Error('Invalid wishlist products response');
    }

    WL_PRODUCTS = apiProducts.map(wlNormalizeProduct);
    const activeIds = new Set(WL_PRODUCTS.map(p => p.id));
    const saved = wlLoad();
    const cleaned = new Set([...saved].map(Number).filter(id => activeIds.has(id)));
    if (cleaned.size !== saved.size) {
      wlSave(cleaned);
    }
    wlRefreshAll();
  } catch (error) {
    console.warn('Using fallback wishlist product data:', error);
  }
}

function wlNormalizeProduct(p) {
  const cat = p.cat || (p.category_slug === 'all-in-one' ? 'allinone' : p.category_slug === 'ink-toner' ? 'ink' : p.category_slug) || 'inkjet';
  return {
    id: Number(p.id),
    name: p.name || 'Product',
    brand: p.brand || p.brand_name || '',
    cat,
    price: Number(p.price) || 0,
    oldPrice: Number(p.oldPrice ?? p.old_price ?? 0) || 0,
    color: p.color || '#f1f5f9',
    iconColor: p.iconColor || '#1e293b',
  };
}

// ---- Add / Remove ----
function wlAdd(id) {
  const s = wlLoad(); s.add(id); wlSave(s);
  wlRefreshAll();
  wlShowToast('Added to Wishlist ❤️');
}
function wlRemove(id) {
  const s = wlLoad(); s.delete(id); wlSave(s);
  wlRefreshAll();
  wlShowToast('Removed from Wishlist');
}
function wlToggle(id) {
  wlLoad().has(id) ? wlRemove(id) : wlAdd(id);
}

// ---- Refresh all heart buttons + badge on current page ----
function wlRefreshAll() {
  const s = wlLoad();

  // Navbar count badge
  const badge = document.getElementById('wl-count');
  if (badge) {
    if (s.size > 0) {
      badge.textContent = s.size;
      badge.classList.remove('hidden');
      badge.classList.add('flex');
    } else {
      badge.classList.add('hidden');
      badge.classList.remove('flex');
    }
  }

  // Navbar heart icon — shows filled if any items in wishlist
  const navBtns = document.querySelectorAll('button[onclick="toggleWishlistDrawer()"]');
  navBtns.forEach(navBtn => {
    const icon = navBtn.querySelector('i');
    if (icon) icon.className = s.size > 0 ? 'ri-heart-fill text-xl text-red-500' : 'ri-heart-3-line text-xl';
  });

  // product-detail page: main-wl button state
  const mainWl = document.getElementById('main-wl');
  if (mainWl && window.currentProduct) {
    const icon = mainWl.querySelector('i');
    if (icon) icon.className = s.has(window.currentProduct.id)
      ? 'ri-heart-fill text-red-500 text-lg'
      : 'ri-heart-3-line text-slate-400 text-lg';
  }

  // product cards (products.php) — buttons with data-wl-id
  document.querySelectorAll('[data-wl-id]').forEach(btn => {
    const id = parseInt(btn.dataset.wlId);
    const icon = btn.querySelector('i');
    if (!icon) return;
    if (s.has(id)) {
      icon.className = icon.className.replace('ri-heart-3-line','ri-heart-fill').replace('text-slate-400','text-red-500').replace('hover:text-red-400','');
      icon.classList.add('ri-heart-fill','text-red-500');
      icon.classList.remove('ri-heart-3-line','text-slate-400');
    } else {
      icon.classList.add('ri-heart-3-line','text-slate-400');
      icon.classList.remove('ri-heart-fill','text-red-500');
    }
  });

  // Refresh wishlist drawer if open
  const drawer = document.getElementById('wl-drawer');
  if (drawer && !drawer.classList.contains('translate-x-full')) {
    wlRenderDrawer();
  }
}

// ---- Wishlist Drawer ----
function toggleWishlistDrawer() {
  const drawer = document.getElementById('wl-drawer');
  const overlay = document.getElementById('wl-overlay');
  if (!drawer) return;
  const isOpen = !drawer.classList.contains('translate-x-full');
  if (isOpen) {
    drawer.classList.add('translate-x-full');
    overlay.classList.add('hidden');
  } else {
    wlRenderDrawer();
    drawer.classList.remove('translate-x-full');
    overlay.classList.remove('hidden');
  }
}

function wlRenderDrawer() {
  const s = wlLoad();
  const container = document.getElementById('wl-items');
  if (!container) return;

  if (s.size === 0) {
    container.innerHTML = `
      <div class="text-center mt-16 px-4">
        <i class="ri-heart-3-line text-slate-200 text-6xl"></i>
        <p class="text-slate-400 text-sm mt-3 font-medium">Your wishlist is empty</p>
        <a href="products.php" class="inline-block mt-4 text-xs font-semibold text-navy-600 hover:underline">Browse Products</a>
      </div>`;
    return;
  }

  const items = [...s].map(id => WL_PRODUCTS.find(p => p.id === id)).filter(Boolean);
  container.innerHTML = items.map(p => `
    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3">
      <div class="flex items-center gap-3">
      <a href="product-detail.php?id=${p.id}" class="rounded-2xl flex items-center justify-center w-16 h-16 shrink-0" style="background:${p.color}">
        <i class="ri-${p.cat==='ink'?'ink-bottle':'printer'}-fill text-3xl" style="color:${p.iconColor}"></i>
      </a>
      <div class="flex-1 min-w-0">
        <a href="product-detail.php?id=${p.id}" class="text-sm font-black text-slate-800 hover:text-navy-600 leading-snug block">${p.name}</a>
        <p class="text-xs text-slate-400 mt-0.5">${p.brand}</p>
        <p class="text-lg font-black text-navy-700 mt-1">$${p.price.toFixed(2)}
          ${p.oldPrice ? `<span class="text-xs text-slate-400 font-normal line-through ml-1">$${p.oldPrice.toFixed(2)}</span>` : ''}
        </p>
      </div>
      </div>
      <div class="mt-3 grid grid-cols-[1fr_44px] gap-2">
        <button onclick="wlAddToCartFromDrawer(${p.id})" title="Add to cart"
          class="h-11 bg-navy-600 hover:bg-navy-700 text-white rounded-xl flex items-center justify-center gap-2 transition text-sm font-bold">
          <i class="ri-shopping-cart-2-line text-lg"></i> Add
        </button>
        <button onclick="wlRemove(${p.id})" title="Remove"
          class="h-11 border border-red-200 bg-white hover:bg-red-50 text-red-500 rounded-xl flex items-center justify-center transition" aria-label="Remove ${p.name} from wishlist">
          <i class="ri-delete-bin-line text-lg"></i>
        </button>
      </div>
    </div>`).join('');
}

function wlAddToCartFromDrawer(id) {
  const p = WL_PRODUCTS.find(x => x.id === id);
  if (!p) return;
  // Try to use the page's addToCart if available
  if (typeof addToCart === 'function') {
    addToCart(p.name, p.price);
  } else {
    // Fallback: write to cart localStorage directly
    const cart = JSON.parse(localStorage.getItem('gss_cart') || '[]');
    const ex = cart.find(i => i.name === p.name);
    ex ? ex.qty++ : cart.push({ name: p.name, price: p.price, qty: 1 });
    localStorage.setItem('gss_cart', JSON.stringify(cart));
    wlShowToast(`${p.name.split(' ').slice(0,3).join(' ')} added to cart!`);
  }
}

// ---- Toast ----
function wlShowToast(msg) {
  // Reuse page toast if available
  const t = document.getElementById('toast');
  const tm = document.getElementById('toast-msg');
  if (t && tm) {
    tm.textContent = msg;
    t.classList.remove('opacity-0','translate-y-4','pointer-events-none');
    t.classList.add('opacity-100','translate-y-0');
    clearTimeout(window._wlToastTimer);
    window._wlToastTimer = setTimeout(() => {
      t.classList.add('opacity-0','translate-y-4');
      t.classList.remove('opacity-100','translate-y-0');
    }, 2500);
    return;
  }
  // Fallback: create a mini toast
  let el = document.getElementById('wl-toast-fallback');
  if (!el) {
    el = document.createElement('div');
    el.id = 'wl-toast-fallback';
    el.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900 text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-xl transition-all duration-300 opacity-0 translate-y-4 pointer-events-none';
    document.body.appendChild(el);
  }
  el.textContent = msg;
  el.classList.remove('opacity-0','translate-y-4','pointer-events-none');
  el.classList.add('opacity-100','translate-y-0');
  clearTimeout(window._wlToastTimer);
  window._wlToastTimer = setTimeout(() => {
    el.classList.add('opacity-0','translate-y-4');
    el.classList.remove('opacity-100','translate-y-0');
  }, 2500);
}

// ---- Init on page load ----
document.addEventListener('DOMContentLoaded', () => {
  wlRefreshAll();
  wlSyncProductsFromApi();
});

// Listen for storage changes (other tabs)
window.addEventListener('storage', (e) => {
  if (e.key === WL_KEY) wlRefreshAll();
});
