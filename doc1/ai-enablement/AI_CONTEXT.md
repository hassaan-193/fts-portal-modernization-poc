# AI Context & Working Guidelines

A practical primer for AI assistants and senior engineers maintaining and extending the FTS Portal codebase.

---

## 1. How to Understand This Monolith

* **Enterprise Operations Monolith**: This repository is a cohesive, production-grade enterprise system powering engineering operations, financials, HR compliance, and field service. It is not a generic template or starter project.
* **Mixed Architectural Patterns**:
  * Legacy CRUD screens follow the standard Model-View-Controller-Repository pattern with FormRequests.
  * Server-side tables utilize Yajra DataTables with complex closures and custom column decorators.
  * Newer subsystems (Payment Bookings, Attendance) follow modern service-oriented architectures with strict domain services (`app/Services`) and transactional boundaries.
* **Respect Existing Code Style**: When fixing or extending a module, adopt the local conventions of that specific module rather than applying blanket refactors.

---

## 2. Core Engineering Principles

1. **Verify Before Modifying**:
   * Always check the active route file, authentication guard, and authorization middleware before changing controller logic.
2. **Preserve Side Effects**:
   * Critical write operations (e.g. creating invoices, approving bookings, updating inventory) often trigger double-entry ledger transactions, email dispatches, or PDF generations. Ensure side effects are preserved inside database transactions (`DB::transaction`).
3. **Never Chain Relationships Unchecked in DataTables**:
   * Chained relationships without null checks (e.g., `$row->quotation->company->name`) will throw PHP exceptions on orphaned or deleted relations, crashing DataTables with HTTP 500 (`Ajax error (tn/7)`). Always use null-safe checks.
4. **Preserve Multi-Guard Isolation**:
   * Keep `web`, `company`, and `api` contexts strictly isolated.

---

## 3. Pre-Flight Quality Checklist

Before committing any modifications:
* [ ] Does the change adhere to the appropriate authentication guard (`web`, `company`, or `api`)?
* [ ] Are all relational property accesses null-safe in DataTables and Blade templates?
* [ ] Are FormRequest validation rules enforced at the controller entry point?
* [ ] Are multi-step database writes wrapped within a database transaction?
* [ ] Is error logging implemented without leaking sensitive tokens or passwords?
