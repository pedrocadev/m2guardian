<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PlatformFeedback extends Model
{
    // "Feedback" e palavra invariavel em ingles; Str::plural() nao adiciona "s".
    // Forcamos o nome da tabela pra bater com a migration (que criou platform_feedbacks).
    protected $table = 'platform_feedbacks';

    // `body` fica no fillable pra backward-compat (registros pre-v2 so tinham body).
    // Nao e editado via UI a partir da v2 (o form usa slides + guardian_image).
    // Feito nullable na migration 2026_08_19_160000 pra permitir INSERTs sem body.
    protected $fillable = ['platform', 'title', 'guardian_image', 'body', 'slides'];

    protected $casts = [
        'slides' => 'array',
    ];

    /**
     * Busca feedback pela chave da plataforma; retorna null se nao existir
     * (ex: plataforma nova adicionada ao enum antes de rodar o seed do feedback).
     */
    public static function forPlatform(string $platform): ?self
    {
        return static::where('platform', $platform)->first();
    }

    /**
     * URL publica da imagem do Guardiao pra essa plataforma, ou null (o front
     * usa o mascote default nesse caso).
     */
    public function getGuardianImageUrlAttribute(): ?string
    {
        return $this->guardian_image
            ? Storage::disk('public')->url($this->guardian_image)
            : null;
    }

    /**
     * Slides normalizados. Se o campo `slides` estiver vazio (backward compat
     * com registros antigos que so tinham `body`), converte o body num slide
     * unico. Garante que o front sempre recebe uma lista de {title, body}.
     */
    public function getNormalizedSlidesAttribute(): array
    {
        $slides = $this->slides ?? [];
        if (empty($slides) && !empty($this->body)) {
            $slides = [['title' => null, 'body' => $this->body]];
        }
        return $slides;
    }
}
