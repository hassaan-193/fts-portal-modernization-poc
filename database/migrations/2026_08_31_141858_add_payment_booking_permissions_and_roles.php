<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Permissions + roles for the Payment Booking module.
 *
 *   paymentBookings           -> opens the module (book, list, view own bookings)
 *   verify_payment_bookings   -> level 1 of the review chain  ("Payment Booking Verifier")
 *   approve_payment_bookings  -> level 2 of the review chain  ("Payment Booking Approver")
 *
 * The two review permissions are separate so the same person is never able to
 * clear both levels of the same booking — App\Services\PaymentBookingService
 * enforces that at decision time as well.
 *
 * `paymentBookings` deliberately gets no dedicated role: it is granted to whichever
 * existing roles need it from /roles, the same way `pettyCashes` and `payments` are.
 */
class AddPaymentBookingPermissionsAndRoles extends Migration
{
    /**
     * Permissions that carry no role of their own.
     */
    private $standalone = [
        'paymentBookings',
    ];

    /**
     * permission name => role that carries it
     */
    private $map = [
        'verify_payment_bookings'  => 'Payment Booking Verifier',
        'approve_payment_bookings' => 'Payment Booking Approver',
    ];

    /**
     * Run the migrations.
     */
    public function up()
    {
        $guard = 'web';

        foreach ($this->standalone as $permissionName) {
            $this->ensurePermission($permissionName, $guard);
        }

        foreach ($this->map as $permissionName => $roleName) {
            $permissionId = $this->ensurePermission($permissionName, $guard);

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
                $this->dropPermission($permission->id);
            }
        }

        foreach ($this->standalone as $permissionName) {
            $permission = DB::table('permissions')
                ->where('name', $permissionName)->where('guard_name', $guard)->first();

            if ($permission) {
                DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
                $this->dropPermission($permission->id);
            }
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function ensurePermission($name, $guard)
    {
        $id = DB::table('permissions')
            ->where('name', $name)->where('guard_name', $guard)->value('id');

        if (!$id) {
            $id = DB::table('permissions')->insertGetId([
                'name'       => $name,
                'guard_name' => $guard,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $id;
    }

    private function dropPermission($permissionId)
    {
        DB::table('model_has_permissions')->where('permission_id', $permissionId)->delete();
        DB::table('permissions')->where('id', $permissionId)->delete();
    }
}
