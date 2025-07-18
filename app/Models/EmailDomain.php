<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Static Methods
    public static function getDefaultDomain()
    {
        return self::where('is_default', true)->where('is_active', true)->first();
    }

    public static function getActiveDomains()
    {
        return self::where('is_active', true)->orderBy('is_default', 'desc')->get();
    }
}
