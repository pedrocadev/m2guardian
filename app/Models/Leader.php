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
        'is_primary',
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
            'is_primary'      => 'bool',
        ];
    }

    protected static function booted(): void
    {
        // Bloqueia delete em 2 casos:
        // 1. Lider principal da empresa (criado junto, vinculo permanente)
        // 2. Ultimo lider ativo (empresa nunca fica sem lider)
        static::deleting(function (Leader $leader) {
            if ($leader->is_primary) {
                throw new \RuntimeException(
                    'Este é o líder principal da empresa (cadastrado junto com a empresa). ' .
                    'Líderes principais não podem ser arquivados.'
                );
            }

            if (!$leader->canBeArchived()) {
                throw new \RuntimeException(
                    'Esta empresa precisa ter pelo menos um líder. ' .
                    'Cadastre outro líder antes de arquivar este.'
                );
            }
        });

        // Bloqueia alteração de company_id e name no lider principal.
        // O save inicial passa porque $leader->exists é false na criação.
        static::saving(function (Leader $leader) {
            if (!$leader->exists || !$leader->is_primary) {
                return;
            }
            if ($leader->isDirty('company_id')) {
                throw new \RuntimeException(
                    'Empresa do líder principal não pode ser alterada.'
                );
            }
            if ($leader->isDirty('name')) {
                throw new \RuntimeException(
                    'Nome do líder principal não pode ser alterado.'
                );
            }
        });
    }

    /**
     * True se este líder pode ser arquivado. Falso se:
     *  - É o líder principal (vínculo permanente com a empresa)
     *  - É o último líder ativo da empresa
     */
    public function canBeArchived(): bool
    {
        if ($this->is_primary) {
            return false;
        }

        return static::where('company_id', $this->company_id)
            ->where('id', '!=', $this->id)
            ->exists();
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
