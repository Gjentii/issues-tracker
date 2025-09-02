@php($editing = isset($project))
<div class="space-y-4">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $project->name ?? '') }}" autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('description', $project->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="start_date" value="Start Date" />
            <x-text-input id="start_date" name="start_date" type="text" inputmode="numeric" pattern="\d{2}-\d{2}-\d{4}" placeholder="dd-mm-yyyy" class="mt-1 block w-full"
                          value="{{ old('start_date', ($editing && !empty($project->start_date)) ? \Illuminate\Support\Carbon::parse($project->start_date)->format('d-m-Y') : '') }}" />
            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="deadline" value="Deadline" />
            <x-text-input id="deadline" name="deadline" type="text" inputmode="numeric" pattern="\d{2}-\d{2}-\d{4}" placeholder="dd-mm-yyyy" class="mt-1 block w-full"
                          value="{{ old('deadline', ($editing && !empty($project->deadline)) ? \Illuminate\Support\Carbon::parse($project->deadline)->format('d-m-Y') : '') }}" />
            <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
        </div>
    </div>
</div>
