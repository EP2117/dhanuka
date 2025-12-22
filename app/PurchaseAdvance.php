<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseAdvance extends Model
{
    public function supplier(){
        return $this->belongsTo('App\Supplier','supplier_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency', 'currency_id', 'id');
    }

    public function purchases()
    {
        return $this->belongsToMany('App\PurchaseInvoice', 'purchase_advance_links', 'advance_id', 'purchase_id')->withPivot('amount_fx');
    }
}
