<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $tag->name ?? '') }}" autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
        <ul id="error-name" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
    </div>
    <div>
        <x-input-label for="color" value="Color" />
        <x-text-input
            id="color"
            name="color"
            type="color"
            class="color-input mt-1 inline-block h-10 w-16 cursor-pointer border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-0"
            value="{{ old('color', $tag->color ?? '#000000') }}"
            aria-label="Pick a color"
        />
        <x-input-error :messages="$errors->get('color')" class="mt-2" />
        <ul id="error-color" class="mt-2 text-sm text-red-600 space-y-1 hidden"></ul>
    </div>
</div>

<style>
    /* Compact, tidy color input styling */
    input[type="color"].color-input {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        padding: 0; /* handled via Tailwind p-0 */
        background: transparent;
        overflow: hidden; /* ensure rounded corners clip swatch */
    }
    input[type="color"].color-input::-webkit-color-swatch-wrapper { padding: 0; }
    input[type="color"].color-input::-webkit-color-swatch { border: none; border-radius: 0.375rem; }
    input[type="color"].color-input::-moz-color-swatch { border: none; border-radius: 0.375rem; }
</style>
