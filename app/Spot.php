<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Spot extends Model
{
    protected $appends = ['type', 'classification', 'descriptors', 'authored'];
    protected $hidden = ['user_id', 'created_at', 'updated_at', 'type_id'];
    protected $fillable = ['title', 'notes', 'building', 'floor', 'approved', 'user_id', 'type_id', 'lat', 'lng'];

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

    public function getAuthoredAttribute()
    {
        $spotAuthor = $this->author()->first();
        $requestUser = request()->user() ?: (auth('api')->user() ?: false);
        if (!($spotAuthor && $requestUser && $requestUser instanceof User)) {
            return false;
        }

        return $requestUser->id == $spotAuthor->id;
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
