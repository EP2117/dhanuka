<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountTransition extends Model
{
    protected $table='account_transitions';
    protected $fillable=['transition_date','sub_account_id','receipt_id','payment_id','credit','debit','is_cashbook','description'];
    protected $with=['sub_account'];
    public function sub_account(){
        return $this->belongsTo(SubAccount::class);
    }
}