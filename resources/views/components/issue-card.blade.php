@props([
    'issue' => null,
    'viewUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
])

@php
    $title = $issue->title ?? ('Issue #'.($issue->id ?? ''));
    $description = $issue->description ?? null;
    $status = $issue->status ?? null;
    $priority = strtolower((string)($issue->priority ?? ''));
    $due = $issue->due_date ?? null;

    $priorityStyles = [
        'low' => 'bg-green-100 text-gray-700 border-green-200',
        'medium' => 'bg-amber-100 text-amber-800 border-amber-200',
        'high' => 'bg-red-100 text-red-800 border-red-200',
        'urgent' => 'bg-red-100 text-red-800 border-red-200',
    ];
    $priorityClass = $priorityStyles[$priority] ?? 'bg-gray-100 text-gray-700 border-gray-200';
@endphp

<div class="group relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
    <div class="absolute inset-x-0 -top-px h-1 rounded-t-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-80"></div>

    <div class="flex items-start justify-between gap-3">
        <h3 class="text-base font-semibold text-gray-900 truncate" title="{{ $title }}">{{ $title }}</h3>
        <div class="flex items-center gap-2">
            @if($status)
                <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 border border-gray-200">{{ ucfirst($status) }}</span>
            @endif
            @if($priority)
                <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium border {{ $priorityClass }}">{{ ucfirst($priority) }}</span>
            @endif
        </div>
    </div>

    @if($description)
        <p class="mt-3 text-sm leading-6 text-gray-600 max-h-20 overflow-hidden">{{ $description }}</p>
    @endif

    @if($due)
        <div class="mt-1 text-xs text-gray-500">Due: <span class="font-medium text-gray-700">{{ \Illuminate\Support\Carbon::parse($due)->toFormattedDateString() }}</span></div>
    @endif


    <div class="mt-5 flex items-center justify-end gap-2">
        @if($viewUrl)
            <a href="{{ $viewUrl }}" class="inline-flex h-9 items-center rounded-md border border-gray-300 bg-white px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">View</a>
        @endif
        @if($editUrl)
            <a href="{{ $editUrl }}" class="inline-flex h-9 items-center rounded-md bg-indigo-600 px-3 text-xs font-medium text-white shadow-sm hover:bg-indigo-500">Edit</a>
        @endif
        @if($deleteUrl)
            <form action="{{ $deleteUrl }}" method="POST" class="inline" onsubmit="return confirm('Delete this issue?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-9 items-center rounded-md bg-red-600 px-3 text-xs font-medium text-white shadow-sm hover:bg-red-500">Delete</button>
            </form>
        @endif
    </div>
</div>
