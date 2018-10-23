<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $hidden = ['id', 'category_id', 'created_at', 'updated_at'];
    protected $appends = ['category'];

    public function getCategoryAttribute()
    {
        return $this->category()->getResults();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
