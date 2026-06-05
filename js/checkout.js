let cart = JSON.parse(localStorage.getItem('gss_cart') || '[]');
  let shippingCost = 0;
  let discountAmt = 0;
  let currentStep = 1;

  document.addEventListener('DOMContentLoaded', () => {
    renderSummary();
    // If cart empty, add a demo item
    if (!cart.length) {
      cart = [{ name:'HP DeskJet 4155e', price:89.99, qty:1 }];
      renderSummary();
    }
  });

  function renderSummary() {
    const items = document.getElementById('summary-items');
    const subtotal = cart.reduce((s,i) => s + i.price * i.qty, 0);
    const tax = subtotal * 0.0825;
    const total = subtotal + shippingCost + tax - discountAmt;
    document.getElementById('summary-count').textContent = cart.reduce((s,i)=>s+i.qty,0) + ' item(s)';
    document.getElementById('sum-subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('sum-shipping').textContent = shippingCost === 0 ? 'FREE' : '$' + shippingCost.toFixed(2);
    document.getElementById('sum-shipping').className = shippingCost === 0 ? 'font-semibold text-emerald-600' : 'font-semibold text-slate-700';
    document.getElementById('sum-tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('sum-total').textContent = '$' + total.toFixed(2);
    items.innerHTML = cart.map(item => `
      <div class="flex items-center gap-3">
        <div class="bg-navy-50 rounded-xl w-12 h-12 flex items-center justify-center shrink-0">
          <i class="ri-printer-fill text-navy-600 text-xl"></i>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-slate-800 truncate">${item.name}</p>
          <p class="text-xs text-slate-400">Qty: ${item.qty}</p>
        </div>
        <span class="font-bold text-slate-800 text-sm shrink-0">$${(item.price * item.qty).toFixed(2)}</span>
      </div>`).join('');
  }

  function goStep(n) {
    if (n === 2) {
      const fname = document.getElementById('f-fname').value.trim();
      const email = document.getElementById('f-email').value.trim();
      if (!fname || !email) { showError('Please fill in your name and email.'); return; }
    }
    if (n === 3) {
      const addr = document.getElementById('f-addr').value.trim();
      if (!addr) { showError('Please enter your shipping address.'); return; }
    }

    currentStep = n;
    [1,2,3].forEach(i => {
      const step = document.getElementById('step'+i);
      const dot = document.getElementById('step'+i+'-dot');
      if (i < n) {
        step.classList.add('opacity-50','pointer-events-none');
        dot.className = 'step-dot done';
        dot.innerHTML = '<i class="ri-check-line text-xs"></i>';
        if (document.getElementById('line'+i)) document.getElementById('line'+i).classList.add('done');
      } else if (i === n) {
        step.classList.remove('opacity-50','pointer-events-none');
        dot.className = 'step-dot active';
        dot.textContent = i;
        const numEl = document.getElementById('step'+i+'-num');
        if (numEl) { numEl.className = 'bg-navy-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-black'; }
      } else {
        step.classList.add('opacity-50','pointer-events-none');
        dot.className = 'step-dot idle';
        dot.textContent = i;
      }
    });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function selectShipping(type, el) {
    document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('selected'));
    el.classList.add('selected');
    shippingCost = type === 'express' ? 12.99 : type === 'overnight' ? 24.99 : 0;
    renderSummary();
  }

  function selectPayTab(tab, btn) {
    ['card','paypal','apple'].forEach(t => {
      document.getElementById('pay-'+t).classList.add('hidden');
      document.getElementById('pay-tab-'+t).className = 'flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-slate-200 text-slate-500 font-semibold text-sm hover:border-navy-400 transition';
    });
    document.getElementById('pay-'+tab).classList.remove('hidden');
    btn.className = 'flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-navy-600 bg-navy-50 text-navy-700 font-bold text-sm transition';
  }

  function formatCard(el) {
    let v = el.value.replace(/\D/g,'').substring(0,16);
    el.value = v.replace(/(.{4})/g,'$1 ').trim();
  }

  function formatExpiry(el) {
    let v = el.value.replace(/\D/g,'').substring(0,4);
    if (v.length >= 2) v = v.substring(0,2) + ' / ' + v.substring(2);
    el.value = v;
  }

  function applyPromo() {
    const code = document.getElementById('promo-input').value.trim().toUpperCase();
    const msg = document.getElementById('promo-msg');
    const row = document.getElementById('discount-row');
    if (code === 'GEEK10') {
      const subtotal = cart.reduce((s,i) => s + i.price * i.qty, 0);
      discountAmt = subtotal * 0.10;
      document.getElementById('discount-amt').textContent = '-$' + discountAmt.toFixed(2);
      row.classList.remove('hidden');
      msg.textContent = '✓ 10% discount applied!';
      msg.className = 'text-xs mt-1.5 text-emerald-600 font-semibold';
      msg.classList.remove('hidden');
      renderSummary();
    } else if (code === 'SAVE20') {
      discountAmt = 20;
      document.getElementById('discount-amt').textContent = '-$20.00';
      row.classList.remove('hidden');
      msg.textContent = '✓ $20 discount applied!';
      msg.className = 'text-xs mt-1.5 text-emerald-600 font-semibold';
      msg.classList.remove('hidden');
      renderSummary();
    } else {
      msg.textContent = '✗ Invalid promo code. Try GEEK10 or SAVE20';
      msg.className = 'text-xs mt-1.5 text-red-500 font-semibold';
      msg.classList.remove('hidden');
    }
  }

  function placeOrder() {
    const card = document.getElementById('f-card').value.trim();
    const expiry = document.getElementById('f-expiry').value.trim();
    const cvv = document.getElementById('f-cvv').value.trim();
    const nameOnCard = document.getElementById('f-cardname').value.trim();
    if (!card || !expiry || !cvv || !nameOnCard) { showError('Please complete all payment fields.'); return; }

    const fname = document.getElementById('f-fname').value.trim();
    const lname = document.getElementById('f-lname').value.trim();
    const email = document.getElementById('f-email').value.trim();
    const phone = document.getElementById('f-phone').value.trim();

    // Show loading state
    const btn = document.querySelector('button[onclick="placeOrder()"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Processing…';
    btn.disabled = true;

    const subtotal = cart.reduce((s,i) => s + i.price * i.qty, 0);
    const tax = subtotal * 0.0825;
    const total = subtotal + shippingCost + tax - discountAmt;
    const orderNum = 'GSS-' + Math.floor(10000 + Math.random() * 90000);
    const productNames = cart.map(item => `${item.name} (x${item.qty})`).join(', ');

    // Send order to dedicated API endpoint
    const formData = new FormData();
    formData.append('customer_name', fname + ' ' + lname);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('product_name', productNames);
    formData.append('amount', total.toFixed(2));
    formData.append('order_no', orderNum);

    fetch('api/orders.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const d = new Date(); d.setDate(d.getDate() + (shippingCost === 24.99 ? 1 : shippingCost === 12.99 ? 2 : 5));
        document.getElementById('order-num').textContent = '#' + orderNum;
        document.getElementById('order-delivery').textContent = d.toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});
        document.getElementById('order-total').textContent = '$' + total.toFixed(2);
        localStorage.removeItem('gss_cart');
        const overlay = document.getElementById('success-overlay');
        overlay.classList.remove('opacity-0','pointer-events-none');
      } else {
        showError(data.message || 'Could not place order. Please try again.');
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    })
    .catch(err => {
      console.error(err);
      showError('An error occurred. Please try again.');
      btn.innerHTML = originalText;
      btn.disabled = false;
    });
  }

  function showError(msg) {
    const t = document.createElement('div');
    t.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-red-500 text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-xl flex items-center gap-2';
    t.innerHTML = `<i class="ri-error-warning-line text-base"></i>${msg}`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
  }
