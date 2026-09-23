# FTS Portal — Complete System & Operations Documentation (`documentation.md`)
**A Comprehensive Technical, Architectural, and Functional Manual Based on the Live Platform UI**
*System: Fire Technical Services Management System (`ft_portal_base`)*

---

## 📑 Table of Contents
1. [System Overview & Architecture Core](#1-system-overview--architecture-core)
2. [Executive Dashboard & Real-Time Monitoring Radar](#2-executive-dashboard--real-time-monitoring-radar)
3. [Navbar Menu Walkthrough & Subsystem Workings](#3-navbar-menu-walkthrough--subsystem-workings)
   - [3.1 Operation Menu (Contracting, CRM & Engineering)](#31-operation-menu-contracting-crm--engineering)
   - [3.2 QR Attendance Menu (Field Geofencing & Reports)](#32-qr-attendance-menu-field-geofencing--reports)
   - [3.3 Trade Menu (Procurement & Supply Chain)](#33-trade-menu-procurement--supply-chain)
   - [3.4 Requests Menu (Employee Self-Service Desk)](#34-requests-menu-employee-self-service-desk)
   - [3.5 Receipt / Payment Menu](#35-receipt--payment-menu)
   - [3.6 Payroll Menu](#36-payroll-menu)
   - [3.7 Wallet Menu (Ledgers, Cheques & Petty Cash)](#37-wallet-menu-ledgers-cheques--petty-cash)
   - [3.8 Authentication Menu (RBAC & User Governance)](#38-authentication-menu-rbac--user-governance)
   - [3.9 Payment Bookings Engine (Two-Tier Financial Approval)](#39-payment-bookings-engine-two-tier-financial-approval)
4. [The 12-Step Business Lifecycle (Connecting All Menus)](#4-the-12-step-business-lifecycle-connecting-all-menus)
5. [Security Governance & Defensive Code Standards](#5-security-governance--defensive-code-standards)
6. [Pre-Configured Test Accounts & Team Lead Demo Script](#6-pre-configured-test-accounts--team-lead-demo-script)

---

## 1. System Overview & Architecture Core

The **FTS Portal (`ft_portal_base`)** is an enterprise ERP, CRM, and field operations platform engineered specifically for **Fire Technical Services (FTS)**. It brings together specialized low-current engineering (CCTV surveillance, biometric access control, fire alarm systems, emergency lighting), Annual Maintenance Contracts (AMC), procurement, double-entry accounting, and mobile field teams.

### 1.1 Tri-Guard Multi-Authentication Architecture
Configured in `config/auth.php`, the system enforces a strict tri-guard security model:

```
                      ┌────────────────────────────┐
                      │   Incoming HTTP Request    │
                      └─────────────┬──────────────┘
                                    │
               Route Pattern & Middleware Resolution
                                    │
         ┌──────────────────────────┼──────────────────────────┐
         │ /login                   │ /company/login           │ /api/v1/*
         ▼                          ▼                          ▼
┌──────────────────┐       ┌──────────────────┐       ┌──────────────────┐
│   `web` Guard    │       │ `company` Guard  │       │   `api` Guard    │
├──────────────────┤       ├──────────────────┤       ├──────────────────┤
│ Driver: session  │       │ Driver: session  │       │ Driver: token    │
│ Model: App\User  │       │ Model: Company   │       │ Model: App\User  │
│ Cookie:          │       │ Cookie:          │       │ Header:          │
│ login_web_<hash> │       │ login_co_<hash>  │       │ Bearer <80-char> │
└────────┬─────────┘       └────────┬─────────┘       └────────┬─────────┘
         │                          │                          │
         ▼                          ▼                          ▼
Admin, Staff & Tech      Tenant-Scoped Extranet      Mobile PWA & Muster
(Spatie RBAC System)     where('company_id', $id)    Roll Attendance Engine
```

* **Session & Cookie Isolation:** 
  Internal staff use `login_web_<hash>`, while corporate clients use `login_company_<hash>`. An administrator and a client can work on the same machine in the same browser without logging each other out.
* **Multi-Tenant Scoping:**
  External clients logging into `/company/dashboard` are strictly scoped via database queries: `where('company_id', Auth::guard('company')->id())`. Cross-company visibility is physically impossible.
* **Stateless API Guard:**
  Field engineers and Android/PWA apps communicate with `/api/v1/*` using 80-character cryptographically random tokens stored in `users.api_token`.

---

## 2. Executive Dashboard & Real-Time Monitoring Radar

When logging in as an administrator (`/login`), the system presents the **Executive Command Dashboard**:

### 2.1 The 6 Top KPI Cards
1. **Companies (Cyan):**
   * Total number of corporate clients registered in the database (`App\Models\Company`).
2. **Projects (Green):**
   * Active turnkey installations and ongoing maintenance agreements (`App\Models\Project`).
3. **User Registrations (Yellow):**
   * Total registered internal staff accounts (`App\User`).
4. **Quotations (Blue):**
   * Open commercial proposals waiting for client award or negotiation (`App\Models\Quotation`).
5. **Pending Requests (Red):**
   * Unresolved employee HR requests (annual leave, loans, passport release) awaiting management sign-off.
6. **Pending Invoices (Coral):**
   * Invoices billed to clients that remain unpaid or partially paid (`App\Models\Invoice`).

### 2.2 The 4 Live Radar Tables
* **Receipt Vouchers Table:**
  Logs recent inward payments received from clients via bank transfer, cheque, or cash.
* **Cheques Notifications (PDC Maturity Register):**
  Monitors post-dated cheques (PDCs), their clearance dates, and maturity statuses to prevent cash flow disruptions.
* **Documents Table:**
  Provides centralized access to uploaded organizational files, trade licenses, and technical blueprints.
* **Staff Profiles (HR Compliance Radar):**
  Scans employee records daily. Displays real-time warnings for employee **Passports, UAE Visas, Labor Cards, and Emirates IDs** expiring within 30 days.

---

## 3. Navbar Menu Walkthrough & Subsystem Workings

Every dropdown menu in the top navigation bar represents a dedicated operational subsystem:

```
[Logo: FTS] | Operation ▾ | QR Attendance ▾ | Trade ▾ | Requests ▾ | Receipt / Payment ▾ | Payroll ▾ | Wallet ▾ | Authentication ▾ | (🔔 0) [Admin 👤]
```

---

### 3.1 Operation Menu (Contracting, CRM & Engineering)
*Permission Gate: `@canany(['companies', 'quotations', 'invoices', 'lpoins', 'projects', 'stafprofile'])`*

1. **Companies (`/companies`):**
   * Corporate client registry (`App\Models\Company`). Stores company name, TRN tax number, credit limits, payment terms, and contact persons.
2. **Quotations (`/quotations`):**
   * Commercial estimation engine (`QuotationController`). Compiles itemized Bill of Quantities (BOQ), material catalogs, labor costs, and configurable markups. Generates branded proposal PDFs via DomPDF.
3. **Invoices (`/invoices`):**
   * VAT 201 compliant tax invoices (`InvoiceController`). Automatically generated upon milestone completion or contract signing.
4. **Lpoins (`/lpoins`):**
   * Local Purchase Orders received from clients (`LpoinController`). Validates that a client has officially awarded the quote before project conversion.
5. **Projects (`/projects`):**
   * Project management hub (`ProjectController`). Manages project timelines, assigned site engineers, and milestone deliverables.
6. **AMC Submenu (`/projects/visit-tracking`, `/projects/visit-form`, `/projects/amc-drafts`):**
   * **Visit Tracking:** Real-time status of scheduled maintenance visits.
   * **History:** Complete log of all past AMC visits across sites.
   * **Report Status:** Review queue for AMC reports submitted by field technicians.
   * **Visit Form:** Mobile-friendly inspection checklist for CCTV, fire alarm, and access control systems. Includes touchscreen digital client signature capture (`signature-pad`).
   * **Drafts:** Offline storage for technicians working in basements or dead zones with zero cell service. Data saves locally and syncs once reconnected.
   * **AMC Invoices:** Specialized recurring maintenance billing schedules.
7. **Staff Ratings (`/staff-ratings`):**
   * Performance appraisal module. Tracks monthly performance scores, timelines, and engineer KPIs.
8. **Staff (`/staf`, `/staf-profile/expiry-list`):**
   * Employee profiles, visa/passport expiration register, and leave periods.
9. **Documents (`/document`):**
   * Centralized archive of site survey photos, engineering handover certificates, and contracts.
10. **Task (`/task`):**
    * Internal delegation desk for technical tasks and engineer follow-ups.
11. **Products (`/products`):**
    * Master materials and low-current hardware catalog (cameras, smoke detectors, patch panels, cables).
12. **Tickets (`/tickets`, `/tickets/status`):**
    * Client support tickets and emergency maintenance dispatch tracker.
13. **Letters (`/letters`):**
    * Formal communication letters (NOCs, handover documents, consultant letters).
14. **Vendor Orders (`/orders`):**
    * Purchase requests sent to suppliers.
15. **Labor System (`/labor-system`, `/labor-system/assignments`):**
    * Technician muster rolls, daily labor allocations, and overtime tracking.
16. **Drawing Receiveds (`/drawing-receiveds`):**
    * Architectural blueprints and shop drawing revision control (`DrawingReceivedController`). Tracks revisions (Rev A, Rev B) and consultant review turnaround times.

---

### 3.2 QR Attendance Menu (Field Geofencing & Reports)
*Permission Gate: `@can('labor_attendance')`*

1. **Scan QR (`/qr-attendance`):**
   * Designed for field technicians on mobile phones.
   * Technician scans the dynamic QR code displayed at the project site.
   * The browser captures device latitude and longitude.
   * **Backend Geofence Math (`QRAttendanceController.php`):**
     The server uses the **Haversine Distance Formula** to measure distance to the project site:
     $$\text{Distance} = 2 R \cdot \arcsin \left( \sqrt{ \sin^2\left(\frac{\Delta \text{lat}}{2}\right) + \cos(\text{lat}_1)\cos(\text{lat}_2)\sin^2\left(\frac{\Delta \text{lon}}{2}\right) } \right)$$
   * **Evaluation Outcome:**
     * If $\text{Distance} \le 500\text{m}$: Punch accepted (`HTTP 200 OK`), timestamp and coordinates logged.
     * If $\text{Distance} > 500\text{m}$: Punch blocked (`HTTP 422 Unprocessable Entity - Outside boundary`).
2. **Attendance Report (`/qr-attendance/report/all`):**
   * Displays full muster rolls, check-in/out timestamps, audit coordinates, and overtime hours.

---

### 3.3 Trade Menu (Procurement & Supply Chain)
*Permission Gate: `@canany(['vendors', 'paymentInvoices', 'lpoouts'])`*

1. **Vendors (`/vendors`):**
   * Master supplier registry (`VendorController`). Stores trade licenses, TRN, bank account numbers, and credit terms (COD, 30 days, 60 days).
2. **Payment_invoices (`/paymentInvoices`):**
   * Incoming vendor bills against delivered goods and materials.
3. **Lpoouts (`/lpoouts`):**
   * Outgoing Local Purchase Orders issued to suppliers (`LpooutController`). Includes revision tracking when prices or quantities change.
4. **Purchase Orders Submenu (`/purchase-orders`):**
   * **PO Request:** Field engineer requests materials against a specific project cost center.
   * **Department Review:** Technical manager reviews quantity and specifications.
   * **Admin Approval:** Executive authorizes final issuance to supplier.
5. **Vendor Payables (`/vendor-payables`):**
   * Accounts payable ledger tracking outstanding liabilities owed to vendors.
6. **Payment Analytics (`/payment-analytics`):**
   * Graphical spending analytics grouped by vendor, project cost center, and equipment category.

---

### 3.4 Requests Menu (Employee Self-Service Desk)
*Permission Gate: `@canany(['invoiceRequests', 'laborRequests', 'staffRequests', 'requestForms'])`*

1. **Invoice_Requests (`/invoiceRequests`):**
   * Project supervisors request milestone billings when on-site deliverables are completed.
2. **Labor Requests (`/laborRequests`):**
   * Site supervisors request extra manpower or specialized subcontractors for critical project phases.
3. **Staff Requests (`/staff_requests` or `/own-staff-request`):**
   * Employee self-service portal:
     * Annual leave applications
     * Salary advance requests
     * Passport release applications
     * Tool and test equipment requisitions (e.g., OTDR meters)
4. **Request_Forms (`/requestForms`):**
   * Standardized templates that route submissions into the management approval queue (`/request-approvals`).

---

### 3.5 Receipt / Payment Menu
*Permission Gate: `@canany(['receipts', 'payments'])`*

1. **Receipts (`/receipts`):**
   * Inward cash/cheque settlements from clients (`ReceiptController`). Automatically matches against open tax invoices and updates accounts receivable.
2. **Payments (`/payments`):**
   * Direct payment register for completed disbursements and supplier liquidations.

---

### 3.6 Payroll Menu
*Permission Gate: `@can('payroll')`*

1. **Staff Payroll (`/staffPayrolls`):**
   * Monthly salary processing module (`StaffPayrollController`).
   * Automatically calculates net pay by combining basic salary, approved overtime hours from QR attendance muster rolls, and subtracting approved salary advance deductions.

---

### 3.7 Wallet Menu (Ledgers, Cheques & Petty Cash)
*Permission Gate: `@canany(['accounts', 'cheques', 'pettyCashes'])`*

1. **Accounts (`/accounts`):**
   * Complete double-entry Chart of Accounts (`AccountController`, `BalanceManager`).
   * Manages Assets, Liabilities, Equity, Revenue, and Expenses.
   * Generates real-time Trial Balance and VAT 201 audit statements.
2. **Cheques (`/cheques`):**
   * PDC Management register. Tracks post-dated cheques issued to suppliers or received from clients, clearance dates, and bank reconciliation.
3. **Petty_Cashes (`/pettyCashes`):**
   * Custodian cash float management for emergency site purchases and technician incidentals.
4. **Petty_Cash_Expenses (`/pettyCashExpenses`):**
   * Itemized petty cash expense receipts linked to project cost centers and VAT receipts.

---

### 3.8 Authentication Menu (RBAC & User Governance)
*Permission Gate: `@canany(['roles', 'users', 'employees'])`*

1. **Roles (`/roles`):**
   * Spatie Role-Based Access Control (`RoleController`). Defines system roles (`Super-User`, `Staff`, `AMC Reporter`, `Payment Booking Approver`) and assigns permissions.
2. **Users (`/users`):**
   * User login credentials manager (`UserController`). Handles email, password, and generates 80-character API bearer tokens for mobile devices.
3. **Employees (`/employees`):**
   * Master human resources directory. Manages employment contracts, designations, joining dates, and emergency contacts.

---

### 3.9 Payment Bookings Engine (Two-Tier Financial Approval)
*Direct Top-Level Menu / Route: `/payment-bookings`*

Financial disbursements (cheques or cash) undergo strict fiduciary controls via `PaymentBookingService`:

```
                  ┌──────────────┐
                  │  0: DRAFT    │ (Accountant creates voucher)
                  └──────┬───────┘
                         │ Submit
                         ▼
             ┌─────────────────────────┐
             │ 1: PENDING VERIFICATION │ (Level 1 Review Queue: verify_payment_bookings)
             └──────┬────────────┬─────┘
       L1 Verify │            │ L1 Reject / Hold
                 ▼            │
     ┌──────────────────────┐ │
     │  2: PENDING APPROVAL │ │ (Level 2 Review Queue: approve_payment_bookings)
     └──────┬────────────┬──┘ │
  L2 Approve│            │    │
            ▼            │ L2 Reject / Hold
     ┌──────────────┐    │    │
     │  3: APPROVED │    ▼    ▼
     └──────────────┘  ┌──────────────┐
                       │ 4: REJECTED  │ (Re-submitting resets back to Level 1)
                       │ 5: ON HOLD   │ (Clarification requested)
                       └──────────────┘
```

#### Strict Fiduciary Rules:
1. **The Two-Person Rule:** A single user **CANNOT** approve both Level 1 and Level 2. If an executive user has both permissions, approving Level 1 automatically hides that voucher from their Level 2 queue.
2. **Sequential Enforcement:** Level 2 cannot approve a voucher until Level 1 has verified it. Bypassing returns `HTTP 403 Forbidden`.
3. **Liquidity Release Math:** Funds are counted as officially disbursed **only** when:
   $$\text{Status} == 3 \quad \text{AND} \quad \text{Effective Date} \le \text{Current Date}$$

---

## 4. The 12-Step Business Lifecycle (Connecting All Menus)

This lifecycle demonstrates how all menus work together across a contracting project:

| Step | Phase | Relevant Navbar Menu | Controller Action | Business Outcome |
| :--- | :--- | :--- | :--- | :--- |
| **1** | **Lead Intake** | `Operation` $\rightarrow$ Inquiries | `InquiryController@store` | Commercial inquiry logged with scope (Contracting vs AMC). |
| **2** | **Site Survey** | `Operation` $\rightarrow$ Inquiries | `InquiryController@engineerReport` | Engineer records site dimensions, cable paths, containment photos. |
| **3** | **Quotation** | `Operation` $\rightarrow$ Quotations | `QuotationController@store` | Estimator builds BOQ, sets margins, generates branded DomPDF. |
| **4** | **Client Award** | Client Extranet Portal | `CompanyHomeController@index` | Client logs into extranet, reviews quote, issues official `LPOin`. |
| **5** | **Active Project** | `Operation` $\rightarrow$ Projects | `ProjectController@store` | Quote converts to project; supervisor and milestones assigned. |
| **6** | **Blueprints** | `Operation` $\rightarrow$ Drawing Receiveds | `DrawingReceivedController@store` | Shop drawings submitted; consultant review turnaround tracked. |
| **7** | **Procurement** | `Trade` $\rightarrow$ Purchase Orders | `PurchaseOrderController@adminApprove` | Material requisitions approved; official `LPOout` issued to vendor. |
| **8** | **AMC Visits** | `Operation` $\rightarrow$ AMC | `ProjectReportController@store` | Tech performs checklist, saves offline draft, gets client signature. |
| **9** | **Tax Invoice** | `Operation` $\rightarrow$ Invoices | `InvoiceController@store` | VAT 201 compliant tax invoice generated and emailed to client. |
| **10** | **Receipt** | `Receipt / Payment` $\rightarrow$ Receipts | `ReceiptController@store` | Client payment logged (Bank Wire / Cheque PDC). |
| **11** | **Disbursement** | `Payment Bookings` | `PaymentBookingController@approve` | Cheque/cash voucher passes Level 1 Verify and Level 2 Approve. |
| **12** | **General Ledger** | `Wallet` $\rightarrow$ Accounts | `AccountController@index` | Released funds automatically post balanced double-entry journals. |

---

## 5. Security Governance & Defensive Code Standards

1. **Dynamic RBAC Route Interception (`RolesAuth` Middleware):**
   * Permissions map directly to route names (e.g., `projects.index` requires `projects`, `quotations.create` requires `quotations`).
2. **Global Delete Protection (`CheckDeletePermission` Middleware):**
   * Any HTTP `DELETE` request across the application is intercepted. Unless the user explicitly holds the `deletes` permission, deletion is rejected.
3. **Defensive Null-Safety in Yajra DataTables:**
   * Prevents AJAX 500 crashes when relationship records are missing or null:
     ```php
     ->addColumn('company_link', function ($query) {
         if (!$query->quotation || !$query->quotation->company) return '-';
         return view('components.datatables_relation_link', [
             'id' => $query->quotation->company->id,
             'name' => $query->quotation->company->name,
             'model' => 'companies'
         ]);
     })
     ```
4. **Anti-IDOR Stealth 404 Responses:**
   * Unauthorized URL access attempts to client resources return `HTTP 404 Not Found` instead of `403 Forbidden` to prevent resource enumeration attacks.

---

## 6. Pre-Configured Test Accounts & Team Lead Demo Script

### 6.1 Test Credentials Directory

| Role | Target Audience | Login URL | Email | Password | Access Capabilities |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | Executive & IT Admin | `/login` | `admin@example.com` | `password` | Full access, RBAC governance, L2 payment approvals, Chart of Accounts |
| **Internal Staff** | Sales, Estimators, Engineers | `/login` | `staff@example.com` | `password` | Inquiries CRM, commercial quotes, projects, vendor LPOs |
| **Technician / User** | Site Techs & Field Staff | `/login` | `user@user.com` | `password` | AMC visit forms, offline drafts, self-service leave/salary advance requests |
| **Client Portal** | Corporate Client Extranet | `/company/login` | `client@example.com` | `password` | Scoped client extranet: review quotes, download VAT invoices, submit LPOin |

### 6.2 5-Minute Team Lead Demo Script

1. **Demonstrate Session Isolation:**
   * Open Chrome: log into `/company/login` using `client@example.com`.
   * Open Edge/Chrome Incognito: log into `/login` using `admin@example.com`.
   * **Show:** Both sessions operate in parallel with zero cookie collisions.
2. **Demonstrate the Operation Flow:**
   * Go to `Operation` $\rightarrow$ `Quotations` $\rightarrow$ open a quotation and generate the branded PDF.
   * Switch to Client window $\rightarrow$ show that the quote appears live on the client dashboard.
3. **Demonstrate Field Tech Capabilities:**
   * Log into `/login` using `user@user.com`.
   * Notice landing on `AMC Visit Form` (`/projects/visit-form`).
   * Show the touchscreen signature canvas and explain offline draft storage (`/projects/amc-drafts`) for basement work.
4. **Demonstrate Fiduciary Controls in Payment Bookings:**
   * Go to `Payment Bookings` $\rightarrow$ show a voucher in `Pending Verification`.
   * Explain that the user who performs Level 1 verification is blocked from performing Level 2 approval (The Two-Person Rule).
   * Show that funds post to `Wallet` $\rightarrow$ `Accounts` only when approved and effective date arrives.
