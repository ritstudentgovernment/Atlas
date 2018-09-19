<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryDescriptor extends Model
{

    public function category(){

        return $this->belongsTo(Category::class);

    }

    public function descriptor(){

        return $this->belongsTo(Descriptors::class);

    }

}
