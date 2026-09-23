<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Attach `paymentBookings` to the roles that carry every other module permission.
 *
 * This app has no Gate::before granting Super-User everything — each module
 * permission is explicitly linked to Super-User and Administration (see
 * RolesAndPermissionsSeeder). The migration that created `paymentBookings` left it
 * linked to no role, so even a Super-User was refused with "You do not have
 * permission to book payments."
 *
 * The two review permissions are deliberately NOT handled here: they stay opt-in per
 * person via the "Payment Booking Verifier" / "Payment Booking Approver" roles, so
 * nobody lands on an approval roster just by being an administrator.
 */
class AttachPaymentBookingsPermissionToAdminRoles extends Migration
{
    /**
     * Roles that should be able to book payments out of the box.
     */
    private $roles = ['Super-User', 'Administration'];

    private $permission = 'paymentBookings';

    /**
     * Run the migrations.
     */
    public function up()
    {
        $guard = 'web';

        $permissionId = DB::table('permissions')
            ->where('name', $this->permission)->where('guard_name', $guard)->value('id');

        if (!$permissionId) {
            // The creating migration has not run yet; nothing to attach.
            return;
        }

        foreach ($this->roles as $roleName) {
            $roleId = DB::table('roles')
                ->where('name', $roleName)->where('guard_name', $guard)->value('id');

            if (!$roleId) {
                continue;
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

        $permissionId = DB::table('permissions')
            ->where('name', $this->permission)->where('guard_name', $guard)->value('id');

        if (!$permissionId) {
            return;
        }

        $roleIds = DB::table('roles')
            ->whereIn('name', $this->roles)->where('guard_name', $guard)->pluck('id');

        DB::table('role_has_permissions')
            ->where('permission_id', $permissionId)
            ->whereIn('role_id', $roleIds)
            ->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
