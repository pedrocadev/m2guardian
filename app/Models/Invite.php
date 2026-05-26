<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitable_type',
        'invitable_id',
        'sent_to_email',
        'sent_by_type',
        'sent_by_id',
        'magic_link_id',
        'status',
        'sent_at',
        'opened_at',
        'error_message',
        'retry_count',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'opened_at' => 'datetime',
        ];
    }

    public function invitable(): MorphTo
    {
        return $this->morphTo();
    }

    public function sentBy(): MorphTo
    {
        return $this->morphTo();
    }

    public function magicLink(): BelongsTo
    {
        return $this->belongsTo(MagicLink::class);
    }
}
