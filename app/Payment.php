<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table='payments';
    protected $fillable=['date','cash_payment_no','debit_id','credit_id','amount','remark','updated_at','created_at','updated_by','created_by'];
    protected $with=['debit','credit'];
    public function debit(){
        return $this->belongsTo(SubAccount::class,'debit_id','id');
    }
    public function credit(){
        return $this->belongsTo(SubAccount::class,'credit_id','id');
    }
}
