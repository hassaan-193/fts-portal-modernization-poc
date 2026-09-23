# FTS Portal — Master System & Operations Guide
**Technical, Architectural, and Operational Manual for Team Leads and Engineers**
*Repository: `ft_portal_base` | Target Audience: Team Leads, System Architects, Full-Stack Developers*

---

## Table of Contents
1. [Executive Summary & System Purpose](#1-executive-summary--system-purpose)
2. [Tri-Guard Multi-Authentication Architecture](#2-tri-guard-multi-authentication-architecture)
3. [The 12-Step End-to-End Operational Lifecycle](#3-the-12-step-end-to-end-operational-lifecycle)
4. [Deep Dive: Core Subsystems & Technical Workings](#4-deep-dive-core-subsystems--technical-workings)
   - [4.1 Payment Bookings Two-Tier Approval State Machine](#41-payment-bookings-two-tier-approval-state-machine)
   - [4.2 Field Operations: Geofenced QR Attendance Engine](#42-field-operations-geofenced-qr-attendance-engine)
   - [4.3 AMC Field Inspections & Offline State Handling](#43-amc-field-inspections--offline-state-handling)
   - [4.4 Commercial Quotations & Dynamic BOQ Engine](#44-commercial-quotations--dynamic-boq-engine)
   - [4.5 HR Document Compliance Radar & Self-Service](#45-hr-document-compliance-radar--self-service)
5. [Security Governance & Defensive Engineering](#5-security-governance--defensive-engineering)
6. [Live Testing Directory & Role Verification](#6-live-testing-directory--role-verification)
7. [Team Lead Walkthrough & Demo Script](#7-team-lead-walkthrough--demo-script)

---

## 1. Executive Summary & System Purpose

The **FTS Portal (`ft_portal_base`)** is an enterprise-grade ERP, CRM, and Field Service Operations platform engineered specifically for **Fire Technical Services (FTS)**. 

### What Core Problem Does It Solve?
Specialized contracting companies face massive communication breakdowns between field engineers, sales estimators, procurement officers, accountants, and corporate clients. Usually, these teams operate in separate tools (Excel sheets, paper site surveys, WhatsApp attendance, accounting software). 

FTS Portal eliminates data fragmentation by unifying all six critical business dimensions into a single database:
1. **CRM & Lead Triage:** Inquiries, technical field survey logging, cable containment notes.
2. **Turnkey Estimation & AMC:** Bill of Quantities (BOQ), margin markups, automatic DomPDF generation.
3. **Engineering Blueprint Governance:** Milestone tracking, shop drawing revisions, consultant review tracking.
4. **Supply Chain & Procurement:** Vendor master registry, local purchase orders (LPOouts), multi-level approvals.
5. **Double-Entry Financial Ledgers:** VAT 201 tax invoices, two-level payment approvals, PDC maturity registers.
6. **Mobile Field Operations:** Geofenced QR attendance, offline AMC inspection forms with digital touchscreen signatures.

---

## 2. Tri-Guard Multi-Authentication Architecture

To isolate internal staff from external clients and mobile background processes, the application enforces a **Tri-Guard Authentication Architecture** configured in `config/auth.php`.

```
                  ┌──────────────────────────────────────────────┐
                  │            Incoming HTTP Request             │
                  └──────────────────────┬───────────────────────┘
                                         │
                   Route Pattern & Middleware Resolution
                                         │
        ┌────────────────────────────────┼───────────────────────────────┐
        │ Route: /login                  │ Route: /company/login         │ Route: /api/v1/*
        ▼                                ▼                               ▼
┌──────────────────┐            ┌──────────────────┐            ┌──────────────────┐
│   `web` Guard    │            │ `company` Guard  │            │   `api` Guard    │
├──────────────────┤            ├──────────────────┤            ├──────────────────┤
│ Driver: session  │            │ Driver: session  │            │ Driver: token    │
│ Model: App\User  │            │ Model: Company   │            │ Model: App\User  │
│ Cookie:          │            │ Cookie:          │            │ Auth Header:     │
│ login_web_<hash> │            │ login_co_<hash>  │            │ Bearer <80-char> │
└────────┬─────────┘            └────────┬─────────┘            └────────┬─────────┘
         │                               │                               │
         ▼                               ▼                               ▼
Spatie RBAC Routing            Tenant-Scoped Extranet           Stateless Field Engine
(Admin / Staff / Tech)         where('company_id', $id)         (Muster Rolls / QR GPS)
```

### Key Architectural Takeaways for the Team Lead:
1. **Cookie Namespacing & Session Isolation:**
   - Internal employees authenticate with session cookie `login_web_<hash>`.
   - External corporate clients authenticate with `login_company_<hash>`.
   - **Impact:** An administrator and a client can be logged into the same browser simultaneously on the same machine without session collision or logout issues.
2. **Database-Level Multi-Tenant Scoping:**
   - Client portal controllers strictly enforce `where('company_id', Auth::guard('company')->id())`. Corporate clients are physically incapable of viewing another client’s quotes, invoices, or projects.
3. **Stateless API Bearer Authentication:**
   - Mobile and PWA field endpoints authenticate via 80-character cryptographically secure bearer tokens stored on `users.api_token`.

---

## 3. The 12-Step End-to-End Operational Lifecycle

The heartbeat of the system is an integrated 12-step operational pipeline where data flows smoothly from first contact to ledger entry without manual re-keying:

```
[1. Lead Intake] ──► [2. Site Survey] ──► [3. Commercial Quote] ──► [4. Client Award (LPOin)]
                                                                           │
                                                                           ▼
[8. AMC / Field Visit] ◄── [7. Procurement LPOs] ◄── [6. Shop Drawings] ◄── [5. Active Project]
         │
         ▼
[9. VAT Tax Invoice] ──► [10. Inward Receipt] ──► [11. 2-Tier Payment] ──► [12. General Ledger]
```

### Detailed Breakdown of Each Step:

#### Step 1: Commercial Lead Intake
* **Route:** `/inquiries`
* **Controller:** `InquiryController`
* **Model:** `App\Models\Inquiry`
* **What Happens:** New leads are logged from phone, email, or client portals. Scope is classified into **Turnkey Contracting** or **Recurring AMC**. Value and priority are assigned.
* **Handoff:** Technical manager assigns a Field Engineer to perform a site visit.

#### Step 2: Technical Field Site Survey
* **Route:** `/inquiries/{id}/engineer/report`
* **Controller:** `InquiryController@engineerReport`
* **What Happens:** The assigned engineer visits the premises, measures cable routes, inspects physical containment, checks power distribution points, and uploads site photos.
* **Handoff:** Status updates to "Survey Completed"; sales estimator is notified.

#### Step 3: Commercial Estimation & BOQ Generation
* **Route:** `/quotations/create`
* **Controller:** `QuotationController`
* **Models:** `App\Models\Quotation`, `App\Models\QuotationItem`
* **What Happens:** Estimator pulls catalog equipment, enters custom items, sets markup percentages, and adds labor costs. System calculates subtotal, 5% UAE VAT, and net profit.
* **Output:** Generates a branded client proposal PDF via DomPDF (`/quotations/{id}/pdf`).

#### Step 4: Client Award & LPOin Issuance
* **Route:** `/company/dashboard`
* **Controller:** `CompanyHomeController`
* **What Happens:** Corporate client logs into the Extranet, reviews the commercial proposal, and uploads an official Purchase Order (`LPOin`).
* **Handoff:** Accepted quotation triggers project conversion.

#### Step 5: Project Conversion & Supervisor Allocation
* **Route:** `/projects`
* **Controller:** `ProjectController`
* **Models:** `App\Models\Project`, `App\Models\ProjectMilestone`
* **What Happens:** Project is created from the accepted quote. A Project Supervisor (Engineer) is assigned, and contract milestones are scheduled.

#### Step 6: Shop Drawings & Consultant Approvals
* **Route:** `/drawing-receiveds`
* **Controller:** `DrawingReceivedController`
* **Model:** `App\Models\DrawingReceived`
* **What Happens:** Architectural low-current blueprints and shop drawings are submitted to the client’s engineering consultant. System tracks revision revisions (Rev A, Rev B) and turnaround times.

#### Step 7: Procurement & Supplier Local Purchase Orders (LPOout)
* **Route:** `/purchase-orders`, `/lpoouts`
* **Controllers:** `PurchaseOrderController`, `LpooutController`
* **Models:** `App\Models\PurchaseOrder`, `App\Models\Lpoout`, `App\Models\Vendor`
* **What Happens:** Material requisitions are created against the project cost center. Once approved through executive review, official supplier purchase orders (`LPOout`) with terms (COD, 30-day, or 60-day PDC) are dispatched to vendors.

#### Step 8: AMC Inspections & Field Reporting
* **Route:** `/projects/visit-form`, `/projects/amc-drafts`
* **Controller:** `ProjectReportController`
* **Models:** `App\Models\VisitSchedule`, `App\Models\ProjectReport`
* **What Happens:** Field technicians conduct preventive maintenance checks on CCTV, fire alarms, and access control. Works offline in basements; captures digital signature on glass; auto-emails PDF report to client.

#### Step 9: VAT 201 Compliant Tax Invoicing
* **Route:** `/invoices`
* **Controller:** `InvoiceController`
* **Model:** `App\Models\Invoice`
* **What Happens:** Milestone completion triggers VAT 201 compliant tax invoice generation with TRN numbers, itemized supply dates, subtotal, and tax breakdowns. Automatically sent to client billing contacts.

#### Step 10: Inward Client Payment Receipts
* **Route:** `/receipts`
* **Controller:** `ReceiptController`
* **Model:** `App\Models\Receipt`
* **What Happens:** Client settlements are recorded via Bank Wire, Cash, or Post-Dated Cheque (PDC). Cheques enter a clearance register with automated maturity alerts.

#### Step 11: Outward Payment Bookings Review (2-Tier Approval)
* **Route:** `/payment-bookings`
* **Controller:** `PaymentBookingController`
* **Model:** `App\Models\PaymentBooking`, `App\Models\PaymentBookingApproval`
* **What Happens:** Expense disbursements (cheque or cash) must pass **Level 1 (Verification)** by an accountant and **Level 2 (Approval)** by an executive. Enforces the strict Two-Person Rule.

#### Step 12: General Ledger & Trial Balance Posting
* **Route:** `/accounts`
* **Controllers:** `AccountController`, `BalanceManager`
* **Models:** `App\Models\Account`, `App\Models\BalanceTransaction`
* **What Happens:** Cleared receipts and approved payment bookings automatically post balanced debit/credit entries to the chart of accounts, generating live trial balances and VAT reports.

---

## 4. Deep Dive: Core Subsystems & Technical Workings

### 4.1 Payment Bookings Two-Tier Approval State Machine

Financial security is enforced by `PaymentBookingService` through a strict 6-state machine:

```
                  ┌──────────────┐
                  │  0: DRAFT    │
                  └──────┬───────┘
                         │ Submit
                         ▼
             ┌─────────────────────────┐
             │ 1: PENDING VERIFICATION │◄──────────────┐
             └──────┬────────────┬─────┘               │
       L1 Verify │            │ L1 Reject/Hold        │
                 ▼            │                       │
     ┌──────────────────────┐ │                       │
     │  2: PENDING APPROVAL │ │                       │
     └──────┬────────────┬──┘ │                       │
  L2 Approve│            │    │                       │
            ▼            │ L2 Reject/Hold             │ Re-submit
     ┌──────────────┐    │    │                       │ restarts from L1
     │  3: APPROVED │    ▼    ▼                       │
     └──────────────┘  ┌──────────────┐               │
                       │ 4: REJECTED  ├───────────────┘
                       │ 5: ON HOLD   │
                       └──────────────┘
```

#### Fiduciary Rules Enforced in Code:
1. **The Two-Person Rule (`PaymentBookingService.php`):**
   - A single person **CANNOT** approve both Level 1 and Level 2 on the same voucher.
   - If an executive user possesses both permissions (`verify_payment_bookings` AND `approve_payment_bookings`), approving Level 1 automatically hides that voucher from their Level 2 queue.
2. **Sequential Gatekeeping:**
   - Level 2 approval cannot be executed if status is not `1` (cleared by L1). Any bypass attempt returns `403 Forbidden`.
3. **Liquidity Release Formula:**
   - Money is mathematically treated as "Released / Paid Out" **only** when:
     $$\text{Status} == 3 \quad \text{AND} \quad \text{Effective Date} \le \text{Current Date}$$
   - *For Cheques:* `effective_date = release_date` (PDC maturity).
   - *For Cash:* `effective_date = payment_date`.

---

### 4.2 Field Operations: Geofenced QR Attendance Engine

To prevent "buddy punching" and fake timecard entries at remote project sites, the system replaces physical biometric devices with GPS-geofenced QR codes:

```
[1. Display Site QR] ──► [2. Mobile Scan] ──► [3. Server Haversine Math] ──► [4. Radius Check]
  Contains signed site      Phone captures GPS     Calculates exact distance   Distance <= 500m?
  token & coordinates       (Lat, Long, Accuracy)  in meters to site center     ├─► [PASS] 200 OK
                                                                                └─► [FAIL] 422 Error
```

#### Technical Implementation (`QRAttendanceController.php`):
* **The Haversine Distance Formula:**
  ```php
  $earthRadius = 6371000; // meters
  $latDelta = deg2rad($deviceLat - $siteLat);
  $lonDelta = deg2rad($deviceLon - $siteLon);

  $a = sin($latDelta / 2) * sin($latDelta / 2) +
       cos(deg2rad($siteLat)) * cos(deg2rad($deviceLat)) *
       sin($lonDelta / 2) * sin($lonDelta / 2);

  $distance = $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
  ```
* **Perimeter Evaluation:**
  - If `$distance <= $site->radius` (default: 500m): Attendance record is created with timestamps and GPS audit coordinates (`HTTP 200 OK`).
  - If `$distance > $site->radius`: The punch is rejected, security alert logged (`HTTP 422 Unprocessable Entity - Device outside boundary`).

#### Daily Foreman Muster Roll:
1. Site Foreman submits crew attendance at `/api/v1/attendance/submit`.
2. Project Manager inspects regular vs overtime hours at `/api/v1/attendance/pending`.
3. Verified hours post directly to the payroll register.

---

### 4.3 AMC Field Inspections & Offline State Handling

Field technicians frequently work in basement pump rooms or shielded communication centers with zero mobile signal.

#### The Offline Draft Protocol (`/projects/amc-drafts`):
1. **Local State Preservation:** Technicians fill out equipment checklists (CCTV, Access Control, Fire Alarm panels) in the browser/app. If network drops, data saves to local browser storage (`IndexedDB` / `localStorage`).
2. **Re-sync On Reconnect:** As soon as network connectivity is restored, the technician clicks "Sync Offline Drafts".
3. **Touchscreen Client Sign-Off:** The building facility manager inspects the completed form and signs directly on the technician’s device using an integrated HTML5 canvas (`signature-pad`).
4. **Automated Dispatch:** Submission automatically compiles a signed PDF report via DomPDF and emails it to the registered client contact.

---

### 4.4 Commercial Quotations & Dynamic BOQ Engine

The estimation system (`/quotations`) accommodates two distinct contracting types:
1. **Turnkey Installation:**
   - Adds master catalog hardware (cameras, sensors, panels).
   - Custom installation line items with individual markup percentages.
   - Labor cost allocations.
2. **Annual Maintenance Contracts (AMC):**
   - Equipment inventory listing.
   - Frequency of preventive visits (Monthly, Quarterly, Bi-Annually).
   - Emergency response SLA terms.

*Formula for Totals:*
$$\text{Net Amount} = \sum (\text{Quantity} \times \text{Unit Price} \times (1 + \text{Markup})) + \text{Labor}$$
$$\text{VAT Amount} = \text{Net Amount} \times 0.05 \quad (5\% \text{ UAE VAT})$$
$$\text{Total Amount} = \text{Net Amount} + \text{VAT Amount}$$

---

### 4.5 HR Document Compliance Radar & Self-Service

* **Compliance Radar (`HomeController.php`):** Scans all staff profiles daily. If any employee's Passport, UAE Visa, Labor Card, or Emirates ID is within **30 days** of expiration, an amber/red alert is pinned directly to the executive dashboard.
* **Employee Self-Service Desk (`/own-staff-request`):**
  - Employees submit paperless requests for: Annual Leave, Salary Advances, Passport Releases, or Equipment Requisitions.
  - Requests flow through a two-person management sign-off queue (`/request-approvals`).

---

## 5. Security Governance & Defensive Engineering

### 5.1 Dynamic RBAC via Route Prefix Interception (`RolesAuth` Middleware)
Instead of manually hardcoding permission checks in every controller method, the system dynamically inspects the active route name:
* Route `projects.index` $\rightarrow$ requires permission `projects`.
* Route `quotations.create` $\rightarrow$ requires permission `quotations`.
* Route `invoices.destroy` $\rightarrow$ requires permission `invoices`.

### 5.2 Global Deletion Protection (`CheckDeletePermission` Middleware)
Any HTTP `DELETE` request across the entire system is globally intercepted. Unless the authenticated user explicitly possesses the specific permission `deletes`, the deletion request is blocked immediately with an unauthorized error.

### 5.3 Defensive Null-Safety in Yajra DataTables
To eliminate server-side DataTables AJAX 500 crashes caused by deleted or null relational foreign keys, relationship columns use defensive fallback rendering:
```php
->addColumn('quotation_link', function ($query) {
    if (!$query->quotation) return '-';
    return view('components.datatables_relation_link', [
        'id' => $query->quotation->id,
        'name' => $query->quotation->name,
        'model' => 'quotations'
    ]);
})
```

### 5.4 Anti-IDOR via Stealth 404 Responses
If a client company attempts to tamper with URL parameters to view another company’s profile or invoice (`/company/99/profile`), the controller returns `HTTP 404 Not Found` rather than `403 Forbidden`. This hides the existence of the resource, defeating enumeration attacks.

---

## 6. Live Testing Directory & Role Verification

For testing and demonstrating the system locally at `http://127.0.0.1:8001`:

| User Role | Target Audience | Login URL | Email | Password | Access Capabilities |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | Executive & IT Admin | `/login` | `admin@example.com` | `password` | Complete access, RBAC governance, L2 payment approvals, Chart of Accounts |
| **Internal Staff** | Sales, Estimators, Engineers | `/login` | `staff@example.com` | `password` | Inquiries CRM, commercial quotes, projects, vendor LPOs |
| **Technician / User** | Site Techs & Field Staff | `/login` | `user@user.com` | `password` | AMC visit forms, offline drafts, self-service leave/salary advance requests |
| **Client Portal** | Corporate Client Extranet | `/company/login` | `client@example.com` | `password` | Scoped client extranet: review quotes, download VAT invoices, submit LPOin |

---

## 7. Team Lead Walkthrough & Demo Script

When presenting this system to your Team Lead, use this 5-minute structured demonstration flow:

### Phase 1: Authentication & Multi-Tenancy (1 Minute)
1. Open an Incognito window: log into `/company/login` using `client@example.com`.
2. Open a standard window: log into `/login` using `admin@example.com`.
3. **Key Point to Highlight:** Show that both user sessions run in parallel without conflicts due to distinct session cookie hashes (`login_web` vs `login_company`).

### Phase 2: Commercial Flow to Active Project (1.5 Minutes)
1. In the Admin/Staff window, navigate to `/inquiries` $\rightarrow$ show how an inquiry captures site visit details.
2. Go to `/quotations` $\rightarrow$ open a quotation and click "Generate PDF" to show the DomPDF output.
3. In the Client window, refresh `/company/dashboard` $\rightarrow$ show that the client immediately sees their quotation and can upload their `LPOin`.
4. Return to Admin $\rightarrow$ convert the quote to an active contract at `/projects`.

### Phase 3: Field Operations & AMC Offline Form (1 Minute)
1. Log in as `user@user.com` (Technician). Notice that the user is immediately routed to `/projects/visit-form`.
2. Fill out the inspection checklist, show the signature canvas, and mention the `/projects/amc-drafts` offline storage capability for basement work.
3. Highlight the mobile GPS geofenced QR attendance engine (Haversine 500m radius check).

### Phase 4: Financial Governance & Two-Person Rule (1.5 Minutes)
1. Navigate to `/payment-bookings` $\rightarrow$ show a pending payment voucher.
2. Explain the **Two-Person Rule**: Show how approving Level 1 locks out the same person from approving Level 2.
3. Point out the **Liquidity Release Rule**: Funds are only marked as disbursed when status is 3 (Approved) AND the cheque maturity date has arrived.
4. Show how approved vouchers link straight into the double-entry Chart of Accounts at `/accounts`.
