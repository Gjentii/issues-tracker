@props([
    'modalId',
    'action' => null,
    'method' => 'DELETE',
    'title' => 'Confirm Delete',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Delete',
    'cancelLabel' => 'Cancel',
])

<div id="{{ $modalId }}" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60" data-close="{{ $modalId }}"></div>
    <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
        <div class="flex items-start justify-between gap-4">
            <h4 class="text-lg font-semibold text-gray-900">{{ $title }}</h4>
            <button type="button" id="{{ $modalId }}-close" class="rounded p-1 text-gray-500 hover:bg-gray-100">✕</button>
        </div>
        <p class="mt-3 text-sm text-gray-600">
            <span data-placeholder="name"></span>
            {{ $message }}
        </p>
        <form id="{{ $modalId }}-form" action="{{ $action }}" method="POST" class="mt-6 flex justify-end gap-2">
            @csrf
            @if (strtoupper($method) !== 'POST')
                <input type="hidden" name="_method" value="{{ $method }}" />
            @endif
            <button type="button" id="{{ $modalId }}-cancel" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $cancelLabel }}</button>
            <button type="submit" id="{{ $modalId }}-confirm" class="inline-flex h-9 items-center rounded-md bg-red-600 px-3 text-xs font-medium text-white shadow-sm hover:bg-red-500">{{ $confirmLabel }}</button>
        </form>
    </div>
    </div>

<script>
{!! file_get_contents(resource_path('js/shared/delete-modal.js')) !!}
window.initDeleteModal && window.initDeleteModal('{{ $modalId }}');
</script>

