# Working Agreements & Engineering Protocols

Operational agreements and protocols for developers and AI contributors collaborating on the FTS Portal repository.

---

## 1. Principles of Collaboration

1. **Analyze First, Modify Second**:
   * Map the entire end-to-end request flow (Route ➔ Middleware ➔ FormRequest ➔ Controller ➔ Service/Repository ➔ View/API Response) before writing or changing code.
2. **Smallest Safe Slice**:
   * Favor atomic, targeted edits that solve the exact requirement over sweeping aesthetic refactors.
3. **Preserve Production Behavior**:
   * In an enterprise ERP handling live financials, payroll, and contracts, unexpected regressions can disrupt business operations. Never refactor surrounding untouched modules without explicit instruction.

---

## 2. Managing Risk & Uncertainty

* **Distinguish Fact from Assumption**:
  * Clearly separate verified facts (derived from database schemas, routes, active code) from assumptions.
* **Preserve Transactional Integrity**:
  * Any operation that affects accounting ledgers, purchase order approvals, or inventory counts must remain wrapped in transactional boundaries.
* **Audit Permissions When Changing Routes**:
  * Route naming directly dictates Spatie permission checks through `RolesAuth`. Modifying a route name without verifying permissions can lock users out or expose sensitive screens.

---

## 3. Pre-Merge Verification

Every change must be validated through:
1. Direct route execution or curl/Postman test.
2. Verification of null-safety in associated Yajra DataTables.
3. Checking error logs in `storage/logs/laravel.log` to confirm no hidden warnings or deprecated PHP notices are thrown.
