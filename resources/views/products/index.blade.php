<x-app-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 mb-8 px-2">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Список ноутбуков
        </h1>
    </div>

    <div class="flex gap-8 items-start">
        <div class="flex-1">
            <div class="grid grid-cols-5 gap-5">
                @foreach($products as $product)
                <div class="box-border flex flex-col justify-between h-full p-1 border border-gray-300 rounded-lg">
                    <a href="{{ route('products.show', $product->slug) }}#reviews" class="flex items-center gap-1 text-sm no-underline text-inherit mb-2">
                        <span class="text-yellow-600">★</span>
                        <strong class="font-bold">{{ $product->rating }}</strong>
                        <span class="text-gray-400">({{ $product->reviews_count }})</span>
                    </a>

                    <a href="{{ route('products.show', $product->slug) }}" class="block no-underline text-inherit">
                        <div class="h-48 flex items-center justify-center overflow-hidden mb-3">
                            <img src="{{ $product->image }}" class="max-h-full max-w-full object-contain">
                        </div>
                    </a>

                    <div class="flex flex-col flex-1">
                        <p class="mb-1 text-sm">Цена: <strong class="font-bold text-base">{{ number_format($product->price, 2) }} BYN</strong></p>

                        <a href="{{ route('products.show', $product->slug) }}" class="block no-underline text-inherit mb-3">
                            <h3 class="line-clamp-2 leading-tight m-0 p-0 text-sm font-medium">{{ $product->name }}</h3>
                        </a>

                        <div class="mt-auto">
                            <livewire:add-to-cart :product-id="$product->id" :key="'add-to-cart-'.$product->id" />
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-5">
                {{ $products->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        <x-filters-sidebar />
    </div>
</x-app-layout>