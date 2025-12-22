<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function customers()
    {
        return $this->hasMany('App\Customer');
    }
}
