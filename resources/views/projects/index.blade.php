<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Projects</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto p-4">
        <div class="mb-4 flex justify-between items-center">
        </div>
        <div class="bg-white shadow rounded overflow-hidden">
            <table class="min-w-full">
                <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left p-3 text-gray-600">Name</th>
                    <th class="text-left p-3 text-gray-600">Description</th>
                </tr>
                </thead>
                <tbody>
                @foreach($projects as $project)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3">
                            {{ $project->name }}                        </td>
                        <td class="p-3">
                            {{ $project->description }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $projects->links() }}</div>
    </div>
</x-app-layout>
