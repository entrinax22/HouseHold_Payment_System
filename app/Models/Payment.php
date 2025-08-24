<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    protected $fillable  = [
        'contribution_id',
        'user_id',
        'amount_paid',
        'payment_status',
        'payment_date',
        'payment_method',
        'payment_note'
    ];
    
    public function contribution()
    {
        return $this->belongsTo(Contribution::class, 'contribution_id');
    }    

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); 
    }
}
