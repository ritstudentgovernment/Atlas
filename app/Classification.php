<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $fillable = ['name', 'type', 'color', 'category_id', 'view_permission', 'create_permission'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function spots()
    {
        return $this->hasMany(Spot::class);
    }

    /**
     * All ForUser methods get all instances of a model that a person may use.
     *
     * @param User/null the user to get Classifications for
     *
     * @return Collection
     */
    public static function forUser(User $user = null)
    {
        return self::all()->filter(function (Classification $classification) use ($user) {
            $requiredCreatePermission = $classification->create_permission;
            if ($user == null || $requiredCreatePermission == null) {
                return $requiredCreatePermission ? false : true;
            }

            return $user->can($requiredCreatePermission) && !$classification->deleted;
        })->values();
    }

    public static function makeDefaultsForCategory(Category $category)
    {
        $defaults = collect([
            [
                'name'              => 'Public',
                'type'              => 'public',
                'color'             => '8d5632',
            ],
            [
                'name'              => 'Designated',
                'type'              => 'designated',
                'color'             => 'ff7700',
                'create_permission' => 'make designated spots',
            ],
            [
                'name'              => 'Under Review',
                'type'              => 'under review',
                'color'             => 'cb0020',
                'view_permission'   => 'view unapproved spots',
                'create_permission' => 'create under review spots',
            ],
        ]);
        $defaults->each(function ($data) use ($category) {
            $data = array_merge($data, [
               'category_id' => $category->id,
            ]);
            Classification::create($data);
        });
    }
}
