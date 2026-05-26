<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioVersion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'scenario_id',
        'version',
        'content_snapshot',
        'edited_by_admin_id',
        'edit_summary',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'content_snapshot' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'edited_by_admin_id');
    }
}
