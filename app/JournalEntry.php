<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table='journal_entries';
    protected $fillable=['date','journal_entry_no','debit_id','credit_id','amount','remark','account_group_id','sub_account_id','updated_at','created_at','updated_by','created_by'];
    protected $with=['debit','credit'];

    public function debit(){
        return $this->belongsTo(SubAccount::class,'debit_id','id');
    }
    
    public function credit(){
        return $this->belongsTo(SubAccount::class,'credit_id','id');
    }
}
