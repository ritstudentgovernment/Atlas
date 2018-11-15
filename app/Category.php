<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $hidden = ['id', 'created_at', 'updated_at', 'crowdsource', 'active'];
    protected $fillable = ['name', 'description', 'icon', 'crowdsource', 'active'];

    public function types()
    {
        return $this->hasMany(Type::class);
    }

    public function descriptors()
    {
        return $this->hasManyThrough(Descriptors::class, CategoryDescriptor::class, 'id', 'id', 'id', 'descriptor_id');
    }
}
