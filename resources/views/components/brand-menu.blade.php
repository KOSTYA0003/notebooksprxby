<nav class="flex justify-between items-center gap-5 px-4 py-3  bg-gray-100">

    <ul class="list-none flex flex-wrap gap-1.5 m-0 p-0">
        @foreach($brands as $brand)
        <li>
            <a href="{{ route('products.index', array_merge(request()->except('page'), ['brand' => $brand->slug])) }}"
                class="no-underline text-gray-900 bg-gray-200 px-7 py-1 rounded-md text-xs font-medium whitespace-nowrap hover:bg-gray-300 hover:text-gray-900 transition-all duration-200 inline-block">
                {{ $brand->name }}
            </a>
        </li>
        @endforeach
    </ul>

    <div class="flex-shrink-0">
        <a href="{{ route('products.index', request()->except(['brand', 'page'])) }}"
            class="no-underline font-bold text-gray-700 px-4 py-1.5 border border-gray-300 rounded-md bg-white whitespace-nowrap shadow-sm hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 text-xs inline-block">
            ВСЕ
        </a>
    </div>
</nav>