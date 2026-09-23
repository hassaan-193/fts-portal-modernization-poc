# Admin & Executive Portal Documentation

---

## 1. Overview & Audience
The **Admin Portal** is designed for executive leadership, operations directors, and senior finance officers. It serves as the administrative cockpit for system-wide governance, financial oversight, role permissions, and multi-tier approval chains.

* **Guard**: `web` (Session driver)
* **Underlying Model**: `App\User` (`users` table)
* **Role Requirement**: `Super-User`
* **Access URL**: `http://127.0.0.1:8001/login`
* **Default Verified Account**: `admin@example.com` / `password`

---

## 2. Core Functional Modules

### 2.1 Executive Dashboard (`/home`)
* **Real-time Business Counters**: Displays dynamic counts of active client companies, running projects, pending quotations, and registered staff members.
* **HR Compliance Radar**: Monitors all employee and laborer records. Automatically flags in red any passport, residency visa, Emirates ID, or labor card due to expire within 30 days.
* **Financial Action Feed**: Identifies overdue customer invoices (>30 days), uncleared post-dated cheques (PDCs), and unallocated client payments.

---

### 2.2 User & Role Governance (`/users`, `/roles`)
* **Managing Staff Accounts**:
  * Create, edit, and deactivate internal user accounts.
  * Assign official roles (`Super-User`, `Staff`, `AMC Reporter`, `Staff Requester`).
* **Spatie RBAC Matrix**:
  * Granular permissions control access to modules (`users`, `roles`, `companies`, `quotations`, `projects`, `invoices`, `deletes`).
  * Gated in routes via `middleware(['can:permission_name'])`.

---

### 2.3 Double-Entry Financial Ledgers (`/accounts`, `/reports/*`)
* **Chart of Accounts**: Manages accounts receivable, accounts payable, material expenses, revenue accounts, and VAT ledgers.
* **Financial Reports**:
  * Trial Balance generation.
  * Customer ledger statements with debit/credit audit trails.
  * Petty cash registers and custodian ledgers.

---

### 2.4 Multi-Tier Purchase Order Approvals (`/purchase-orders-admin/index`)
High-value procurement requests raised by project managers arrive in the executive approval queue:
* Inspect attached supplier quotations, linked project codes, and payment preferences.
* Click **Approve & Email** to issue signed purchase orders directly to suppliers.
* Reject or place on hold with required audit revision notes.

---

### 2.5 Two-Person HR Request Sign-off (`/request-approvals`)
* Employee leave applications, passport release requests, and salary advance applications arrive in this review workspace.
* Provides dual-level operational and managerial approvals before records update in the HR database.

---

### 2.6 Global Delete Interception (`CheckDeletePermission`)
Every HTTP `DELETE` action across the system passes through the global `CheckDeletePermission` middleware. Unless the logged-in administrator explicitly holds the `deletes` permission, the request is halted before records are affected.
