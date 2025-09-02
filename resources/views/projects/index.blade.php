<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects') }}
        </h2>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-row items-center justify-end gap-4 mb-2">
                        <div class="sm:ml-4 flex-shrink-0">
                            <x-custom.button-link href="{{ route('projects.create') }}" name="Create Project"/>
                        </div>
                    </div>

                    <div class="max-w-7xl mx-auto p-4">

                        <x-bladewind::table divider="thin">

                            <x-slot name="header">
                                <th>Name</th>
                                <th>Descriptions</th>
                                <th>Start Date</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </x-slot>
                            @foreach($projects as $project)
                                <tr id="project-row-{{ $project->id }}">
                                    <td>{{ $project->name }}</td>
                                    <td>{{ $project->description }}</td>
                                    <td>{{ $project->start_date ?? '' }}</td>
                                    <td>{{ $project->deadline ?? '' }}</td>
                                    <td class="p-3 gap-3">
                                        <div class="flex items-center gap-3">
                                            <a class="text-blue-700 hover:!text-blue-800 font-medium" href="{{ route('projects.show', $project) }}">View</a>
                                            <a class="!text-blue-700 hover:!text-blue-800 font-medium" href="{{ route('projects.edit', $project) }}">Edit</a>
                                            <button type="button"
                                                    data-open-modal="delete-modal"
                                                    data-action="{{ route('projects.destroy', $project) }}"
                                                    data-method="DELETE"
                                                    data-name="{{ $project->name }}"
                                                    data-remove="#project-row-{{ $project->id }}"
                                                    class="text-red-600 hover:underline">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-bladewind::table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-delete-modal modalId="delete-modal" title="Delete Project" message="This action cannot be undone." />
</x-app-layout>
