<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginToken extends Model
{
    protected $guarded = [];

    protected $dates = [
        'expires_at', 'consumed_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isConsumed();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isBefore(now());
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function consume(): void
    {
        $this->consumed_at = now();
        $this->save();
    }
}
