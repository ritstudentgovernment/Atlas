<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DescriptorSpot extends Model
{

    public function spot(){

        return $this->belongsTo(Spot::class);

    }

    public function descriptor(){

        return $this->belongsTo(Descriptors::class,'descriptor_id');

    }

}
