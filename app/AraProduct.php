<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AraProduct extends Model
{
    public function ara_sales()
    {
        return $this->belongsToMany('App\AraSale', 'ara_product_sale', 'ara_product_id', 'ara_sale_id')->withPivot('id','quantity','rate','total_amount');
    }
}
