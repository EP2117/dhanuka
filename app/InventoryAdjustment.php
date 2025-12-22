<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $table='inventory_adjustment';
    protected $with=['products','branch','warehouse'];
    public function products()
	{
	    return $this->belongsToMany('App\Product', 'product_inventory_adjustment', 'adjustment_id', 'product_id')->withPivot('id','uom_id','add_qty','less_qty');
	}

	public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id','id')->select('id', 'branch_name');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse', 'warehouse_id', 'id')->select('id', 'warehouse_name'); 
    }
}
