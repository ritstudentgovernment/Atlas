<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpotCategory extends Model
{
    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['name', 'description', 'colorCode'];

    public function types()
    {
        return $this->hasMany(SpotType::class);
    }
}
