<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DescriptorSpot extends Model
{
    protected $hidden = ['updated_at', 'created_at', 'spot_id', 'descriptor_id', 'descriptor'];
    protected $appends = ['name', 'icon', 'defaultValue'];

    public function getNameAttribute()
    {
        return $this->descriptor->name;
    }

    public function getIconAttribute()
    {
        return $this->descriptor->icon;
    }
    public function getDefaultValueAttribute()
    {
        return $this->descriptor->default_value;
    }

    public function spot()
    {
        return $this->belongsTo(Spot::class);
    }

    public function descriptor()
    {
        return $this->belongsTo(Descriptors::class, 'descriptor_id');
    }
}
