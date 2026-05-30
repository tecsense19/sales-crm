<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'mobile_no', 'website', 'project_link', 'location',
        'technology', 'linkedin', 'facebook', 'instagram', 'youtube', 'x',
        'telegram', 'whatsapp', 'teams', 'date_added', 'status',
        'last_contacted_date', 'follow_up_days', 'assigned_to', 'notes', 'source_url', 'next_followup_date'
    ];

    protected $casts = [
        'date_added' => 'date',
        'last_contacted_date' => 'date',
        'next_followup_date' => 'date',
        'follow_up_days' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($client) {
            if (!$client->isDirty('next_followup_date')) {
                if ($client->last_contacted_date && $client->follow_up_days !== null) {
                    $lastContacted = \Carbon\Carbon::parse($client->last_contacted_date);
                    $client->next_followup_date = $lastContacted->copy()->addDays((int)$client->follow_up_days)->format('Y-m-d');
                } else {
                    $client->next_followup_date = null;
                }
            }
        });
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getNextFollowUpDateAttribute($value = null)
    {
        $dateValue = $value ?: ($this->attributes['next_followup_date'] ?? null);
        
        if ($dateValue) {
            return \Carbon\Carbon::parse($dateValue);
        }
        
        if (!$this->last_contacted_date) return null;
        
        // Ensure last_contacted_date is parsed as Carbon
        $lastContacted = \Carbon\Carbon::parse($this->last_contacted_date);
        return $lastContacted->copy()->addDays((int)$this->follow_up_days);
    }

    public function isFollowUpOverdue()
    {
        $next = $this->next_follow_up_date;
        return $next && $next->isPast() && $this->status !== 'Closed Won' && $this->status !== 'Closed Lost';
    }
}
