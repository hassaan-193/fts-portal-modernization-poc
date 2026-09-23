<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('roles')->truncate();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'users']);
        Permission::create(['name' => 'roles']);
        Permission::create(['name' => 'companies']);
        Permission::create(['name' => 'quotations']);
        Permission::create(['name' => 'lpoins']);
        Permission::create(['name' => 'vendors']);
        Permission::create(['name' => 'lpoouts']);
        Permission::create(['name' => 'media']);
        Permission::create(['name' => 'invoices']);
        Permission::create(['name' => 'paymentInvoices']);
        Permission::create(['name' => 'projects']);
        Permission::create(['name' => 'invoiceRequests']);
        Permission::create(['name' => 'laborRequests']);
        Permission::create(['name' => 'staffRequests']);
        Permission::create(['name' => 'accounts']);
        Permission::create(['name' => 'receipts']);
        Permission::create(['name' => 'payments']);
        Permission::create(['name' => 'cheques']);
        Permission::create(['name' => 'requestForms']);
        Permission::create(['name' => 'pettyCashes']);
        Permission::create(['name' => 'employees']);
        Permission::create(['name' => 'stafprofile']);
        Permission::create(['name' => 'document']);
        Permission::create(['name' => 'payroll']);
        Permission::create(['name' => 'tasks']);
        Permission::create(['name' => 'products']);
        Permission::create(['name' => 'deletes']);
        // Reports permissions
        Permission::create(['name' => 'report_pettycash']);
        // For accounts role
        Permission::create(['name' => 'ticket_status']);
        // For hr role 
        Permission::create(['name' => 'ticket_creation']);
        // For foreman role
        Permission::create(['name' => 'labor_attendance']);
        // for vendor order
        Permission::create(['name' => 'vendor_order']);
        // for letter creation - whatsapp notification
        Permission::create(['name' => 'letters']);

        $role = Role::create(['name' => 'Super-User']);
        $role->givePermissionTo(Permission::all());

        $user =  User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password
            'api_token' => Str::random(60),
        ]);
        $user->assignRole($role->name);
    }
}
