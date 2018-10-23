<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    protected $appends = ['type', 'classification', 'descriptors'];
    protected $hidden = ['user_id', 'created_at', 'updated_at', 'type_id'];
    protected $fillable = [];

    public function getTypeAttribute()
    {
        return $this->type()->first();
    }

    public function getClassificationAttribute()
    {
        return $this->classification()->get();
    }

    public function getDescriptorsAttribute()
    {
        return $this->descriptors()->get();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function descriptors()
    {
        return $this->hasManyThrough(DescriptorSpot::class, Descriptors::class, 'id', 'descriptor_id');
    }
}
