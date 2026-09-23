<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddApproveAmcReportsPermissionAndRole extends Migration
{
    /**
     * Run the migrations.
     * Creates the 'approve_amc_reports' permission and 'AMC Report Approver' role, then links them.
     *
     * Holders get the AMC report-status page (view, edit, approve/disapprove submitted reports).
     * Filling in the visit form and managing drafts stays with 'amc_report' / 'AMC Reporter'.
     */
    public function up()
    {
        $guard = 'web';

        // Insert permission if not already present
        $permissionId = DB::table('permissions')
            ->where('name', 'approve_amc_reports')
            ->where('guard_name', $guard)
            ->value('id');

        if (!$permissionId) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name'       => 'approve_amc_reports',
                'guard_name' => $guard,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert role if not already present
        $roleId = DB::table('roles')
            ->where('name', 'AMC Report Approver')
            ->where('guard_name', $guard)
            ->value('id');

        if (!$roleId) {
            $roleId = DB::table('roles')->insertGetId([
                'name'       => 'AMC Report Approver',
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
            ->where('name', 'approve_amc_reports')->where('guard_name', $guard)->first();

        $role = DB::table('roles')
            ->where('name', 'AMC Report Approver')->where('guard_name', $guard)->first();

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
            DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
            DB::table('model_has_permissions')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
