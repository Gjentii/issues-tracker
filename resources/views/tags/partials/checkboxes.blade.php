@php
    $tags = isset($tags) ? $tags : \App\Models\Tag::query()->orderBy('name')->get();
    $selected = collect(old('tags', $selected ?? []))->map(fn($v) => (int) $v)->all();
@endphp

<div class="space-y-2">
    <x-input-label value="Tags" />
    @if($tags->isEmpty())
        <p class="text-sm text-gray-500">No tags available.</p>
    @else
        <div class="flex flex-wrap gap-3">
            @foreach($tags as $tag)
                <label class="inline-flex items-center gap-2 text-sm cursor-pointer select-none border border-gray-300 rounded-md px-2 py-1 hover:bg-gray-50">
                    <input
                        type="checkbox"
                        name="tags[]"
                        value="{{ $tag->id }}"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        {{ in_array($tag->id, $selected, true) ? 'checked' : '' }}
                    />
                    <span class="inline-block h-3 w-3 rounded-full" style="background: {{ $tag->color ?? '#e5e7eb' }}"></span>
                    <span class="text-gray-700">{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>
    @endif
</div>
