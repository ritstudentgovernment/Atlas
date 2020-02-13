<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Spot extends Model
{
    protected $appends = ['type', 'classification', 'descriptors', 'authored'];
    protected $hidden = ['user_id', 'author', 'created_at', 'updated_at', 'type_id'];
    protected $fillable = ['notes', 'classification_id', 'approved_classification_id', 'approved', 'user_id', 'type_id', 'lat', 'lng', 'image_url'];

    public function getAuthoredAttribute()
    {
        $spotAuthor = $this->author()->first();
        $requestUser = request()->user() ?: (auth('api')->user() ?: false);
        // Check for edge cases where a user isn't logged in or the spot author is undefined.
        if (!($spotAuthor && $requestUser && $requestUser instanceof User)) {
            return false;
        }

        return $requestUser->id == $spotAuthor->id;
    }

    public function getCategoryAttribute()
    {
        return $this->category();
    }

    public function getClassificationAttribute()
    {
        return $this->classification()->first();
    }

    public function getDescriptorsAttribute()
    {
        return $this->descriptors()->get();
    }

    public function getTypeAttribute()
    {
        return $this->type()->first();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->type->category;
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function approvedClassification()
    {
        return $this->belongsTo(Classification::class, 'approved_classification_id');
    }

    public function descriptors()
    {
        return $this->belongsToMany(Descriptors::class, 'descriptors_spot', 'spot_id', 'descriptor_id', 'id', 'id')->withPivot('value');
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function approve()
    {
        $this->approved = true;
        if ($this->approvedClassification) {
            $this->classification_id = $this->approvedClassification->id;
        }
        $this->save();

        return $this;
    }

    public function getNameAttribute()
    {
        if ($this->approvedClassification) {
            return $this->approvedClassification->name.' '.$this->type->name.' '.$this->category->name;
        }

        return $this->classification->name.' '.$this->type->name.' '.$this->category->name;
    }
}
