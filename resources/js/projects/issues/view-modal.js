(() => {
  const modal = document.getElementById('issue-view-modal');
  if (!modal) return;
  const btnClose = document.getElementById('issue-view-close');
  const btnOk = document.getElementById('issue-view-ok');
  const overlay = modal.querySelector('[data-close="issue-view-modal"]');

  const elTitle = document.getElementById('view-issue-title');
  const elDesc = document.getElementById('view-issue-description');
  const elDue = document.getElementById('view-issue-due');
  const elProject = document.getElementById('view-issue-project');
  const elStatus = document.getElementById('view-issue-status');
  const elPriority = document.getElementById('view-issue-priority');
  const elTags = document.getElementById('view-issue-tags');
  const commentsList = document.getElementById('view-issue-comments-list');

  function setBadge(el, value, map){
    if (!el) return;
    if (!value){ el.classList.add('hidden'); el.textContent = ''; return; }
    const styles = map[value] || map._default;
    el.className = 'shrink-0 rounded-full px-2 py-0.5 text-xs font-medium border ' + styles;
    el.textContent = value.replace('_',' ').replace(/\b\w/g, c => c.toUpperCase());
    el.classList.remove('hidden');
  }

  const statusMap = {
    open: 'bg-green-100 text-green-800 border-green-200',
    in_progress: 'bg-blue-100 text-blue-800 border-blue-200',
    closed: 'bg-gray-100 text-gray-800 border-gray-200',
    _default: 'bg-gray-100 text-gray-800 border-gray-200',
  };
  const priorityMap = {
    low: 'bg-emerald-100 text-emerald-800 border-emerald-200',
    medium: 'bg-amber-100 text-amber-800 border-amber-200',
    high: 'bg-red-100 text-red-800 border-red-200',
    _default: 'bg-gray-100 text-gray-800 border-gray-200',
  };

  function open(opener){
    const ds = opener?.dataset || {};
    if (elTitle) elTitle.textContent = ds.title || '—';
    if (elDesc) elDesc.textContent = ds.description || '';
    if (elDue) elDue.textContent = ds.due_date || '—';
    if (elProject) elProject.textContent = ds.project || '—';
    setBadge(elStatus, ds.status, statusMap);
    setBadge(elPriority, ds.priority, priorityMap);

    // Wire comments endpoint + reset list
    if (ds.comments_url) modal.dataset.commentsUrl = ds.comments_url;
    if (ds.comments_index_url) modal.dataset.commentsIndexUrl = ds.comments_index_url;
    if (ds.issue_id) modal.dataset.issueId = ds.issue_id;
    if (commentsList) commentsList.innerHTML = '';

    // Wire comments endpoint + reset list
    if (ds.comments_url) modal.dataset.commentsUrl = ds.comments_url;
    if (ds.issue_id) modal.dataset.issueId = ds.issue_id;
    if (commentsList) commentsList.innerHTML = '';

    // Render tags
    if (elTags){
      elTags.innerHTML = '';
      try {
        const list = ds.tags ? JSON.parse(ds.tags) : [];
        if (Array.isArray(list) && list.length){
          list.forEach(t => {
            const wrap = document.createElement('span');
            wrap.className = 'inline-flex items-center gap-2 text-xs border border-gray-300 rounded-md px-2 py-1';
            const dot = document.createElement('span');
            dot.className = 'inline-block h-3 w-3 rounded-full';
            dot.style.background = (t && t.color) ? t.color : '#e5e7eb';
            const name = document.createElement('span');
            name.className = 'text-gray-700';
            name.textContent = (t && t.name) ? t.name : '';
            wrap.appendChild(dot);
            wrap.appendChild(name);
            elTags.appendChild(wrap);
          });
        } else {
          const none = document.createElement('span');
          none.className = 'text-sm text-gray-500';
          none.textContent = 'No tags';
          elTags.appendChild(none);
        }
      } catch (_) {
        const none = document.createElement('span');
        none.className = 'text-sm text-gray-500';
        none.textContent = 'No tags';
        elTags.appendChild(none);
      }
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    // Notify listeners (e.g., comments.js) to load comments
    const evt = new CustomEvent('issue-view-open', {
      detail: {
        issueId: modal.dataset.issueId,
        commentsIndexUrl: modal.dataset.commentsIndexUrl,
      }
    });
    document.dispatchEvent(evt);
  }

  function close(){
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }

  // Delegated open
  document.addEventListener('click', (e) => {
    const opener = e.target.closest('[data-open-modal="issue-view-modal"]');
    if (opener){ e.preventDefault(); open(opener); }
  });

  btnClose && btnClose.addEventListener('click', close);
  btnOk && btnOk.addEventListener('click', close);
  // Do not close on overlay click or ESC for view modal
})();
