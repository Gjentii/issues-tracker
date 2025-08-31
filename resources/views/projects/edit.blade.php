<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Project</h2>
    </x-slot>
    <div class="max-w-3xl mx-auto p-4">
        <form action="{{ route('projects.update', $project) }}" method="POST" class="bg-white p-6 shadow rounded-lg space-y-6">
            @csrf @method('PUT')
            @include('projects.form', ['project' => $project])
            <div class="flex justify-end space-x-2">
                <a href="{{ route('projects.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Cancel</a>
                <x-primary-button> Save </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
