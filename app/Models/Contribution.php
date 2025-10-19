<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $primaryKey = 'contribution_id';
    protected $fillable = [
        'user_id',
        'amount',
        'contribution_date',
        'contribution_type',
        'status',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'contribution_id', 'contribution_id');
    }

    public function isPaid()
    {
        return $this->payments()->where('payment_status', 'paid')->sum('amount_paid') >= $this->amount;
    }
}
