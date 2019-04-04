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

    public function spots()
    {
        return $this->hasManyThrough(Spot::class, Type::class);
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
        return $this->spots()->count();
    }

    public function getRouteKeyName()
    {
        return 'name';
    }
}
