<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = ['name', 'category_id'];
    protected $hidden = ['category_id', 'created_at', 'updated_at'];
    protected $appends = ['category'];

    public function getCategoryAttribute()
    {
        return $this->category()->getResults();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function spots()
    {
        return $this->hasMany(Spot::class);
    }
}
