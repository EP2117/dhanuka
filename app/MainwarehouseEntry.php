<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MainwarehouseEntry extends Model
{
    public function products()
	{
	    return $this->belongsToMany('App\Product', 'product_mainwarehouse_entry', 'entry_id', 'product_id')->withPivot('id','uom_id','product_quantity');
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
