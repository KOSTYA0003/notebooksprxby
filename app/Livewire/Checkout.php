<?php

namespace App\Livewire;

use Livewire\Component;

class Checkout extends Component
{
    public $name = '';

    public $phone = '';

    public $orderPlaced = false;

    protected $rules = [
        'name' => 'required|min:2',
        'phone' => 'required|min:10',
    ];

    public function placeOrder()
    {
        $this->validate();

        $this->orderPlaced = true;

        session()->forget('cart');
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}
