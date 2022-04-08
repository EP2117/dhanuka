<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseAdvance extends Model
{
    public function supplier(){
        return $this->belongsTo('App\Supplier','supplier_id','id');
    }
}
