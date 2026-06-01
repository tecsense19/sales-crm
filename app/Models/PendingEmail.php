<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingEmail extends Model
{
    protected $fillable = [
        'campaign_id', 'recipient_email', 'recipient_name',
        'recipient_location', 'recipient_type',
        'status', 'attempts', 'last_error', 'next_attempt_at',
    ];

    protected $casts = [
        'next_attempt_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Scope: emails ready to retry.
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('next_attempt_at')
                  ->orWhere('next_attempt_at', '<=', now());
            });
    }
}
