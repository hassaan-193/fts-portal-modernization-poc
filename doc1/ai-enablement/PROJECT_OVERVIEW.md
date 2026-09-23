# Project Overview & Architecture Narrative

## 1. System Purpose & Scope
The **FTS Portal** (`ft_portal_base`) is the primary operational, commercial, and financial ERP for an engineering contracting firm specializing in Low-Current / ELV (Extra-Low Voltage), CCTV, Access Control, Fire Alarm systems, and Facility Maintenance. It combines CRM, technical estimation, procurement, project delivery, accounting ledgers, HR compliance, and site attendance into a unified Laravel monolith.

---

## 2. Architectural Pillars

* **Framework & Foundation**: Laravel 7.27 running strictly on **PHP 7.4**. (PHP 8.x is incompatible due to deprecation of curly brace array indexing and parameter signatures in legacy dependencies).
* **Delivery Paradigms**:
  * **Server-Rendered Blade**: AdminLTE 3 / Bootstrap 4 views for administrative layouts and standard forms.
  * **Yajra Laravel DataTables**: Server-side AJAX-paginated, filtered, searchable tables for high-volume entities (Projects, Quotations, Invoices, Inquiries).
  * **Laravel Livewire 1.x & Vue 2**: Dynamic, reactive sub-components and modals.
  * **RESTful JSON API**: Versioned endpoints (`/api/v1/*`) with Bearer token authentication for mobile check-in, QR geofencing, and payment approvals.
* **Data Access Strategy**:
  * Eloquent ORM combined with repository classes (`app/Repositories`).
  * Dedicated service classes (`app/Services`) encapsulating multi-step domain workflows (Payment Booking reviews, Balance adjustments, Geofenced QR evaluations).
* **Multi-Guard Access Architecture**:
  * `web` guard: Internal personnel (`App\User`), partitioned into roles (`Super-User`, `Staff`, `Staff Requester`, `Payment Booking Approver`) via Spatie Laravel-Permission.
  * `company` guard: External clients (`App\Models\Company`) accessing an isolated extranet scoped strictly to their primary key (`where company_id = ?`).
  * `api` guard: Stateless token-authenticated access for mobile devices and companion apps.

---

## 3. Core Business Capabilities

1. **Commercial Pipeline**: Inquiries ➔ Technical Feasibility Review ➔ On-site Engineering Surveys ➔ Multi-line Quotations with DomPDF export ➔ Client Acceptance.
2. **Project Delivery & Contracting**: Fixed fit-out installation contracts, milestone billing, and periodic Annual Maintenance Contracts (AMC) with mobile inspection checklists and touch-signature capture.
3. **Supply Chain & Procurement**: Material requisitions, supplier quotes, purchase order generation, and multi-tier executive approval queues before vendor dispatch.
4. **Accounting & Ledgers**: Double-entry ledger updates, customer account statements, trial balances, and VAT return audits via `CustomBalanceManager`.
5. **Mobile Geofenced Attendance**: Site foremen labor muster rolls and individual QR check-ins verified against project GPS coordinates.
