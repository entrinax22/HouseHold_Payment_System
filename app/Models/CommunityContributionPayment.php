<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityContributionPayment extends Model
{
    protected $fillable = [
        'contribution_id',
        'user_id',
        'payment_status',
    ];

    public function contribution()
    {
        return $this->belongsTo(Contribution::class, 'contribution_id', 'contribution_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}