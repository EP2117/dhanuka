<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderApproval extends Model
{
    public function order()
    {
        return $this->belongsTo('App\Order');
        //return $this->belongsTo('App\Order', 'order_id', 'id')->select('id', 'order_no','customer_id','sale_man_id'); 
    }

    public function products()
    {
        return $this->belongsToMany('App\Product', 'order_approval_product', 'approval_id', 'product_id')->withPivot('id','order_id','uom_id','approval_qty','price','price_variant','total_amount','is_foc');
    }
}
