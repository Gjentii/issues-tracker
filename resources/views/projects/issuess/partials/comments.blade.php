<div>
    <div class="text-sm font-semibold text-gray-900 mb-2">Comments</div>
    <form id="view-issue-comment-form" class="flex items-start gap-2 mb-3">
        @csrf
        <input type="text" id="view-issue-comment-input" name="content" placeholder="Add a comment..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
        <x-primary-button type="submit" id="view-issue-comment-submit">Post</x-primary-button>
    </form>
    <ul id="view-issue-comments-list" class="space-y-3 mb-2 max-h-52 overflow-auto"></ul>
    <div class="flex items-center justify-between mb-3" id="view-issue-comments-pagination">
        <div class="flex items-center gap-2">
            <button type="button" id="view-issue-comments-prev" class="inline-flex h-8 items-center rounded-md border border-gray-300 bg-white px-2 text-xs font-medium text-gray-700 hover:bg-gray-50">Back</button>
            <button type="button" id="view-issue-comments-next" class="inline-flex h-8 items-center rounded-md border border-gray-300 bg-white px-2 text-xs font-medium text-gray-700 hover:bg-gray-50">Next</button>
        </div>
        <div id="view-issue-comments-page" class="text-xs text-gray-500"></div>
    </div>
    <div id="view-issue-comments-loading" class="text-xs text-gray-500 mb-3 hidden">Loading…</div>
    <p id="view-issue-comment-error" class="mt-1 text-xs text-red-600 hidden"></p>
</div>

