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

        @include('projects.issuess.modal')
    </x-card-section>
</x-app-layout>
