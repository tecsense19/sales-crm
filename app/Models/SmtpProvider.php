<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SmtpProvider extends Model
{
    protected $fillable = [
        'name', 'driver', 'host', 'port', 'encryption',
        'username', 'password', 'from_email', 'from_name',
        'api_key', 'daily_limit', 'sent_today', 'limit_reset_date',
        'is_active', 'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'limit_reset_date' => 'date',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::retrieved(function (self $provider) {
            $provider->resetIfNewDay();
        });
    }

    public function getApiKeyAttribute($value)
    {
        if (empty($value)) return $value;
        try {
            return decrypt($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return $value;
        }
    }

    public function setApiKeyAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['api_key'] = $value;
        } else {
            $this->attributes['api_key'] = encrypt($value);
        }
    }

    public function getPasswordAttribute($value)
    {
        if (empty($value)) return $value;
        try {
            return decrypt($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return $value;
        }
    }

    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['password'] = $value;
        } else {
            $this->attributes['password'] = encrypt($value);
        }
    }

    /**
     * Reset today's sent counter if the date has changed.
     */
    public function resetIfNewDay(): void
    {
        $today = Carbon::today()->toDateString();

        if ($this->limit_reset_date?->toDateString() !== $today) {
            $this->update([
                'sent_today' => 0,
                'limit_reset_date' => $today,
            ]);
        }
    }

    /**
     * Check if this provider still has capacity for today.
     */
    public function hasCapacity(): bool
    {
        $this->resetIfNewDay();
        return $this->sent_today < $this->daily_limit;
    }

    /**
     * Increment the sent counter.
     */
    public function incrementSent(): void
    {
        $this->increment('sent_today');
    }

    /**
     * Get the next available provider (ordered by priority, with capacity remaining).
     */
    public static function getAvailable(): ?self
    {
        $providers = self::where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($providers as $provider) {
            if ($provider->hasCapacity()) {
                return $provider;
            }
        }

        return null; // All providers exhausted for today
    }

    /**
     * Get the next available provider excluding a specific one.
     * Used for fallback when the first chosen provider API call fails.
     */
    public static function getAvailableExcept(int $excludeId): ?self
    {
        $providers = self::where('is_active', true)
            ->where('id', '!=', $excludeId)
            ->orderBy('priority')
            ->get();

        foreach ($providers as $provider) {
            if ($provider->hasCapacity()) {
                return $provider;
            }
        }

        return null;
    }
}
