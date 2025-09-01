<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tags') }}
        </h2>

    </x-slot>

    <x-card-section>
        <form id="tag-form" action="{{ route('tags.store') }}" method="POST" class="space-y-6">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Create Tag') }}
                </h2>
            </header>
            @csrf
            <div id="tag-success" class="hidden rounded-md bg-green-50 p-4 text-green-800 text-sm"></div>
            @include('tags.form')
            <div class="flex justify-end space-x-2">
                <a href="{{ route('tags.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Cancel</a>
                <x-primary-button> Create </x-primary-button>
            </div>
        </form>
    </x-card-section>

    <x-card-section>
        <div id="tags-container">
            @include('tags.table', ['tags' => $tags])
        </div>
    </x-card-section>
</x-app-layout>

<script>
    (function() {
        const form = document.getElementById('tag-form');
        if (!form) return;

        const nameInput = document.getElementById('name');
        const colorInput = document.getElementById('color');
        const successBox = document.getElementById('tag-success');

        const errorEls = {
            name: document.getElementById('error-name'),
            color: document.getElementById('error-color')
        };

        function setErrors(field, messages) {
            const el = errorEls[field];
            if (!el) return;
            el.innerHTML = '';
            if (messages && messages.length) {
                el.classList.remove('hidden');
                for (const m of messages) {
                    const li = document.createElement('li');
                    li.textContent = m;
                    el.appendChild(li);
                }
            } else {
                el.classList.add('hidden');
            }
        }

        function clearAllErrors() {
            setErrors('name', []);
            setErrors('color', []);
        }

        function debounce(fn, wait) {
            let t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), wait); }
        }

        async function validateField(field, value) {
            try {
                const payload = new FormData();
                payload.set('field', field);
                payload.set(field, value ?? '');
                const res = await fetch('{{ route('tags.validate') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: payload
                });
                if (res.status === 422) {
                    const data = await res.json();
                    setErrors(field, data.errors?.[field] || []);
                } else {
                    setErrors(field, []);
                }
            } catch (e) {
                // fail silently
            }
        }

        const debouncedValidate = debounce((field, value) => validateField(field, value), 350);

        nameInput?.addEventListener('input', (e) => debouncedValidate('name', e.target.value));
        colorInput?.addEventListener('input', (e) => debouncedValidate('color', e.target.value));

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearAllErrors();
            successBox?.classList.add('hidden');
            successBox && (successBox.textContent = '');

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new FormData(form)
                });

                if (res.status === 201 || res.ok) {
                    const data = await res.json().catch(() => ({}));
                    form.reset();
                    successBox.textContent = data.message || 'Tag created successfully.';
                    successBox.classList.remove('hidden');
                    // Refresh the tags table markup
                    const container = document.getElementById('tags-container');
                    try {
                        const htmlRes = await fetch('{{ route('tags.table') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } });
                        if (htmlRes.ok) {
                            const html = await htmlRes.text();
                            container.innerHTML = html;
                        }
                    } catch (_) {}
                } else if (res.status === 422) {
                    const data = await res.json();
                    const errs = data.errors || {};
                    setErrors('name', errs.name || []);
                    setErrors('color', errs.color || []);
                } else {
                    // unexpected error
                }
            } catch (err) {
                // network or other error; ignore in UI for now
            }
        });
    })();
</script>
