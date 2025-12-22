<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseCreditNote extends Model
{
    protected $table='debit_notes';
    protected $with=['supplier'];
    protected $fillable=['credit_note_no','credit_note_date','branch_id','warehouse_id','supplier_id','credit_note_type_id','purchase_id','amount','account_group_id','sub_account_id'];
    public function products(){
        return $this->belongsToMany('App\Product','debit_note_product','debit_note_id','product_id')->withPivot('id','product_purchase_pivot_id','uom_id','qty','old_qty','discount','remark','rate','total_amount');
    }
    public function supplier(){
        return $this->belongsTo('App\Supplier','supplier_id','id'); 
    }
    public function purchase(){
        return $this->belongsTo('App\PurchaseInvoice','purchase_id','id'); 
    }
 
    public function branch()
    {
        return $this->belongsTo('App\Branch','branch_id','id');
    }
    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse','warehouse_id','id');
    }
}
