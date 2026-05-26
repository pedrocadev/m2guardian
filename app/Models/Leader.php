<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Leader extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'phone',
        'role_label',
        'status',
        'last_login_at',
        'last_login_ip',
        'failed_attempts',
        'locked_until',
        'password_set_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at'   => 'datetime',
            'locked_until'    => 'datetime',
            'password_set_at' => 'datetime',
            'password'        => 'hashed',
        ];
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invitedCollaborators(): HasMany
    {
        return $this->hasMany(Collaborator::class, 'invited_by_leader_id');
    }

    public function magicLinks(): MorphMany
    {
        return $this->morphMany(MagicLink::class, 'tokenable');
    }
}
