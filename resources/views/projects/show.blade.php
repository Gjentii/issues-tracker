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
            <x-primary-button type="button" id="open-issue-modal"
                data-open-modal="issue-modal"
                data-mode="create"
                data-action="{{ route('issues.store') }}"
                data-method="POST"
                data-success="#issues-success"
                data-success-message="Issue created successfully.">
                New Issue
            </x-primary-button>
        </div>
        <div id="issues-success" class="hidden rounded-md bg-green-50 p-4 text-green-800 text-sm mb-4"></div>

        <!-- Filters -->
        <form method="GET" action="{{ route('projects.show', $project) }}" class="mb-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="q" value="Search" />
                    <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" placeholder="Search title or description" value="{{ request('q') }}" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Any</option>
                        @foreach(($statuses ?? []) as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="priority" value="Priority" />
                    <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Any</option>
                        @foreach(($priorities ?? []) as $key => $label)
                            <option value="{{ $key }}" @selected(request('priority') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                @include('tags.partials.checkboxes', [
                    'tags' => ($tags ?? collect()),
                    'selected' => (array) request('tags', []),
                ])
            </div>
            <div class="flex items-center gap-2">
                <x-primary-button type="submit">Filter</x-primary-button>
                <a href="{{ route('projects.show', $project) }}" class="inline-flex h-10 items-center rounded-md border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
            </div>
        </form>

        @if(($issues ?? collect())->isEmpty())
            <p class="text-sm text-gray-500" id="no-issues-msg">No issues yet for this project.</p>
        @endif
        <div id="issues-grid" data-ajax-url="{{ route('projects.issues', $project) }}">
            @include('projects.partials.issues-grid', ['issues' => $issues ?? collect()])
        </div>

        @include('projects.issuess.modal')
        @include('projects.issuess.view-modal')
        <x-delete-modal modalId="delete-modal" title="Delete Item" message="This action cannot be undone." />
        <script>
            (function(){
                const form = document.querySelector('form[action="{{ route('projects.show', $project) }}"]');
                if(!form) return;
                const grid = document.getElementById('issues-grid');
                const noMsg = document.getElementById('no-issues-msg');
                const ajaxUrl = grid?.getAttribute('data-ajax-url');

                const resetLink = form.querySelector('a[href="{{ route('projects.show', $project) }}"]');

                const debounce = (fn, wait=350) => {
                    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
                };

                const fetchAndRender = async () => {
                    if(!ajaxUrl) return;
                    const params = new URLSearchParams(new FormData(form));
                    // Ensure multiple tags[] are preserved
                    const url = ajaxUrl + (params.toString() ? ('?' + params.toString()) : '');
                    try{
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if(!res.ok) return;
                        const data = await res.json();
                        if(grid && data.html !== undefined){ grid.innerHTML = data.html; }
                        if(noMsg){ noMsg.style.display = (data.count && data.count > 0) ? 'none' : ''; }
                    }catch(e){ /* noop */ }
                };

                const debounced = debounce(fetchAndRender, 400);

                // input listeners
                form.querySelectorAll('input[type="text"]').forEach(el => {
                    el.addEventListener('input', debounced);
                });
                form.querySelectorAll('select').forEach(el => {
                    el.addEventListener('change', debounced);
                });
                form.querySelectorAll('input[type="checkbox"]').forEach(el => {
                    el.addEventListener('change', debounced);
                });

                form.addEventListener('submit', function(ev){ ev.preventDefault(); fetchAndRender(); });

                if(resetLink){
                    resetLink.addEventListener('click', function(ev){
                        ev.preventDefault();
                        // clear all fields
                        form.reset();
                        // Ensure tag checkboxes from partial are cleared too
                        form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                        fetchAndRender();
                    });
                }
            })();
        </script>
    </x-card-section>
</x-app-layout>
