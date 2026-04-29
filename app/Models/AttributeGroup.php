<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
