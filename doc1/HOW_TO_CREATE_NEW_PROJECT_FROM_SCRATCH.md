# How to Build a Multi-Portal Laravel Application from Scratch

A complete, step-by-step master blueprint for building an enterprise Laravel application identical to this portal architecture (Multi-Guard Auth, Spatie RBAC, Yajra DataTables, Tenant Isolation, and REST API).

---

## 📑 Table of Contents
1. [System Stack & Architectural Prerequisites](#1-system-stack--architectural-prerequisites)
2. [Step 1: Project Initialization & Dependency Installation](#step-1-project-initialization--dependency-installation)
3. [Step 2: Database Configuration & Core Migrations](#step-2-database-configuration--core-migrations)
4. [Step 3: Multi-Guard Authentication Configuration](#step-3-multi-guard-authentication-configuration)
5. [Step 4: Authenticatable Models & Security Traits](#step-4-authenticatable-models--security-traits)
6. [Step 5: Multi-Portal Login Controllers](#step-5-multi-portal-login-controllers)
7. [Step 6: Multi-Auth Middleware & Routing Guards](#step-6-multi-auth-middleware--routing-guards)
8. [Step 7: Role-Based Access Control (Spatie RBAC)](#step-7-role-based-access-control-spatie-rbac)
9. [Step 8: High-Performance Yajra DataTables](#step-8-high-performance-yajra-datatables)
10. [Step 9: Routing Structure & Prefix Organization](#step-9-routing-structure--prefix-organization)
11. [Step 10: Client Portal Data Scoping (Tenant Isolation)](#step-10-client-portal-data-scoping-tenant-isolation)
12. [Step 11: Frontend Layouts & Blade Templating](#step-11-frontend-layouts--blade-templating)
13. [Step 12: Deployment & Run Checklist](#step-12-deployment--run-checklist)

---

## 1. System Stack & Architectural Prerequisites
* **PHP**: 7.4 or 8.x
* **Composer**: 2.x
* **MySQL / MariaDB**: 5.7+ / 8.0+
* **Laravel**: Framework 7.x, 8.x, 10.x, or 11.x
* **Architecture Pattern**: Multi-Guard Authentication + Repository Pattern + Server-Side DataTables

---

## Step 1: Project Initialization & Dependency Installation

### 1.1 Create the Laravel Project
```bash
composer create-project laravel/laravel my_portal_app
cd my_portal_app
```

### 1.2 Install Required Packages
```bash
# 1. Yajra DataTables (Server-side rendering, HTML builder, export buttons)
composer require yajra/laravel-datatables-oracle yajra/laravel-datatables-html yajra/laravel-datatables-buttons

# 2. Spatie Roles & Permissions (RBAC)
composer require spatie/laravel-permission

# 3. Spatie Media Library (File attachments, avatars, documents)
composer require spatie/laravel-medialibrary

# 4. Form & HTML Helpers
composer require laravelcollective/html

# 5. Excel / CSV Export Engine
composer require maatwebsite/excel
```

### 1.3 Publish Vendor Assets & Migrations
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Yajra\DataTables\DataTablesServiceProvider"
php artisan vendor:publish --provider="Yajra\DataTables\ButtonsServiceProvider"
```

### 💡 Detailed Explanation for Step 1: Why & How This Works
* **Why Yajra DataTables?** Standard Laravel pagination works well for simple blogs, but enterprise ERPs require dynamic column sorting, instant live search, and multi-format exports (Excel, PDF, CSV, Print). Yajra intercepts frontend requests and executes SQL `LIMIT`, `OFFSET`, and `WHERE LIKE` queries directly in MySQL, avoiding browser memory overload.
* **Why Spatie Permission?** Instead of hardcoding role checks (`if ($user->is_admin)`), Spatie introduces a standard database-driven Role-Based Access Control (RBAC) matrix. You can attach granular permissions (e.g. `create_invoices`, `delete_projects`, `view_reports`) to roles or directly to users, allowing dynamic permission assignment from the UI.
* **Why Spatie MediaLibrary?** Uploading files manually using `move()` or `Storage::put()` creates scattered file handling logic. Spatie MediaLibrary binds files directly to Eloquent models using a polymorphic `media` table, automatically managing file conversions, storage disks, and cleanup on model deletion.
* **Publishing Vendor Assets**: When you run `vendor:publish`, Laravel copies package migration files (like `create_permission_tables.php`) and configuration files into your application's `database/migrations/` and `config/` directories so you can run them and customize settings.

---

## Step 2: Database Configuration & Core Migrations

### 2.1 Set Up `.env`
```ini
APP_NAME="Portal System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_portal_db
DB_USERNAME=root
DB_PASSWORD=
```

### 2.2 Define the `users` Table (Internal Employees / Admins)
In `database/migrations/xxxx_xx_xx_create_users_table.php`:
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('image')->nullable();
    $table->string('api_token', 80)->unique()->nullable();
    $table->unsignedBigInteger('staf_profile_id')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

### 2.3 Define the `companies` Table (External Clients)
Run `php artisan make:model Models/Company -m`. In the generated migration:
```php
Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password')->nullable(); // Hashed client login password
    $table->string('contact_person')->nullable();
    $table->string('contact_no')->nullable();
    $table->string('vat_no')->nullable();
    $table->text('location')->nullable();
    $table->text('billing_address')->nullable();
    $table->string('billing_email')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

### 💡 Detailed Explanation for Step 2: Why & How This Works
* **Separate Tables for Separate Entities**: Internal employees and client companies have completely different data lifecycles. Employees have salaries, roles, departments, and attendance. Client companies have VAT registration numbers, trade licenses, billing terms, and shipping addresses. Keeping them in separate tables (`users` vs `companies`) preserves clean database normalization.
* **The `password` and `rememberToken()` in `companies`**: Normally, a CRM stores company details as plain contact data. But because this architecture features a dedicated **Client Portal**, the `companies` table must have an encrypted `password` column and a `remember_token` column so clients can log in securely and maintain persistent sessions.
* **The `api_token` Column in `users`**: Enables stateless API authentication for mobile apps (Flutter) or field web applications (PWA) used by foremen on construction sites.

---

## Step 3: Multi-Guard Authentication Configuration

Edit `config/auth.php` to define the independent guards, user providers, and password reset brokers:

```php
// config/auth.php

'defaults' => [
    'guard' => 'web',       // Default guard for standard internal routes
    'passwords' => 'users',
],

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'company' => [
        'driver' => 'session',
        'provider' => 'companies',
    ],
    'api' => [
        'driver' => 'token',
        'provider' => 'users',
        'hash' => false,
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\User::class, // or App\Models\User::class
    ],
    'companies' => [
        'driver' => 'eloquent',
        'model' => App\Models\Company::class,
    ],
],

'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
    ],
    'companies' => [
        'provider' => 'companies',
        'table' => 'password_resets',
        'expire' => 60,
    ],
],
```

### 💡 Detailed Explanation for Step 3: Why & How This Works
* **What is a Guard?** A guard specifies how the application authenticates requests. 
  - The `session` driver uses cookies and server-side session storage for web browsers.
  - The `token` driver reads an API Bearer token from the HTTP headers for mobile apps.
* **What is a User Provider?** The provider defines how the user entity is retrieved from the database. Here, provider `'users'` fetches records from `App\User`, while provider `'companies'` fetches records from `App\Models\Company`.
* **Session Storage Isolation**: When an internal employee logs in, Laravel stores their ID in the session under key `login_web_<sha1>`. When a client company logs in, Laravel stores their ID under `login_company_<sha1>`. Because these keys are unique, an admin and a client can be logged in at the same time in the same browser without conflicting or kicking each other out.
* **Default Guard**: Setting `'defaults.guard' => 'web'` ensures that generic calls like `Auth::user()` or `middleware('auth')` automatically default to the internal staff guard.

---

## Step 4: Authenticatable Models & Security Traits

### 4.1 `App\User.php` (Internal Staff)
```php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'image', 'api_token', 'staf_profile_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
```

### 4.2 `App\Models\Company.php` (Client Organization)
```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Company extends Authenticatable implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'companies';

    protected $fillable = [
        'name', 'email', 'password', 'contact_person', 'contact_no', 'vat_no', 'location', 'billing_address', 'billing_email',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function quotations()
    {
        return $this->hasMany(\App\Models\Quotation::class, 'company_id', 'id');
    }
}
```

### 💡 Detailed Explanation for Step 4: Why & How This Works
* **Why extend `Authenticatable` instead of `Model`?** By default, Eloquent models extend `Illuminate\Database\Eloquent\Model`. However, Laravel's authentication system requires the model to implement `Illuminate\Contracts\Auth\Authenticatable`. Extending `Illuminate\Foundation\Auth\User as Authenticatable` equips `Company` with authentication methods like `getAuthPassword()`, `getRememberToken()`, and password hashing verification out of the box.
* **The `$hidden` Array**: Whenever an Eloquent model is serialized into JSON (e.g. in an API response or AJAX call), attributes listed in `$hidden` (like `password` and `remember_token`) are automatically stripped, preventing catastrophic credential exposure.
* **The `HasRoles` Trait on `User`**: Connects the `User` model to Spatie's permission database tables. It injects helper methods like `$user->hasRole('Admin')`, `$user->can('create_project')`, and `$user->givePermissionTo(...)`.

---

## Step 5: Multi-Portal Login Controllers

### 5.1 Staff / Admin Login Controller (`LoginController.php`)
```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    // Role-specific post-login routing
    protected function authenticated(Request $request, $user) {
        if ($user->hasRole('AMC Reporter')) {
            return redirect()->route('projects.showForm');
        }
        if ($user->hasRole('Staff Requester')) {
            return redirect()->route('own_requests.index');
        }
    }
}
```

### 5.2 Client Company Login Controller (`CompanyLoginController.php`)
```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyLoginController extends Controller
{
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm() {
        return view('auth.company_login');
    }

    public function login(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Explicitly authenticate against the 'company' guard
        if (Auth::guard('company')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->remember
        )) {
            return redirect()->intended(route('company.dashboard'));
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    public function logout() {
        Auth::guard('company')->logout();
        return redirect('/company/login');
    }
}
```

### 💡 Detailed Explanation for Step 5: Why & How This Works
* **Explicit Guard Call (`Auth::guard('company')->attempt(...)`)**: In `CompanyLoginController`, calling `Auth::guard('company')` tells Laravel: *"Do NOT check the `users` table. Check the `companies` table using the `App\Models\Company` model."*
* **Dynamic Post-Login Redirection (`authenticated()` hook)**: In enterprise portals, different staff members have different jobs. Field inspectors don't need to see the executive revenue dashboard; they need to submit inspection forms immediately. The `authenticated()` method intercepts successful logins and redirects users dynamically based on their Spatie role.
* **Session Destruction on Logout**: In `CompanyLoginController@logout`, calling `Auth::guard('company')->logout()` flushes only the client session cookie. If an internal admin happens to be logged in on another tab, their admin session remains active.

---

## Step 6: Multi-Auth Middleware & Routing Guards

### 6.1 `RedirectIfAuthenticated.php`
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        // If logged in as client company, redirect to company dashboard
        if (Auth::guard('company')->check()) {
            return redirect()->route('company.dashboard');
        }

        // If logged in as staff/admin, redirect to main home
        if (Auth::guard('web')->check()) {
            return redirect('/');
        }

        return $next($request);
    }
}
```

### 6.2 Global Delete Protection Middleware (`CheckDeletePermission.php`)
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;

class CheckDeletePermission
{
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('delete')) {
            $user = Auth::user();
            if (!$user || !$user->can('deletes')) {
                Flash::error("You don't have permission to delete this!");
                return redirect()->back();
            }
        }
        return $next($request);
    }
}
```
Register this in the `'web'` middleware group in `app/Http/Kernel.php`.

### 💡 Detailed Explanation for Step 6: Why & How This Works
* **Why Custom `RedirectIfAuthenticated`?** Default Laravel assumes only one login system. If an authenticated company user visits `/login` or `/company/login`, default Laravel would send them to `/home`, where the `web` guard would fail and bounce them back in an infinite redirect loop. Checking both guards (`company` and `web`) guarantees users land on their respective portal dashboard.
* **Why the Global `CheckDeletePermission` Middleware?** In business software, accidentally deleting invoices, quotations, or projects causes serious accounting discrepancies. Instead of writing deletion authorization checks in 30 different controllers, this middleware catches every HTTP `DELETE` request across the entire system. Unless the employee explicitly holds the `deletes` permission, the request is rejected before touching the controller.

---

## Step 7: Role-Based Access Control (Spatie RBAC)

Create `database/seeds/RolesAndPermissionsSeeder.php`:
```php
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Reset cached roles & permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create granular permissions
        $permissions = [
            'users', 'roles', 'companies', 'quotations',
            'projects', 'invoices', 'tickets', 'deletes'
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // 3. Super-User role (Full system access)
        $superRole = Role::firstOrCreate(['name' => 'Super-User']);
        $superRole->syncPermissions(Permission::all());

        // 4. Staff role (Operational daily tasks only)
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $staffRole->syncPermissions(['companies', 'quotations', 'projects', 'tickets']);

        // 5. Create Default Super Admin Account
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'api_token' => Str::random(60),
        ]);
        $admin->syncRoles(['Super-User']);
    }
}
```

### 💡 Detailed Explanation for Step 7: Why & How This Works
* **Permission Caching (`forgetCachedPermissions`)**: Spatie caches all role/permission mappings in Redis or application memory to avoid querying the database on every page load. Whenever you seed or modify roles/permissions programmatically, you must clear this cache; otherwise, Laravel will evaluate stale permissions.
* **Sync vs Attach (`syncPermissions`)**: Using `syncPermissions()` is idempotent. If a permission is already assigned, it leaves it alone; if new permissions are added, it attaches them; if obsolete ones are removed, it detaches them without throwing MySQL duplicate key errors.
* **Separation between `Super-User` and `Staff`**: Staff members can create quotations and manage projects, but cannot access user accounts, role definitions, or delete buttons.

---

## Step 8: High-Performance Yajra DataTables

### 8.1 Create a DataTable Class
```bash
php artisan datatables:make ProjectDataTable
```

### 8.2 Safe Server-Side Implementation (`ProjectDataTable.php`)
```php
namespace App\DataTables;

use App\Models\Project;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class ProjectDataTable extends DataTable
{
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable
            ->addColumn('action', 'projects.datatables_actions')
            ->addColumn('quotation_link', function ($query) {
                // Defensive programming: prevent 500 error if quotation is null
                if (!$query->quotation) return '-';
                return view('components.datatables_relation_link', [
                    'id' => $query->quotation->id,
                    'name' => $query->quotation->name,
                    'model' => 'quotations'
                ]);
            })
            ->addColumn('company_link', function ($query) {
                // Defensive programming: prevent crash if quotation or company is null
                if (!$query->quotation || !$query->quotation->company) return '-';
                return view('components.datatables_relation_link', [
                    'id' => $query->quotation->company->id,
                    'name' => $query->quotation->company->name,
                    'model' => 'companies'
                ]);
            })
            ->addColumn('contract_value', function ($query) {
                return $query->quotation ? $query->quotation->amount : '-';
            })
            ->rawColumns(['action', 'quotation_link', 'company_link']);
    }

    public function query(Project $model)
    {
        // Eager load relationships to prevent N+1 query performance penalties
        return $model->newQuery()->with(['quotation.company'])->orderBy('id', 'desc');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => ['export', 'reload', 'create'],
            ]);
    }

    protected function getColumns()
    {
        return [
            'date' => new Column(['title' => 'Date', 'data' => 'date']),
            'quotation_id' => new Column(['title' => 'Quotation', 'data' => 'quotation_link']),
            'company_id' => new Column(['title' => 'Company', 'data' => 'company_link']),
            'contract_value' => new Column(['title' => 'Contract Value', 'data' => 'contract_value']),
        ];
    }
}
```

### 8.3 Controller Usage
```php
public function index(ProjectDataTable $projectDataTable)
{
    return $projectDataTable->render('projects.index');
}
```

### 💡 Detailed Explanation for Step 8: Why & How This Works
* **The Dreaded `tn/7 Ajax error` Pitfall**: When DataTables makes an AJAX request, any unhandled PHP Notice or Exception causes Laravel to return an HTTP 500 status code. DataTables catches the 500 error and displays a generic popup (`DataTables warning: Ajax error http://datatables.net/tn/7`). By adding null-safe checks (`if (!$query->quotation) return '-'`), you ensure the table never crashes even if relationships are missing or unassigned.
* **Eager Loading (`with(['quotation.company'])`)**: Without `with()`, displaying 25 rows on a page triggers 50+ extra database queries (the "N+1 problem"). Eager loading fetches all related quotations and companies in a single optimized SQL statement.
* **`rawColumns(['action', ...])`**: By default, Yajra escapes HTML characters (`<`, `>`, `&`) to prevent Cross-Site Scripting (XSS). Adding column names to `rawColumns()` tells Yajra to render HTML buttons and links properly.

---

## Step 9: Routing Structure & Prefix Organization

In `routes/web.php`:

```php
// ============================================
// 1. PUBLIC AUTH
// ============================================
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// ============================================
// 2. CLIENT COMPANY PORTAL (Guard: company)
// ============================================
Route::prefix('company')->group(function () {
    Route::get('/login', 'Auth\CompanyLoginController@showLoginForm')->name('company.login');
    Route::post('/login', 'Auth\CompanyLoginController@login')->name('company.login.submit');
    Route::post('/logout', 'Auth\CompanyLoginController@logout')->name('company.logout');
});

Route::prefix('company')->middleware('auth:company')->group(function () {
    Route::get('/dashboard', 'CompanyHomeController@index')->name('company.dashboard');
    Route::get('/{id}/profile', 'CompanyHomeController@user_profile')->name('company.user_profile');
    Route::put('/{id}/profile', 'CompanyHomeController@update_user_profile')->name('company.update_user_profile');
});

// ============================================
// 3. INTERNAL ADMIN & STAFF PORTAL (Guard: web)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');

    // Administration & Security (RBAC Protected)
    Route::resource('users', 'UserController')->middleware(['can:users']);
    Route::resource('roles', 'RoleController')->middleware(['can:roles']);

    // Operations & Sales
    Route::resource('companies', 'CompanyController');
    Route::resource('quotations', 'QuotationController');
    Route::resource('projects', 'ProjectController');
    Route::resource('invoices', 'InvoiceController');
});
```

### 💡 Detailed Explanation for Step 9: Why & How This Works
* **Prefixing with `Route::prefix('company')`**: Isolates all customer-facing routes under the `/company` URL namespace (`/company/login`, `/company/dashboard`), preventing collision with administrative routes (`/login`, `/dashboard`).
* **Route Middleware `middleware('auth:company')`**: Restricts the entire group to authenticated client sessions. If an unauthenticated user or internal staff member attempts to open `/company/dashboard`, they are rejected.
* **The `can:permission_name` Middleware**: Spatie registers the `can` middleware with Laravel's authorization Gate. Writing `middleware(['can:users'])` automatically checks if the logged-in employee has permission to manage users. If not, Laravel halts execution with `403 Unauthorized`.

---

## Step 10: Client Portal Data Scoping (Tenant Isolation)

In `app/Http/Controllers/CompanyHomeController.php`:
```php
namespace App\Http\Controllers;

use App\DataTables\CompanyCards\CompanyQuotationDataTable;
use App\DataTables\CompanyCards\CompanyInvoiceDataTable;
use Illuminate\Support\Facades\Auth;

class CompanyHomeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:company');
    }

    public function index(CompanyQuotationDataTable $quotationTable, CompanyInvoiceDataTable $invoiceTable)
    {
        return $quotationTable->render('company_home', compact('quotationTable', 'invoiceTable'));
    }

    public function user_profile($id)
    {
        // Enforce identity verification to prevent ID tampering in URLs
        if (Auth::guard('company')->user()->id != $id) {
            abort(404);
        }
        $user = Auth::guard('company')->user();
        return view('companies.profile', compact('user'));
    }
}
```

In the Company DataTables:
```php
public function query(Invoice $model)
{
    $companyId = \Auth::guard('company')->user()->id;

    // Filter strictly by the authenticated company
    return $model->newQuery()
        ->whereHas('quotation', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
}
```

### 💡 Detailed Explanation for Step 10: Why & How This Works
* **Preventing Insecure Direct Object References (IDOR)**: In `user_profile($id)`, an attacker might try changing the URL from `/company/1/profile` to `/company/2/profile` to view another client's trade license or billing information. The condition `Auth::guard('company')->user()->id != $id` immediately terminates the request with `404 Not Found` if the ID does not match the session.
* **Data Isolation in Queries**: In multi-tenant systems, client companies must never see records belonging to competitors. By scoping DataTable queries with `where('company_id', Auth::guard('company')->user()->id)`, the database query engine filters the data at the SQL level before it ever reaches the application layer.

---

## Step 11: Frontend Layouts & Blade Templating

Create two separate layout files in `resources/views/layouts/`:

1. **`layouts.master`**: Includes the admin navigation bar, left sidebar with Spatie `@can` menu links, and dark mode toggles for internal staff.
2. **`layouts.company_layouts.master`**: A streamlined, lightweight layout tailored for client organizations with company profile headers and quotation/invoice cards.

### Example View (`resources/views/projects/index.blade.php`):
```blade
@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Projects Management</h3>
    </div>
    <div class="card-body">
        {!! $dataTable->table(['width' => '100%', 'class' => 'table table-bordered table-striped']) !!}
    </div>
</div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
@endsection
```

### 💡 Detailed Explanation for Step 11: Why & How This Works
* **Why Separate Layouts?** Internal staff need sidebars with 20+ modules (HR, payroll, fleet, procurement, accounts). External customers only need to see their own documents. Giving them separate master layouts guarantees that administrative navigation links, scripts, and sensitive UI elements are never delivered to client browsers.
* **Dynamic Table Rendering (`{!! $dataTable->table() !!}` & `{!! $dataTable->scripts() !!}`)**: Yajra generates the precise HTML table markup and the matching jQuery initialization script (complete with CSRF tokens, AJAX endpoints, column definitions, and button handlers) automatically.

---

## Step 12: Deployment & Run Checklist

Follow this exact terminal command sequence to set up and start the application:

```bash
# 1. Install PHP dependencies
composer install

# 2. Setup application key
php artisan key:generate

# 3. Build database schema and seed default data
php artisan migrate:fresh --seed

# 4. Create public storage symlink/junction
php artisan storage:link

# 5. Clear application caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Start the local server
php artisan serve --port=8000
```

### 💡 Detailed Explanation for Step 12: Why & How This Works
* **`migrate:fresh --seed`**: Drops all existing tables, runs every migration in order, and triggers `DatabaseSeeder` to create your roles, permissions, and admin user.
* **`storage:link`**: Creates an active symbolic link from `public/storage` to `storage/app/public` so files uploaded by users can be accessed via the browser.
* **Cache Clearing**: Laravel caches configuration files, routes, and compiled views for performance. When building or modifying authentication guards and service providers, clearing these caches ensures PHP executes the latest code without caching stale configurations.

---

## 🎯 Verification Test
* Visit `http://127.0.0.1:8000/login` $\rightarrow$ Log in as `admin@example.com` with password `password` (Super-User access).
* Visit `http://127.0.0.1:8000/company/login` $\rightarrow$ Log in as `client@example.com` with password `password` (Client portal access).
