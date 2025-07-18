<?php

namespace App\Modules\Incident\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'color',
        'is_closed',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'status_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
