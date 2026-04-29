<!DOCTYPE html>
<html lang="ru">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <title>Учебный магазин ноутбуков</title>
</head>

<body class="font-sans max-w-[1380px] mx-auto pt-8 pb-8">

    <div class="bg-gray-50/50 rounded-lg p-6">
        <header class="text-center mb-8">
            <a href="{{ route('products.index') }}" class="block text-center no-underline">
                <h1 class="text-2xl font-bold">НОУТБУКИ</h1>
                <span class="text-gray-600">учебный интернет-магазин</span>
            </a>
        </header>

        @if(Route::is('products.index'))
        <x-brand-menu />
        @endif

        {{ $slot }}
    </div>

    <div class="fixed top-6 inset-x-0 z-50 pointer-events-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

            <div class="absolute right-4 sm:right-6 lg:right-8 pointer-events-auto">
                <a href="{{ route('cart.index') }}"
                    class="group flex items-center gap-3 px-5 py-2.5 bg-white border-2 border-gray-100 rounded-2xl shadow-xl hover:shadow-2xl hover:border-[#e52e6b]/30 transition-all duration-300 no-underline text-[#e52e6b]">

                    <div class="relative">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>

                        <div class="absolute -top-3 -right-3">
                            <livewire:cart-counter />
                        </div>
                    </div>

                    <span class="hidden md:block text-sm font-bold text-gray-700 group-hover:text-[#e52e6b] transition-colors">
                        Корзина
                    </span>
                </a>
            </div>

        </div>
    </div>

    {{-- Ранее здесь были кастомные CSS-стили, заменены на Tailwind утилиты --}}

</body>

</html>