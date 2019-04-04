<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{

    protected $fillable = ['name', 'color', 'category_id', 'view_permission', 'create_permission'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function spots()
    {
        return $this->hasMany(Spot::class);
    }
}
