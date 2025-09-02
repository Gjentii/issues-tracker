(() => {
  const modal = document.getElementById('issue-view-modal');
  if (!modal) return;
  const form = document.getElementById('view-issue-comment-form');
  const input = document.getElementById('view-issue-comment-input');
  const list = document.getElementById('view-issue-comments-list');
  const errorEl = document.getElementById('view-issue-comment-error');
  const submitBtn = document.getElementById('view-issue-comment-submit');
  const loadingEl = document.getElementById('view-issue-comments-loading');
  const prevBtn = document.getElementById('view-issue-comments-prev');
  const nextBtn = document.getElementById('view-issue-comments-next');
  const pageInfo = document.getElementById('view-issue-comments-page');

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

  // State for pagination
  function getState(){
    return {
      page: parseInt(modal.dataset.commentsPage || '1', 10),
      hasMore: modal.dataset.commentsHasMore !== 'false',
      loading: modal.dataset.commentsLoading === 'true',
      lastPage: parseInt(modal.dataset.commentsLastPage || '1', 10),
    };
  }
  function setState(next){
    if (next.page !== undefined) modal.dataset.commentsPage = String(next.page);
    if (next.hasMore !== undefined) modal.dataset.commentsHasMore = String(!!next.hasMore);
    if (next.loading !== undefined) modal.dataset.commentsLoading = String(!!next.loading);
    if (next.lastPage !== undefined) modal.dataset.commentsLastPage = String(next.lastPage);
  }
  function setLoading(v){
    setState({ loading: v });
    if (loadingEl){ loadingEl.classList.toggle('hidden', !v); }
  }

  function updatePager(){
    const st = getState();
    const prevDisabled = st.page <= 1 || st.loading;
    const nextDisabled = st.page >= st.lastPage || st.loading;
    if (prevBtn){
      prevBtn.setAttribute('aria-disabled', String(prevDisabled));
      prevBtn.classList.toggle('opacity-50', prevDisabled);
      prevBtn.classList.toggle('cursor-not-allowed', prevDisabled);
    }
    if (nextBtn){
      nextBtn.setAttribute('aria-disabled', String(nextDisabled));
      nextBtn.classList.toggle('opacity-50', nextDisabled);
      nextBtn.classList.toggle('cursor-not-allowed', nextDisabled);
    }
    if (pageInfo){ pageInfo.textContent = `Page ${st.page} of ${st.lastPage}`; }
  }

  async function loadComments(page = 1, append = false){
    let base = modal.dataset.commentsIndexUrl;
    if (!base){
      const id = modal.dataset.issueId;
      if (id) base = `/issues/${id}/comments`;
    }
    if (!base) return;
    const url = new URL(base, window.location.origin);
    url.searchParams.set('page', String(page));
    try {
      setLoading(true);
      const res = await fetch(url.toString(), { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
      if (res.ok){
        const payload = await res.json();
        const items = payload?.data || [];
        if (append){ items.forEach(it => list.appendChild(renderComment(it))); }
        else { renderList(items); if (list) list.scrollTop = 0; }
        const hasMore = !!payload?.next_page_url;
        const lastPage = parseInt(payload?.last_page || 1, 10);
        setState({ page, hasMore, lastPage });
        updatePager();
      }
    } catch(_){}
    finally { setLoading(false); }
  }

  // Load list when the modal opens for an issue
  document.addEventListener('issue-view-open', () => {
    showError('');
    setState({ page: 1, hasMore: true, loading: false, lastPage: 1 });
    loadComments(1, false);
  });

  // Delegated pager clicks so it always works
  document.addEventListener('click', (e) => {
    const prev = e.target.closest('#view-issue-comments-prev');
    const next = e.target.closest('#view-issue-comments-next');
    if (!prev && !next) return;
    // Only when modal is open
    if (!modal || modal.classList.contains('hidden')) return;
    e.preventDefault();
    const st = getState();
    if (prev && st.page > 1 && !st.loading){
      loadComments(st.page - 1, false);
    } else if (next && st.page < st.lastPage && !st.loading){
      loadComments(st.page + 1, false);
    }
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
        // After posting, reload first page to keep page size at 3 and order by latest
        input.value = '';
        setState({ page: 1 });
        await loadComments(1, false);
        if (list) list.scrollTop = 0;
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
