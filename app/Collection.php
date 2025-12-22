<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public function sales()
    {
        return $this->belongsToMany('App\Sale', 'collection_sale', 'collection_id', 'sale_id')->withPivot('id','paid_amount','paid_amount_fx','discount','discount_fx','gain_amount','loss_amount');
    }

    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }
 
    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id','id')->select('id', 'branch_name');
    }
}
