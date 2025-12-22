<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    public function customers()
    {
        return $this->hasMany('App\Customer');
    }
}
