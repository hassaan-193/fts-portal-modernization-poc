# Complete Database Schema Specification

- **Database**: `fts_portal`
- **Extraction Timestamp**: 2026-09-18 15:57:57
- **Total Tables**: 80
- **SQL DDL File**: [`scratch/complete_db_schema.sql`](file:///D:/FTSITS/ft_portal_base(2)/ft_portal_base/scratch/complete_db_schema.sql)
- **JSON Schema File**: [`scratch/complete_db_schema.json`](file:///D:/FTSITS/ft_portal_base(2)/ft_portal_base/scratch/complete_db_schema.json)

---

## Table of Contents

### 1. Core Authentication, Users & Spatie RBAC
- [`users`](#table-users) (14 columns, 3 rows)
- [`roles`](#table-roles) (5 columns, 2 rows)
- [`permissions`](#table-permissions) (5 columns, 33 rows)
- [`model_has_permissions`](#table-model-has-permissions) (3 columns, 0 rows)
- [`model_has_roles`](#table-model-has-roles) (3 columns, 3 rows)
- [`role_has_permissions`](#table-role-has-permissions) (2 columns, 50 rows)
- [`password_resets`](#table-password-resets) (3 columns, 0 rows)
- [`failed_jobs`](#table-failed-jobs) (6 columns, 0 rows)
- [`verify_emails`](#table-verify-emails) (4 columns, 0 rows)

### 2. Client Management & Corporate Extranet
- [`companies`](#table-companies) (23 columns, 3 rows)

### 3. Inquiries CRM, Lead Routing & Engineering Site Visits
- [`inquiries`](#table-inquiries) (21 columns, 2 rows)
- [`inquiry_activities`](#table-inquiry-activities) (8 columns, 0 rows)
- [`inquiry_department_reviews`](#table-inquiry-department-reviews) (17 columns, 0 rows)
- [`inquiry_engineer_reports`](#table-inquiry-engineer-reports) (11 columns, 0 rows)
- [`inquiry_follow_ups`](#table-inquiry-follow-ups) (9 columns, 0 rows)
- [`inquiry_quotations`](#table-inquiry-quotations) (9 columns, 0 rows)
- [`inquiry_routing_configs`](#table-inquiry-routing-configs) (6 columns, 4 rows)

### 4. Commercial Proposals, Quotations & Products
- [`quotations`](#table-quotations) (22 columns, 0 rows)
- [`quotation_products`](#table-quotation-products) (8 columns, 0 rows)
- [`products`](#table-products) (5 columns, 6 rows)

### 5. Projects, Maintenance Contracts (AMC), Drawings & Site Visits
- [`projects`](#table-projects) (17 columns, 1 rows)
- [`project_types`](#table-project-types) (4 columns, 6 rows)
- [`projectreports`](#table-projectreports) (45 columns, 0 rows)
- [`project_extension`](#table-project-extension) (7 columns, 0 rows)
- [`project_lpoout`](#table-project-lpoout) (5 columns, 0 rows)
- [`amc_report_system_items`](#table-amc-report-system-items) (6 columns, 0 rows)
- [`drawing_receiveds`](#table-drawing-receiveds) (11 columns, 0 rows)
- [`drawing_received_contributions`](#table-drawing-received-contributions) (8 columns, 0 rows)
- [`visit_schedules`](#table-visit-schedules) (8 columns, 0 rows)
- [`visit_schedule_comments`](#table-visit-schedule-comments) (5 columns, 0 rows)
- [`visit_schedule_history`](#table-visit-schedule-history) (9 columns, 0 rows)

### 6. Procurement, Approved Vendors & Purchase Orders
- [`vendors`](#table-vendors) (19 columns, 2 rows)
- [`purchase_orders`](#table-purchase-orders) (44 columns, 0 rows)
- [`order_items`](#table-order-items) (9 columns, 0 rows)
- [`orders`](#table-orders) (16 columns, 0 rows)
- [`lpoouts`](#table-lpoouts) (32 columns, 0 rows)
- [`lpo_out_types`](#table-lpo-out-types) (4 columns, 2 rows)
- [`lpoins`](#table-lpoins) (12 columns, 0 rows)

### 7. Invoicing, Billing, Bank Accounts & Ledger Transactions
- [`invoices`](#table-invoices) (20 columns, 0 rows)
- [`invoice_types`](#table-invoice-types) (4 columns, 2 rows)
- [`invoice_banks`](#table-invoice-banks) (10 columns, 4 rows)
- [`invoice_product_details`](#table-invoice-product-details) (11 columns, 0 rows)
- [`invoice_service_details`](#table-invoice-service-details) (6 columns, 0 rows)
- [`invoice_requests`](#table-invoice-requests) (10 columns, 0 rows)
- [`invoice_request_products`](#table-invoice-request-products) (11 columns, 0 rows)
- [`payment_invoices`](#table-payment-invoices) (23 columns, 0 rows)
- [`balance_accounts`](#table-balance-accounts) (6 columns, 9 rows)
- [`balance_transactions`](#table-balance-transactions) (8 columns, 0 rows)
- [`transactions`](#table-transactions) (20 columns, 0 rows)
- [`petty_cashes`](#table-petty-cashes) (16 columns, 0 rows)
- [`petty_cash_expenses`](#table-petty-cash-expenses) (7 columns, 0 rows)

### 8. Payment Bookings (Sequential 2-Level Review Engine)
- [`payment_bookings`](#table-payment-bookings) (24 columns, 0 rows)
- [`payment_booking_approvals`](#table-payment-booking-approvals) (9 columns, 0 rows)

### 9. Staff, HR Personnel, Requisitions, Payroll & Letters
- [`staf_profile`](#table-staf-profile) (26 columns, 2 rows)
- [`staf_dates`](#table-staf-dates) (7 columns, 0 rows)
- [`staff_requests`](#table-staff-requests) (12 columns, 0 rows)
- [`request_approvals`](#table-request-approvals) (9 columns, 0 rows)
- [`request_forms`](#table-request-forms) (9 columns, 0 rows)
- [`labor_requests`](#table-labor-requests) (10 columns, 0 rows)
- [`labour_assignments`](#table-labour-assignments) (10 columns, 0 rows)
- [`letters`](#table-letters) (11 columns, 0 rows)
- [`monthly_staff_reports`](#table-monthly-staff-reports) (15 columns, 0 rows)
- [`staff_ratings`](#table-staff-ratings) (15 columns, 0 rows)
- [`tickets`](#table-tickets) (14 columns, 0 rows)
- [`payrolls`](#table-payrolls) (11 columns, 0 rows)
- [`employees`](#table-employees) (8 columns, 0 rows)
- [`tasks`](#table-tasks) (9 columns, 0 rows)

### 10. Labor Management, Sites & Geofenced QR Attendance
- [`sites`](#table-sites) (5 columns, 0 rows)
- [`attendances`](#table-attendances) (11 columns, 0 rows)
- [`attendance_approvals`](#table-attendance-approvals) (9 columns, 0 rows)
- [`attendance_labor_details`](#table-attendance-labor-details) (8 columns, 0 rows)
- [`attendance_sessions`](#table-attendance-sessions) (20 columns, 0 rows)
- [`qr_staff_attendances`](#table-qr-staff-attendances) (25 columns, 0 rows)

### 11. System Platform, Media, Lookups & Notifications
- [`media`](#table-media) (15 columns, 0 rows)
- [`notifications`](#table-notifications) (8 columns, 0 rows)
- [`comments`](#table-comments) (8 columns, 0 rows)
- [`device_tokens`](#table-device-tokens) (8 columns, 0 rows)
- [`documents`](#table-documents) (6 columns, 0 rows)
- [`lookups`](#table-lookups) (5 columns, 13 rows)
- [`migrations`](#table-migrations) (3 columns, 131 rows)

---

## Detailed Table Specifications

### 1. Core Authentication, Users & Spatie RBAC

#### <a id="table-users"></a> Table: `users`
- **Columns**: `14` | **Records**: `3`
- **Foreign Key Constraints**:
  - `staf_profile_id` ➔ `staf_profile.id` *(users_staf_profile_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | YES | - | - | - |
| `email` | `varchar(191)` | NO | **UNI** | - | - |
| `email_verified_at` | `timestamp` | YES | - | - | - |
| `password` | `varchar(191)` | NO | - | - | - |
| `image` | `varchar(191)` | YES | - | - | - |
| `remember_token` | `varchar(100)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `firebase_token` | `varchar(191)` | YES | - | - | - |
| `api_token` | `varchar(80)` | YES | **UNI** | - | - |
| `staf_profile_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `balance` | `double(8,2)` | NO | - | - | - |
| `deleted_at` | `timestamp` | YES | - | - | - |

#### <a id="table-roles"></a> Table: `roles`
- **Columns**: `5` | **Records**: `2`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `guard_name` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-permissions"></a> Table: `permissions`
- **Columns**: `5` | **Records**: `33`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `guard_name` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-model-has-permissions"></a> Table: `model_has_permissions`
- **Columns**: `3` | **Records**: `0`
- **Foreign Key Constraints**:
  - `permission_id` ➔ `permissions.id` *(model_has_permissions_permission_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `permission_id` | `bigint(20) unsigned` | NO | **PRI** | - | - |
| `model_type` | `varchar(191)` | NO | **PRI** | - | - |
| `model_id` | `bigint(20) unsigned` | NO | **PRI** | - | - |

#### <a id="table-model-has-roles"></a> Table: `model_has_roles`
- **Columns**: `3` | **Records**: `3`
- **Foreign Key Constraints**:
  - `role_id` ➔ `roles.id` *(model_has_roles_role_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `role_id` | `bigint(20) unsigned` | NO | **PRI** | - | - |
| `model_type` | `varchar(191)` | NO | **PRI** | - | - |
| `model_id` | `bigint(20) unsigned` | NO | **PRI** | - | - |

#### <a id="table-role-has-permissions"></a> Table: `role_has_permissions`
- **Columns**: `2` | **Records**: `50`
- **Foreign Key Constraints**:
  - `permission_id` ➔ `permissions.id` *(role_has_permissions_permission_id_foreign)*
  - `role_id` ➔ `roles.id` *(role_has_permissions_role_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `permission_id` | `bigint(20) unsigned` | NO | **PRI** | - | - |
| `role_id` | `bigint(20) unsigned` | NO | **PRI** | - | - |

#### <a id="table-password-resets"></a> Table: `password_resets`
- **Columns**: `3` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `email` | `varchar(191)` | NO | **MUL** | - | - |
| `token` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |

#### <a id="table-failed-jobs"></a> Table: `failed_jobs`
- **Columns**: `6` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `connection` | `text` | NO | - | - | - |
| `queue` | `text` | NO | - | - | - |
| `payload` | `longtext` | NO | - | - | - |
| `exception` | `longtext` | NO | - | - | - |
| `failed_at` | `timestamp` | NO | - | `current_timestamp()` | - |

#### <a id="table-verify-emails"></a> Table: `verify_emails`
- **Columns**: `4` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `email` | `varchar(191)` | NO | - | - | - |
| `token` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 2. Client Management & Corporate Extranet

#### <a id="table-companies"></a> Table: `companies`
- **Columns**: `23` | **Records**: `3`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `email` | `varchar(191)` | YES | **UNI** | - | - |
| `contact_person` | `varchar(191)` | YES | - | - | - |
| `contact_no` | `varchar(191)` | YES | - | - | - |
| `contact_no_two` | `varchar(191)` | YES | - | - | - |
| `vat_no` | `varchar(191)` | YES | - | - | - |
| `location` | `text` | YES | - | - | - |
| `file` | `text` | YES | - | - | - |
| `billing_address` | `text` | YES | - | - | - |
| `billing_contact_person` | `varchar(191)` | YES | - | - | - |
| `billing_pob` | `varchar(191)` | YES | - | - | - |
| `billing_email` | `varchar(191)` | YES | - | - | - |
| `shipping_address` | `varchar(191)` | YES | - | - | - |
| `shipping_contact_person` | `varchar(191)` | YES | - | - | - |
| `shipping_pob` | `varchar(191)` | YES | - | - | - |
| `shipping_email` | `varchar(191)` | YES | - | - | - |
| `payment_terms` | `varchar(191)` | YES | - | - | - |
| `credit_limit` | `varchar(191)` | YES | - | - | - |
| `password` | `varchar(191)` | YES | - | - | - |
| `remember_token` | `varchar(100)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 3. Inquiries CRM, Lead Routing & Engineering Site Visits

#### <a id="table-inquiries"></a> Table: `inquiries`
- **Columns**: `21` | **Records**: `2`
- **Foreign Key Constraints**:
  - `created_by` ➔ `users.id` *(inquiries_created_by_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `inquiry_no` | `varchar(191)` | NO | **UNI** | - | - |
| `created_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `client_name` | `varchar(191)` | NO | - | - | - |
| `phone` | `varchar(20)` | NO | - | - | - |
| `email` | `varchar(191)` | YES | - | - | - |
| `location` | `text` | YES | - | - | - |
| `project` | `varchar(191)` | YES | - | - | - |
| `inquiry_type` | `enum('Project','AMC','Installation','Other')` | NO | - | - | - |
| `other_type` | `varchar(191)` | YES | - | - | - |
| `source` | `enum('Call','Email','Walk-in','Website','Referral')` | NO | - | - | - |
| `expected_price` | `decimal(15,2)` | YES | - | - | - |
| `status` | `enum('New','Assigned','Under Review','Site Visit Pending','Site Visit Done','Sent to Sales','Quotation Created','Under Follow-up','Won','Lost','Closed')` | NO | - | `New` | - |
| `priority` | `enum('Low','Medium','High')` | NO | - | `Medium` | - |
| `follow_up_date` | `date` | YES | - | - | - |
| `expected_closing_date` | `date` | YES | - | - | - |
| `assigned_department` | `varchar(191)` | YES | - | - | - |
| `notes` | `text` | YES | - | - | - |
| `metadata` | `longtext` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-inquiry-activities"></a> Table: `inquiry_activities`
- **Columns**: `8` | **Records**: `0`
- **Foreign Key Constraints**:
  - `inquiry_id` ➔ `inquiries.id` *(inquiry_activities_inquiry_id_foreign)*
  - `user_id` ➔ `users.id` *(inquiry_activities_user_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `inquiry_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `user_id` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `action` | `varchar(191)` | NO | - | - | - |
| `description` | `text` | YES | - | - | - |
| `metadata` | `longtext` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-inquiry-department-reviews"></a> Table: `inquiry_department_reviews`
- **Columns**: `17` | **Records**: `0`
- **Foreign Key Constraints**:
  - `assigned_to` ➔ `users.id` *(inquiry_department_reviews_assigned_to_foreign)*
  - `inquiry_id` ➔ `inquiries.id` *(inquiry_department_reviews_inquiry_id_foreign)*
  - `reviewed_by` ➔ `users.id` *(inquiry_department_reviews_reviewed_by_foreign)*
  - `visit_assigned_to` ➔ `users.id` *(inquiry_department_reviews_visit_assigned_to_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `inquiry_id` | `bigint(20) unsigned` | NO | **UNI** | - | - |
| `assigned_department` | `varchar(191)` | NO | - | - | - |
| `assigned_to` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `assignment_date` | `timestamp` | YES | - | - | - |
| `current_status` | `varchar(191)` | YES | - | - | - |
| `internal_comments` | `text` | YES | - | - | - |
| `priority` | `enum('Low','Medium','High')` | NO | - | `Medium` | - |
| `response_deadline` | `timestamp` | YES | - | - | - |
| `technical_review_status` | `varchar(191)` | YES | - | - | - |
| `site_visit_required` | `tinyint(1)` | NO | - | `0` | - |
| `proposed_visit_date` | `date` | YES | - | - | - |
| `visit_assigned_to` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `visit_notes` | `text` | YES | - | - | - |
| `reviewed_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-inquiry-engineer-reports"></a> Table: `inquiry_engineer_reports`
- **Columns**: `11` | **Records**: `0`
- **Foreign Key Constraints**:
  - `inquiry_id` ➔ `inquiries.id` *(inquiry_engineer_reports_inquiry_id_foreign)*
  - `submitted_by` ➔ `users.id` *(inquiry_engineer_reports_submitted_by_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `inquiry_id` | `bigint(20) unsigned` | NO | **UNI** | - | - |
| `visit_completed` | `tinyint(1)` | NO | - | `0` | - |
| `site_condition_notes` | `text` | YES | - | - | - |
| `scope_understanding` | `text` | YES | - | - | - |
| `materials_required` | `text` | YES | - | - | - |
| `challenges_risks` | `text` | YES | - | - | - |
| `estimated_cost` | `decimal(15,2)` | YES | - | - | - |
| `submitted_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-inquiry-follow-ups"></a> Table: `inquiry_follow_ups`
- **Columns**: `9` | **Records**: `0`
- **Foreign Key Constraints**:
  - `created_by` ➔ `users.id` *(inquiry_follow_ups_created_by_foreign)*
  - `inquiry_id` ➔ `inquiries.id` *(inquiry_follow_ups_inquiry_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `inquiry_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `follow_up_date` | `date` | NO | - | - | - |
| `follow_up_notes` | `text` | YES | - | - | - |
| `client_feedback` | `text` | YES | - | - | - |
| `status` | `varchar(191)` | YES | - | - | - |
| `created_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-inquiry-quotations"></a> Table: `inquiry_quotations`
- **Columns**: `9` | **Records**: `0`
- **Foreign Key Constraints**:
  - `created_by` ➔ `users.id` *(inquiry_quotations_created_by_foreign)*
  - `inquiry_id` ➔ `inquiries.id` *(inquiry_quotations_inquiry_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `inquiry_id` | `bigint(20) unsigned` | NO | **UNI** | - | - |
| `quotation_amount` | `decimal(15,2)` | YES | - | - | - |
| `scope_of_work` | `text` | YES | - | - | - |
| `terms_conditions` | `text` | YES | - | - | - |
| `validity_date` | `date` | YES | - | - | - |
| `created_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-inquiry-routing-configs"></a> Table: `inquiry_routing_configs`
- **Columns**: `6` | **Records**: `4`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `inquiry_type` | `varchar(191)` | NO | **UNI** | - | - |
| `department` | `varchar(191)` | NO | - | - | - |
| `is_active` | `tinyint(1)` | NO | - | `1` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 4. Commercial Proposals, Quotations & Products

#### <a id="table-quotations"></a> Table: `quotations`
- **Columns**: `22` | **Records**: `0`
- **Foreign Key Constraints**:
  - `approved_by` ➔ `users.id` *(quotations_approved_by_foreign)*
  - `company_id` ➔ `companies.id` *(quotations_company_id_foreign)*
  - `reference_project_id` ➔ `projects.id` *(quotations_reference_project_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `reference_project_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `name` | `varchar(191)` | NO | - | - | - |
| `company_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `ref_no` | `varchar(191)` | YES | - | - | - |
| `quotation_type_id` | `int(11)` | YES | - | - | - |
| `quotation_company` | `int(11)` | YES | - | - | - |
| `amount` | `double` | NO | - | - | - |
| `vat` | `double` | NO | - | - | - |
| `total_amount` | `double` | NO | - | - | - |
| `date` | `date` | YES | - | - | - |
| `subject` | `varchar(191)` | YES | - | - | - |
| `location` | `text` | YES | - | - | - |
| `file` | `text` | YES | - | - | - |
| `payment` | `text` | YES | - | - | - |
| `exclusion` | `text` | YES | - | - | - |
| `status` | `int(10) unsigned` | NO | - | `0` | - |
| `approved_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `category` | `varchar(191)` | YES | - | - | - |
| `number_of_visits` | `int(11)` | YES | - | - | - |

#### <a id="table-quotation-products"></a> Table: `quotation_products`
- **Columns**: `8` | **Records**: `0`
- **Foreign Key Constraints**:
  - `product_id` ➔ `products.id` *(quotation_products_product_id_foreign)*
  - `quotation_id` ➔ `quotations.id` *(quotation_products_quotation_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `quotation_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `product_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `quantity` | `int(11)` | NO | - | - | - |
| `unit_price` | `decimal(10,2)` | NO | - | - | - |
| `total_price` | `decimal(10,2)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-products"></a> Table: `products`
- **Columns**: `5` | **Records**: `6`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `price` | `double` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 5. Projects, Maintenance Contracts (AMC), Drawings & Site Visits

#### <a id="table-projects"></a> Table: `projects`
- **Columns**: `17` | **Records**: `1`
- **Foreign Key Constraints**:
  - `quotation_id` ➔ `quotations.id` *(projects_quotation_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `date` | `date` | YES | - | - | - |
| `quotation_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `project_type_id` | `int(10) unsigned` | NO | - | - | - |
| `subject` | `varchar(191)` | YES | - | - | - |
| `payment_terms` | `varchar(191)` | YES | - | - | - |
| `labour_charges` | `double` | YES | - | - | - |
| `material_charges` | `double` | YES | - | - | - |
| `project_source` | `varchar(191)` | YES | - | - | - |
| `project_estimation` | `varchar(191)` | YES | - | - | - |
| `user_id` | `int(11)` | YES | - | - | - |
| `note` | `text` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `category` | `varchar(191)` | NO | - | `normal` | - |
| `visits` | `int(11)` | YES | - | - | - |
| `visit_schedule` | `longtext` | YES | - | - | - |

#### <a id="table-project-types"></a> Table: `project_types`
- **Columns**: `4` | **Records**: `6`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-projectreports"></a> Table: `projectreports`
- **Columns**: `45` | **Records**: `0`
- **Foreign Key Constraints**:
  - `company_id` ➔ `companies.id` *(projectreports_company_id_foreign)*
  - `project_id` ➔ `projects.id` *(projectreports_project_id_foreign)*
  - `visit_schedule_id` ➔ `visit_schedules.id` *(projectreports_visit_schedule_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `reference_number` | `varchar(191)` | NO | - | - | - |
| `date` | `date` | NO | - | - | - |
| `inspector_visiting_time` | `time` | YES | - | - | - |
| `inspector_leaving_time` | `time` | YES | - | - | - |
| `company_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `is_manual_client` | `tinyint(1)` | NO | - | `0` | - |
| `manual_client_name` | `varchar(191)` | YES | - | - | - |
| `project_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `visit_schedule_id` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `is_emergency_visit` | `tinyint(1)` | NO | - | `0` | - |
| `emergency_visit_date` | `date` | YES | - | - | - |
| `amc_type` | `enum('existing','new')` | NO | - | `existing` | - |
| `site_location` | `varchar(191)` | YES | - | - | - |
| `site_name` | `varchar(191)` | YES | - | - | - |
| `used_items` | `text` | YES | - | - | - |
| `required_items` | `text` | YES | - | - | - |
| `block_info` | `longtext` | YES | - | - | - |
| `note` | `text` | YES | - | - | - |
| `client_signature` | `text` | YES | - | - | - |
| `file_path` | `longtext` | YES | - | - | - |
| `status` | `enum('draft','pending','approved','disapproved')` | NO | - | `pending` | - |
| `created_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `fire_alarm_data` | `longtext` | YES | - | - | - |
| `fire_fighting_data` | `longtext` | YES | - | - | - |
| `fm200_data` | `longtext` | YES | - | - | - |
| `foam_tank_data` | `longtext` | YES | - | - | - |
| `voice_evacuation_data` | `longtext` | YES | - | - | - |
| `emergency_lighting_data` | `longtext` | YES | - | - | - |
| `system_interfacing_data` | `longtext` | YES | - | - | - |
| `exit_route_data` | `longtext` | YES | - | - | - |
| `storage_conditions_data` | `longtext` | YES | - | - | - |
| `pump_data` | `longtext` | YES | - | - | - |
| `deluge_data` | `longtext` | YES | - | - | - |
| `urgent_summary` | `longtext` | YES | - | - | - |
| `photos_data` | `longtext` | YES | - | - | - |
| `scope_data` | `longtext` | YES | - | - | - |
| `next_due_dates` | `longtext` | YES | - | - | - |
| `notes_used_items` | `text` | YES | - | - | - |
| `next_inspection_due` | `date` | YES | - | - | - |
| `expiry_update_required_on` | `date` | YES | - | - | - |
| `client_eid_details` | `varchar(191)` | YES | - | - | - |
| `client_phone` | `varchar(191)` | YES | - | - | - |

#### <a id="table-project-extension"></a> Table: `project_extension`
- **Columns**: `7` | **Records**: `0`
- **Foreign Key Constraints**:
  - `project_id` ➔ `projects.id` *(project_extension_project_id_foreign)*
  - `quotation_id` ➔ `quotations.id` *(project_extension_quotation_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `value` | `double` | NO | - | - | - |
| `project_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `quotation_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-project-lpoout"></a> Table: `project_lpoout`
- **Columns**: `5` | **Records**: `0`
- **Foreign Key Constraints**:
  - `lpoout_id` ➔ `lpoouts.id` *(project_lpoout_lpoout_id_foreign)*
  - `project_id` ➔ `projects.id` *(project_lpoout_project_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `project_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `lpoout_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-amc-report-system-items"></a> Table: `amc_report_system_items`
- **Columns**: `6` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `system_key` | `varchar(50)` | NO | **MUL** | - | - |
| `item_slug` | `varchar(191)` | NO | - | - | - |
| `item_label` | `varchar(255)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-drawing-receiveds"></a> Table: `drawing_receiveds`
- **Columns**: `11` | **Records**: `0`
- **Foreign Key Constraints**:
  - `lpoin_id` ➔ `lpoins.id` *(drawing_receiveds_lpoin_id_foreign)*
  - `responsible_engineer_id` ➔ `users.id` *(drawing_receiveds_responsible_engineer_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `lpoin_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `responsible_engineer_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `type_of_work` | `varchar(191)` | NO | - | - | - |
| `start_date` | `date` | NO | - | - | - |
| `review_comments_date` | `date` | YES | - | - | - |
| `approval_date` | `date` | YES | - | - | - |
| `status` | `varchar(191)` | NO | **MUL** | `Under Review` | - |
| `notes` | `longtext` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-drawing-received-contributions"></a> Table: `drawing_received_contributions`
- **Columns**: `8` | **Records**: `0`
- **Foreign Key Constraints**:
  - `contributed_by_id` ➔ `users.id` *(drawing_received_contributions_contributed_by_id_foreign)*
  - `drawing_received_id` ➔ `drawing_receiveds.id` *(drawing_received_contributions_drawing_received_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `drawing_received_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `contributed_by_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `contribution_type` | `varchar(191)` | NO | **MUL** | - | - |
| `description` | `longtext` | YES | - | - | - |
| `status` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-visit-schedules"></a> Table: `visit_schedules`
- **Columns**: `8` | **Records**: `0`
- **Foreign Key Constraints**:
  - `project_id` ➔ `projects.id` *(visit_schedules_project_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `project_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `visit_date` | `date` | NO | - | - | - |
| `status` | `enum('pending','done','upcoming')` | NO | - | `pending` | - |
| `file_uploaded` | `tinyint(1)` | NO | - | `0` | - |
| `file_path` | `varchar(191)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-visit-schedule-comments"></a> Table: `visit_schedule_comments`
- **Columns**: `5` | **Records**: `0`
- **Foreign Key Constraints**:
  - `visit_schedule_id` ➔ `visit_schedules.id` *(visit_schedule_comments_visit_schedule_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `visit_schedule_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `comment` | `text` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-visit-schedule-history"></a> Table: `visit_schedule_history`
- **Columns**: `9` | **Records**: `0`
- **Foreign Key Constraints**:
  - `project_id` ➔ `projects.id` *(visit_schedule_history_project_id_foreign)*
  - `visit_schedule_id` ➔ `visit_schedules.id` *(visit_schedule_history_visit_schedule_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `project_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `visit_schedule_id` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `visit_date` | `date` | YES | - | - | - |
| `status` | `varchar(191)` | NO | - | - | - |
| `company_name` | `varchar(191)` | NO | - | - | - |
| `project_name` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 6. Procurement, Approved Vendors & Purchase Orders

#### <a id="table-vendors"></a> Table: `vendors`
- **Columns**: `19` | **Records**: `2`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `email` | `varchar(191)` | YES | **UNI** | - | - |
| `emails` | `longtext` | YES | - | - | - |
| `contact_person` | `varchar(191)` | YES | - | - | - |
| `contact_no` | `varchar(191)` | YES | - | - | - |
| `contact_no_two` | `varchar(191)` | YES | - | - | - |
| `vat_no` | `varchar(191)` | YES | - | - | - |
| `payment_terms` | `varchar(191)` | YES | - | - | - |
| `terms_and_conditions` | `longtext` | YES | - | - | - |
| `credit_limit` | `varchar(191)` | YES | - | - | - |
| `location` | `text` | YES | - | - | - |
| `file` | `text` | YES | - | - | - |
| `payment_preference` | `varchar(191)` | NO | - | `cod` | - |
| `pdc_number_of_days` | `int(11)` | YES | - | - | - |
| `pdc_payment_option` | `varchar(191)` | YES | - | - | - |
| `vendor_specialization` | `varchar(191)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-purchase-orders"></a> Table: `purchase_orders`
- **Columns**: `44` | **Records**: `0`
- **Foreign Key Constraints**:
  - `lpout_id` ➔ `lpoouts.id` *(purchase_orders_lpout_id_foreign)*
  - `revised_from_lpoout_id` ➔ `lpoouts.id` *(purchase_orders_revised_from_lpoout_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `request_number` | `varchar(191)` | NO | **UNI** | - | - |
| `request_type` | `varchar(191)` | NO | - | `general` | - |
| `quotation_id` | `bigint(20)` | YES | - | - | - |
| `project_id` | `bigint(20)` | YES | - | - | - |
| `other_info` | `text` | YES | - | - | - |
| `created_by` | `bigint(20)` | YES | - | - | - |
| `date` | `date` | YES | - | - | - |
| `due_date` | `date` | YES | - | - | - |
| `delivery_date` | `date` | YES | - | - | - |
| `items` | `longtext` | YES | - | - | - |
| `total_amount` | `decimal(15,2)` | NO | - | `0.00` | - |
| `status` | `varchar(191)` | NO | - | `Pending` | - |
| `urgency_level` | `enum('normal','urgent')` | NO | - | `normal` | - |
| `department_status` | `varchar(191)` | NO | - | `Pending` | - |
| `department_notes` | `text` | YES | - | - | - |
| `sent_back_notes` | `text` | YES | - | - | - |
| `sent_back_at` | `timestamp` | YES | - | - | - |
| `sent_back_by` | `bigint(20)` | YES | - | - | - |
| `sent_back_count` | `int(10) unsigned` | NO | - | `0` | - |
| `admin_id` | `bigint(20)` | YES | - | - | - |
| `admin_notes` | `text` | YES | - | - | - |
| `lpout_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `revised_from_lpoout_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `lpout_name` | `varchar(191)` | YES | - | - | - |
| `lpout_vendor_id` | `bigint(20)` | YES | - | - | - |
| `lpout_trn_no` | `varchar(191)` | YES | - | - | - |
| `lpout_kindly_attn` | `varchar(191)` | YES | - | - | - |
| `lpout_date` | `date` | YES | - | - | - |
| `lpout_payment_type` | `varchar(191)` | YES | - | - | - |
| `lpout_cheque_date` | `date` | YES | - | - | - |
| `lpout_vat` | `tinyint(4)` | YES | - | `0` | - |
| `lpout_payment_preference_option` | `varchar(191)` | YES | - | `default` | - |
| `lpout_payment_preference` | `varchar(191)` | YES | - | - | - |
| `lpout_pdc_number_of_days` | `int(11)` | YES | - | - | - |
| `lpout_pdc_payment_option` | `varchar(191)` | YES | - | - | - |
| `lpout_items` | `longtext` | YES | - | - | - |
| `lpout_pricing_mode` | `varchar(191)` | NO | - | `unit` | - |
| `lpout_manual_total` | `decimal(15,2)` | YES | - | - | - |
| `has_item_code` | `tinyint(1)` | NO | - | `0` | - |
| `lpout_terms` | `longtext` | YES | - | - | - |
| `payment_preference` | `text` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-order-items"></a> Table: `order_items`
- **Columns**: `9` | **Records**: `0`
- **Foreign Key Constraints**:
  - `order_id` ➔ `orders.id` *(order_items_order_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `order_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `item_description` | `varchar(191)` | NO | - | - | - |
| `unit` | `varchar(191)` | YES | - | - | - |
| `quantity` | `double(8,2)` | NO | - | - | - |
| `unit_price` | `double(8,2)` | NO | - | - | - |
| `total` | `double(8,2)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-orders"></a> Table: `orders`
- **Columns**: `16` | **Records**: `0`
- **Foreign Key Constraints**:
  - `vendor_id` ➔ `vendors.id` *(orders_vendor_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `date` | `date` | NO | - | - | - |
| `trn` | `varchar(191)` | NO | - | - | - |
| `vendor_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `attn` | `varchar(191)` | YES | - | - | - |
| `ship_to` | `varchar(191)` | YES | - | - | - |
| `address` | `text` | YES | - | - | - |
| `contact` | `varchar(191)` | YES | - | - | - |
| `ref_no` | `varchar(191)` | YES | - | - | - |
| `total_amount` | `decimal(14,2)` | NO | - | `0.00` | - |
| `discount` | `decimal(14,2)` | NO | - | `0.00` | - |
| `total_after_discount` | `decimal(14,2)` | NO | - | `0.00` | - |
| `vat` | `decimal(14,2)` | NO | - | `0.00` | - |
| `total_with_vat` | `decimal(14,2)` | NO | - | `0.00` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-lpoouts"></a> Table: `lpoouts`
- **Columns**: `32` | **Records**: `0`
- **Foreign Key Constraints**:
  - `lpo_out_type_id` ➔ `lpo_out_types.id` *(lpoouts_lpo_out_type_id_foreign)*
  - `project_id` ➔ `projects.id` *(lpoouts_project_id_foreign)*
  - `vendor_id` ➔ `vendors.id` *(lpoouts_vendor_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `lpo_invoice_no` | `varchar(191)` | YES | - | - | - |
| `name` | `varchar(191)` | NO | - | - | - |
| `lpo_out_type_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `project_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `vendor_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `trn_no` | `varchar(191)` | YES | - | - | - |
| `kindly_attn` | `varchar(191)` | YES | - | - | - |
| `date` | `date` | YES | - | - | - |
| `amount` | `double` | NO | - | - | - |
| `terms` | `longtext` | YES | - | - | - |
| `payment_type` | `varchar(191)` | YES | - | - | - |
| `cheque_date` | `date` | YES | - | - | - |
| `items` | `longtext` | YES | - | - | - |
| `pricing_mode` | `varchar(191)` | NO | - | `unit` | - |
| `has_item_code` | `tinyint(1)` | NO | - | `0` | - |
| `status` | `enum('Pending','Approved','Not Approved')` | NO | - | `Pending` | - |
| `revision_number` | `int(11)` | NO | - | `1` | - |
| `parent_lpoout_id` | `bigint(20) unsigned` | YES | - | - | - |
| `is_latest_revision` | `tinyint(1)` | NO | - | `1` | - |
| `revised_by` | `bigint(20) unsigned` | YES | - | - | - |
| `revision_reason` | `text` | YES | - | - | - |
| `revised_at` | `timestamp` | YES | - | - | - |
| `lpo_payment_preference_option` | `varchar(191)` | NO | - | `default` | - |
| `lpo_payment_preference` | `varchar(191)` | YES | - | - | - |
| `lpo_pdc_number_of_days` | `int(11)` | YES | - | - | - |
| `lpo_pdc_payment_option` | `varchar(191)` | YES | - | - | - |
| `vat` | `double` | NO | - | - | - |
| `total_amount` | `double` | NO | - | - | - |
| `file` | `text` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-lpo-out-types"></a> Table: `lpo_out_types`
- **Columns**: `4` | **Records**: `2`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-lpoins"></a> Table: `lpoins`
- **Columns**: `12` | **Records**: `0`
- **Foreign Key Constraints**:
  - `quotation_id` ➔ `quotations.id` *(lpoins_quotation_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `quotation_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `ref_no` | `varchar(191)` | YES | - | - | - |
| `payment_terms` | `varchar(191)` | YES | - | - | - |
| `civil_defence_fee` | `double` | NO | - | `0` | - |
| `government_fee` | `double` | NO | - | `0` | - |
| `adjustment_fee` | `double` | NO | - | `0` | - |
| `date_issue` | `date` | YES | - | - | - |
| `date_due` | `date` | YES | - | - | - |
| `amount` | `double(8,2)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 7. Invoicing, Billing, Bank Accounts & Ledger Transactions

#### <a id="table-invoices"></a> Table: `invoices`
- **Columns**: `20` | **Records**: `0`
- **Foreign Key Constraints**:
  - `invoice_type_id` ➔ `invoice_types.id` *(invoices_invoice_type_id_foreign)*
  - `quotation_id` ➔ `quotations.id` *(invoices_quotation_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `invoice_no` | `varchar(191)` | NO | - | - | - |
| `invoice_type_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `quotation_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `invoice_request_id` | `int(10) unsigned` | NO | - | - | - |
| `start_date` | `date` | YES | - | - | - |
| `end_date` | `date` | YES | - | - | - |
| `delivery_date` | `date` | YES | - | - | - |
| `currency` | `varchar(191)` | NO | - | `AED` | - |
| `payment_terms` | `varchar(191)` | YES | - | - | - |
| `amount_in_word` | `varchar(191)` | YES | - | - | - |
| `note1` | `text` | YES | - | - | - |
| `note2` | `text` | YES | - | - | - |
| `invoice_bank_id` | `int(10) unsigned` | NO | - | `0` | - |
| `amount` | `double` | NO | - | `0` | - |
| `vat` | `double` | NO | - | `0` | - |
| `total_amount` | `double` | NO | - | `0` | - |
| `status` | `int(11)` | NO | - | `0` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-invoice-types"></a> Table: `invoice_types`
- **Columns**: `4` | **Records**: `2`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-invoice-banks"></a> Table: `invoice_banks`
- **Columns**: `10` | **Records**: `4`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `beneficary_account_name` | `varchar(191)` | NO | - | - | - |
| `bank_name` | `varchar(191)` | NO | - | - | - |
| `bank_branch` | `varchar(191)` | YES | - | - | - |
| `account_no` | `varchar(191)` | NO | - | - | - |
| `account_currency` | `varchar(191)` | NO | - | - | - |
| `iban_no` | `varchar(191)` | YES | - | - | - |
| `swift_code` | `varchar(191)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-invoice-product-details"></a> Table: `invoice_product_details`
- **Columns**: `11` | **Records**: `0`
- **Foreign Key Constraints**:
  - `invoice_id` ➔ `invoices.id` *(invoice_product_details_invoice_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `invoice_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `product` | `varchar(191)` | NO | - | - | - |
| `unit` | `varchar(191)` | YES | - | - | - |
| `qty` | `int(11)` | NO | - | - | - |
| `rate` | `double` | NO | - | - | - |
| `amount` | `double` | NO | - | - | - |
| `vat` | `double` | NO | - | - | - |
| `total_amount` | `double` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-invoice-service-details"></a> Table: `invoice_service_details`
- **Columns**: `6` | **Records**: `0`
- **Foreign Key Constraints**:
  - `invoice_id` ➔ `invoices.id` *(invoice_service_details_invoice_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `invoice_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `description` | `text` | NO | - | - | - |
| `amount` | `double` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-invoice-requests"></a> Table: `invoice_requests`
- **Columns**: `10` | **Records**: `0`
- **Foreign Key Constraints**:
  - `user_id` ➔ `users.id` *(invoice_requests_user_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `user_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `requestable_id` | `int(10) unsigned` | NO | - | - | - |
| `requestable_type` | `varchar(191)` | NO | - | - | - |
| `note` | `text` | YES | - | - | - |
| `payment_terms` | `varchar(191)` | YES | - | - | - |
| `delivery_date` | `date` | YES | - | - | - |
| `status` | `int(11)` | NO | - | `0` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-invoice-request-products"></a> Table: `invoice_request_products`
- **Columns**: `11` | **Records**: `0`
- **Foreign Key Constraints**:
  - `request_id` ➔ `invoice_requests.id` *(invoice_request_products_request_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `request_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `product` | `varchar(191)` | NO | - | - | - |
| `unit` | `varchar(191)` | YES | - | - | - |
| `qty` | `int(11)` | NO | - | - | - |
| `rate` | `double` | NO | - | - | - |
| `amount` | `double` | NO | - | - | - |
| `vat` | `double` | NO | - | - | - |
| `total_amount` | `double` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-payment-invoices"></a> Table: `payment_invoices`
- **Columns**: `23` | **Records**: `0`
- **Foreign Key Constraints**:
  - `lpoout_id` ➔ `lpoouts.id` *(payment_invoices_lpoout_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `type` | `varchar(191)` | YES | - | - | - |
| `invoice_no` | `varchar(191)` | YES | - | - | - |
| `lpoout_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `invoice_request_id` | `int(11)` | YES | - | - | - |
| `vendor_id` | `int(11)` | YES | - | - | - |
| `project_id` | `int(11)` | YES | - | - | - |
| `start_date` | `date` | YES | - | - | - |
| `end_date` | `date` | YES | - | - | - |
| `amount` | `double` | NO | - | - | - |
| `vat` | `double` | NO | - | - | - |
| `total_amount` | `double` | NO | - | - | - |
| `note` | `text` | YES | - | - | - |
| `status` | `int(11)` | NO | - | `0` | - |
| `is_historical` | `tinyint(1)` | NO | - | `0` | - |
| `source_reference` | `varchar(191)` | YES | - | - | - |
| `source_date` | `date` | YES | - | - | - |
| `historical_party` | `varchar(191)` | YES | - | - | - |
| `historical_project` | `varchar(191)` | YES | - | - | - |
| `created_by` | `bigint(20) unsigned` | YES | - | - | - |
| `document_path` | `varchar(191)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-balance-accounts"></a> Table: `balance_accounts`
- **Columns**: `6` | **Records**: `9`
- **Foreign Key Constraints**:
  - `user_id` ➔ `users.id` *(balance_accounts_user_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `balance` | `double` | NO | - | `0` | - |
| `type` | `varchar(191)` | NO | **MUL** | - | - |
| `user_id` | `bigint(20) unsigned` | YES | **MUL** | `1` | - |
| `code` | `varchar(191)` | YES | - | - | - |
| `account_type` | `varchar(191)` | YES | - | - | - |

#### <a id="table-balance-transactions"></a> Table: `balance_transactions`
- **Columns**: `8` | **Records**: `0`
- **Foreign Key Constraints**:
  - `account_id` ➔ `balance_accounts.id` *(balance_transactions_account_id_foreign)*
  - `extra_account_id` ➔ `balance_accounts.id` *(balance_transactions_extra_account_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `account_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `extra_account_id` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `amount` | `double` | NO | - | `0` | - |
| `data` | `longtext` | YES | - | - | - |
| `created_at` | `timestamp` | NO | - | `current_timestamp()` | `on update current_timestamp()` |
| `reference_type` | `varchar(191)` | YES | **MUL** | - | - |
| `reference_id` | `bigint(20) unsigned` | YES | **MUL** | - | - |

#### <a id="table-transactions"></a> Table: `transactions`
- **Columns**: `20` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `date_time` | `timestamp` | NO | - | `current_timestamp()` | `on update current_timestamp()` |
| `type` | `varchar(191)` | NO | - | - | - |
| `transactionable_id` | `int(11)` | YES | - | - | - |
| `transactionable_type` | `varchar(191)` | YES | - | - | - |
| `expense_account` | `int(10) unsigned` | YES | - | - | - |
| `account_id` | `int(10) unsigned` | NO | - | - | - |
| `transaction_type` | `int(11)` | NO | - | - | - |
| `payment_type` | `int(11)` | NO | - | - | - |
| `payment_no` | `varchar(191)` | YES | - | - | - |
| `clearance_date` | `varchar(191)` | YES | - | - | - |
| `bank_name` | `varchar(191)` | YES | - | - | - |
| `vat` | `double` | NO | - | - | - |
| `amount` | `double` | NO | - | - | - |
| `total` | `double` | NO | - | - | - |
| `note` | `text` | YES | - | - | - |
| `from_account` | `int(11)` | YES | - | - | - |
| `status` | `int(11)` | NO | - | `0` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-petty-cashes"></a> Table: `petty_cashes`
- **Columns**: `16` | **Records**: `0`
- **Foreign Key Constraints**:
  - `vendor_id` ➔ `vendors.id` *(petty_cashes_vendor_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `voucher_no` | `varchar(191)` | YES | - | - | - |
| `account_id` | `int(10) unsigned` | NO | - | - | - |
| `vendor_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `user_id` | `bigint(20)` | YES | - | - | - |
| `project_id` | `int(10) unsigned` | YES | - | - | - |
| `date_time` | `timestamp` | NO | - | `current_timestamp()` | `on update current_timestamp()` |
| `type` | `int(11)` | NO | - | - | - |
| `description` | `text` | YES | - | - | - |
| `amount` | `double(8,2)` | NO | - | - | - |
| `vat` | `double(8,2)` | NO | - | - | - |
| `total_amount` | `double(8,2)` | NO | - | - | - |
| `is_advance` | `int(11)` | YES | - | - | - |
| `is_user_deduction` | `int(11)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-petty-cash-expenses"></a> Table: `petty_cash_expenses`
- **Columns**: `7` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `category` | `varchar(191)` | YES | - | - | - |
| `amount` | `double(8,2)` | YES | - | - | - |
| `date` | `date` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 8. Payment Bookings (Sequential 2-Level Review Engine)

#### <a id="table-payment-bookings"></a> Table: `payment_bookings`
- **Columns**: `24` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `reference_no` | `varchar(40)` | NO | **UNI** | - | - |
| `booking_type` | `varchar(10)` | NO | **MUL** | - | - |
| `payee` | `varchar(191)` | NO | - | - | - |
| `payment_against` | `varchar(191)` | YES | - | - | - |
| `purpose` | `text` | YES | - | - | - |
| `amount` | `decimal(15,2)` | NO | - | `0.00` | - |
| `project_cost_centre` | `varchar(191)` | YES | - | - | - |
| `booking_date` | `date` | YES | - | - | - |
| `cheque_number` | `varchar(60)` | YES | - | - | - |
| `cheque_date` | `date` | YES | - | - | - |
| `bank_account` | `varchar(191)` | YES | - | - | - |
| `release_date` | `date` | YES | **MUL** | - | - |
| `cash_account` | `varchar(191)` | YES | - | - | - |
| `payment_date` | `date` | YES | **MUL** | - | - |
| `status` | `tinyint(3) unsigned` | NO | **MUL** | `0` | - |
| `created_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `submitted_at` | `timestamp` | YES | - | - | - |
| `verified_at` | `timestamp` | YES | - | - | - |
| `approved_at` | `timestamp` | YES | - | - | - |
| `rejected_at` | `timestamp` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `deleted_at` | `timestamp` | YES | - | - | - |

#### <a id="table-payment-booking-approvals"></a> Table: `payment_booking_approvals`
- **Columns**: `9` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `payment_booking_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `level` | `tinyint(3) unsigned` | NO | - | - | - |
| `user_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `decision` | `tinyint(3) unsigned` | NO | - | - | - |
| `note` | `text` | YES | - | - | - |
| `decided_at` | `timestamp` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 9. Staff, HR Personnel, Requisitions, Payroll & Letters

#### <a id="table-staf-profile"></a> Table: `staf_profile`
- **Columns**: `26` | **Records**: `2`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | YES | - | - | - |
| `staf_type` | `varchar(191)` | YES | - | - | - |
| `last_name` | `varchar(191)` | YES | - | - | - |
| `nationality` | `varchar(191)` | YES | - | - | - |
| `gender` | `varchar(191)` | YES | - | - | - |
| `joining_date` | `date` | YES | - | - | - |
| `dob` | `date` | YES | - | - | - |
| `passport_expiry` | `date` | YES | - | - | - |
| `visa_expiry` | `date` | YES | - | - | - |
| `emirates_id_expiry` | `date` | YES | - | - | - |
| `labor_card_expiry` | `date` | YES | - | - | - |
| `driver_permit_expiry` | `date` | YES | - | - | - |
| `last_vacation_start` | `date` | YES | - | - | - |
| `last_vacation_end` | `date` | YES | - | - | - |
| `last_vacation_days` | `int(11)` | YES | - | - | - |
| `last_increment` | `date` | YES | - | - | - |
| `last_increment_amount` | `int(11)` | YES | - | - | - |
| `basic_salary` | `double` | NO | - | `0` | - |
| `total_salary` | `double` | NO | - | `0` | - |
| `overtime_rate` | `double` | NO | - | `0` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `mobile_no` | `varchar(191)` | YES | - | - | - |
| `home_mobile_no` | `varchar(191)` | YES | - | - | - |
| `exclude_from_expiry` | `tinyint(1)` | NO | - | `0` | - |

#### <a id="table-staf-dates"></a> Table: `staf_dates`
- **Columns**: `7` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `staff_id` | `int(11)` | NO | - | - | - |
| `start_date` | `date` | NO | - | - | - |
| `end_date` | `date` | YES | - | - | - |
| `days` | `int(11)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-staff-requests"></a> Table: `staff_requests`
- **Columns**: `12` | **Records**: `0`
- **Foreign Key Constraints**:
  - `staf_id` ➔ `staf_profile.id` *(staff_requests_staf_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `staf_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `type` | `varchar(191)` | YES | - | - | - |
| `start_date` | `date` | YES | - | - | - |
| `end_date` | `date` | YES | - | - | - |
| `advance_money` | `double` | YES | - | - | - |
| `device_type` | `varchar(191)` | YES | - | - | - |
| `letter_type` | `varchar(191)` | YES | - | - | - |
| `note` | `text` | YES | - | - | - |
| `status` | `tinyint(1)` | NO | - | `0` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-request-approvals"></a> Table: `request_approvals`
- **Columns**: `9` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `request_type` | `varchar(20)` | NO | **MUL** | - | - |
| `request_id` | `int(10) unsigned` | NO | - | - | - |
| `user_id` | `int(10) unsigned` | NO | - | - | - |
| `decision` | `tinyint(3) unsigned` | NO | - | - | - |
| `note` | `text` | YES | - | - | - |
| `decided_at` | `timestamp` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-request-forms"></a> Table: `request_forms`
- **Columns**: `9` | **Records**: `0`
- **Foreign Key Constraints**:
  - `user_id` ➔ `users.id` *(request_forms_user_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `date_time` | `timestamp` | NO | - | `current_timestamp()` | `on update current_timestamp()` |
| `user_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `name` | `varchar(191)` | NO | - | - | - |
| `note` | `text` | NO | - | - | - |
| `comments` | `text` | YES | - | - | - |
| `status` | `int(11)` | NO | - | `0` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-labor-requests"></a> Table: `labor_requests`
- **Columns**: `10` | **Records**: `0`
- **Foreign Key Constraints**:
  - `labor_id` ➔ `staf_profile.id` *(labor_requests_labor_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `labor_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `type` | `varchar(191)` | YES | - | - | - |
| `start_date` | `date` | YES | - | - | - |
| `end_date` | `date` | YES | - | - | - |
| `advance_money` | `double` | YES | - | - | - |
| `note` | `text` | YES | - | - | - |
| `status` | `tinyint(1)` | NO | - | `0` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-labour-assignments"></a> Table: `labour_assignments`
- **Columns**: `10` | **Records**: `0`
- **Foreign Key Constraints**:
  - `labor_id` ➔ `staf_profile.id` *(labour_assignments_labor_id_foreign)*
  - `project_id` ➔ `projects.id` *(labour_assignments_project_id_foreign)*
  - `visit_schedule_id` ➔ `visit_schedules.id` *(labour_assignments_visit_schedule_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `labor_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `project_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `visit_schedule_id` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `assignment_start_date` | `date` | YES | - | - | - |
| `assignment_end_date` | `date` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `hours_worked` | `int(11)` | YES | - | - | - |
| `overtime_hours` | `int(11)` | NO | - | `0` | - |

#### <a id="table-letters"></a> Table: `letters`
- **Columns**: `11` | **Records**: `0`
- **Foreign Key Constraints**:
  - `staff_profile_id` ➔ `staf_profile.id` *(letters_staff_profile_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `ref_no` | `varchar(191)` | YES | - | - | - |
| `days_deduct` | `int(10) unsigned` | YES | - | - | - |
| `staff_profile_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `type` | `enum('warning','appreciation','general_notice','poor_performance_notice','accommodation_notice','vehicle_notice','attendance_notice','weather_notice','eid_holidays_notice')` | YES | - | - | - |
| `title` | `varchar(191)` | NO | - | - | - |
| `content` | `text` | NO | - | - | - |
| `issued_by` | `varchar(191)` | NO | - | - | - |
| `issued_at` | `date` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-monthly-staff-reports"></a> Table: `monthly_staff_reports`
- **Columns**: `15` | **Records**: `0`
- **Foreign Key Constraints**:
  - `staff_id` ➔ `staf_profile.id` *(monthly_staff_reports_staff_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `staff_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `month` | `tinyint(3) unsigned` | NO | - | - | - |
| `year` | `smallint(5) unsigned` | NO | - | - | - |
| `safety_avg` | `double(8,2)` | NO | - | - | - |
| `communication_avg` | `double(8,2)` | NO | - | - | - |
| `attendance_avg` | `double(8,2)` | NO | - | - | - |
| `time_management_avg` | `double(8,2)` | NO | - | - | - |
| `job_responsibility_avg` | `double(8,2)` | NO | - | - | - |
| `material_handling_avg` | `double(8,2)` | NO | - | - | - |
| `document_handling_avg` | `double(8,2)` | NO | - | - | - |
| `competency_avg` | `double(8,2)` | NO | - | - | - |
| `final_score` | `double(8,2)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-staff-ratings"></a> Table: `staff_ratings`
- **Columns**: `15` | **Records**: `0`
- **Foreign Key Constraints**:
  - `engineer_id` ➔ `users.id` *(staff_ratings_engineer_id_foreign)*
  - `staff_id` ➔ `staf_profile.id` *(staff_ratings_staff_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `staff_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `engineer_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `month` | `tinyint(3) unsigned` | NO | - | - | - |
| `year` | `smallint(5) unsigned` | NO | - | - | - |
| `safety_compliance` | `tinyint(3) unsigned` | NO | - | - | - |
| `communication` | `tinyint(3) unsigned` | NO | - | - | - |
| `attendance` | `tinyint(3) unsigned` | NO | - | - | - |
| `time_management` | `tinyint(3) unsigned` | NO | - | - | - |
| `job_responsibility` | `tinyint(3) unsigned` | NO | - | - | - |
| `material_handling` | `tinyint(3) unsigned` | NO | - | - | - |
| `document_handling` | `tinyint(3) unsigned` | NO | - | - | - |
| `competency` | `tinyint(3) unsigned` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-tickets"></a> Table: `tickets`
- **Columns**: `14` | **Records**: `0`
- **Foreign Key Constraints**:
  - `staf_id` ➔ `staf_profile.id` *(tickets_staf_id_foreign)*
  - `updated_by` ➔ `users.id` *(tickets_updated_by_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `staf_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `ticket_date` | `date` | NO | - | - | - |
| `agent_name` | `varchar(191)` | NO | - | - | - |
| `travel_type` | `enum('One Way','Two Way')` | NO | - | - | - |
| `travel_date` | `date` | NO | - | - | - |
| `return_date` | `date` | YES | - | - | - |
| `amount` | `decimal(10,2)` | NO | - | - | - |
| `vat` | `decimal(10,2)` | NO | - | - | - |
| `total_value` | `decimal(10,2)` | NO | - | - | - |
| `payment_status` | `varchar(191)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `updated_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |

#### <a id="table-payrolls"></a> Table: `payrolls`
- **Columns**: `11` | **Records**: `0`
- **Foreign Key Constraints**:
  - `member_id` ➔ `staf_profile.id` *(payrolls_member_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `date` | `date` | YES | - | - | - |
| `member_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `absents` | `int(11)` | YES | - | - | - |
| `hours` | `double` | YES | - | - | - |
| `plus_adjustment` | `double` | NO | - | `0` | - |
| `minus_adjustment` | `double` | NO | - | `0` | - |
| `total_amount` | `double` | NO | - | - | - |
| `note` | `text` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-employees"></a> Table: `employees`
- **Columns**: `8` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `email` | `varchar(191)` | YES | - | - | - |
| `code` | `varchar(191)` | YES | - | - | - |
| `contact_no` | `varchar(191)` | YES | - | - | - |
| `balance` | `double` | NO | - | `0` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-tasks"></a> Table: `tasks`
- **Columns**: `9` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `start_date` | `datetime` | YES | - | - | - |
| `end_date` | `datetime` | YES | - | - | - |
| `title` | `varchar(191)` | YES | - | - | - |
| `assigned` | `bigint(20)` | YES | - | - | - |
| `description` | `varchar(191)` | YES | - | - | - |
| `status` | `tinyint(1)` | YES | - | `1` | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

### 10. Labor Management, Sites & Geofenced QR Attendance

#### <a id="table-sites"></a> Table: `sites`
- **Columns**: `5` | **Records**: `0`
- **Foreign Key Constraints**:
  - `engineer_id` ➔ `users.id` *(sites_engineer_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `site_name` | `varchar(191)` | NO | **UNI** | - | - |
| `engineer_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-attendances"></a> Table: `attendances`
- **Columns**: `11` | **Records**: `0`
- **Foreign Key Constraints**:
  - `approved_by` ➔ `users.id` *(attendances_approved_by_foreign)*
  - `foreman_id` ➔ `users.id` *(attendances_foreman_id_foreign)*
  - `labor_id` ➔ `staf_profile.id` *(attendances_labor_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `labor_id` | `int(10) unsigned` | YES | **MUL** | - | - |
| `foreman_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `approved_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `attendance_date` | `date` | NO | **MUL** | - | - |
| `status` | `enum('pending','approved','rejected')` | NO | **MUL** | `pending` | - |
| `notes` | `text` | YES | - | - | - |
| `marked_at` | `timestamp` | YES | - | - | - |
| `approved_at` | `timestamp` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-attendance-approvals"></a> Table: `attendance_approvals`
- **Columns**: `9` | **Records**: `0`
- **Foreign Key Constraints**:
  - `approved_by` ➔ `users.id` *(attendance_approvals_approved_by_foreign)*
  - `attendance_id` ➔ `attendances.id` *(attendance_approvals_attendance_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `attendance_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `approved_by` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `action` | `enum('approved','rejected')` | NO | - | - | - |
| `informed` | `enum('informed','uninformed')` | YES | - | - | - |
| `specific_reason` | `varchar(191)` | YES | - | - | - |
| `reason` | `text` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-attendance-labor-details"></a> Table: `attendance_labor_details`
- **Columns**: `8` | **Records**: `0`
- **Foreign Key Constraints**:
  - `attendance_id` ➔ `attendances.id` *(attendance_labor_details_attendance_id_foreign)*
  - `labor_id` ➔ `staf_profile.id` *(attendance_labor_details_labor_id_foreign)*

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `attendance_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `labor_id` | `int(10) unsigned` | NO | **MUL** | - | - |
| `overtime_hours` | `decimal(5,2)` | NO | - | `0.00` | - |
| `site_id` | `bigint(20) unsigned` | YES | - | - | - |
| `custom_site_name` | `varchar(191)` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-attendance-sessions"></a> Table: `attendance_sessions`
- **Columns**: `20` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `user_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `session_date` | `date` | NO | **MUL** | - | - |
| `clock_in_time` | `datetime` | NO | - | - | - |
| `clock_in_latitude` | `decimal(10,8)` | NO | - | - | - |
| `clock_in_longitude` | `decimal(11,8)` | NO | - | - | - |
| `clock_in_distance_meters` | `decimal(8,2)` | YES | - | - | - |
| `clock_out_time` | `datetime` | YES | - | - | - |
| `clock_out_latitude` | `decimal(10,8)` | YES | - | - | - |
| `clock_out_longitude` | `decimal(11,8)` | YES | - | - | - |
| `clock_out_distance_meters` | `decimal(8,2)` | YES | - | - | - |
| `duration_minutes` | `int(11)` | YES | - | - | - |
| `shift_window` | `enum('shift_1','shift_2')` | NO | **MUL** | - | - |
| `is_late` | `tinyint(1)` | NO | **MUL** | `0` | - |
| `work_mode` | `enum('split_shift','continuous')` | NO | - | `split_shift` | - |
| `session_status` | `enum('open','closed')` | NO | **MUL** | `open` | - |
| `notes` | `text` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `deleted_at` | `timestamp` | YES | - | - | - |

#### <a id="table-qr-staff-attendances"></a> Table: `qr_staff_attendances`
- **Columns**: `25` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `staff_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `scanned_by` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `site_id` | `bigint(20) unsigned` | YES | - | - | - |
| `custom_site_name` | `varchar(191)` | YES | - | - | - |
| `attendance_date` | `date` | NO | **MUL** | - | - |
| `check_in_time` | `datetime` | NO | - | - | - |
| `check_out_time` | `datetime` | YES | - | - | - |
| `duration_minutes` | `int(11)` | YES | - | - | - |
| `overtime_minutes` | `int(11)` | NO | - | `0` | - |
| `shift_end_time` | `time` | NO | - | `17:00:00` | - |
| `status` | `enum('checked_in','checked_out')` | NO | **MUL** | `checked_in` | - |
| `review_status` | `enum('pending','approved','rejected')` | NO | **MUL** | `pending` | - |
| `reviewed_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `reviewed_at` | `datetime` | YES | - | - | - |
| `review_notes` | `text` | YES | - | - | - |
| `final_overtime_source` | `enum('manual','qr','custom')` | YES | **MUL** | - | - |
| `final_overtime_minutes` | `int(11)` | YES | - | - | - |
| `finalized_by` | `bigint(20) unsigned` | YES | **MUL** | - | - |
| `finalized_at` | `datetime` | YES | - | - | - |
| `final_decision_notes` | `text` | YES | - | - | - |
| `notes` | `text` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |
| `deleted_at` | `timestamp` | YES | - | - | - |

### 11. System Platform, Media, Lookups & Notifications

#### <a id="table-media"></a> Table: `media`
- **Columns**: `15` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `model_type` | `varchar(191)` | NO | **MUL** | - | - |
| `model_id` | `bigint(20) unsigned` | NO | - | - | - |
| `collection_name` | `varchar(191)` | NO | - | - | - |
| `name` | `varchar(191)` | NO | - | - | - |
| `file_name` | `varchar(191)` | NO | - | - | - |
| `mime_type` | `varchar(191)` | YES | - | - | - |
| `disk` | `varchar(191)` | NO | - | - | - |
| `size` | `bigint(20) unsigned` | NO | - | - | - |
| `manipulations` | `longtext` | NO | - | - | - |
| `custom_properties` | `longtext` | NO | - | - | - |
| `responsive_images` | `longtext` | NO | - | - | - |
| `order_column` | `int(10) unsigned` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-notifications"></a> Table: `notifications`
- **Columns**: `8` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `char(36)` | NO | **PRI** | - | - |
| `type` | `varchar(191)` | NO | - | - | - |
| `notifiable_type` | `varchar(191)` | NO | **MUL** | - | - |
| `notifiable_id` | `bigint(20) unsigned` | NO | - | - | - |
| `data` | `text` | NO | - | - | - |
| `read_at` | `timestamp` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-comments"></a> Table: `comments`
- **Columns**: `8` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `commentable_id` | `bigint(20) unsigned` | NO | - | - | - |
| `commentable_type` | `varchar(191)` | NO | - | - | - |
| `comments` | `text` | NO | - | - | - |
| `status` | `int(11)` | NO | - | `0` | - |
| `user_id` | `int(11)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-device-tokens"></a> Table: `device_tokens`
- **Columns**: `8` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `user_id` | `bigint(20) unsigned` | NO | **MUL** | - | - |
| `token` | `varchar(512)` | NO | **UNI** | - | - |
| `platform` | `varchar(20)` | NO | - | `android` | - |
| `device_name` | `varchar(191)` | YES | - | - | - |
| `last_used_at` | `timestamp` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-documents"></a> Table: `documents`
- **Columns**: `6` | **Records**: `0`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `type` | `varchar(191)` | NO | - | - | - |
| `date` | `date` | YES | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-lookups"></a> Table: `lookups`
- **Columns**: `5` | **Records**: `13`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | **PRI** | - | `auto_increment` |
| `name` | `varchar(191)` | NO | - | - | - |
| `tag` | `varchar(191)` | NO | - | - | - |
| `created_at` | `timestamp` | YES | - | - | - |
| `updated_at` | `timestamp` | YES | - | - | - |

#### <a id="table-migrations"></a> Table: `migrations`
- **Columns**: `3` | **Records**: `131`

| Field Name | Type | Nullable | Key | Default | Extra |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | **PRI** | - | `auto_increment` |
| `migration` | `varchar(191)` | NO | - | - | - |
| `batch` | `int(11)` | NO | - | - | - |

