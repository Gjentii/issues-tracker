<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tags') }}
        </h2>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form action="{{ route('projects.store') }}" method="POST" class="bg-white p-6 shadow rounded-lg space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Create Tag') }}
                            </h2>
                        </header>
                        @csrf
                        @include('tags.form')
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('projects.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Cancel</a>
                            <x-primary-button> Create </x-primary-button>
                        </div>
                    </form>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="max-w-7xl mx-auto p-4">

                        <x-bladewind::table divider="thin">
                            <x-slot name="header">
                                <th>Name</th>
                                <th>Color</th>
                                <th>Created At</th>
                            </x-slot>
                            @foreach($tags as $tag)
                                <tr>
                                    <td>{{$tag->name}}</td>
                                    <td><div class="w-7 h-7 rounded-full ml-1.5" style="background: {{ $tag->color ?? '#e5e7eb' }}"></div></td>
                                    <td>{{$tag->created_at}}</td>
                                </tr>
                            @endforeach
                        </x-bladewind::table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
