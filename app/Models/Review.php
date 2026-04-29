<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_name',
        'pros',
        'cons',
        'comment',
        'rating',
        'reply_text',
        'publish_date',
    ];

    protected $casts = [
        'publish_date' => 'date',
    ];
}
