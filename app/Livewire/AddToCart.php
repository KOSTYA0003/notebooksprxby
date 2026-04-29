<?php

namespace App\Livewire;

use Livewire\Component;

class AddToCart extends Component
{
    public $productId;

    public function add()
    {

        $cart = session()->get('cart', []);

        $cart[$this->productId] = ($cart[$this->productId] ?? 0) + 1;

        session()->put('cart', $cart);

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
