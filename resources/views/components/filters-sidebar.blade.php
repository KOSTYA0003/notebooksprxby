<style>
    html [x-cloak] {
        display: none !important;
    }
</style>

<aside class="w-[300px] text-[11px] bg-white p-4 border border-gray-300 rounded-lg h-fit top-5">

    <h3 class="text-base font-bold mb-3">Фильтры</h3>

    <form action="{{ route('products.index') }}" method="GET" id="filter-form">

        {{-- Вставляем внутрь <form id="filter-form"> --}}
        @if(request()->has('brand'))
        @foreach((array)request('brand') as $brandValue)
        <input type="hidden" name="brand[]" value="{{ $brandValue }}">
        @endforeach
        @endif

        <div class="mb-2.5">
            <label class="block font-bold mb-1.5 text-[11px]">Сортировка</label>
            <select name="sort" class="w-full p-1.5 border border-gray-300 rounded text-[11px]" onchange="this.form.submit()">
                <option value="popular" @selected(request('sort')=='popular' || !request('sort'))>Сначала популярные</option>
                <option value="cheap" @selected(request('sort')=='cheap' )>Дешевле</option>
                <option value="expensive" @selected(request('sort')=='expensive' )>Дороже</option>
            </select>
        </div>

        <div class="mb-2.5">
            <label class="block font-bold mb-1.5 text-[11px]">Цена</label>
            <div class="flex gap-1.5">
                <input type="number"
                    name="attrs_min[Цена]"
                    placeholder="От"
                    value="{{ request('attrs_min.Цена') }}"
                    class="w-1/2 p-1.5 border border-gray-300 rounded text-[11px]">
                <input type="number"
                    name="attrs_max[Цена]"
                    placeholder="До"
                    value="{{ request('attrs_max.Цена') }}"
                    class="w-1/2 p-1.5 border border-gray-300 rounded text-[11px]">
            </div>
        </div>

        @foreach($sidebarSpecs as $attribute)

        @if($attribute->name === 'Цена')
        @continue
        @endif

        @if($attribute->filter_type === 'brand_list')
        @php
        $sortedBrands = $brands->sortBy('name');
        @endphp

        <div class="mb-2.5">
            <label class="block font-bold mb-2 text-[11px] text-center border-b border-gray-200 pb-1">{{ $attribute->name }}</label>

            <div class="grid grid-cols-2 gap-1.5">
                @foreach($sortedBrands->take(4) as $brand)
                <label class="flex items-center gap-1 text-[11px] cursor-pointer bg-gray-50 px-2 py-1 rounded-full border border-gray-300">
                    <input type="checkbox"
                        name="brand[]"
                        value="{{ $brand->slug }}"
                        class="accent-green-600 w-3.5 h-3.5 m-0"
                        {{ (is_array(request('brand')) && in_array($brand->slug, request('brand', []))) ? 'checked' : '' }}>
                    <span>{{ $brand->name }}</span>
                </label>
                @endforeach
            </div>

            @if($sortedBrands->count() > 4)
            <div x-data="{ open: false }" class="mt-1.5 relative">
                <button type="button"
                    @click="open = !open"
                    class="text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded px-3 py-1.5 text-[11px] font-medium transition-all duration-200 w-full text-center shadow-sm">

                    <span x-show="!open" class="flex items-center justify-center gap-1">
                        <span>+ еще {{ $sortedBrands->count() - 4 }}</span>
                    </span>

                    <span x-show="open" class="flex items-center justify-center gap-1">
                        <span>− Скрыть</span>
                    </span>
                </button>

                <div x-show="open"
                    @click.away="open = false"
                    x-cloak
                    class="absolute top-full left-0 z-50 mt-1 p-2 bg-white border border-gray-300 rounded-lg max-h-36 overflow-y-auto w-full shadow-md">
                    @foreach($sortedBrands->slice(4) as $brand)
                    <label class="block py-0.5 text-[11px] cursor-pointer hover:bg-gray-50 px-1 rounded">
                        <input type="checkbox" name="brand[]" value="{{ $brand->slug }}" class="mr-1.5 accent-green-600"
                            {{ (is_array(request('brand')) && in_array($brand->slug, request('brand', []))) ? 'checked' : '' }}>
                        {{ $brand->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @elseif($attribute->filter_type === 'number')
        <div class="mb-2.5">
            <label class="block font-bold mb-1.5 text-[11px]">{{ $attribute->name }}</label>
            <div class="flex gap-1.5">
                <input type="number"
                    name="attrs_min[{{ $attribute->name }}]"
                    placeholder="От"
                    value="{{ request("attrs_min.{$attribute->name}") }}"
                    class="w-1/2 p-1.5 border border-gray-300 rounded text-[11px]">
                <input type="number"
                    name="attrs_max[{{ $attribute->name }}]"
                    placeholder="До"
                    value="{{ request("attrs_max.{$attribute->name}") }}"
                    class="w-1/2 p-1.5 border border-gray-300 rounded text-[11px]">
            </div>
        </div>

        {{-- Для boolean (т.е. для тех, у коготорый есть дети) --}}
        @elseif($attribute->filter_type === 'boolean')
        @php
        $children = $attribute->children;
        $hasChildren = $children->isNotEmpty();
        $items = $hasChildren ? $children : collect([$attribute]);
        @endphp

        <div class="mb-2.5">
            <label class="block font-bold mb-2 text-[11px] text-center border-b border-gray-200 pb-1">{{ $attribute->name }}</label>

            <div class="grid grid-cols-2 gap-1.5">
                @foreach($items->take(4) as $item)
                <label class="flex items-center gap-1 text-[11px] cursor-pointer bg-gray-50 px-2 py-1 rounded-full border border-gray-300">
                    <input type="checkbox"
                        name="attrs[{{ $hasChildren ? $attribute->name : $item->name }}][]"
                        value="{{ $hasChildren ? $item->name : 'есть' }}"
                        class="accent-green-600 w-3.5 h-3.5 m-0"
                        {{ $hasChildren 
                            ? (is_array(request("attrs." . $attribute->name)) && in_array($item->name, request("attrs." . $attribute->name, [])) ? 'checked' : '')
                            : (is_array(request("attrs." . $item->name)) ? 'checked' : '')
                        }}>
                    <span class="text-[11px]">{{ $item->name }}</span>
                </label>
                @endforeach
            </div>

            @if($items->count() > 4)
            <div x-data="{ open: false }" class="mt-1.5 relative">
                <button type="button"
                    @click="open = !open"
                    class="text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded px-3 py-1.5 text-[11px] font-medium transition-all duration-200 w-full text-center shadow-sm">

                    <span x-show="!open" class="flex items-center justify-center gap-1">
                        <span>+ еще {{ $items->count() - 4 }}</span>
                    </span>

                    <span x-show="open" class="flex items-center justify-center gap-1">
                        <span>− Скрыть</span>
                    </span>
                </button>

                <div x-show="open"
                    @click.away="open = false"
                    x-cloak
                    class="absolute top-full left-0 z-50 mt-1 p-2 bg-white border border-gray-300 rounded-lg max-h-36 overflow-y-auto w-full shadow-md">
                    @foreach($items->slice(4) as $item)
                    <label class="block py-0.5 text-[11px] cursor-pointer hover:bg-gray-50 px-1 rounded">
                        <input type="checkbox"
                            name="attrs[{{ $hasChildren ? $attribute->name : $item->name }}][]"
                            value="{{ $hasChildren ? $item->name : 'есть' }}"
                            class="mr-1.5 accent-green-600"
                            {{ $hasChildren 
                                ? (is_array(request("attrs." . $attribute->name)) && in_array($item->name, request("attrs." . $attribute->name, [])) ? 'checked' : '')
                                : (is_array(request("attrs." . $item->name)) ? 'checked' : '')
                            }}>
                        {{ $item->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @elseif($attribute->filter_type === 'range')
        @php
        $values = $attribute->getUniqueValues();
        @endphp

        <div class="mb-2.5">
            <label class="block font-bold mb-1.5 text-[11px]">{{ $attribute->name }}</label>
            <div class="flex gap-1.5">
                <select name="attrs_min[{{ $attribute->name }}]" class="w-1/2 p-1.5 border border-gray-300 rounded text-[11px]">
                    <option value="">От</option>
                    @foreach($values as $value)
                    <option value="{{ $value }}" @selected(request("attrs_min.{$attribute->name}") == $value)>{{ $value }}</option>
                    @endforeach
                </select>
                <select name="attrs_max[{{ $attribute->name }}]" class="w-1/2 p-1.5 border border-gray-300 rounded text-[11px]">
                    <option value="">До</option>
                    @foreach($values as $value)
                    <option value="{{ $value }}" @selected(request("attrs_max.{$attribute->name}") == $value)>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @else
        @php
        $items = $attribute->getUniqueValues();
        @endphp

        <div class="mb-2.5">
            <label class="block font-bold mb-2 text-[11px] text-center border-b border-gray-200 pb-1">{{ $attribute->name }}</label>

            @if($items->isNotEmpty())
            <div class="grid grid-cols-2 gap-1.5">
                @foreach($items->take(4) as $value)
                <label class="flex items-center gap-1 text-[11px] cursor-pointer bg-gray-50 px-2 py-1 rounded-full border border-gray-300">
                    <input type="checkbox"
                        name="attrs[{{ $attribute->name }}][]"
                        value="{{ $value }}"
                        class="accent-green-600 w-3.5 h-3.5 m-0"
                        {{ (is_array(request("attrs.{$attribute->name}")) && in_array($value, request("attrs.{$attribute->name}", []))) ? 'checked' : '' }}>
                    <span>{{ $value }}</span>
                </label>
                @endforeach
            </div>

            @if($items->count() > 4)
            <div x-data="{ open: false }" class="mt-1.5 relative">
                <button type="button"
                    @click="open = !open"
                    class="text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded px-3 py-1.5 text-[11px] font-medium transition-all duration-200 w-full text-center shadow-sm">

                    <span x-show="!open" class="flex items-center justify-center gap-1">
                        <span>+ еще {{ $items->count() - 4 }}</span>
                    </span>

                    <span x-show="open" class="flex items-center justify-center gap-1">
                        <span>− Скрыть</span>
                    </span>
                </button>

                <div x-show="open"
                    @click.away="open = false"
                    x-cloak
                    class="absolute top-full left-0 z-50 mt-1 p-2 bg-white border border-gray-300 rounded-lg max-h-99 overflow-y-auto w-full shadow-md">
                    @foreach($items->slice(4) as $value)
                    <label class="block py-0.5 text-[11px] cursor-pointer hover:bg-gray-50 px-1 rounded">
                        <input type="checkbox"
                            name="attrs[{{ $attribute->name }}][]"
                            value="{{ $value }}"
                            class="mr-1.5 accent-green-600"
                            {{ (is_array(request("attrs.{$attribute->name}")) && in_array($value, request("attrs.{$attribute->name}", []))) ? 'checked' : '' }}>
                        {{ $value }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
            @else
            <p class="text-gray-400 text-center p-2">Нет доступных значений</p>
            @endif
        </div>
        @endif
        @endforeach

        <div class="flex gap-2.5 sticky bottom-5 mt-5">
            <a href="{{ route('products.index', ['sort' => request('sort', 'popular')]) }}"
                class="flex-1 bg-gray-100 text-gray-600 p-2.5 border border-gray-300 rounded-lg font-bold cursor-pointer text-center no-underline text-sm hover:bg-gray-200 transition-colors">
                Сбросить все
            </a>
            <button type="submit" class="flex-[2] bg-green-600 text-white p-2.5 border-none rounded-lg font-bold cursor-pointer text-sm shadow-md hover:bg-green-700 transition-colors">
                Применить
            </button>
        </div>

    </form>
</aside>