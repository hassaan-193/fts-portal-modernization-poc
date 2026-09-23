# Repository Map & Architecture Inventory

A comprehensive directory and symbol inventory mapping all major subsystems across `ft_portal_base`.

---

## 1. Directory Structure Overview

```
ft_portal_base/
├── app/
│   ├── Console/Commands/       # Artisan jobs (PDC alert emails, project archival)
│   ├── DataTables/             # Yajra server-side DataTable definitions
│   ├── Http/
│   │   ├── Controllers/        # Web MVC Controllers (Admin, Staff, Company, User)
│   │   │   ├── API/            # REST API Controllers (Attendance, Sites, Bookings)
│   │   │   └── Company/        # Client Extranet Controllers (isolated company guard)
│   │   ├── Middleware/         # Auth, RolesAuth, CheckDeletePermission, Guard checks
│   │   └── Requests/           # FormRequest validation rules per resource
│   ├── Models/                 # Eloquent domain entities (Company, Project, Quotation, etc.)
│   ├── Repositories/           # Data access & persistence logic
│   └── Services/               # Domain workflow engines (PaymentBooking, BalanceManager)
├── config/                     # Core configs (auth.php, permission.php, filesystems.php)
├── database/
│   ├── migrations/             # 131 migration files defining relational schema
│   └── seeds/                  # PermissionSeeder, RoleSeeder, Initial database seeders
├── doc1/                       # Complete project documentation suite
├── public/                     # Web root, compiled CSS/JS assets, storage symlink
├── resources/
│   ├── views/                  # Blade templates (AdminLTE 3 layouts, module CRUD views)
│   ├── js/                     # Vue 2 and vanilla frontend scripts
│   └── sass/                   # SCSS stylesheets
└── routes/
    ├── web.php                 # Primary web routes (Admin, Staff, User, Company portals)
    └── api.php                 # Token-authenticated REST API routes (v1)
```

---

## 2. Key Controllers & Endpoints

| Controller | Guard / Auth | Primary Responsibility |
|---|---|---|
| `App\Http\Controllers\HomeController` | `auth` (`web`) | Admin executive dashboard, compliance radar, overdue alerts |
| `App\Http\Controllers\CompanyHomeController` | `auth:company` | Client portal dashboard, customer profile, scoped statistics |
| `App\Http\Controllers\ProjectController` | `auth` (`web`) | Project management, AMC scheduling, service visits |
| `App\Http\Controllers\QuotationController` | `auth` (`web`) | Sales proposals, line-item BOQs, DomPDF generation |
| `App\Http\Controllers\InvoiceController` | `auth` (`web`) | Tax invoices, milestone billing, double-entry ledger hooks |
| `App\Http\Controllers\PaymentBookingController` | `auth` (`web`) | Portal cheque/cash booking and sequential review UI |
| `App\Http\Controllers\API\PaymentBooking\PaymentBookingController` | `auth:api` | Mobile REST endpoints for payment booking lifecycle |
| `App\Http\Controllers\API\Attendance\ForemanAttendanceController` | `auth:api` | Foreman daily labor attendance submission |
| `App\Http\Controllers\API\Attendance\QRAttendanceController` | `auth:api` | QR scan check-in and GPS geofence validation |

---

## 3. Key Models & Relational Connections

* `App\User`: System personnel, authenticated via `web` and `api`. Belongs to Spatie roles.
* `App\Models\Company`: Corporate clients, authenticated via `company` guard. Owns Projects, Quotations, and Invoices.
* `App\Models\Project`: Active contracting job or AMC contract. Belongs to `Company`, optionally linked to originating `Quotation`.
* `App\Models\Quotation`: Commercial proposal. Has many `QuotationItem` records. Belongs to `Company`.
* `App\Models\Invoice`: Tax invoice. Has many `InvoiceItem` records. Hooks into `Illuminatech\Balance` ledger transactions.
* `App\Models\PaymentBooking`: Cheque and cash expenditures. Has many `PaymentBookingApproval` review entries.
