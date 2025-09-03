<!-- Issue Create Modal -->
<div id="issue-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60"></div>
    <div class="relative w-full max-w-xl rounded-lg bg-white p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <h4 id="issue-modal-title" class="text-lg font-semibold text-gray-900">Create Issue</h4>
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
                    <select id="issue-status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" >
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

            {{-- Tags selection --}}
            @include('tags.partials.checkboxes')

            {{-- Members selection --}}
            <div class="pt-3 border-t">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Members</span>
                    <button type="button" id="issue-members-add-btn" class="inline-flex h-8 items-center rounded-md border border-gray-300 bg-white px-2.5 text-xs font-medium text-gray-700 hover:bg-gray-50">+ Add member</button>
                </div>
                <div id="issue-members-list" class="mt-2 flex flex-wrap gap-2"></div>
                <div id="issue-members-add-row" class="mt-3 hidden items-center gap-2">
                    <select id="issue-members-select" class="h-9 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select a user...</option>
                        @foreach(($users ?? collect()) as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    <x-primary-button type="button" id="issue-members-attach" class="h-9">Attach</x-primary-button>
                    <button type="button" id="issue-members-cancel" class="inline-flex h-9 items-center rounded-md border border-gray-300 bg-white px-3 text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                </div>
                {{-- Hidden container for create mode selections --}}
                <div id="issue-members-hidden" class="hidden"></div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" id="cancel-issue-modal" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <x-primary-button type="submit" id="issue-submit">Create</x-primary-button>
            </div>
        </form>
    </div>
    </div>
<script>
{!! file_get_contents(resource_path('js/projects/issues/modal.js')) !!}
{!! file_get_contents(resource_path('js/projects/issues/members.js')) !!}
</script>
