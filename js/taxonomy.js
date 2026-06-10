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

  function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
      if (timeoutId) clearTimeout(timeoutId);
      timeoutId = setTimeout(() => {
        func.apply(this, args);
      }, delay);
    };
  }

  function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }

  window.debounce = debounce;
  window.throttle = throttle;

  function initHeaderSearch() {
    const searchInputs = document.querySelectorAll('.header-search-input');

    searchInputs.forEach(input => {
      const form = input.closest('.header-search-form');
      if (!form) return;

      // Check if dropdown already exists to prevent duplicate initialization
      let dropdown = form.querySelector('.header-search-dropdown');
      if (!dropdown) {
        dropdown = document.createElement('div');
        dropdown.className = 'header-search-dropdown absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-[100] hidden overflow-hidden max-h-[380px] overflow-y-auto';
        form.appendChild(dropdown);
      }

      dropdown.addEventListener('mousedown', (e) => {
        e.preventDefault();
      });

      const closeDropdown = () => {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
      };

      input.addEventListener('input', debounce(async (e) => {
        const query = e.target.value.trim();
        if (query.length < 2) {
          closeDropdown();
          return;
        }

        try {
          const res = await fetch(`api/products.php?limit=5&q=${encodeURIComponent(query)}&_=${Date.now()}`);
          const data = await res.json();
          const products = data?.data?.products || [];

          if (products.length === 0) {
            dropdown.innerHTML = `
              <div class="p-4 text-center text-slate-500 text-xs font-semibold">
                No products found for "${esc(query)}"
              </div>`;
            dropdown.classList.remove('hidden');
            return;
          }

          const itemsHtml = products.map(p => {
            const price = Number(p.price) || 0;
            const oldPrice = Number(p.old_price) || 0;
            const image = p.image_url || '';
            const brand = p.brand_name || '';

            const regex = new RegExp(`(${query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
            const highlightedName = p.name.replace(regex, '<strong class="text-navy-600 font-bold">$1</strong>');

            const imgTag = image 
              ? `<img src="${esc(image)}" class="w-8 h-8 object-contain shrink-0 rounded-md" />`
              : `<div class="w-8 h-8 rounded-md bg-slate-100 flex items-center justify-center shrink-0"><i class="ri-printer-line text-slate-400 text-sm"></i></div>`;

            return `
              <a href="product-detail.php?id=${p.id}" class="flex items-center gap-3 p-3 hover:bg-slate-50 border-b border-slate-100 last:border-0 transition">
                ${imgTag}
                <div class="flex-1 min-w-0">
                  <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">${esc(brand)}</div>
                  <div class="text-xs text-slate-700 font-medium truncate">${highlightedName}</div>
                </div>
                <div class="text-right shrink-0">
                  <div class="text-xs font-bold text-slate-800">$${price.toFixed(2)}</div>
                  ${oldPrice > price ? `<div class="text-[10px] text-slate-400 line-through">$${oldPrice.toFixed(2)}</div>` : ''}
                </div>
              </a>`;
          }).join('');

          dropdown.innerHTML = `
            <div class="p-2 bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Products</div>
            ${itemsHtml}
            <button type="submit" class="w-full text-center p-2.5 bg-slate-50 hover:bg-slate-100 text-xs text-navy-600 font-bold border-t border-slate-100 transition flex items-center justify-center gap-1">
              View all results <i class="ri-arrow-right-line"></i>
            </button>`;

          const viewAllBtn = dropdown.querySelector('button[type="submit"]');
          if (viewAllBtn) {
            viewAllBtn.addEventListener('click', () => {
              form.submit();
            });
          }

          dropdown.classList.remove('hidden');
        } catch (err) {
          console.warn('Autocomplete fetch failed:', err);
        }
      }, 300));

      input.addEventListener('blur', () => {
        setTimeout(closeDropdown, 200);
      });

      input.addEventListener('focus', (e) => {
        if (e.target.value.trim().length >= 2) {
          dropdown.classList.remove('hidden');
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeDropdown();
        }
      });
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('.header-search-form')) {
        document.querySelectorAll('.header-search-dropdown').forEach(d => {
          d.classList.add('hidden');
        });
      }
    });
  }

  function initScrollReveal() {
    // 1. Inject transition CSS styles into head
    const style = document.createElement('style');
    style.innerHTML = `
      .reveal {
        opacity: 0;
        will-change: transform, opacity;
        transition: opacity 0.85s cubic-bezier(0.16, 1, 0.3, 1), transform 0.85s cubic-bezier(0.16, 1, 0.3, 1);
      }
      .reveal-up { transform: translateY(50px); }
      .reveal-down { transform: translateY(-50px); }
      .reveal-left { transform: translateX(50px); }
      .reveal-right { transform: translateX(-50px); }
      .reveal-diagonal-left { transform: translate(40px, 40px); }
      .reveal-diagonal-right { transform: translate(-40px, 40px); }
      .reveal-scale { transform: scale(0.92); }

      .reveal-delay-80 { transition-delay: 80ms; }
      .reveal-delay-100 { transition-delay: 100ms; }
      .reveal-delay-160 { transition-delay: 160ms; }
      .reveal-delay-200 { transition-delay: 200ms; }
      .reveal-delay-240 { transition-delay: 240ms; }
      .reveal-delay-300 { transition-delay: 300ms; }
      .reveal-delay-320 { transition-delay: 320ms; }
      .reveal-delay-400 { transition-delay: 400ms; }
      .reveal-delay-500 { transition-delay: 500ms; }

      .reveal-active {
        opacity: 1 !important;
        transform: translate(0, 0) scale(1) !important;
      }
    `;
    document.head.appendChild(style);

    // 2. Automatically find and tag sections/cards for reveal animations
    const sections = document.querySelectorAll('section:not(#hero-slider):not(#hero):not(.support-hero)');
    sections.forEach((sec, idx) => {
      if (!sec.classList.contains('reveal')) {
        sec.classList.add('reveal', idx % 2 === 0 ? 'reveal-up' : 'reveal-scale');
      }

      const grids = sec.querySelectorAll('.grid, [data-home-category-slider], #active-chips, #faq-list');
      grids.forEach(grid => {
        const children = [...grid.children].filter(child => {
          return child.tagName !== 'SCRIPT' && !child.classList.contains('hidden') && !child.id?.includes('empty');
        });

        const revealTypes = ['reveal-left', 'reveal-up', 'reveal-right', 'reveal-diagonal-left', 'reveal-diagonal-right'];
        
        children.forEach((child, cIdx) => {
          if (!child.classList.contains('reveal')) {
            child.classList.add('reveal');
            const type = revealTypes[cIdx % revealTypes.length];
            child.classList.add(type);
            
            const delay = (cIdx % 5) * 100;
            if (delay > 0) {
              child.classList.add(`reveal-delay-${delay}`);
            }
          }
        });
      });
    });

    // Also auto-tag product cards in search list
    const productGrid = document.getElementById('product-grid');
    if (productGrid) {
      const observer = new MutationObserver((mutations) => {
        mutations.forEach(mutation => {
          if (mutation.addedNodes.length) {
            const cards = productGrid.querySelectorAll('.card-lift, div.card-lift');
            const revealTypes = ['reveal-up', 'reveal-scale', 'reveal-diagonal-left', 'reveal-diagonal-right'];
            cards.forEach((card, idx) => {
              if (!card.classList.contains('reveal')) {
                card.classList.add('reveal');
                card.classList.add(revealTypes[idx % revealTypes.length]);
                const delay = (idx % 4) * 80;
                if (delay > 0) {
                  card.classList.add(`reveal-delay-${delay}`);
                }
                revealObserver.observe(card);
              }
            });
          }
        });
      });
      observer.observe(productGrid, { childList: true });
    }

    // 3. Set up Intersection Observer to trigger active classes
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('reveal-active');
          revealObserver.unobserve(entry.target);
        }
      });
    }, {
      root: null,
      threshold: 0.08,
      rootMargin: '0px 0px -40px 0px'
    });

    // Observe all tagged elements
    setTimeout(() => {
      document.querySelectorAll('.reveal').forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight) {
          el.classList.add('reveal-active');
        } else {
          revealObserver.observe(el);
        }
      });
    }, 100);
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
      initHeaderSearch();
      initScrollReveal();
    } catch (error) {
      console.warn('Could not load taxonomy:', error);
      initHeaderSearch();
      initScrollReveal();
    }
  }

  document.addEventListener('DOMContentLoaded', loadTaxonomy);
})();
