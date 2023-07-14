<?php

namespace App\Models;

use App\Mail\MagicLoginLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'slug',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function books(): BelongsToMany {
        return $this->belongsToMany(Book::class, 'books_users')->withPivot('order')->orderBy('books_users.order');
    }

    public function loginTokens(): HasMany
    {
        return $this->hasMany(LoginToken::class);
    }

    public function sendLoginLink(): void
    {
        $plaintext = Str::random(32);

        $token = $this->loginTokens()->create([
            'token' => hash('sha256', $plaintext),
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($this->email)->send(new MagicLoginLink($plaintext, $token->expires_at));
    }

    public function emailIsConfirmed(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function emailConfirm(): void
    {
        $this->email_verified_at = now();
        $this->save();
    }
}
