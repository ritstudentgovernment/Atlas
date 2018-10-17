<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    protected $appends = ['type', 'classification'];
    protected $hidden = ['type_id', 'id', 'status', 'created_at', 'updated_at'];
    protected $fillable = ['title', 'quietLevel', 'notes', 'type_id', 'user_id', 'lat', 'lng'];

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function getClassificationAttribute()
    {
        $classifications = [

            0 => 'review',
            1 => 'public',
            2 => 'designated',

        ];

        return array_key_exists($this->status, $classifications) ? $classifications[$this->status] : 'review';
    }

    public function getTypeAttribute()
    {
        $type = $this->type()->getResults();

        return $type;
    }

    public function type()
    {
        return $this->belongsTo(SpotType::class);
    }
}
