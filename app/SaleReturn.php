<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_return', 'return_id', 'product_id')->withPivot('id','sale_product_pivot_id','uom_id','product_quantity','price','price_variant','total_amount','is_foc','rate','actual_rate','discount','other_discount');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse', 'warehouse_id', 'id')->select('id', 'warehouse_name'); 
    }

    public function sale()
    {
        return $this->belongsTo('App\Sale', 'sale_id', 'id'); 
    }

    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id','id')->select('id', 'branch_name');
    }

    public function office_sale_man(){
        return $this->belongsTo('App\SaleMan','office_sale_man_id','id');
    }

    public function return_payments(){
        return $this->hasMany('App\ReturnPayment');
    }
}
