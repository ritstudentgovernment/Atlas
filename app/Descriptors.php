<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Descriptors extends Model
{
    protected $fillable = ['name', 'value_type', 'default_value', 'allowed_values', 'icon'];

    public function spots()
    {
        return $this->belongsToMany(Spot::class, 'descriptors_spot', 'descriptor_id', 'spot_id')->withPivot('value');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_descriptors', 'descriptor_id', 'category_id');
    }
}
