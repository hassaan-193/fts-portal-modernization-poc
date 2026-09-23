# FTS Mobile Client — Agent Build Brief

**Audience**: Engineers and AI agents implementing the FTS Android client.  
**Companion Documents**:
* [PAYMENT_BOOKINGS_MODULE.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/api/PAYMENT_BOOKINGS_MODULE.md)
* [UI_SPEC.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/UI_SPEC.md)
* [BUILD_PLAN.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/BUILD_PLAN.md)

---

## 1. Project Scope & Architecture

The mobile app serves as a native Android companion for the **Payment Bookings** system, enabling accountants to book payments on the fly and allowing managers to verify and approve transactions from their smartphones.

### Guiding Architectural Principle:
**Zero Business Logic in the App.**
* The client is strictly a presentation and interaction layer.
* Status determination, whether a booking is editable, whether Level 2 can approve, and released liquidity math are computed entirely on the server.
* The mobile application renders the UI purely from server-supplied flags.

---

## 2. Technology Stack

| Component | Choice | Rationale |
|---|---|---|
| **Framework** | Flutter (Stable Channel) | High performance, single codebase, fast native compilation |
| **Target OS** | Android only (Min SDK 23 / Android 6.0) | Covers internal enterprise phone fleet |
| **Networking** | `dio` | Interceptors for Bearer token injection and automatic 401 logout handling |
| **State Management** | `flutter_riverpod` | Declarative, compile-safe dependency injection and caching |
| **Secure Storage** | `flutter_secure_storage` | Encrypted token storage (AES / Android Keystore) |
| **Navigation** | `go_router` | Declarative URL routing with redirection guards |
| **Formatting** | `intl` | AED currency and standardized date formatting |

---

## 3. User Roles & Experience

The application dynamically tailors screens based on user roles received upon authentication:

1. **Payment Booking User (Accountant)**:
   * Books new cheques and cash payments.
   * Edits drafts and resubmits rejected bookings.
   * Monitors personal monthly booking vs release metrics.
2. **Payment Booking Verifier (Level 1 Reviewer)**:
   * Reviews incoming bookings in the Level 1 queue.
   * Can Approve, Reject (with mandatory note), or Place on Hold.
3. **Payment Booking Approver (Level 2 Reviewer)**:
   * Reviews bookings that have already passed Level 1.
   * Provides final sign-off to approve payment release, or rejects/holds with required notes.

---

## 4. Authentication Flow

* **No Public Registration**: All users are provisioned by system administrators in the web portal.
* **Login Handshake**:
  * The user submits `email` and `password` to `/api/v1/foreman/login`.
  * Server returns an 80-character Bearer token and user profile object.
  * Token is persisted in `flutter_secure_storage`.
* **Authenticated Requests**:
  * Injected into HTTP headers: `Authorization: Bearer <token>`.
* **Token Invalidation**:
  * Receiving HTTP `401 Unauthorized` wipes the local keystore and redirects immediately to the Login screen.

---

## 5. Offline & Network Rules

* **No Offline Writes**: All financial creations and approvals require real-time server connectivity.
* **Cached Reads**: Recent summaries and lists may be cached in memory for smooth transitions, but mutating operations always demand an online round-trip.
* **Graceful Rate Limiting**: The server enforces a 60 req/min limit. The mobile app should gracefully handle HTTP `429` with a friendly "Please slow down" banner.

---

## 6. Distribution & Packaging

* **Format**: Release-mode signed APK (`app-release.apk`).
* **Deployment**: Direct enterprise side-loading via internal download link or MDM (Mobile Device Management).
