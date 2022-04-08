<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleAdvance extends Model
{
    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }
}
