<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Township extends Model
{
    public function state()
    {
        return $this->belongsTo('App\State')->select('id', 'state_name');
    }

    public function customers()
    {
        return $this->hasMany('App\Customer');
    }
}
