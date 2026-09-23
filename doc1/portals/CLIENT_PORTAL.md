# Client & Company Portal Documentation

---

## 1. Overview & Audience
The **Client Portal** (Customer Extranet) provides an external, self-service window for registered corporate clients, general contractors, and facility managers. It delivers full commercial transparency, allowing customers to view active proposals, download approved tax invoices, inspect purchase orders, and maintain their corporate profile.

* **Guard**: `company` (Session driver, distinct from the `web` guard)
* **Underlying Model**: `App\Models\Company` (`companies` table)
* **Access URL**: `http://127.0.0.1:8001/company/login`
* **Default Verified Account**: `client@example.com` / `password`
* **Associated Client**: Al Futtaim Engineering

---

## 2. Multi-Tenant Security & Strict Data Isolation

A key architectural feature of the Client Portal is strict database-level multi-tenancy:
* The client authenticates through the `company` guard:
  ```php
  $company = Auth::guard('company')->user();
  ```
* Every query executed in `CompanyHomeController` and associated DataTables explicitly scopes data by the authenticated company's primary key:
  ```php
  $quotations = Quotation::where('company_id', $company->id)->get();
  $invoices   = Invoice::where('company_id', $company->id)->get();
  ```
* This guarantees that cross-company data leakage is impossible, ensuring full confidentiality between competing clients.

---

## 3. Core Functional Modules

### 3.1 Client Command Dashboard (`/company/dashboard`)
Upon logging in, the client is greeted with an executive summary:
* **Company Profile Card**: Displays registered legal entity name, Tax Registration Number (TRN/VAT), primary contact person, billing address, and default shipping address.
* **Commercial Metrics**:
  * Total Active Proposals / Quotations.
  * Billed Invoices & Outstanding Balances.
  * Registered Purchase Orders (LPO-ins).

---

### 3.2 Quotations Review & PDF Download
* **Browse Quotations**:
  * Displays a table of all commercial proposals issued to the client organization.
  * Columns include Reference Number, Revision Number, Issue Date, Expiry Date, Total Value, and Approval Status.
* **Detailed Proposal Inspection**:
  * Clients can review scope descriptions, equipment bill of materials, and agreed payment terms.
* **Official PDF Export**:
  * Download original, digitally stamped commercial proposals in PDF format for internal corporate review and management approvals.

---

### 3.3 Tax Invoices & Billing History
* **Invoice Ledger**:
  * Lists all issued tax invoices, milestone progress claims, and maintenance billings.
* **Status Badges**:
  * `Paid`: Fully settled invoices.
  * `Partially Paid`: Milestone payments with remaining balance due.
  * `Pending`: Open invoices awaiting payment processing.
* **Tax Compliance (VAT)**:
  * Download official Tax Invoices featuring verified TRN numbers and VAT 5% breakdowns for filing corporate VAT returns.

---

### 3.4 Purchase Order Tracking (LPO-In)
* Review copies of purchase orders that the client's procurement department has issued to FTS.
* Verifies that the order has been acknowledged, entered into the ERP, and scheduled for material requisition or project execution.

---

### 3.5 Corporate Profile & Address Management (`/company/profile`)
* Allows authorized client personnel to update billing email addresses, finance contact phone numbers, and job-site delivery locations.
* Ensures invoices and technician dispatch notices are always directed to the current stakeholders.
