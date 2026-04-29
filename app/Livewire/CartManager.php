<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class CartManager extends Component
{
    public $cart = [];

    public $products = [];

    public $total = 0;

    protected $listeners = ['cart-updated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
        $this->products = [];
        $this->total = 0;

        if (! empty($this->cart)) {
            $this->products = Product::whereIn('id', array_keys($this->cart))->get();

            foreach ($this->products as $product) {
                $product->quantity = $this->cart[$product->id];
                $product->subtotal = $product->price * $product->quantity;
                $this->total += $product->subtotal;
            }
        }
    }

    public function increase($productId)
    {
        $cart = session()->get('cart', []);
        $cart[$productId] = ($cart[$productId] ?? 0) + 1;
        session()->put('cart', $cart);

        $this->dispatch('cart-updated');
        $this->loadCart();
    }

    public function decrease($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($cart[$productId] > 1) {
                $cart[$productId]--;
            } else {
                unset($cart[$productId]);
            }

            session()->put('cart', $cart);
            $this->dispatch('cart-updated');
            $this->loadCart();
        }
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);

        $this->dispatch('cart-updated');
        $this->loadCart();
    }

    public function clearCart()
    {
        session()->forget('cart');
        $this->dispatch('cart-updated');
        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.cart-manager');
    }
}
