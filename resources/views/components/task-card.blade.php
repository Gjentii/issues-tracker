@props([
    'task' => null,
    'viewUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
])

@php
    $title = $task->title ?? $task->name ?? ($task->subject ?? ('Task #'.($task->id ?? '')));
    $description = $task->description ?? $task->details ?? null;
    $status = $task->status ?? null;
    $assignee = $task->assignee->name ?? ($task->user->name ?? ($task->assigned_to ?? null));
@endphp

<div class="group relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
    <div class="absolute inset-x-0 -top-px h-1 rounded-t-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-80"></div>

    <div class="flex items-start justify-between gap-3">
        <h3 class="text-base font-semibold text-gray-900 truncate" title="{{ $title }}">{{ $title }}</h3>
        @if($status)
            <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 border border-gray-200">{{ ucfirst($status) }}</span>
        @endif
    </div>

    @if($assignee)
        <div class="mt-1 text-xs text-gray-500">Assigned to: <span class="font-medium text-gray-700">{{ $assignee }}</span></div>
    @endif

    @if($description)
        <p class="mt-3 text-sm leading-6 text-gray-600 overflow-hidden">{{ $description }}</p>
    @endif

    <div class="mt-5 flex items-center justify-end gap-2">
        @if($viewUrl)
            <a href="{{ $viewUrl }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">View</a>
        @endif
        @if($editUrl)
            <a href="{{ $editUrl }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-indigo-500">Edit</a>
        @endif
        @if($deleteUrl)
            <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm('Delete this task?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-500">Delete</button>
            </form>
        @endif
    </div>
</div>
