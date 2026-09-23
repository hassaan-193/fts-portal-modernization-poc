# Engineering & Coding Standards

Defensive development rules, design patterns, and repository conventions for the FTS Portal.

---

## 1. Defensive Coding & Reliability Rules

### 1.1 Yajra DataTables Null-Safety (CRITICAL)
In Yajra DataTables closures, **never** chain object relationships directly without null checks.
* ❌ **Vulnerable Pattern**:
  ```php
  // Crashes with HTTP 500 if quotation is unassigned or deleted,
  // throwing DataTables warning: Ajax error (tn/7) on the frontend:
  return $query->quotation->company->name;
  ```
* ✅ **Mandatory Null-Safe Pattern**:
  ```php
  if (!$query->quotation || !$query->quotation->company) {
      return '<span class="text-muted">-</span>';
  }
  return e($query->quotation->company->name);
  ```

---

### 1.2 Global Delete Interception
All HTTP `DELETE` requests in the web portal are globally intercepted by `App\Http\Middleware\CheckDeletePermission`.
* Deletion requires the authenticated user to explicitly possess the Spatie `deletes` permission.
* Never bypass this middleware in route definitions. If an entity requires soft or hard deletion, ensure the acting role is assigned `deletes`.

---

### 1.3 Windows Storage Symlink Preservation
In Windows / NTFS environments, Git clones can extract Linux symlinks as 21-byte plain text files pointing to `../storage/app/public`.
* If file uploads or signatures return 404, delete the plain text file `public/storage` and execute:
  ```powershell
  php artisan storage:link
  ```
  This creates an active NTFS directory junction pointing to `storage\app\public`.

---

## 2. Architectural Layers & Separation of Concerns

1. **Routes (`routes/web.php`, `routes/api.php`)**:
   * Keep route definitions thin. Bind middleware groups (`auth`, `auth:company`, `auth:api`, `role:Super-User`).
   * Route names must follow module prefixes (`projects.*`, `quotations.*`) because authorization middleware derives required permissions from route names.
2. **Form Requests (`app/Http/Requests/*`)**:
   * Perform all input validation in dedicated `FormRequest` classes (`CreateProjectRequest`, `UpdateProjectRequest`).
   * Do not write manual `$request->validate(...)` blocks inside controllers.
3. **Services (`app/Services/*`)**:
   * Encapsulate multi-step business logic, financial ledger postings, and state machine transitions in dedicated service classes.
   * Controllers should solely orchestrate input parsing, service invocation, and view/JSON response generation.
4. **Repositories (`app/Repositories/*`)**:
   * Encapsulate complex database queries and transactional persistence.
   * Do not scatter raw database joins across controller methods.

---

## 3. Response Conventions

* **Web Controllers**:
  * Flash success/error messages to the session (`Flash::success(...)` or `Flash::error(...)`).
  * Return redirects to standard resource routes (`return redirect(route('projects.index'));`).
* **API Controllers**:
  * Return structured JSON with consistent envelopes:
    ```json
    {
      "success": true,
      "data": { ... },
      "message": "Action completed successfully."
    }
    ```
  * Return explicit HTTP status codes: `200 OK`, `201 Created`, `422 Unprocessable Entity`, `401 Unauthorized`, `404 Not Found`.
