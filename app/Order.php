<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_order', 'order_id', 'product_id')->withPivot('id','uom_id','product_quantity','accepted_quantity','approved_quantity','price','price_variant','total_amount','is_foc','rate','actual_rate','discount','other_discount');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse', 'warehouse_id', 'id')->select('id', 'warehouse_name'); 
    }

    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id')->withTrashed();
    }

    public function approvals()
    {
        return $this->hasMany('App\OrderApproval');
    }

    public function sale_man(){
        return $this->belongsTo('App\SaleMan','sale_man_id','id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id','id')->select('id', 'branch_name');
    }
}
