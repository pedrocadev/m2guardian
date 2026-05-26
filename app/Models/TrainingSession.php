<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'collaborator_id',
        'started_at',
        'completed_at',
        'total_scenarios',
        'total_questions',
        'score',
        'duration_seconds',
        'client_user_agent',
        'client_ip',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function scorePercentage(): ?float
    {
        if ($this->score === null || $this->total_questions === 0) {
            return null;
        }
        return round(($this->score / $this->total_questions) * 100, 1);
    }
}
