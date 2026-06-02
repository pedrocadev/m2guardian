<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MagicLink extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'token_hash',
        'tokenable_type',
        'tokenable_id',
        'purpose',
        'expires_at',
        'consumed_at',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function generateFor(Model $tokenable, string $purpose, int $expiresDays = 7): array
    {
        // 12 chars de [A-Za-z0-9] = ~71 bits de entropia.
        // Suficiente pra link single-use com rate-limit (10 req/min/IP) e expiração curta.
        $plainToken = Str::random(12);

        $link = self::create([
            'token_hash' => hash('sha256', $plainToken),
            'tokenable_type' => $tokenable::class,
            'tokenable_id' => $tokenable->getKey(),
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addDays($expiresDays),
            'created_at' => Carbon::now(),
        ]);

        return [
            'plain_token' => $plainToken,
            'magic_link' => $link,
        ];
    }

    public static function generateUrlFor(Model $tokenable, string $purpose, int $expiresDays = 7): string
    {
        ['plain_token' => $token] = self::generateFor($tokenable, $purpose, $expiresDays);
        return route('magic-link.short', ['token' => $token]);
    }

    public static function findValid(string $plainToken): ?self
    {
        return self::where('token_hash', hash('sha256', $plainToken))
            ->whereNull('consumed_at')
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function consume(?string $ip = null, ?string $userAgent = null): void
    {
        $this->update([
            'consumed_at' => Carbon::now(),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    public function scopeUsable(Builder $query): Builder
    {
        return $query->whereNull('consumed_at')
            ->where('expires_at', '>', Carbon::now());
    }
}
