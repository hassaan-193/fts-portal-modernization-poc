# Payment Bookings — Module Specification & API Reference

Backend reference manual for the `payment_bookings` module. Written for engineers, mobile developers, and technical auditors building integrations or extending financial workflows.

---

## 1. Business Logic & Core Functionality

The **Payment Bookings** module allows financial controllers and accountants to record payments about to be issued from the company — either by **Cheque** or in **Cash**. Every entry is known as a *booking*. 

To maintain strict fiduciary integrity, every booking passes through a **sequential two-level review chain** (Verification by Level 1, followed by Final Approval by Level 2) before funds are recognized as released.

### Key Design Principles:
* **Decoupled Architecture**: Payee, project cost center, bank account, and cash account are stored as clean string descriptors rather than restrictive foreign keys. The module functions independently of legacy vendor or invoice tables.
* **Single Table Storage**: Cheque and cash entries are unified in `payment_bookings`. Type-specific attributes are automatically nulled by `PaymentBookingService` during writes to prevent stale data cross-contamination.

---

## 2. Database Schema

### `payment_bookings`
| Column | Type | Description |
|---|---|---|
| `id` | int unsigned PK | Auto-incrementing primary key |
| `reference_no` | varchar(40) unique | Unique serial (e.g. `CHQ-2026-0001` or `CSH-2026-0001`). Generated at insert. |
| `booking_type` | varchar(10) | `cheque` or `cash`. Immutable after creation. |
| `payee` | varchar(191) | Payee / Beneficiary name (Cheque) or Recipient name (Cash). |
| `payment_against` | varchar(191) | Context descriptor (e.g., "Project Material Advance", "Subcontractor Retention"). |
| `purpose` | text | Detailed operational explanation of the expenditure. |
| `amount` | decimal(15,2) | Transaction value in AED. |
| `project_cost_centre` | varchar(191) | Associated project code or overhead cost center. |
| `booking_date` | date | Date entry was officially recorded. |
| `cheque_number` | varchar(60) null | Physical cheque serial number (Cheque only). |
| `cheque_date` | date null | Issue date written on the cheque (Cheque only). |
| `bank_account` | varchar(191) null | Originating bank account (Cheque only). |
| `release_date` | date null | Cheque release date — **drives cash release calculation**. |
| `cash_account` | varchar(191) null | Originating petty cash / vault account (Cash only). |
| `payment_date` | date null | Date cash is disbursed — **drives cash release calculation**. |
| `status` | tinyint unsigned | Numerical state (0 to 5, see state machine below). |
| `created_by` | bigint unsigned | References `users.id` (Booking originator). |
| `submitted_at` | timestamp null | Timestamp when moved from Draft to Pending Verification. |
| `verified_at` | timestamp null | Timestamp when Level 1 was approved. |
| `approved_at` | timestamp null | Timestamp when Level 2 was approved. |
| `rejected_at` | timestamp null | Timestamp if rejected at any level. |
| `deleted_at` | timestamp null | Soft delete timestamp. |

### `payment_booking_approvals`
Tracks the sequential decision trail. Maximum of two records per booking.
| Column | Type | Description |
|---|---|---|
| `id` | int unsigned PK | Primary key |
| `payment_booking_id` | int unsigned | Foreign key to `payment_bookings.id` |
| `level` | tinyint unsigned | `1` = Verification, `2` = Final Approval |
| `user_id` | bigint unsigned | Deciding user ID (`users.id`) |
| `decision` | tinyint unsigned | `1` = Approved, `2` = Rejected, `3` = On Hold |
| `note` | text null | Decision comments. Mandatory for Rejections and Holds. |
| `decided_at` | timestamp | Decision audit timestamp |

---

## 3. Finite State Machine (Status Lifecycle)

```
[0: Draft] 
   │ (Submit)
   ▼
[1: Pending Verification] (Level 1 Review)
   ├── (Approve) ──▶ [2: Pending Approval] (Level 2 Review)
   │                    ├── (Approve) ──▶ [3: Approved] (Locked Financial Record)
   │                    ├── (Reject)  ──▶ [4: Rejected] ──(Edit & Resubmit)──▶ [1: Pending Verification]
   │                    └── (Hold)    ──▶ [5: On Hold]   ──(Resume)─────────▶ [2: Pending Approval]
   ├── (Reject)  ──▶ [4: Rejected] ──(Edit & Resubmit)──────────────────────▶ [1: Pending Verification]
   └── (Hold)    ──▶ [5: On Hold]   ──(Resume)─────────────────────────────▶ [1: Pending Verification]
```

### Status Code Dictionary:
* **0 (Draft)**: Created and saved by the accountant. Visible only to creator. Fully editable and deletable.
* **1 (Pending Verification)**: In the Level 1 queue. Still editable by creator.
* **2 (Pending Approval)**: Level 1 passed. Waiting in Level 2 queue. **Locked against creator edits.**
* **3 (Approved)**: Both levels cleared. Immutable financial document. Never deletable. Counts toward released liquidity once date arrives.
* **4 (Rejected)**: Refused by either Level 1 or Level 2. Mandatory note attached explaining why. Creator can edit fields and re-submit, which wipes the decision trail and restarts from Level 1.
* **5 (On Hold)**: Paused for clarification. Resuming puts the booking back into the queue of whichever level placed it on hold.

---

## 4. Governance & Role Permissions

The module enforces separation of duties using Spatie Laravel-Permission:

| Permission | Canonical Role | Capability |
|---|---|---|
| `paymentBookings` | Payment Booking User, Super-User | Create, edit, submit, and soft-delete own draft bookings |
| `verify_payment_bookings` | Payment Booking Verifier | Level 1 review queue, read all bookings, approve/reject/hold L1 |
| `approve_payment_bookings` | Payment Booking Approver | Level 2 review queue, read all bookings, approve/reject/hold L2 |

### Fiduciary Separation Rules:
1. **The Two-Person Rule**: A user cannot decide both Level 1 and Level 2 on the same booking. If an administrator holds both permissions, once they approve Level 1, the booking is hidden from their Level 2 queue.
2. **Sequential Lock**: Level 2 cannot make a decision until Level 1 has cleared. Attempting to bypass returns HTTP `403 Forbidden`.
3. **Private Scoping**: A user without review permissions can only read their own bookings (`created_by = auth()->id()`). Requesting another user's booking returns `404 Not Found`.

---

## 5. Released Money Calculation Engine

"Released money" is dynamically computed and never stored statically:
```
Released = (status === 3) AND (effective_date <= CURRENT_DATE)
```
Where `effective_date` is:
* `release_date` for Cheques.
* `payment_date` for Cash.

### Liquidity Summary Fields (`GET /api/v1/payment-bookings/summary`):
* `booked_this_month`: Total value booked in current calendar month (excluding drafts and rejected).
* `released_this_month`: Approved bookings whose effective date occurred within current month.
* `pending_release_this_month`: Approved bookings dated later in the current month.
* `released_next_month`: Approved bookings scheduled for the following month.
* `not_released`: Total approved funds with effective dates in the future.
* `awaiting_review`: Committed funds currently pending in review status (1, 2, or 5).
