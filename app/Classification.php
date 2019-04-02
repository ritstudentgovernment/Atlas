<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function spots()
    {
        return $this->hasMany(Spot::class);
    }
}
