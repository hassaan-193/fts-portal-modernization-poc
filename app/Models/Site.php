<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Site extends Model
{
    protected $table = 'sites';

    protected $fillable = [
        'site_name',
        'engineer_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the engineer who created this site
     */
    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope to filter sites by engineer
     */
    public function scopeByEngineer($query, $engineerId)
    {
        return $query->where('engineer_id', $engineerId);
    }

    /**
     * Scope to search sites by name
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('site_name', 'like', '%' . $searchTerm . '%');
    }
}