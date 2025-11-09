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
        return $this->belongsTo(Contribution::class, 'contribution_id', 'contribution_id');
    }    

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); 
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            try {
                $contribution = $payment->contribution()->first();
                if ($contribution && $contribution->contribution_type === 'community') {
                    \App\Models\CommunityContributionPayment::updateOrCreate(
                        [
                            'contribution_id' => $payment->contribution_id,
                            'user_id' => $payment->user_id,
                        ],
                        [
                            'payment_status' => $payment->payment_status ?? 'paid'
                        ]
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Error syncing community payment on Payment created: ' . $e->getMessage());
            }
        });
    }
}
