(() => {
  const modal = document.getElementById('issue-view-modal');
  if (!modal) return;
  const form = document.getElementById('view-issue-comment-form');
  const input = document.getElementById('view-issue-comment-input');
  const list = document.getElementById('view-issue-comments-list');
  const errorEl = document.getElementById('view-issue-comment-error');
  const submitBtn = document.getElementById('view-issue-comment-submit');

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  function showError(msg){
    if (!errorEl) return;
    if (msg){
      errorEl.textContent = msg;
      errorEl.classList.remove('hidden');
    } else {
      errorEl.textContent = '';
      errorEl.classList.add('hidden');
    }
  }

  function renderComment(item){
    const li = document.createElement('li');
    li.className = 'rounded-md border border-gray-200 p-3 bg-white';
    const p = document.createElement('p');
    p.className = 'text-sm text-gray-800';
    p.textContent = item?.content ?? '';
    const meta = document.createElement('div');
    meta.className = 'mt-1 text-xs text-gray-500';
    meta.textContent = item?.created_at ? new Date(item.created_at).toLocaleString() : '';
    li.appendChild(p);
    li.appendChild(meta);
    return li;
  }

  function renderList(items){
    if (!list) return;
    list.innerHTML = '';
    (items || []).forEach(item => {
      list.appendChild(renderComment(item));
    });
  }

  async function loadComments(){
    let url = modal.dataset.commentsIndexUrl;
    if (!url){
      const id = modal.dataset.issueId;
      if (id) url = `/issues/${id}/comments`;
    }
    if (!url) return;
    try {
      const res = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
      if (res.ok){
        const items = await res.json();
        renderList(items);
      }
    } catch(_){}
  }

  // Load list when the modal opens for an issue
  document.addEventListener('issue-view-open', () => {
    showError('');
    loadComments();
  });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    showError('');
    if (!input || !input.value.trim()){
      showError('Please enter a comment.');
      return;
    }
    let url = modal.dataset.commentsUrl;
    if (!url) {
      const id = modal.dataset.issueId;
      if (id) url = `/issues/${id}/comments`;
    }
    if (!url) return;
    const original = submitBtn?.textContent;
    if (submitBtn){ submitBtn.textContent = 'Posting…'; submitBtn.disabled = true; submitBtn.classList.add('opacity-70','cursor-not-allowed'); }
    try {
      const fd = new FormData();
      fd.append('content', input.value.trim());
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf || '',
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
        },
        credentials: 'same-origin',
        body: fd,
      });
      if (res.ok){
        const data = await res.json();
        const li = renderComment(data);
        if (list && li){ list.insertBefore(li, list.firstChild); }
        input.value = '';
      } else if (res.status === 422){
        const data = await res.json();
        const msg = (data?.errors?.content?.[0]) || 'Validation error.';
        showError(msg);
      }
    } catch(_){}
    finally {
      if (submitBtn){ submitBtn.textContent = original || 'Post'; submitBtn.disabled = false; submitBtn.classList.remove('opacity-70','cursor-not-allowed'); }
    }
  });
})();
