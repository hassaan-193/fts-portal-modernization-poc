<?php

namespace App\Models;

use Eloquent as Model;

/**
 * One approver's decision on a single Staff Request or Labor Request.
 *
 * Uniqueness is enforced on (request_type, request_id, user_id) so an approver
 * has exactly one standing decision per request.
 */
class RequestApproval extends Model
{
    const TYPE_STAFF = 'staff';
    const TYPE_LABOR = 'labor';

    const DECISION_APPROVED = 1;
    const DECISION_REJECTED = 2;

    public $table = 'request_approvals';

    public $fillable = [
        'request_type',
        'request_id',
        'user_id',
        'decision',
        'note',
        'decided_at',
    ];

    protected $casts = [
        'id'           => 'integer',
        'request_type' => 'string',
        'request_id'   => 'integer',
        'user_id'      => 'integer',
        'decision'     => 'integer',
        'note'         => 'string',
    ];

    protected $dates = ['decided_at'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
