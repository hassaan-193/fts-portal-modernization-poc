# Staff & Operations Portal Documentation

---

## 1. Overview & Audience
The **Staff Portal** is the operational engine of FTS Portal, tailored for sales engineers, commercial estimators, site supervisors, project managers, and procurement personnel. It drives day-to-day business operations from initial sales inquiry to project handover.

* **Guard**: `web` (Session driver)
* **Underlying Model**: `App\User` (`users` table)
* **Role Requirement**: `Staff`
* **Access URL**: `http://127.0.0.1:8001/login`
* **Default Verified Account**: `staff@example.com` / `password`

---

## 2. Core Functional Modules

### 2.1 CRM & Inquiries Pipeline (`/inquiries`)
The inquiries module tracks sales opportunities through four distinct lifecycle stages:

1. **Inquiry Ingestion (`/inquiries/create`)**:
   * Create an inquiry by selecting a registered client company or entering prospect details.
   * Categorize by source (Phone, Email, Referral, Tender), scope (`CCTV`, `Access Control`, `Fire Alarm`, `AMC`, `Turnkey Contracting`), and urgency level.
2. **Technical Department Review (`/inquiries/{id}/department/review`)**:
   * Operations managers assess feasibility, assign a technical lead, and schedule an on-site survey.
3. **Field Engineer Survey Report (`/inquiries/{id}/engineer/report`)**:
   * The assigned engineer records structural site requirements: cable route lengths, power containment, device mounting conditions, and survey site photos.
4. **Sales Pipeline & Quote Conversion (`/inquiries/sales/pipeline`)**:
   * Commercial teams take the completed survey report, configure the bill of materials, and convert the inquiry directly into a formal Quotation with one click.

---

### 2.2 Commercial Quotation Estimation & PDF Generation (`/quotations`)
Quotations represent formal, legally binding price estimates presented to clients.

* **Creating a Quotation (`/quotations/create`)**:
  * Select client company, payment terms, and reference inquiry.
  * Add line items from the master product catalog (`/products`) or enter ad-hoc engineering items.
  * Define quantity, unit cost, markup/margin percentage, and installation labor charges.
* **Payment Terms & Schedules**:
  * Set staged milestones (e.g. 50% Advance with LPO, 40% On Equipment Delivery, 10% On Testing & Handover).
* **Automated PDF Generation**:
  * Powered by `Barryvdh\DomPDF`. Click **Print / Export PDF** to generate an official branded PDF proposal containing scope of work, company header, payment schedule, and terms & conditions.
* **Revision Tracking**:
  * Maintain quote revisions (Rev 0, Rev 1, Rev 2) while keeping historical commercial records intact.

---

### 2.3 Project Execution & Contract Tracking (`/projects`)
Once a quotation is accepted and the client issues an official Local Purchase Order (LPO), it converts into an active Project.

* **Project Onboarding (`/projects/create`)**:
  * Linked directly to the originating Quotation.
  * Record project contract value, client purchase order number, start date, target completion date, and assign a site supervisor.
* **Project Types**:
  * **Installation / Fit-Out**: Fixed-term contracting jobs with delivery phases.
  * **AMC (Annual Maintenance Contract)**: Periodic preventive maintenance contracts with scheduled visit frequencies (e.g., Quarterly, Semi-Annual).
* **Project Extensions & Milestones**:
  * Log variations, scope adjustments, and completion sign-offs.

---

### 2.4 Procurement & Vendor LPOs (`/purchase-orders`, `/lpoouts`)
Material procurement ensures site engineers receive parts and equipment on schedule.

* **Raising a Purchase Order**:
  * Specify the destination Project and the selected supplier from the master vendor directory (`/vendors`).
  * Itemize equipment, quantities, agreed supplier unit rates, and delivery dates.
* **Payment Terms Configuration**:
  * Specify payment mode: Cash on Delivery (COD), Wire Transfer, or Post-Dated Cheque (PDC) specifying maturity days (e.g., 30, 60, 90 days).
* **Submission for Approval**:
  * Automatically routes the PO to executive management in the Admin Portal for formal review and digital authorization.

---

### 2.5 Product & Master Equipment Catalog (`/products`)
* Maintain standard equipment specs, part numbers, manufacturer details, and baseline unit costs.
* Speeds up quotation line-item creation and standardizes pricing across all sales engineers.
