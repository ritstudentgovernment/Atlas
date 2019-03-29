<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $hidden = ['id', 'created_at', 'updated_at', 'active'];
    protected $fillable = ['name', 'description', 'icon', 'crowdsource', 'active'];
    protected $appends = ['numSpots'];

    public function types()
    {
        return $this->hasMany(Type::class)->where('deleted', false);
    }

    public function descriptors()
    {
        return $this->belongsToMany(Descriptors::class, 'category_descriptors', 'category_id', 'descriptor_id');
    }

    public function classifications()
    {
        return $this->hasMany(Classification::class);
    }

    public function getNumSpotsAttribute()
    {
        /*$numSpots = 0;
        $this->types->each(function (Type $type) use ($numSpots) {
            $numSpots += $type->spots->count();
        });
        return $numSpots;*/
    }

    public function getRouteKeyName()
    {
        return 'name';
    }
}
