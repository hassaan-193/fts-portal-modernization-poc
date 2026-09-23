<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Creates the 'own_request_form' permission and 'Staff Requester' role, then links them.
 *
 * Done in a migration rather than the UI because /roles can only tick permissions that
 * already exist — it has no way to mint a new permission name.
 */
class AddOwnRequestFormPermissionAndRole extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $guard = 'web';

        // Insert permission if not already present
        $permissionId = DB::table('permissions')
            ->where('name', 'own_request_form')
            ->where('guard_name', $guard)
            ->value('id');

        if (!$permissionId) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name'       => 'own_request_form',
                'guard_name' => $guard,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert role if not already present
        $roleId = DB::table('roles')
            ->where('name', 'Staff Requester')
            ->where('guard_name', $guard)
            ->value('id');

        if (!$roleId) {
            $roleId = DB::table('roles')->insertGetId([
                'name'       => 'Staff Requester',
                'guard_name' => $guard,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Link permission → role
        $alreadyLinked = DB::table('role_has_permissions')
            ->where('permission_id', $permissionId)
            ->where('role_id', $roleId)
            ->exists();

        if (!$alreadyLinked) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permissionId,
                'role_id'       => $roleId,
            ]);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $guard = 'web';

        $permission = DB::table('permissions')
            ->where('name', 'own_request_form')->where('guard_name', $guard)->first();

        $role = DB::table('roles')
            ->where('name', 'Staff Requester')->where('guard_name', $guard)->first();

        if ($permission && $role) {
            DB::table('role_has_permissions')
                ->where('permission_id', $permission->id)
                ->where('role_id', $role->id)
                ->delete();
        }

        if ($role) {
            DB::table('model_has_roles')->where('role_id', $role->id)->delete();
            DB::table('roles')->where('id', $role->id)->delete();
        }

        if ($permission) {
            DB::table('model_has_permissions')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
