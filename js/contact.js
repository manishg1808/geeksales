function handleSubmit(e) {
  e.preventDefault();
  const form = e.target;

  // Collect fields
  const firstName = form.querySelector('[name="first_name"]')?.value.trim() ?? '';
  const lastName  = form.querySelector('[name="last_name"]')?.value.trim()  ?? '';
  const email     = form.querySelector('[name="email"]')?.value.trim()      ?? '';
  const phone     = form.querySelector('[name="phone"]')?.value.trim()      ?? '';
  const topic     = form.querySelector('[name="topic"]')?.value.trim()      ?? '';
  const orderNo   = form.querySelector('[name="order_no"]')?.value.trim()   ?? '';
  const message   = form.querySelector('[name="message"]')?.value.trim()    ?? '';

  // Basic client-side guard
  if (!firstName || !lastName || !email || !message) {
    alert('Please fill in all required fields.');
    return;
  }

  // Build payload
  const formData = new FormData();
  formData.append('first_name', firstName);
  formData.append('last_name',  lastName);
  formData.append('email',      email);
  formData.append('phone',      phone);
  formData.append('topic',      topic);
  formData.append('order_no',   orderNo);
  formData.append('message',    message);

  // Disable submit button
  const button = form.querySelector('button[type="submit"]');
  const originalText = button.innerHTML;
  button.disabled = true;
  button.innerHTML = '<i class="ri-loader-4-line animate-spin text-lg"></i> Sending...';

  // POST to dedicated API endpoint
  fetch('api/leads.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      form.classList.add('hidden');
      document.getElementById('success-msg').classList.remove('hidden');
    } else {
      const errMsg = data.message || 'An error occurred. Please try again.';
      alert(errMsg);
      button.disabled = false;
      button.innerHTML = originalText;
    }
  })
  .catch(err => {
    console.error('[API/leads]', err);
    alert('An error occurred. Please try again.');
    button.disabled = false;
    button.innerHTML = originalText;
  });
}


function toggleFaq(btn) {
  const body = btn.nextElementSibling;
  const icon = btn.querySelector('.faq-icon');
  const isOpen = !body.classList.contains('hidden');
  // Close all FAQs
  document.querySelectorAll('.faq-body').forEach(b => b.classList.add('hidden'));
  document.querySelectorAll('.faq-icon').forEach(i => { 
    i.classList.remove('ri-subtract-line'); 
    i.classList.add('ri-add-line'); 
    i.style.transform=''; 
  });
  if (!isOpen) {
    body.classList.remove('hidden');
    icon.classList.remove('ri-add-line');
    icon.classList.add('ri-subtract-line');
  }
}
