<?php

namespace App\Models;

use Eloquent as Model;

class InquiryRoutingConfig extends Model
{
    public $table = 'inquiry_routing_configs';

    public $fillable = [
        'inquiry_type',
        'department',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Resolve which department should receive an inquiry of the given type.
     * Falls back to "Project Team" if no active rule is found.
     */
    public static function departmentFor(string $inquiryType): string
    {
        $config = static::where('inquiry_type', $inquiryType)
            ->where('is_active', true)
            ->first();

        return $config ? $config->department : 'Project Team';
    }
}
