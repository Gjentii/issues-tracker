(() => {
  const openBtn = document.getElementById('open-issue-modal');
  const closeBtn = document.getElementById('close-issue-modal');
  const cancelBtn = document.getElementById('cancel-issue-modal');
  const modal = document.getElementById('issue-modal');
  const form = document.getElementById('issue-form');
  const grid = document.getElementById('issues-grid');
  const noIssuesMsg = document.getElementById('no-issues-msg');

  if (!modal || !openBtn) return;

  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function openModal() {
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

  openBtn?.addEventListener('click', openModal);
  closeBtn?.addEventListener('click', closeModal);
  cancelBtn?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    setErrors({});

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
        if (noIssuesMsg) noIssuesMsg.remove();
        if (data.html && grid) {
          const wrapper = document.createElement('div');
          wrapper.innerHTML = data.html.trim();
          const card = wrapper.firstElementChild;
          const gridInner = grid.querySelector('.grid') || grid;
          gridInner.insertBefore(card, gridInner.firstChild);
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
  });
})();

