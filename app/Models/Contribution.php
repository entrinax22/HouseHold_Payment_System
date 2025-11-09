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

    public function communityPayments()
    {
        return $this->hasMany(CommunityContributionPayment::class, 'contribution_id', 'contribution_id');
    }

    /**
     * Users who are expected to participate in this community contribution.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'contribution_participants', 'contribution_id', 'user_id', 'contribution_id', 'id');
    }

    public function isPaid()
    {
        try {
            if ($this->contribution_type === 'community') {
                // For community contributions, check if the current user has paid
                    return $this->communityPayments()
                        ->where('user_id', auth()->id())
                        ->where('payment_status', 'paid')
                        ->exists();
            }
            
            // For personal contributions, check if total payments meet or exceed the amount
            $totalPaid = $this->payments()
                ->where('payment_status', 'paid')
                ->sum('amount_paid');
                
            return $totalPaid >= $this->amount;
        } catch (\Exception $e) {
            \Log::error('Error checking payment status: ' . $e->getMessage());
            return false;
            }
    }
}
