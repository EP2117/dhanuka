<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;
    
    public function products()
    {
         return $this->belongsToMany('App\Product', 'product_transfer', 'transfer_id', 'product_id')->withPivot('id','uom_id','product_quantity','cost_price');
    }

    public function from_warehouse()
    {
        return $this->belongsTo('App\Warehouse', 'from_warehouse_id', 'id')->select('id', 'warehouse_name'); 
    }
    
    public function to_warehouse()
    {
        return $this->belongsTo('App\Warehouse', 'to_warehouse_id', 'id')->select('id', 'warehouse_name');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id','id')->select('id', 'branch_name');
    }
}
