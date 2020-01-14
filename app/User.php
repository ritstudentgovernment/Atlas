<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password'];
    protected $appends = ['numSpotsCreated', 'isAdmin', 'isReviewer'];
    protected $hidden = ['password', 'remember_token', 'spots'];

    public function spots()
    {
        return $this->hasMany(Spot::class);
    }

    public static function allUsers()
    {
        return self::select(['id', 'first_name', 'last_name', 'email']);
    }

    public static function staff()
    {
        $users = self::allUsers()->get();

        return $users->filter(function (self $user) {
            return $user->hasAnyRole(['admin', 'reviewer']);
        })->values()->all();
    }

    public function getNumSpotsCreatedAttribute()
    {
        return $this->spots->count();
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('admin');
    }

    public function getIsReviewerAttribute()
    {
        return $this->hasRole('reviewer');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
