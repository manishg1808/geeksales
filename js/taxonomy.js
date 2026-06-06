(function () {
  const TAXONOMY_URL = `api/taxonomy.php?_=${Date.now()}`;
  const colorClasses = {
    navy: ['bg-navy-50', 'text-navy-600', 'group-hover:bg-navy-600'],
    slate: ['bg-slate-100', 'text-slate-600', 'group-hover:bg-slate-700'],
    emerald: ['bg-emerald-50', 'text-emerald-600', 'group-hover:bg-emerald-600'],
    amber: ['bg-amber2-50', 'text-amber2-600', 'group-hover:bg-amber2-500'],
    red: ['bg-red-50', 'text-red-600', 'group-hover:bg-red-600'],
  };
  const categoryCardThemes = {
    navy: 'from-navy-500 to-navy-800',
    slate: 'from-slate-500 to-slate-800',
    emerald: 'from-emerald-500 to-emerald-800',
    amber: 'from-amber2-500 to-amber2-800',
    red: 'from-red-500 to-red-800',
  };

  function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
    }[char]));
  }

  function categoryHref(category) {
    return `products.php?cat=${encodeURIComponent(category.frontend_key || category.slug)}`;
  }

  function assetUrl(path) {
    const value = String(path || '').trim();
    if (!value) return '';
    if (/^(https?:)?\/\//i.test(value) || value.startsWith('data:')) return value;
    return value.replace(/^\/+/, '');
  }

  function renderSubnav(categories) {
    document.querySelectorAll('[data-taxonomy-subnav]').forEach(nav => {
      nav.innerHTML = `
        <a href="products.php" class="text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-printer-line"></i> All Printers</a>
        ${categories.map(category => `
          <a href="${categoryHref(category)}" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition">
            <i class="${esc(category.icon)}"></i> ${esc(category.name)}
          </a>
        `).join('')}
        <a href="products.php?cat=deals" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-flashlight-line"></i> Flash Deals</a>
        <a href="contact.php" class="hover:text-navy-600 whitespace-nowrap flex items-center gap-1 transition"><i class="ri-headphone-line"></i> Tech Support</a>`;
    });
  }

  function renderProductFilters(categories, brands) {
    const categoryWrap = document.querySelector('[data-product-category-filters]');
    if (categoryWrap) {
      categoryWrap.innerHTML = `
        <label class="flex items-center gap-2.5 cursor-pointer group">
          <input type="radio" name="cat" value="all" class="filter-cb accent-navy-600" checked onchange="applyFilters()"/>
          <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">All Printers</span>
        </label>
        ${categories.map(category => `
          <label class="flex items-center gap-2.5 cursor-pointer group">
            <input type="radio" name="cat" value="${esc(category.frontend_key || category.slug)}" class="filter-cb" onchange="applyFilters()"/>
            <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">${esc(category.name)}</span>
            <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md">${Number(category.product_count) || 0}</span>
          </label>
        `).join('')}`;
    }

    const brandWrap = document.querySelector('[data-product-brand-filters]');
    if (brandWrap) {
      brandWrap.innerHTML = brands.map(brand => `
        <label class="flex items-center gap-2.5 cursor-pointer group">
          <input type="checkbox" value="${esc(brand.name)}" class="filter-cb brand-cb" onchange="applyFilters()"/>
          <span class="text-sm text-slate-700 group-hover:text-navy-600 transition">${esc(brand.name)}</span>
          <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md">${Number(brand.product_count) || 0}</span>
        </label>
      `).join('');
    }
  }

  function renderHomeCategories(categories) {
    const wrap = document.querySelector('[data-home-category-slider]');
    if (!wrap) return;

    wrap.innerHTML = categories.map(category => {
      const theme = categoryCardThemes[category.color] || categoryCardThemes.navy;
      const image = assetUrl(category.image_url);
      return `
        <a href="${categoryHref(category)}" class="card-lift relative overflow-hidden rounded-2xl border border-slate-200 text-white group shrink-0 w-[180px] sm:w-[190px] lg:w-[200px] h-40 bg-gradient-to-br ${theme}">
          ${image ? `<img src="${esc(image)}" alt="${esc(category.name)}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition">` : ''}
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent"></div>
          <div class="${image ? 'hidden' : 'absolute'} inset-0 flex items-center justify-center opacity-95 group-hover:scale-105 transition">
            <i class="${esc(category.icon)} text-white/90 text-7xl"></i>
          </div>
          <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/80 to-transparent p-4 text-left">
            <h3 class="font-bold text-white text-sm">${esc(category.name)}</h3>
            <span class="text-white/80 text-xs font-semibold mt-1 inline-block">${Number(category.product_count) || 0} products <i class="ri-arrow-right-line"></i></span>
          </div>
        </a>`;
    }).join('');
  }

  function applyUrlFiltersAfterRender() {
    const params = new URLSearchParams(window.location.search);
    const cat = params.get('cat');
    const brand = params.get('brand');
    if (cat) {
      const safeCat = window.CSS && CSS.escape ? CSS.escape(cat) : cat.replace(/"/g, '\\"');
      const radio = document.querySelector(`input[name="cat"][value="${safeCat}"]`);
      if (radio) radio.checked = true;
    }
    if (brand) {
      document.querySelectorAll('.brand-cb').forEach(cb => {
        cb.checked = cb.value.toLowerCase() === brand.toLowerCase();
      });
    }
  }

  async function loadTaxonomy() {
    try {
      const response = await fetch(TAXONOMY_URL, { cache: 'no-store' });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error('Taxonomy API failed');
      const categories = result.data.categories || [];
      const brands = result.data.brands || [];
      window.GSS_TAXONOMY = { categories, brands };
      renderSubnav(categories);
      renderProductFilters(categories, brands);
      renderHomeCategories(categories);
      applyUrlFiltersAfterRender();
      if (typeof applyFilters === 'function') applyFilters();
    } catch (error) {
      console.warn('Could not load taxonomy:', error);
    }
  }

  document.addEventListener('DOMContentLoaded', loadTaxonomy);
})();
