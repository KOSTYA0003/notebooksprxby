<div>
    <button wire:click="add"
        wire:loading.attr="disabled"
        class="w-full py-2.5 px-5 bg-[#e52e6b] text-white border-none cursor-pointer rounded hover:bg-[#c41e56] transition-colors disabled:opacity-70 disabled:cursor-not-allowed">

        <span wire:loading.remove wire:target="add">В корзину</span>

        <span wire:loading wire:target="add">Добавляю...</span>
    </button>

    @if(isset(session('cart')[$productId]))
    <div class="text-xs text-[#e52e6b] mt-1 text-center">
        В корзине: {{ session('cart')[$productId] }} шт.
    </div>
    @endif
</div>