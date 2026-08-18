<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Scenario extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'platform',
        'category',
        'slug',
        'label',
        'avatar',
        'avatar_image',
        'bg_color',
        'preview',
        'intro',
        'content',
        'is_default',
        'demo_eligible',
        'target_areas',
        'version',
        'status',
        'updated_by_admin_id',
    ];

    public const AREAS = [
        'todos'        => 'Todos os colaboradores',
        'diretoria'    => 'Diretoria / C-Level',
        'financeiro'   => 'Financeiro / Contas a Pagar',
        'rh'           => 'Recursos Humanos',
        'ti'           => 'TI / Tecnologia',
        'comercial'    => 'Comercial / Vendas',
        'juridico'     => 'Jurídico / Compliance',
        'operacional'  => 'Operacional / Logística',
        'compras'      => 'Compras / Suprimentos',
    ];

    public const CATEGORIES = [
        'validacao_links'              => 'Validação de links',
        'atencao_remetentes'           => 'Atenção a remetentes',
        'solicitacoes_urgentes'        => 'Solicitações urgentes',
        'compartilhamento_informacoes' => 'Compartilhamento de informações',
        'cuidado_senhas'               => 'Cuidado com senhas e credenciais',
        'anexos_downloads'             => 'Anexos e downloads suspeitos',
    ];

    public const PLATFORM_LABELS = [
        'wapp'     => 'WhatsApp',
        'teams'    => 'Teams',
        'email'    => 'E-mail',
        'telegram' => 'Telegram',
        'slack'    => 'Slack',
        'outro'    => 'Outra',
    ];

    public const PLATFORM_COLORS = [
        'wapp'     => 'success',
        'teams'    => 'primary',
        'email'    => 'warning',
        'telegram' => 'info',
        'slack'    => 'danger',
        'outro'    => 'gray',
    ];

    public const STATUS_LABELS = [
        'active'   => 'Ativo',
        'draft'    => 'Rascunho',
        'archived' => 'Arquivado',
    ];

    public const STATUS_COLORS = [
        'active'   => 'success',
        'draft'    => 'warning',
        'archived' => 'danger',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'target_areas' => 'array',
            'is_default' => 'boolean',
            'demo_eligible' => 'boolean',
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_scenario')->withTimestamps();
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ScenarioVersion::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    // Cenarios padrao M2 = fallback usado por empresas SEM vinculos proprios no pivot.
    // Nao filtra por doesntHave('companies') de proposito: um cenario is_default pode
    // ADICIONALMENTE estar vinculado a X empresas (aparece pra elas + pras sem vinculo).
    public function scopeDefaults(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function scopeDemoEligible(Builder $query): Builder
    {
        return $query->where('demo_eligible', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // URL publica da foto do remetente (retorna null se nao tiver upload → views caem no emoji do campo 'avatar').
    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar_image)) {
            return null;
        }
        return Storage::disk('public')->url($this->avatar_image);
    }
}
