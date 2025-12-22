<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTransition extends Model
{
	public function products() {
    	return $this->belongsToMany('App\Product', 'product_transitions','id','product_id');
    }
}
