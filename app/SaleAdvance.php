<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleAdvance extends Model
{
    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency', 'currency_id', 'id');
    }

    public function sales()
    {
        return $this->belongsToMany('App\Sale', 'sale_advance_links', 'advance_id', 'sale_id')->withPivot('amount_fx');
    }
}
