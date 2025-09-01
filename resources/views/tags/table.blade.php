<x-bladewind::table divider="thin">
    <x-slot name="header">
        <th>Name</th>
        <th>Color</th>
        <th>Created At</th>
    </x-slot>
    @foreach($tags as $tag)
        <tr>
            <td>{{ $tag->name }}</td>
            <td><div class="w-7 h-7 rounded-full ml-1.5" style="background: {{ $tag->color ?? '#e5e7eb' }}"></div></td>
            <td>{{ $tag->created_at }}</td>
        </tr>
    @endforeach
</x-bladewind::table>

