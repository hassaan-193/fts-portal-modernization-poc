<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Permissions + roles that drive the two-person approval flow.
 *
 * Whoever holds `approve_staff_requests` is a required approver on every Staff
 * Request; whoever holds `approve_labor_requests` is a required approver on every
 * Labor Request. A request is Approved only once EVERY holder has approved.
 *
 * Two separate permissions (rather than one) because the two sides need different
 * approver sets: staff = Prasad + HR, labor = Saad + HR. HR simply gets both roles.
 */
class AddRequestApprovalPermissionsAndRoles extends Migration
{
    /**
     * permission name => role that carries it
     */
    private $map = [
        'approve_staff_requests' => 'Staff Request Approver',
        'approve_labor_requests' => 'Labor Request Approver',
    ];

    /**
     * Run the migrations.
     */
    public function up()
    {
        $guard = 'web';

        foreach ($this->map as $permissionName => $roleName) {
            $permissionId = DB::table('permissions')
                ->where('name', $permissionName)->where('guard_name', $guard)->value('id');

            if (!$permissionId) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name'       => $permissionName,
                    'guard_name' => $guard,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $roleId = DB::table('roles')
                ->where('name', $roleName)->where('guard_name', $guard)->value('id');

            if (!$roleId) {
                $roleId = DB::table('roles')->insertGetId([
                    'name'       => $roleName,
                    'guard_name' => $guard,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $alreadyLinked = DB::table('role_has_permissions')
                ->where('permission_id', $permissionId)->where('role_id', $roleId)->exists();

            if (!$alreadyLinked) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id'       => $roleId,
                ]);
            }
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $guard = 'web';

        foreach ($this->map as $permissionName => $roleName) {
            $permission = DB::table('permissions')
                ->where('name', $permissionName)->where('guard_name', $guard)->first();

            $role = DB::table('roles')
                ->where('name', $roleName)->where('guard_name', $guard)->first();

            if ($permission && $role) {
                DB::table('role_has_permissions')
                    ->where('permission_id', $permission->id)->where('role_id', $role->id)->delete();
            }

            if ($role) {
                DB::table('model_has_roles')->where('role_id', $role->id)->delete();
                DB::table('roles')->where('id', $role->id)->delete();
            }

            if ($permission) {
                DB::table('model_has_permissions')->where('permission_id', $permission->id)->delete();
                DB::table('permissions')->where('id', $permission->id)->delete();
            }
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
