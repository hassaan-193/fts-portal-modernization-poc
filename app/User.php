<?php

namespace App;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia
{
    use HasMediaTrait, HasRoles, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'name',
        'email',
        'password',
        'image',
        'api_token',
        'staf_profile_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'email' => 'required|unique:users',
        'password' => 'required|confirmed',
        'role' => 'required',
        'staf_profile_id' => 'nullable|exists:staf_profile,id',
    ];

    public static $updaterules = [
        'name' => 'required',
        'email' => 'required',
        'password' => 'sometimes|confirmed',
        'role' => 'required',
        'staf_profile_id' => 'nullable|exists:staf_profile,id',
    ];

    public static $profileRules = [
        'password' => 'sometimes|confirmed',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
	{
        parent::boot();

		static::saving(function ($model) {
            if(request('file')){
                $model->clearMediaCollection();
                $model->addMedia(request('file'))->toMediaCollection();
            }
        });
    }

    /**
     * Get all attendances marked by this foreman
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'foreman_id');
    }

    /**
     * Get all attendances approved by this manager
     */
    public function approvalsGiven()
    {
        return $this->hasMany(Attendance::class, 'approved_by');
    }

    /**
     * The staff profile this login is linked to (for self-service request form access)
     */
    public function stafProfile()
    {
        return $this->belongsTo(\App\Models\StafProfile::class, 'staf_profile_id');
    }

    /**
     * Whether this login holds an approval permission for any request type.
     */
    public function isRequestApprover()
    {
        return !empty(\App\Services\RequestApprovalService::typesForUser($this));
    }
}
