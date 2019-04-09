<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
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

    public function getNumSpotsAttribute()
    {
        return $this->spots()->count();
    }

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function classifications()
    {
        return $this->hasMany(Classification::class);
    }

    public function designatedClassifications()
    {
        return $this->classifications()->where('type', 'designated')->get();
    }

    public function publicClassifications()
    {
        return $this->classifications()->where('type', 'public')->get();
    }

    public function underReviewClassification()
    {
        return $this->classifications()->where('type', 'under review')->get()->first();
    }

    /**
     * All ForUser methods get all instances of a model that a person may use.
     *
     * @param User/null the user to get categories for
     *
     * @return Collection
     */
    public static function forUser(User $user = null)
    {
        $categories = self::with(['classifications', 'descriptors', 'types']);
        if ($user && $user->can('make designated spots')) {
            return $categories->get();
        }

        return $categories->where('crowdsource', true)->get();
    }
}
