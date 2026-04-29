<div>
    @if(count($products) > 0)
    <div class="divide-y divide-gray-200">
        @foreach($products as $product)
        <div class="flex items-center gap-5 p-4 hover:bg-gray-50 transition-colors">
            <div class="w-[100px]">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="max-w-full max-h-20 object-contain">
            </div>

            <div class="flex-1">
                <a href="{{ route('products.show', $product->slug) }}" class="no-underline text-gray-800 font-bold hover:text-gray-600">
                    {{ $product->name }}
                </a>
                <div class="text-gray-500 text-sm mt-1">
                    {{ number_format($product->price, 2) }} BYN
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <button wire:click="decrease({{ $product->id }})" class="px-2.5 py-1 border border-gray-300 bg-gray-50 cursor-pointer hover:bg-gray-200 transition-colors">−</button>
                <span class="min-w-[30px] text-center">{{ $product->quantity }}</span>
                <button wire:click="increase({{ $product->id }})" class="px-2.5 py-1 border border-gray-300 bg-gray-50 cursor-pointer hover:bg-gray-200 transition-colors">+</button>
            </div>

            <div class="min-w-[120px] text-right">
                <div class="font-bold">{{ number_format($product->subtotal, 2) }} BYN</div>
                <button wire:click="remove({{ $product->id }})" class="bg-transparent border-none text-red-500 cursor-pointer text-sm mt-1 hover:text-red-700 transition-colors">
                    ✕ Удалить
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8 p-5 bg-gray-50 rounded-lg">
        <div class="flex justify-between items-center mb-5">
            <span class="text-lg font-bold">Итого:</span>
            <span class="text-2xl font-bold text-green-600">{{ number_format($total, 2) }} BYN</span>
        </div>

        <div class="flex gap-4">
            <button wire:click="clearCart" class="flex-1 py-3 bg-gray-600 text-white border-none rounded cursor-pointer hover:bg-gray-700 transition-colors">
                Очистить корзину
            </button>
            <a href="{{ route('checkout') }}" class="flex-[2] py-3 bg-green-600 text-white no-underline rounded text-center hover:bg-green-700 transition-colors">
                Перейти к оформлению
            </a>
        </div>
    </div>

    @else
    <div class="text-center py-16 px-5">
        <div class="text-5xl mb-5">🛒</div>
        <h3 class="text-gray-500 mb-5">Ваша корзина пуста</h3>
        <a href="{{ route('products.index') }}" class="inline-block py-3 px-8 bg-green-600 text-white no-underline rounded hover:bg-green-700 transition-colors">
            Перейти к покупкам
        </a>
    </div>
    @endif
</div>