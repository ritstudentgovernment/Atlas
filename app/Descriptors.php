<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Descriptors extends Model
{
    public function spots()
    {
        return $this->hasManyThrough(Spot::class, DescriptorSpot::class);
    }

    public function categories()
    {
        return $this->hasManyThrough(Category::class, CategoryDescriptor::class);
    }
}
