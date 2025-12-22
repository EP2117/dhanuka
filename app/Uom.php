<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
	//relationship for selling uom
    public function products()
	{
	    return $this->belongsToMany('App\Product', 'product_selling_uom');
	}

	//relationship for warehouse uom
	public function uom_products()
    {
        return $this->hasMany('App\Product');
    }
}
