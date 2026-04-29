<div class="mb-5 break-inside-avoid inline-block w-full">
    <h3 class="bg-gray-100 py-2 px-2.5 border-b-2 border-gray-300 text-lg font-bold">
        {{ $groupAttributes->first()->attribute_group->name }}
    </h3>
    <table class="w-full border-collapse">
        @foreach($groupAttributes as $attribute)
        <tr class="border-b border-gray-200">
            <td class="py-2 px-1.5 text-gray-500 w-1/2 text-sm">{{ $attribute->name }}</td>
            <td class="py-2 px-1.5 font-bold text-sm">{{ $attribute->pivot->value }}</td>
        </tr>
        @endforeach
    </table>
</div>