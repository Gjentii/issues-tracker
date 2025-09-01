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
</x-app-layout>
