# Security Architecture & Rules of Engagement

Security standards, multi-guard authentication boundaries, and permission governance for FTS Portal.

---

## 1. Authentication Guards & Boundaries

The application isolates user contexts using three distinct authentication guards:

| Guard | Driver | Provider Model | Purpose | Session Key |
|---|---|---|---|---|
| `web` | `session` | `App\User` | Internal office staff, engineers, and administrators | `login_web_*` |
| `company` | `session` | `App\Models\Company` | External client contacts on the customer extranet | `login_company_*` |
| `api` | `token` | `App\User` | Mobile companion apps and programmatic integrations | Stateless Bearer |

### Guard Separation Rule:
* Never cross-authenticate models across guards.
* Never use `auth()->user()` in client portal routes without specifying `Auth::guard('company')->user()`.
* Internal staff sessions and client sessions run on separate cookie namespaces and do not collide.

---

## 2. Authorization & RBAC Matrix

1. **Role-Based Routing (`RolesAuth` Middleware)**:
   * The global `RolesAuth` middleware dynamically derives required permissions from the current route name prefix:
     * e.g., Route `projects.index` checks for `projects` permission.
     * Route `quotations.create` checks for `quotations` permission.
   * Modifying route names can silently alter required permissions. Always audit permissions when editing route names.
2. **Global Delete Lock (`CheckDeletePermission`)**:
   * Any HTTP request matching `DELETE` method is blocked unless the user explicitly possesses the `deletes` permission.
3. **The Two-Person Review Rule (Payment Bookings)**:
   * A single user is programmatically prevented from approving both Level 1 (Verification) and Level 2 (Final Approval) on the same booking record, eliminating unilateral financial fraud.

---

## 3. Multi-Tenant Scoping & IDOR Prevention

* **Client Extranet Scoping**:
  All client queries must be explicitly scoped to the authenticated company:
  ```php
  $companyId = Auth::guard('company')->id();
  $records = Invoice::where('company_id', $companyId)->get();
  ```
* **Private Entity Scoping (HTTP 404 vs 403)**:
  When an unauthorized user attempts to view a private financial record (such as an unapproved payment booking owned by another accountant), return HTTP `404 Not Found` rather than `403 Forbidden`. This makes private records completely indistinguishable from non-existent records, preventing attackers from harvesting valid record IDs.

---

## 4. File Upload & Storage Security

* All user-submitted files (AMC site survey photos, receipts, identity documents) must pass through Spatie Media Library or strict FormRequest validation checking:
  * Allowed extensions and MIME types (`image/jpeg`, `image/png`, `application/pdf`).
  * File size limits (maximum 10MB per attachment).
* Never permit executable uploads (`.php`, `.sh`, `.exe`, `.bat`).
* Storage disk must point to `storage/app/public` linked via directory junction to `public/storage`.
