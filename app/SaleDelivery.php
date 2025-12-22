<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleDelivery extends Model
{
    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product', 'sale_delivery_product', 'delivery_id', 'product_id')->withPivot('id','sale_id','uom_id','delivery_qty','price','price_variant','total_amount','is_foc');
    }
}
