<div>
    @if(!$orderPlaced)
    <div class="max-w-[500px] mx-auto p-5">
        <h2 class="mb-8 text-2xl font-bold">Оформление заказа</h2>

        <form wire:submit="placeOrder">
            <div class="mb-5">
                <label class="block mb-2 font-bold">Имя</label>
                <input type="text"
                    wire:model="name"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-8">
                <label class="block mb-2 font-bold">Телефон</label>
                <input type="tel"
                    wire:model="phone"
                    placeholder="+375 (29) 123-45-67"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full py-4 bg-green-600 text-white border-none rounded-lg text-base font-bold cursor-pointer hover:bg-green-700 transition-colors">
                Заказать
            </button>
        </form>
    </div>
    @else
    <div class="max-w-[500px] mx-auto py-10 px-5 text-center">
        <div class="text-6xl mb-5">✅</div>
        <h2 class="mb-4 text-2xl font-bold">Спасибо за заказ!</h2>
        <p class="text-gray-500 mb-8">
            Ваш заказ успешно оформлен. Но он к вам не приедет, потому что это учебный проект 😊
        </p>
        <p class="text-gray-500 mb-8">
            Зато вы можете сделать настоящий заказ на сайте
            <a href="https://www.21vek.by/" target="_blank" class="text-blue-600 no-underline font-bold hover:underline">
                21vek.by
            </a>
        </p>
        <a href="{{ route('products.index') }}" class="inline-block py-3 px-8 bg-green-600 text-white no-underline rounded hover:bg-green-700 transition-colors">
            Вернуться к покупкам
        </a>
    </div>
    @endif
</div>