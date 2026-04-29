<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'filter_type',
        'pos_photo',
        'pos_sidebar',
        'pos_modal',
        'attribute_group_id',
    ];

    public function getUniqueValues()
    {
        return \DB::table('attribute_product')
            ->where('attribute_id', $this->id)
            ->whereNotNull('value')
            ->distinct()
            ->pluck('value')
            ->sort(SORT_NATURAL | SORT_FLAG_CASE);
    }

    public function attribute_group()
    {
        return $this->belongsTo(AttributeGroup::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_product')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function scopeForLocation($query, string $location)
    {
        $column = 'pos_'.$location;

        return $query
            ->where($column, '>', 0)
            ->orderBy($column, 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(Attribute::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Attribute::class, 'parent_id');
    }
}
