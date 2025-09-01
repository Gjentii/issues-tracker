<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project') }} : {{$project->name}}
        </h2>

    </x-slot>

    <x-card-section>
        <div class="flex items-baseline gap-2">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Project Description') }}: </h2>
            <p class="text-md text-gray-600">{{ $project->description }}</p>
        </div>
    </x-card-section>

    <x-card-section>
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Issues</h3>
            <x-primary-button type="button" id="open-issue-modal">New Issue</x-primary-button>
        </div>
        @php($issues = $project->issues()->latest()->get())
        @if($issues->isEmpty())
            <p class="text-sm text-gray-500" id="no-issues-msg">No issues yet for this project.</p>
        @endif
        <div id="issues-grid">
            <x-cards-grid>
                @foreach(($issues ?? collect()) as $issue)
                    <x-issue-card
                        :issue="$issue"
                        :view-url="route('issues.show', $issue)"
                        :edit-url="route('issues.edit', $issue)"
                        :delete-url="route('issues.destroy', $issue)"
                    />
                @endforeach
            </x-cards-grid>
        </div>

        <!-- Issue Create Modal -->
        <div id="issue-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
            <div class="absolute inset-0 bg-gray-900/60"></div>
            <div class="relative w-full max-w-xl rounded-lg bg-white p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-semibold text-gray-900">Create Issue</h4>
                    <button type="button" id="close-issue-modal" class="rounded p-1 text-gray-500 hover:bg-gray-100">✕</button>
                </div>
                <form id="issue-form" action="{{ route('issues.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}" />

                    <div>
                        <x-input-label for="issue-title" value="Title" />
                        <x-text-input id="issue-title" name="title" type="text" class="mt-1 block w-full" />
                        <ul id="error-title" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
                    </div>

                    <div>
                        <x-input-label for="issue-description" value="Description" />
                        <textarea id="issue-description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4"></textarea>
                        <ul id="error-description" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="issue-status" value="Status" />
                            <select id="issue-status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                            </select>
                            <ul id="error-status" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
                        </div>
                        <div>
                            <x-input-label for="issue-priority" value="Priority" />
                            <select id="issue-priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <ul id="error-priority" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
                        </div>
                        <div>
                            <x-input-label for="issue-due-date" value="Due Date" />
                            <input id="issue-due-date" name="due_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <ul id="error-due_date" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" id="cancel-issue-modal" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <x-primary-button type="submit">Create</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </x-card-section>
</x-app-layout>

<script>
    (function() {
        const openBtn = document.getElementById('open-issue-modal');
        const closeBtn = document.getElementById('close-issue-modal');
        const cancelBtn = document.getElementById('cancel-issue-modal');
        const modal = document.getElementById('issue-modal');
        const form = document.getElementById('issue-form');
        const grid = document.getElementById('issues-grid');
        const noIssuesMsg = document.getElementById('no-issues-msg');

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
            const fields = ['title','description','status','priority','due_date'];
            for (const f of fields) {
                const el = document.getElementById('error-' + f);
                if (!el) continue;
                el.innerHTML = '';
                const msgs = map?.[f] || [];
                if (msgs.length) {
                    el.classList.remove('hidden');
                    msgs.forEach(m => { const li = document.createElement('li'); li.textContent = m; el.appendChild(li); });
                } else {
                    el.classList.add('hidden');
                }
            }
        }

        openBtn?.addEventListener('click', openModal);
        closeBtn?.addEventListener('click', closeModal);
        cancelBtn?.addEventListener('click', closeModal);
        modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

        form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            setErrors({});

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new FormData(form)
                });
                if (res.status === 201 || res.ok) {
                    const data = await res.json();
                    // Clear placeholder message if present
                    if (noIssuesMsg) noIssuesMsg.remove();
                    // Insert returned card HTML at the start of the grid
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
                } else {
                    // non-validation error: silently ignore for now
                }
            } catch (_e) { /* ignore */ }
        });
    })();
</script>
