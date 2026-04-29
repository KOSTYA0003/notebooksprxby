<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageCache extends Model
{
    protected $fillable = ['url', 'html'];
}
