<?php

namespace App\Livewire;

use Livewire\Component;

class CartCounter extends Component
{
    protected $listeners = ['cart-updated' => '$refresh'];

    public function getCountProperty()
    {
        return array_sum(session()->get('cart', []));
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
