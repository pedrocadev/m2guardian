<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'brand_logo_url',
        'brand_primary_color',
        'email_sender_name',
        'email_signature',
        'notify_leader_on_completion',
        'notify_m2_on_completion',
        'notify_m2_email',
        'locale',
        'timezone',
    ];

    protected function casts(): array
    {
        return [
            'notify_leader_on_completion' => 'boolean',
            'notify_m2_on_completion' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
