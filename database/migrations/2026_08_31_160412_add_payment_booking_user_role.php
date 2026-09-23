<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A dedicated role for people who only book payments.
 *
 * Without it, `paymentBookings` sits solely on Super-User and Administration, so
 * onboarding an accountant for the mobile app would mean handing them portal-wide
 * admin rights. This gives the module the same shape at every level:
 *
 *   Payment Booking User      -> books payments        (paymentBookings)
 *   Payment Booking Verifier  -> level 1 of the review (verify_payment_bookings)
 *   Payment Booking Approver  -> level 2 of the review (approve_payment_bookings)
 *
 * Tick one on /users and that person can sign into the API immediately.
 */
class AddPaymentBookingUserRole extends Migration
{
    private $role = 'Payment Booking User';

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

        $roleId = DB::table('roles')
            ->where('name', $this->role)->where('guard_name', $guard)->value('id');

        if (!$roleId) {
            $roleId = DB::table('roles')->insertGetId([
                'name'       => $this->role,
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

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $guard = 'web';

        $roleId = DB::table('roles')
            ->where('name', $this->role)->where('guard_name', $guard)->value('id');

        if ($roleId) {
            DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
            DB::table('model_has_roles')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
