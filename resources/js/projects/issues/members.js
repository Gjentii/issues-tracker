(() => {
  if (window.__issueMembersInit) return;
  window.__issueMembersInit = true;
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  function renderBadge(user){
    const wrap = document.createElement('span');
    wrap.className = 'issue-member-badge inline-flex items-center gap-1.5 text-xs rounded-full border border-indigo-200 bg-indigo-50 text-indigo-800 pl-2 pr-1 py-1';
    wrap.dataset.userId = String(user?.id || '');
    const name = document.createElement('span');
    name.className = 'font-medium';
    name.textContent = user?.name || 'Unknown';
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.title = 'Remove';
    btn.className = 'issue-member-remove inline-flex items-center justify-center h-5 w-5 rounded-full text-indigo-700 hover:text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400';
    btn.innerHTML = '<span aria-hidden="true">×</span>';
    wrap.appendChild(name);
    wrap.appendChild(btn);
    return wrap;
  }

  async function fetchMembers(issueId){
    if (!issueId) return [];
    try {
      const res = await fetch(`/issues/${issueId}/members`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
      if (res.ok){ const data = await res.json(); return Array.isArray(data?.data) ? data.data : []; }
    } catch(_){}
    return [];
  }

  function openAddRow(rowEl){ if (rowEl) rowEl.classList.remove('hidden'); }
  function closeAddRow(rowEl){ if (rowEl) rowEl.classList.add('hidden'); const sel = rowEl?.querySelector('select'); if (sel) sel.value = ''; }

  // Utilities to hide options for already attached users
  function hideSelectedOptions(selectEl, ids){
    if (!selectEl) return;
    const set = new Set(ids || []);
    Array.from(selectEl.options).forEach(opt => {
      const id = parseInt(opt.value || '0', 10);
      if (!id) return; // ignore placeholder
      const hide = set.has(id);
      opt.disabled = hide;
      opt.hidden = hide;
      opt.style.display = hide ? 'none' : '';
    });
  }
  function hideOption(selectEl, id, hide){
    if (!selectEl) return;
    Array.from(selectEl.options).forEach(opt => {
      const vid = parseInt(opt.value || '0', 10);
      if (vid !== id) return;
      opt.disabled = !!hide;
      opt.hidden = !!hide;
      opt.style.display = hide ? 'none' : '';
    });
  }

  function hideSelectedOptions(selectEl, ids){
    if (!selectEl) return;
    const set = new Set(ids || []);
    Array.from(selectEl.options).forEach(opt => {
      const id = parseInt(opt.value || '0', 10);
      if (!id) return; // skip placeholder
      const hide = set.has(id);
      opt.disabled = hide;
      opt.hidden = hide;
      if (hide) opt.style.display = 'none'; else opt.style.display = '';
    });
  }

  function hideOption(selectEl, id, hide){
    if (!selectEl) return;
    Array.from(selectEl.options).forEach(opt => {
      const vid = parseInt(opt.value || '0', 10);
      if (vid !== id) return;
      opt.disabled = !!hide;
      opt.hidden = !!hide;
      opt.style.display = hide ? 'none' : '';
    });
  }

  // Create/Edit modal handling
  const issueModal = document.getElementById('issue-modal');
  if (issueModal){
    const list = document.getElementById('issue-members-list');
    const hidden = document.getElementById('issue-members-hidden');
    const addBtn = document.getElementById('issue-members-add-btn');
    const row = document.getElementById('issue-members-add-row');
    const sel = document.getElementById('issue-members-select');
    const attachBtn = document.getElementById('issue-members-attach');
    const cancelBtn = document.getElementById('issue-members-cancel');

    function clearList(){ if (list) list.innerHTML=''; }
    function appendToList(user){ if (list) list.appendChild(renderBadge(user)); }
    function addHiddenMember(id){ if (!hidden) return; const input = document.createElement('input'); input.type='hidden'; input.name='members[]'; input.value=String(id); hidden.appendChild(input); }
    function selectedIds(){ return Array.from(hidden?.querySelectorAll('input[name="members[]"]') || []).map(i => parseInt(i.value,10)).filter(n => !Number.isNaN(n)); }

    function disableSelectedOptions(ids){ hideSelectedOptions(sel, ids); }

    function setSubmitLabel(){
      const form = document.getElementById('issue-form');
      const submitBtn = document.getElementById('issue-submit');
      if (!form || !submitBtn) return;
      const mode = form.dataset.mode || 'create';
      submitBtn.textContent = mode === 'edit' ? 'Update' : 'Create';
    }

    // When the issue modal opens, initialize members UI
    document.addEventListener('click', async (e) => {
      const opener = e.target.closest('[data-open-modal="issue-modal"]');
      if (!opener) return;
      // Defer a tick to let modal.js hydrate
      setTimeout(async () => {
        const mode = (document.getElementById('issue-form')?.dataset?.mode) || 'create';
        clearList();
        if (mode === 'edit'){
          const issueId = opener.getAttribute('data-issue_id');
          const members = await fetchMembers(issueId);
          members.forEach(m => appendToList(m));
          // hide options for already attached
          disableSelectedOptions(members.map(m => m.id));
        } else {
          // create mode: use any prefilled hidden members if present
          disableSelectedOptions(selectedIds());
        }
      }, 0);
    });

    addBtn?.addEventListener('click', () => openAddRow(row));
    cancelBtn?.addEventListener('click', () => closeAddRow(row));
    attachBtn?.addEventListener('click', async () => {
      const id = parseInt(sel?.value || '0', 10);
      if (!id) return;
      const form = document.getElementById('issue-form');
      const mode = form?.dataset?.mode || 'create';
      if (mode === 'edit'){
        // AJAX attach to issue
        const opener = document.querySelector('[data-open-modal="issue-modal"][data-method="PUT"][data-action="'+(form?.action||'')+'"][data-issue_id]') || document.querySelector('[data-open-modal="issue-modal"][data-issue_id]');
        const issueId = opener?.getAttribute('data-issue_id');
        const url = issueId ? `/issues/${issueId}/members` : '';
        if (!url) return;
        const fd = new FormData(); fd.append('user_id', String(id));
        try {
          const res = await fetch(url, { method:'POST', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, body: fd, credentials: 'same-origin' });
          if (res.status === 201 || res.ok){ const data = await res.json(); appendToList(data.member); hideOption(sel, id, true); closeAddRow(row); }
        } catch(_){}
      } else {
        // create mode: add hidden and render badge
        const opt = sel.options[sel.selectedIndex];
        addHiddenMember(id);
        appendToList({ id, name: opt?.text?.replace(/\s*\([^)]*\)\s*$/, '') || opt?.text || 'User' });
        hideOption(sel, id, true);
        closeAddRow(row);
      }
      setSubmitLabel();
    });

    // Remove (detach or local remove)
    list?.addEventListener('click', async (e) => {
      const btn = e.target.closest?.('.issue-member-remove');
      if (!btn) return;
      const badge = btn.closest('.issue-member-badge');
      const userId = parseInt(badge?.dataset?.userId || '0', 10);
      if (!userId) return;
      const form = document.getElementById('issue-form');
      const mode = form?.dataset?.mode || 'create';
      if (mode === 'edit'){
        const issueId = form?.dataset?.issueId;
        if (!issueId) return;
        try {
          const res = await fetch(`/issues/${issueId}/members/${userId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, credentials: 'same-origin' });
          if (res.ok){
            badge?.remove();
            // unhide option
            hideOption(sel, userId, false);
          }
        } catch(_){}
      } else {
        // create mode: remove hidden input and badge
        const hidden = document.getElementById('issue-members-hidden');
        hidden?.querySelectorAll('input[name="members[]"]').forEach(i => { if (parseInt(i.value,10) === userId) i.remove(); });
        badge?.remove();
        hideOption(sel, userId, false);
      }
    });
  }

  // View modal handling
  const viewModal = document.getElementById('issue-view-modal');
  if (true){
    const list = document.getElementById('view-issue-members-list');
    const addBtn = document.getElementById('view-issue-members-add-btn');
    const row = document.getElementById('view-issue-members-add-row');
    const sel = document.getElementById('view-issue-members-select');
    const attachBtn = document.getElementById('view-issue-members-attach');
    const cancelBtn = document.getElementById('view-issue-members-cancel');

    function clearList(){ if (list) list.innerHTML=''; }
    function appendToList(user){ if (list) list.appendChild(renderBadge(user)); }
    function disableSelectedOptions(ids){ if (!sel) return; Array.from(sel.options).forEach(opt => { if (!opt.value) return; const id = parseInt(opt.value,10); opt.disabled = ids.includes(id); }); }

    document.addEventListener('issue-view-open', async () => {
      // Re-select elements now that the modal is open
      const vm = document.getElementById('issue-view-modal');
      const listEl = document.getElementById('view-issue-members-list');
      const addBtnEl = document.getElementById('view-issue-members-add-btn');
      const rowEl = document.getElementById('view-issue-members-add-row');
      const selEl = document.getElementById('view-issue-members-select');
      const attachBtnEl = document.getElementById('view-issue-members-attach');
      const cancelBtnEl = document.getElementById('view-issue-members-cancel');

      if (listEl) listEl.innerHTML = '';
      const effectiveIssueId = vm?.dataset?.issueId;
      const loadedMembers = await fetchMembers(effectiveIssueId);
      loadedMembers.forEach(m => { if (listEl) listEl.appendChild(renderBadge(m)); });
      hideSelectedOptions(selEl, loadedMembers.map(m => m.id));

      if (vm && vm.dataset.membersBound !== 'true'){
        addBtnEl?.addEventListener('click', () => openAddRow(rowEl));
        cancelBtnEl?.addEventListener('click', () => closeAddRow(rowEl));
        attachBtnEl?.addEventListener('click', async () => {
          const id = parseInt(selEl?.value || '0', 10);
          if (!id) return;
          const url = effectiveIssueId ? `/issues/${effectiveIssueId}/members` : '';
          if (!url) return;
          const fd = new FormData(); fd.append('user_id', String(id));
          try {
            const res = await fetch(url, { method:'POST', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, body: fd, credentials: 'same-origin' });
            if (res.status === 201 || res.ok){ const data = await res.json(); if (listEl) listEl.appendChild(renderBadge(data.member)); hideOption(selEl, id, true); closeAddRow(rowEl); }
          } catch(_){}
        });
        listEl?.addEventListener('click', async (e) => {
          const btn = e.target.closest?.('.issue-member-remove');
          if (!btn) return;
          const badge = btn.closest('.issue-member-badge');
          const userId = parseInt(badge?.dataset?.userId || '0', 10);
          if (!userId || !effectiveIssueId) return;
          try {
            const res = await fetch(`/issues/${effectiveIssueId}/members/${userId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, credentials: 'same-origin' });
            if (res.ok){ badge?.remove(); hideOption(selEl, userId, false); }
          } catch(_){}
        });
        vm.dataset.membersBound = 'true';
      }
      clearList();
      const issueId = viewModal?.dataset?.issueId;
      const members = await fetchMembers(issueId);
      members.forEach(m => appendToList(m));
      hideSelectedOptions(sel, members.map(m => m.id));

      // Bind controls once modal exists (idempotent)
      if (viewModal && viewModal.dataset.membersBound !== 'true'){
        addBtn?.addEventListener('click', () => openAddRow(row));
        cancelBtn?.addEventListener('click', () => closeAddRow(row));
        attachBtn?.addEventListener('click', async () => {
          const id = parseInt(sel?.value || '0', 10);
          if (!id) return;
          const url = issueId ? `/issues/${issueId}/members` : '';
          if (!url) return;
          const fd = new FormData(); fd.append('user_id', String(id));
          try {
            const res = await fetch(url, { method:'POST', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, body: fd, credentials: 'same-origin' });
            if (res.status === 201 || res.ok){ const data = await res.json(); appendToList(data.member); hideOption(sel, id, true); closeAddRow(row); }
          } catch(_){}
        });
        list?.addEventListener('click', async (e) => {
          const btn = e.target.closest?.('.issue-member-remove');
          if (!btn) return;
          const badge = btn.closest('.issue-member-badge');
          const userId = parseInt(badge?.dataset?.userId || '0', 10);
          if (!userId || !issueId) return;
          try {
            const res = await fetch(`/issues/${issueId}/members/${userId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, credentials: 'same-origin' });
            if (res.ok){ badge?.remove(); hideOption(sel, userId, false); }
          } catch(_){}
        });
        viewModal.dataset.membersBound = 'true';
      }
    });

    addBtn?.addEventListener('click', () => openAddRow(row));
    cancelBtn?.addEventListener('click', () => closeAddRow(row));
    attachBtn?.addEventListener('click', async () => {
      const id = parseInt(sel?.value || '0', 10);
      if (!id) return;
      const issueId = viewModal?.dataset?.issueId;
      const url = issueId ? `/issues/${issueId}/members` : '';
      if (!url) return;
      const fd = new FormData(); fd.append('user_id', String(id));
      try {
        const res = await fetch(url, { method:'POST', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, body: fd, credentials: 'same-origin' });
        if (res.status === 201 || res.ok){ const data = await res.json(); appendToList(data.member); hideOption(sel, id, true); closeAddRow(row); }
      } catch(_){}
    });

    // Detach from view modal
    list?.addEventListener('click', async (e) => {
      const btn = e.target.closest?.('.issue-member-remove');
      if (!btn) return;
      const badge = btn.closest('.issue-member-badge');
      const userId = parseInt(badge?.dataset?.userId || '0', 10);
      const issueId = viewModal?.dataset?.issueId;
      if (!userId || !issueId) return;
      try {
        const res = await fetch(`/issues/${issueId}/members/${userId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, credentials: 'same-origin' });
        if (res.ok){ badge?.remove(); hideOption(sel, userId, false); }
      } catch(_){}
    });
  }
})();
