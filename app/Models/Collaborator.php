<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Collaborator extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'invited_by_leader_id',
        'name',
        'email',
        'department',
        'profile',
        'invited_at',
        'first_access_at',
        'completed_at',
        'score',
        'total_questions',
    ];

    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
            'first_access_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(Leader::class, 'invited_by_leader_id');
    }

    /**
     * Todas as tentativas do colaborador, mais recente primeiro.
     * Cada refazer cria uma nova TrainingSession — o histórico fica preservado.
     *
     * Ordena por `id` (auto_increment, sempre monotônico) em vez de `started_at`
     * porque sessions historicas podem ter `started_at` gravado em UTC enquanto
     * sessions novas ficam em BRT — a comparação por timestamp fica caótica e
     * `latestOfMany('started_at')` pode retornar a session antiga em vez da nova.
     */
    public function trainingSessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class)->latest('id');
    }

    /**
     * Tentativa atual (a mais recente). Mantém a interface `$collaborator->trainingSession`
     * usada em vários pontos do código; agora aponta pra última em vez da única.
     */
    public function trainingSession(): HasOne
    {
        return $this->hasOne(TrainingSession::class)->latestOfMany('id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function magicLinks(): MorphMany
    {
        return $this->morphMany(MagicLink::class, 'tokenable');
    }

    public function hasCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
