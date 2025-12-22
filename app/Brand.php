<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function country_head_user(){
        return $this->belongsTo('App\User','country_head_id','id');
    }

    public function categories()
    {
        return $this->hasMany('App\Category');
    }
}
