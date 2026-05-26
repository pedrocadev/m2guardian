<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scenario extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'platform',
        'slug',
        'label',
        'avatar',
        'bg_color',
        'preview',
        'intro',
        'content',
        'is_default',
        'demo_eligible',
        'version',
        'status',
        'updated_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_default' => 'boolean',
            'demo_eligible' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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

    public function scopeDefaults(Builder $query): Builder
    {
        return $query->whereNull('company_id')->where('is_default', true);
    }

    public function scopeDemoEligible(Builder $query): Builder
    {
        return $query->where('demo_eligible', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
