<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{

    protected $hidden  = ['type_id', 'id', 'created_at', 'updated_at'];
    protected $appends = ['type'];

    public function getTypeAttribute() {

        $type = $this->type()->getResults();

        return $type;

    }

    public function type(){

        return $this->belongsTo(SpotType::class);

    }

}
