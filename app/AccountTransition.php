<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountTransition extends Model
{
    protected $table='account_transitions';
    protected $fillable=['transition_date','sub_account_id','receipt_id','payment_id','supplier_id','customer_id','purchase_id','purchase_advance_id','sale_id','return_id','customer_return_id','return_payment_id','sale_advance_id','credit','debit','is_cashbook','vochur_no','description','status','created_by','updated_by'];
    protected $with=['sub_account','customer','supplier','sale'];
    protected $dates = [
        'transition_date',
    ];
    public function sub_account(){
        return $this->belongsTo(SubAccount::class);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function sale(){
        return $this->belongsTo(Sale::class);
    }
    public function scopeGetPL($query){
        // return $query->where('')
    }
}


