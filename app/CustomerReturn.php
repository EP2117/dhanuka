<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerReturn extends Model
{
    public function sales()
    {
        return $this->belongsToMany('App\Sale', 'return_invoices','customer_return_id','sale_id')->withPivot('return_amount');
    }

    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }
}
