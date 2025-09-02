@php($editing = isset($project))
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $editing ? 'Edit Project' : 'New Project' }}</h2>
    </x-slot>
    <div class="max-w-3xl mx-auto p-4">
        <form action="{{ $editing ? route('projects.update', $project) : route('projects.store') }}" method="POST" class="bg-white p-6 shadow rounded-lg space-y-6">
            @csrf
            @if($editing)
                @method('PUT')
            @endif
            @include('projects.form', $editing ? ['project' => $project] : [])
            <div class="flex justify-end space-x-2">
                <a href="{{ route('projects.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Cancel</a>
                <x-primary-button> {{ $editing ? 'Save' : 'Create' }} </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>

