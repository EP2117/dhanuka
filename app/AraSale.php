<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AraSale extends Model
{
    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }

    public function ara_products()
    {
        return $this->belongsToMany('App\AraProduct', 'ara_product_sale', 'ara_sale_id', 'ara_product_id')->withPivot('id','quantity','rate','total_amount');
    }
}
