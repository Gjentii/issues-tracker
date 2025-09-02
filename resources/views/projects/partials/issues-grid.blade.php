@props(['issues' => collect()])

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

