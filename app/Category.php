<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //advanced approach for the categories index page display
    public function categories()
    {
        return $this->hasMany('App\Category','parent_id');
    }
}
