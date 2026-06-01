<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'template_id', 'subject', 'body', 'status', 'sent_count', 'failed_count', 'target_status', 'scheduled_at', 'selected_clients', 'external_emails'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'selected_clients' => 'array',
        'external_emails' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function pendingEmails()
    {
        return $this->hasMany(PendingEmail::class);
    }
}
