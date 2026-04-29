<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by brand
        if ($request->filled('brand')) {
            $query->whereHas('brand', fn ($q) => $q->whereIn('slug', (array) $request->brand));
        }

        // FILTER BY PRICE (separately, since the price is in products.price)
        if ($request->has('attrs_min.Цена') || $request->has('attrs_max.Цена')) {
            $minPrice = $request->input('attrs_min.Цена');
            $maxPrice = $request->input('attrs_max.Цена');

            if ($minPrice !== null && $minPrice !== '') {
                $query->where('price', '>=', $minPrice);
            }

            if ($maxPrice !== null && $maxPrice !== '') {
                $query->where('price', '<=', $maxPrice);
            }
        }

        // Filtering by characteristics (all attributes except price)
        if ($request->has('attrs')) {
            foreach ($request->attrs as $attrName => $values) {
                if ($attrName === 'Цена') {
                    continue;
                }

                $values = (array) $values;

                if (in_array($attrName, ['Оплата частями', 'Срок доставки', 'Спецпредложения', 'Популярные параметры'])) {
                    $query->whereHas('attributes', function ($q) use ($values) {
                        $q->whereIn('attributes.name', $values)
                            ->where(function ($subQ) {
                                $subQ->where('attribute_product.value', '1')
                                    ->orWhere('attribute_product.value', 'есть');
                            });
                    });
                } else {
                    $query->whereHas('attributes', function ($q) use ($attrName, $values) {
                        $q->where('attributes.name', $attrName)
                            ->whereIn('attribute_product.value', $values);
                    });
                }
            }
        }

        switch ($request->sort) {
            case 'cheap':
                $query->orderBy('price', 'asc');
                break;
            case 'expensive':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('is_popular', 'desc')->orderBy('reviews_count', 'desc')->orderBy('rating', 'desc');
                break;
        }

        $products = $query->where('price', '>', 0)->paginate(60)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::with([
            'brand',
            'productImages',
            'attributes',
            'reviews' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $photoSpecs = $product->attributes()
            ->forLocation('photo')
            ->get();

        return view('products.show', compact('product', 'photoSpecs'));
    }
}
