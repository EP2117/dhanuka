<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseCollection extends Model
{
    protected $with=['supplier'];
    protected $table='credit_purchase_collections';
    protected $fillable=['account_group_id','sub_account_id','supplier_id','collection_no','collection_date','branch_id','auto_payment','currency_id','currency_rate','total_paid_amount','total_paid_amount_fx','created_at','updated_at','created_by','updated_by'];
    public function purchases()
    {
        return $this->belongsToMany(PurchaseInvoice::class, 'collection_purchase', 'purchase_collection_id', 'purchase_id')->withPivot('id','paid_amount','paid_amount_fx','discount','discount_fx','gain_amount','loss_amount');
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id','id')->select('id', 'branch_name');
    }
}
