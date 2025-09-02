<!-- Issue View Modal -->
<div id="issue-view-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60" data-close="issue-view-modal"></div>
    <div class="relative w-full max-w-2xl rounded-lg bg-white p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-gray-900">Issue Details</h4>
            <button type="button" id="issue-view-close" class="rounded p-1 text-gray-500 hover:bg-gray-100">✕</button>
        </div>

        <div class="mt-4 space-y-4">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <div class="text-sm text-gray-500">Title</div>
                    <div id="view-issue-title" class="mt-1 text-base font-semibold text-gray-900">—</div>
                </div>
                <div class="flex items-start gap-6">
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Status</div>
                        <span id="view-issue-status" class="mt-1 hidden shrink-0 rounded-full px-2 py-0.5 text-xs font-medium border"></span>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Priority</div>
                        <span id="view-issue-priority" class="mt-1 hidden shrink-0 rounded-full px-2 py-0.5 text-xs font-medium border"></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Due Date</div>
                    <div id="view-issue-due" class="mt-1 text-sm text-gray-800">—</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Project</div>
                    <div id="view-issue-project" class="mt-1 text-sm text-gray-800">—</div>
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Description</div>
                <p id="view-issue-description" class="mt-1 text-sm leading-6 text-gray-700 whitespace-pre-line"></p>
            </div>

            <div>
                <div class="text-sm text-gray-500">Tags</div>
                <div id="view-issue-tags" class="mt-2 flex flex-wrap gap-2"></div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" id="issue-view-ok" class="inline-flex h-9 items-center rounded-md bg-gray-800 px-3 text-xs font-medium text-white shadow-sm hover:bg-gray-700">Close</button>
        </div>
    </div>
    </div>

<script>
{!! file_get_contents(resource_path('js/projects/issues/view-modal.js')) !!}
</script>
