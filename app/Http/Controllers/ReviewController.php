<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'user_name' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'pros' => 'nullable|string',
            'cons' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);

        $review = new Review([
            'user_name' => $request->user_name,
            'rating' => $request->rating,
            'pros' => $request->pros,
            'cons' => $request->cons,
            'comment' => $request->comment,
            'product_id' => $product->id,
            'publish_date' => now(),
        ]);

        $review->save();

        return back()->with('success', 'Отзыв добавлен');
    }
}
