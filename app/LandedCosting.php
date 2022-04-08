<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LandedCosting extends Model
{
    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_landed_costing', 'landed_costing_id', 'product_id')->withPivot('id','total_ctn','pcs_per_ctn','total_pcs','rmb_rate','total_rmb','mmk_per_rmb','mmk_rate','duty_charges','landed_cost_per_product','cost','total_cost');
    }

    public function supplier(){
        return $this->belongsTo('App\Supplier','supplier_id','id');
    }
}
