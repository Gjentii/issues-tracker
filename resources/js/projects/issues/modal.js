(() => {
  const openBtn = document.getElementById('open-issue-modal');
  const closeBtn = document.getElementById('close-issue-modal');
  const cancelBtn = document.getElementById('cancel-issue-modal');
  const modal = document.getElementById('issue-modal');
  const form = document.getElementById('issue-form');
  const grid = document.getElementById('issues-grid');
  const noIssuesMsg = document.getElementById('no-issues-msg');
  const titleEl = document.getElementById('issue-modal-title');
  const submitBtn = document.getElementById('issue-submit');

  if (!modal) return;

  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function setMethod(method){
    let input = form.querySelector('input[name="_method"]');
    if (method && method.toUpperCase() !== 'POST'){
      if (!input){ input = document.createElement('input'); input.type='hidden'; input.name='_method'; form.appendChild(input); }
      input.value = method;
    } else if (input) { input.remove(); }
  }

  function hydrateFromOpener(opener){
    const mode = opener?.dataset?.mode || 'create';
    // Defaults for create
    form.action = opener?.dataset?.action || form.action;
    setMethod(opener?.dataset?.method || 'POST');
    if (opener?.dataset?.issue_id){ form.dataset.issueId = opener.dataset.issue_id; } else { delete form.dataset.issueId; }

    if (titleEl) titleEl.textContent = mode === 'edit' ? 'Edit Issue' : 'Create Issue';
    if (submitBtn) submitBtn.textContent = mode === 'edit' ? 'Update' : 'Create';

    // Prefill
    const fields = ['title','description','status','priority','due_date'];
    for (const f of fields){
      const el = document.getElementById('issue-' + f.replace('_','-'));
      if (!el) continue;
      const v = opener?.dataset?.[f];
      if (v !== undefined){
        if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') el.value = v ?? '';
      } else if (mode === 'create'){
        if (el.tagName === 'SELECT'){
          // keep defaults
        } else {
          el.value = '';
        }
      }
    }

    // Handle tag checkboxes
    const tagInputs = form.querySelectorAll('input[name="tags[]"]');
    if (tagInputs && tagInputs.length){
      if (mode === 'edit'){
        const tagStr = opener?.dataset?.tags || '';
        const selected = new Set(
          tagStr
            .split(',')
            .map(s => parseInt(s.trim(), 10))
            .filter(n => !Number.isNaN(n))
        );
        tagInputs.forEach(cb => {
          const id = parseInt(cb.value, 10);
          cb.checked = selected.has(id);
        });
      } else {
        // create mode: clear all
        tagInputs.forEach(cb => { cb.checked = false; });
      }
    }
    form.dataset.mode = mode;
    // For replacement on success
    form.dataset.replace = opener?.dataset?.replace || '';
    form.dataset.success = opener?.dataset?.success || '';
    form.dataset.successMessage = opener?.dataset?.successMessage || '';
  }

  function openModal(opener) {
    if (opener) hydrateFromOpener(opener);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => document.getElementById('issue-title')?.focus(), 0);
  }
  function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }

  function setErrors(map) {
    const fields = ['title', 'description', 'status', 'priority', 'due_date'];
    for (const f of fields) {
      const el = document.getElementById('error-' + f);
      if (!el) continue;
      el.innerHTML = '';
      const msgs = (map && map[f]) || [];
      if (msgs.length) {
        el.classList.remove('hidden');
        msgs.forEach((m) => {
          const li = document.createElement('li');
          li.textContent = m;
          el.appendChild(li);
        });
      } else {
        el.classList.add('hidden');
      }
    }
  }

  // Event delegation so dynamically replaced cards still work
  document.addEventListener('click', (e) => {
    const opener = e.target.closest('[data-open-modal="issue-modal"]');
    if (opener) {
      e.preventDefault();
      openModal(opener);
    }
  });
  closeBtn?.addEventListener('click', closeModal);
  cancelBtn?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    setErrors({});
    // loading state on submit button
    const mode = form.dataset.mode || 'create';
    const originalText = submitBtn ? submitBtn.textContent : '';
    if (submitBtn){
      submitBtn.textContent = mode === 'edit' ? 'Updating...' : 'Creating...';
      submitBtn.disabled = true;
      submitBtn.classList.add('opacity-70','cursor-not-allowed');
    }

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
        },
        body: new FormData(form),
      });
      if (res.status === 201 || res.ok) {
        const data = await res.json();
        const mode = form.dataset.mode || 'create';
        if (noIssuesMsg) noIssuesMsg.remove();
        if (data.html){
          const wrapper = document.createElement('div');
          wrapper.innerHTML = data.html.trim();
          const card = wrapper.firstElementChild;
          if (mode === 'edit' && form.dataset.replace){
            const target = document.querySelector(form.dataset.replace);
            if (target && card) target.replaceWith(card);
          } else if (grid && card){
            const gridInner = grid.querySelector('.grid') || grid;
            gridInner.insertBefore(card, gridInner.firstChild);
          }
        }
        // show success message if target provided
        if (form.dataset.success){
          const box = document.querySelector(form.dataset.success);
          if (box){
            box.textContent = form.dataset.successMessage || (mode === 'edit' ? 'Issue updated successfully.' : 'Issue created successfully.');
            box.classList.remove('hidden');
          }
        }
        form.reset();
        closeModal();
      } else if (res.status === 422) {
        const data = await res.json();
        setErrors(data.errors || {});
      }
    } catch (err) {
      // ignore
    }
    finally {
      if (submitBtn){
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-70','cursor-not-allowed');
      }
    }
  });
})();
