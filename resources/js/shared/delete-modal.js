window.initDeleteModal = function(modalId){
  const modal = document.getElementById(modalId);
  if (!modal) return;
  const form = document.getElementById(modalId + '-form');
  const btnClose = document.getElementById(modalId + '-close');
  const btnCancel = document.getElementById(modalId + '-cancel');
  const btnConfirm = document.getElementById(modalId + '-confirm');
  const overlay = modal.querySelector('[data-close="'+modalId+'"]');
  const namePlaceholders = modal.querySelectorAll('[data-placeholder="name"]');
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';
  let lastOpener = null;

  function open(opener){
    if (form && opener && opener.dataset){
      lastOpener = opener;
      if (opener.dataset.action) form.action = opener.dataset.action;
      const methodInput = form.querySelector('input[name="_method"]');
      const method = opener.dataset.method || 'DELETE';
      if (methodInput) methodInput.value = method;
      else if (method && method.toUpperCase() !== 'POST'){
        const hidden = document.createElement('input');
        hidden.type = 'hidden'; hidden.name = '_method'; hidden.value = method;
        form.appendChild(hidden);
      }
      const name = opener.dataset.name || '';
      namePlaceholders.forEach(el => { el.textContent = name ? `Delete "${name}"? ` : ''; });
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
  }
  function close(){
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }
  function setLoading(loading){
    if (!btnConfirm) return;
    if (loading){
      btnConfirm.dataset.originalText = btnConfirm.textContent;
      btnConfirm.textContent = 'Deleting...';
      btnConfirm.disabled = true;
      btnConfirm.classList.add('opacity-70', 'cursor-not-allowed');
    } else {
      if (btnConfirm.dataset.originalText) btnConfirm.textContent = btnConfirm.dataset.originalText;
      btnConfirm.disabled = false;
      btnConfirm.classList.remove('opacity-70', 'cursor-not-allowed');
    }
  }

  // Openers: any element with data-open-modal="<modalId>"
  const openers = document.querySelectorAll('[data-open-modal="'+modalId+'"][data-action]');
  openers.forEach(opener => {
    opener.addEventListener('click', (e) => { e.preventDefault(); open(opener); });
  });

  btnClose && btnClose.addEventListener('click', close);
  btnCancel && btnCancel.addEventListener('click', close);
  overlay && overlay.addEventListener('click', close);

  // AJAX submit with loading and success handling
  form && form.addEventListener('submit', async (e) => {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: new FormData(form)
      });
      if (res.ok){
        let data = {};
        try { data = await res.json(); } catch(_) {}
        // Remove target element if provided
        if (lastOpener && lastOpener.dataset.remove){
          const el = document.querySelector(lastOpener.dataset.remove);
          el && el.remove();
        }
        // Show success message if target provided
        if (lastOpener && lastOpener.dataset.success){
          const box = document.querySelector(lastOpener.dataset.success);
          if (box){
            box.textContent = lastOpener.dataset.successMessage || data.message || 'Deleted successfully.';
            box.classList.remove('hidden');
          }
        }
        close();
      } else {
        // fallback to standard submit
        form.submit();
      }
    } catch(_e){
      form.submit();
    } finally {
      setLoading(false);
    }
  });
}
