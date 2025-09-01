<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $tag->name ?? '') }}" autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
        <ul id="error-name" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
    </div>
    <div>
        <x-input-label for="color" value="Color" />
        <x-text-input id="color" name="color" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('color', $tag->color ?? '') }}</x-text-input>
        <x-input-error :messages="$errors->get('color')" class="mt-2" />
        <ul id="error-color" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
    </div>
</div>
