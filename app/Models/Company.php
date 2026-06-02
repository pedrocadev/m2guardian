<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'cnpj',
        'slug',
        'license',
        'max_collaborators',
        'status',
        'license_expires_at',
        'contact_phone',
        'contact_email',
        'notes',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'license_expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Gera slug único automaticamente quando a empresa é criada.
        // Slug é usado internamente (ex: nome do arquivo PDF do relatório).
        static::saving(function (Company $company) {
            if (empty($company->slug)) {
                $base = Str::slug($company->name) ?: 'empresa';
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->where('id', '!=', $company->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $company->slug = $slug;
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function leaders(): HasMany
    {
        return $this->hasMany(Leader::class);
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(Collaborator::class);
    }

    public function scenarios(): HasMany
    {
        return $this->hasMany(Scenario::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

    public function isPro(): bool
    {
        return $this->license === 'pro';
    }

    public function isDemo(): bool
    {
        return $this->license === 'demo';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
