<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Descriptors extends Model
{
    protected $fillable = ['name', 'default_value', 'allowed_values', 'icon'];

    public function spots()
    {
        return $this->hasManyThrough(Spot::class, DescriptorSpot::class, 'id', 'id', 'id','descriptor_id');
    }

    public function categories()
    {
        return $this->hasManyThrough(Category::class, CategoryDescriptor::class);
    }
}
