# User & Employee Self-Service Portal Documentation

---

## 1. Overview & Audience
The **User Portal** is the dedicated self-service interface for field technicians, service engineers, site supervisors, and general office employees. It provides streamlined access to personal HR requests, mobile-first field inspection forms, offline drafts, and automated site check-in.

* **Guard**: `web` (Session driver)
* **Underlying Model**: `App\User` (`users` table)
* **Role Requirement**: `Staff` / `Staff Requester` / `AMC Reporter`
* **Access URL**: `http://127.0.0.1:8001/login`
* **Default Verified Account**: `user@user.com` / `password`

---

## 2. Core Functional Modules

### 2.1 Employee Self-Service — "My Requests" (`/own-staff-request`)
Eliminates paper forms by offering a digital request pipeline for employee needs:

1. **Leave Applications**:
   * Request Annual, Sick, Emergency, or Unpaid Leave.
   * Enter starting date, end date, total calendar days, and operational coverage notes.
   * Attach medical certificates or travel itineraries where required.
2. **Salary Advance & Emergency Loans**:
   * Request an emergency payroll advance with proposed deduction installments.
3. **Passport & Official Identity Document Release**:
   * Submit requests for temporary release of physical passports held for safe custody (e.g. for visa renewal, banking, or official travel).
4. **Tools, Laptops & Safety Equipment Requests**:
   * Request engineering tools (OTDR optical meters, drills, cable testers, laptops) or replacement personal protective equipment (PPE).
5. **Real-time Lifecycle Tracker**:
   * The employee tracks their request status in real time:
     `Submitted` ➔ `Department Head Endorsed` ➔ `HR / Director Approved` ➔ `Fulfilled`.

---

### 2.2 AMC Site Visit Reports & Field Service (`/projects/visit-form`)
Technicians conducting scheduled preventive maintenance or emergency breakdown repairs use this module directly on smartphones and tablets.

1. **Service Call Initialization**:
   * Select the target Client Company and active Project from the dropdown.
   * The system automatically generates a unique, sequential report code (e.g., `VR-2026-0042`).
2. **System Health Inspection Checklist**:
   * Mark the operational status of all installed subsystems:
     * **CCTV**: Cameras, recording storage, video encoders, power supplies, lens focus.
     * **Access Control**: Magnetic locks, push buttons, card readers, backup batteries.
     * **Fire Alarm / Audio**: Control panels, smoke detectors, manual call points.
   * Record engineer observations, corrective actions taken, and remaining deficiencies.
3. **Offline Draft Preservation (`/projects/amc-drafts`)**:
   * When working in underground basements, remote substations, or areas with poor cellular signal, technicians can click **Save Draft**.
   * Drafts remain preserved locally and in the database until network connectivity is restored.
4. **Digital Touchscreen Signature Capture**:
   * Present the mobile screen to the client's on-site facility representative.
   * The representative signs directly on the touchscreen canvas.
5. **Automated Submission & Dispatch**:
   * On submission, the system stamps the report with timestamp and GPS metadata, compiles a branded PDF report via `Barryvdh\DomPDF`, and delivers copies to both the client's email and internal management.

---

### 2.3 QR Attendance & Geofenced Check-In (`/api/v1/attendance`)
Designed for fast, non-contact attendance tracking for mobile engineers and site labor teams:

* **Site QR Verification**:
  * Technicians scan the active project or branch QR code using their mobile camera or companion app.
* **Geofence Protection**:
  * The backend verifies that the device's GPS coordinates fall within the authorized project radius (default: 500 meters) to prevent off-site check-ins.
* **Audit Trail**:
  * Records exact check-in time, check-out time, total shift duration, and site location.
