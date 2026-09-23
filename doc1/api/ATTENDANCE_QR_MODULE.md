# Attendance & QR Geofencing API Specification

Technical guide and API specification for the **Labor Attendance, Site Management, and Geofenced QR Check-In** system.

---

## 1. Architectural Overview

The attendance system provides mobile capabilities for site foremen, field technicians, and project managers:
1. **Foreman Daily Labor Attendance**: A site foreman can take roll call for assigned site laborers, record working hours and overtime, and submit the daily muster roll for manager approval.
2. **Manager Approval Workflow**: Construction managers review pending daily attendance rosters, verifying labor allocations against project budget milestones.
3. **QR + Geofenced Check-In**: Individual technicians scan a site-specific QR code. The server cross-validates the mobile device's GPS coordinates against the project site's allowed perimeter radius.

---

## 2. Authentication

All endpoints require Bearer Token authentication via the `api` guard:
```http
Authorization: Bearer <api_token>
```
* Foremen and managers obtain tokens via `POST /api/v1/foreman/login`.

---

## 3. Foreman Labor Attendance Endpoints

### 3.1 Fetch Today's Labor Roster
* **Method**: `GET /api/v1/attendance/today`
* **Query Parameters**: `site_id` (integer, optional)
* **Response (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "date": "2026-09-16",
      "site": {
        "id": 12,
        "name": "Downtown Hotel Project"
      },
      "labors": [
        {
          "id": 105,
          "name": "Muhammad Rashid",
          "trade": "Electrician",
          "default_status": "present"
        }
      ]
    }
  }
  ```

### 3.2 Submit Daily Attendance
* **Method**: `POST /api/v1/attendance/submit`
* **Payload**:
  ```json
  {
    "site_id": 12,
    "date": "2026-09-16",
    "records": [
      {
        "labor_id": 105,
        "status": "present",
        "hours_worked": 8,
        "overtime_hours": 2,
        "remarks": "Assisted with main cable pull"
      },
      {
        "labor_id": 108,
        "status": "absent",
        "remarks": "Medical leave"
      }
    ]
  }
  ```
* **Response (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Daily attendance submitted for manager review."
  }
  ```

### 3.3 Check Daily Submission Status
* **Method**: `GET /api/v1/attendance/check-submitted?site_id=12&date=2026-09-16`
* Returns whether the roster for the selected day has already been locked or submitted.

---

## 4. Manager Approval Pipeline

### 4.1 Pending Approvals Queue
* **Method**: `GET /api/v1/attendance/pending`
* Returns all daily attendance rosters awaiting manager review.

### 4.2 Approve / Reject Roster
* **Approve**: `POST /api/v1/attendance/{attendance_id}/approve`
* **Reject**: `POST /api/v1/attendance/{attendance_id}/reject`
  * Payload: `{ "note": "Overtime exceeds allowed daily ceiling for project." }`

---

## 5. QR Code + Geofencing Subsystem

### 5.1 Dynamic Scan & Verification
* **Method**: `POST /api/v1/qr-attendance/scan`
* **Payload**:
  ```json
  {
    "qr_token": "SITE-77-TOKEN-HASH",
    "latitude": 25.204849,
    "longitude": 55.270783,
    "accuracy_meters": 12.5,
    "device_info": "Samsung SM-G998B"
  }
  ```
* **Processing Flow**:
  1. Decodes `qr_token` to identify the designated project site.
  2. Compares device GPS coordinates against the site's registered center (`sites.latitude`, `sites.longitude`).
  3. Uses the Haversine formula to compute distance in meters.
  4. If `distance > site.radius_meters`, rejects with `422 Unprocessable Entity` ("Device is outside site boundary").
  5. If within boundary, toggles technician check-in or check-out state and records the session.

### 5.2 Open Session & Daily Summary
* **Active Session**: `GET /api/v1/qr-attendance/open-session` (Returns if user is currently checked in).
* **Daily Status**: `GET /api/v1/qr-attendance/today` (Returns today's punches and total accumulated hours).
* **Summary Metrics**: `GET /api/v1/qr-attendance/summary` (Returns monthly shifts, days present, and late arrivals).
