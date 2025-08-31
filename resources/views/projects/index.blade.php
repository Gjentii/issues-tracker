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
                    <div class="flex flex-row items-center justify-between gap-4">

                        <form class="flex-1 min-w-0 max-w-2xl">
                            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="relative">
                                <input type="search" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search" />
                                <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 ">Search</button>
                            </div>
                        </form>
                        <div class="sm:ml-4 flex-shrink-0">
                            <x-custom.button-link href="{{ route('projects.create') }}" name="Create Project"/>
                        </div>
                    </div>

                    <div class="max-w-7xl mx-auto p-4">

                        <x-bladewind::table divider="thin">

                            <x-slot name="header">
                                <th>Name</th>
                                <th>Descriptions</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </x-slot>
                            @foreach($projects as $project)
                                <tr>
                                    <td>{{$project->name}}</td>
                                    <td>{{$project->description}}</td>
                                    <td>{{$project->created_at}}</td>
                                    <td class="p-3">
                                        <a class="text-blue-700 font-medium" href="{{ route('projects.show', $project) }}">View</a>
                                        <a class="text-blue-700 font-medium" href="{{ route('projects.edit', $project) }}">Edit</a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Delete project?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </x-bladewind::table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
