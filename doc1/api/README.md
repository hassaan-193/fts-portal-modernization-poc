# FTS Portal API Reference & Integration Suite

Welcome to the **FTS Portal API Reference**. This documentation covers token authentication, mobile endpoints, sequential payment approval pipelines, and GPS-geofenced site attendance.

---

## 1. Authentication Architecture

All mobile and programmatic integrations interact with the `/api/v1/*` surface using HTTP Bearer Token authentication.

### Obtaining a Bearer Token
Clients exchange web portal user credentials (`email` + `password`) for an access token:

* **Endpoint**: `POST /api/v1/auth/login` (or module login such as `/api/v1/payment-bookings/auth/login`)
* **Headers**: `Accept: application/json`, `Content-Type: application/json`
* **Payload**:
  ```json
  {
    "email": "accountant@example.com",
    "password": "password"
  }
  ```
* **Success Response (200 OK)**:
  ```json
  {
    "success": true,
    "token": "1|abcdef1234567890...",
    "user": {
      "id": 4,
      "name": "Staff Accountant",
      "email": "accountant@example.com",
      "roles": ["Payment Booking User"]
    }
  }
  ```

---

## 2. API Modules Directory

* 💳 **[PAYMENT_BOOKINGS_MODULE.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/api/PAYMENT_BOOKINGS_MODULE.md)**:
  Complete reference for Cheque and Cash payment booking lifecycle, sequential two-level review chain, released-money calculations, and Postman testing procedures.
* 📍 **[ATTENDANCE_QR_MODULE.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/api/ATTENDANCE_QR_MODULE.md)**:
  Field technician and site labor daily check-in/out, dynamic QR code verification, GPS geofencing radius validation, and monthly summary calculation APIs.

---

## 3. Global Conventions & Standards

1. **Request Envelope**:
   * All API requests must include `Accept: application/json`.
   * POST and PUT payloads must include `Content-Type: application/json`.
2. **Response Envelope**:
   * Successful responses standardly return:
     ```json
     {
       "success": true,
       "data": { ... },
       "message": "Operation executed successfully"
     }
     ```
   * Validation errors return `422 Unprocessable Entity`:
     ```json
     {
       "message": "The given data was invalid.",
       "errors": {
         "amount": ["The amount must be a positive number."]
       }
     }
     ```
3. **Rate Limiting**:
   * The API enforces a 60 requests/minute rate limit.
   * Exceeding the rate limit yields HTTP `429 Too Many Attempts`.
4. **Tenant Scoping & Object Privacy**:
   * Querying an entity belonging to another user or unauthorized department returns HTTP `404 Not Found` rather than `403 Forbidden` to prevent object enumeration or IDOR vulnerability probing.
