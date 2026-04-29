<x-app-layout>
    <div class="flex flex-row justify-between items-center mt-6 mb-8 px-2">
        <nav class="flex items-center gap-2 text-sm">
            <a href="/" class="text-gray-900 font-bold hover:text-[#e52e6b] transition-colors no-underline">
                Главная
            </a>
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="/?brand={{ $product->brand->slug }}" class="text-gray-500 hover:text-gray-900 transition-colors no-underline">
                {{ $product->brand->name }}
            </a>
        </nav>
    </div>

    <div class="flex gap-10 mt-5">
        <div class="w-1/2">
            <div class="mb-2.5 bg-white">
                <img id="current-image" src="{{ $product->image }}" class="w-full h-[500px] object-contain border border-gray-300 block">
            </div>
            <div class="flex gap-2.5 overflow-x-auto pb-1">
                @foreach($product->productImages as $img)
                <img src="{{ $img->path }}" onclick="document.getElementById('current-image').src=this.src" class="w-20 h-20 object-contain flex-shrink-0 cursor-pointer border border-gray-300">
                @endforeach
            </div>
        </div>

        <div class="w-1/2">
            <h1 class="text-2xl mt-0">{{ $product->name }}</h1>
            <p class="my-2.5">Бренд: <strong>{{ $product->brand->name }}</strong></p>
            <div class="text-2xl text-orange-600 my-5">{{ number_format($product->price, 2) }} BYN</div>
            <div class="text-center max-w-[450px]">
                <livewire:add-to-cart :product-id="$product->id" :key="'add-to-cart-'.$product->id" />
            </div>

            <div class="mt-8 pl-5 max-w-[450px]">
                <table class="w-full mb-2.5 border-collapse">
                    @foreach($photoSpecs as $attribute)
                    <tr class="border-b border-gray-300">
                        <td class="text-gray-500 text-sm py-1.5 w-3/5 align-top leading-tight">{{ $attribute->name }}</td>
                        <td class="text-gray-800 font-medium text-sm py-1.5 align-top leading-tight">{{ $attribute->pivot->value }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <div class="mt-12">
        <h2 class="text-2xl font-bold">Характеристики</h2>
        <div class="columns-2 gap-10">
            @foreach($product->attributes->where('is_visible', true)->groupBy('attribute_group_id') as $groupId => $groupAttributes)
            @php $group = $groupAttributes->first()->attribute_group; @endphp
            <div class="break-inside-avoid mb-5">
                <h3 class="bg-gray-100 py-2 px-2.5 border-b-2 border-gray-300 text-lg font-bold m-0 mb-2.5">
                    {{ $group->name ?? 'Без группы' }}
                </h3>
                <table class="w-full border-collapse">
                    @foreach($groupAttributes as $attribute)
                    <tr class="border-b border-gray-200">
                        <td class="py-2 px-1.5 text-gray-500 w-1/2 text-sm">{{ $attribute->name }}</td>
                        <td class="py-2 px-1.5 text-sm">{{ $attribute->pivot->value }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-12">
        <h2 class="text-2xl font-bold">Отзывы ({{ $product->reviews->count() }})</h2>

        <div class="my-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-bold mb-4">Оставить отзыв</h3>

            <form action="{{ route('reviews.store', $product) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block font-bold mb-2 text-sm">Ваше имя</label>
                    <input type="text" name="user_name" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-4">
                    <label class="block font-bold mb-2 text-sm">Оценка</label>
                    <select name="rating" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="5">5 ★</option>
                        <option value="4">4 ★</option>
                        <option value="3">3 ★</option>
                        <option value="2">2 ★</option>
                        <option value="1">1 ★</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block font-bold mb-2 text-sm">Достоинства</label>
                    <textarea name="pros" rows="2" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block font-bold mb-2 text-sm">Недостатки</label>
                    <textarea name="cons" rows="2" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block font-bold mb-2 text-sm">Комментарий</label>
                    <textarea name="comment" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition-colors">
                    Отправить отзыв
                </button>
            </form>
        </div>

        <div class="mt-12">
            <h2 class="text-2xl font-bold">Отзывы ({{ $product->reviews->count() }})</h2>
            @forelse($product->reviews as $review)
            <div class="border-b border-gray-200 py-5">
                <div class="flex justify-between mb-3">
                    <div>
                        <div class="font-bold text-base mb-1">{{ $review->user_name ?? 'Аноним' }}</div>
                        <div class="text-yellow-500 text-base">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                    </div>
                    <div class="text-gray-400 text-sm whitespace-nowrap">
                        {{ $review->publish_date ? \Carbon\Carbon::parse($review->publish_date)->translatedFormat('d M, Y') : '' }}
                    </div>
                </div>

                @if($review->pros)
                <div class="mb-3">
                    <div class="font-bold mb-1 text-sm">Достоинства</div>
                    <p class="m-0 leading-normal text-gray-700 text-sm">{{ $review->pros }}</p>
                </div>
                @endif

                @if($review->cons)
                <div class="mb-3">
                    <div class="font-bold mb-1 text-sm">Недостатки</div>
                    <p class="m-0 leading-normal text-gray-700 text-sm">{{ $review->cons }}</p>
                </div>
                @endif

                @if($review->comment)
                <div class="mb-3">
                    <div class="font-bold mb-1 text-sm">Резюме</div>
                    <p class="m-0 leading-normal text-gray-700 text-sm">{{ $review->comment }}</p>
                </div>
                @endif

                @if($review->reply_text)
                <div class="mt-4 ml-8 p-3 bg-gray-50 rounded-lg relative">
                    <div class="absolute -left-2.5 top-4 w-0 h-0 border-t-[10px] border-t-transparent border-b-[10px] border-b-transparent border-r-[10px] border-r-gray-50"></div>
                    <div class="flex items-center mb-1.5">
                        <span class="font-bold text-blue-600 text-sm">Сотрудник 21vek.by</span>
                    </div>
                    <p class="m-0 text-gray-600 text-sm">{{ $review->reply_text }}</p>
                </div>
                @endif
            </div>
            @empty
            <p class="text-gray-400 text-center py-10">Пока нет отзывов. Будьте первым!</p>
            @endforelse
        </div>
</x-app-layout>