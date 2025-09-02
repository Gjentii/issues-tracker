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
    ];
    $priorityClass = $priorityStyles[$priority] ?? 'bg-gray-100 text-gray-700 border-gray-200';

    $statusStyles = [
        'open' => 'bg-green-100 text-gray-700 border-green-200',
        'in_progress' => 'bg-green-100 text-gray-700 border-green-200',
        'closed' => 'bg-red-100 text-red-800 border-red-200',
    ];

    $statusClass = $statusStyles[$status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
    $cardId = 'issue-card-'.($issue->id ?? uniqid());
@endphp

<div id="{{ $cardId }}" class="group relative h-full rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md flex flex-col">
    <div class="absolute inset-x-0 -top-px h-1 rounded-t-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-80"></div>

    <div class="flex-1">
        <div class="flex items-start justify-between gap-3">
            <h3 class="text-base font-semibold text-gray-900 truncate" title="{{ $title }}">{{ $title }}</h3>
            <div class="flex items-center gap-2">
                @if($status)
                    <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">{{ ucfirst($status) }}</span>
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
    </div>

    <div class="mt-auto pt-4 flex items-center justify-end gap-2">
        @if($viewUrl)
            <a href="#" role="button"
               data-open-modal="issue-view-modal"
               data-issue_id="{{ $issue->id }}"
               data-comments_url="{{ route('issues.comments.store', $issue) }}"
               data-comments_index_url="{{ route('issues.comments.index', $issue) }}"
               data-title="{{ $issue->title }}"
               data-description="{{ $issue->description }}"
               data-status="{{ $issue->status }}"
               data-priority="{{ $issue->priority }}"
               data-due_date="{{ $issue->due_date }}"
               data-tags='@json($issue->tags->map(fn($t) => ["name" => $t->name, "color" => $t->color])->values())'
               data-project="{{ optional($issue->project)->name }}"
               class="inline-flex h-9 items-center rounded-md border border-gray-300 bg-white px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">
               View
            </a>
        @endif
        @if($editUrl)
            <a href="#" role="button"
               data-open-modal="issue-modal"
               data-mode="edit"
               data-action="{{ route('issues.update', $issue) }}"
               data-method="PUT"
               data-title="{{ $issue->title }}"
               data-description="{{ $issue->description }}"
               data-status="{{ $issue->status }}"
               data-priority="{{ $issue->priority }}"
               data-due_date="{{ $issue->due_date }}"
               data-tags="{{ $issue->tags->pluck('id')->join(',') }}"
               data-replace="#{{ $cardId }}"
                data-success="#issues-success"
               data-success-message="Issue updated successfully."
               class="inline-flex h-9 items-center rounded-md bg-blue-600 px-3 text-xs font-medium text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
               Edit
            </a>
        @endif
        @if($deleteUrl)
            <button type="button"
                data-open-modal="delete-modal"
                data-action="{{ $deleteUrl }}"
                data-method="DELETE"
                data-name="{{ $title }}"
                data-remove="#{{ $cardId }}"
                data-success="#issues-success"
                data-success-message="Issue deleted successfully."
                class="inline-flex h-9 items-center rounded-md bg-red-600 px-3 text-xs font-medium text-white shadow-sm hover:bg-red-500">
                Delete
            </button>
        @endif
    </div>
</div>
