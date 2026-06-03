<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'released_at',
        'content',
        'published',
    ];

    protected function casts(): array
    {
        return [
            'released_at' => 'date',
            'published'   => 'bool',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    /**
     * Retorna a release publicada mais recente, ou null se não houver nenhuma.
     * Usada no popup que aparece a cada login.
     */
    public static function latestPublished(): ?self
    {
        return self::published()
            ->latest('released_at')
            ->latest('id')
            ->first();
    }
}
