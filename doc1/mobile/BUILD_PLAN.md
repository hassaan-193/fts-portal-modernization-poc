# FTS Mobile — Phased Build Plan & Verification

A step-by-step engineering roadmap for assembling, testing, and packaging the FTS Android application.

---

## 1. Toolchain & Prerequisites

* **Flutter SDK**: Version 3.19+ (Stable channel)
* **Dart**: Version 3.3+
* **Java Development Kit**: JDK 17 (Temurin recommended for Android Gradle Plugin 8.x)
* **Android SDK**: Build Tools 34.0.0, Platform 34 (Android 14)
* **IDE**: VS Code with Dart & Flutter plugins, or Android Studio

---

## 2. Phased Build Sequence

### Phase 1: Scaffolding & Design System
* Initialize project: `flutter create --org com.fts.portal --platforms android fts_mobile`.
* Establish directory structure: `core/`, `features/auth/`, `features/bookings/`, `features/approvals/`, `shared/widgets/`.
* Implement `AppColors`, `AppTypography`, and atomic components (`StatusBadge`, `BookingCard`, `HeroAmountCard`).
* **Checkpoint**: Design gallery screen renders all atomic widgets in light mode.

### Phase 2: Secure Authentication & Session Guard
* Integrate `dio` HTTP client and `flutter_secure_storage`.
* Configure interceptor:
  * Attach `Authorization: Bearer <token>` on all requests.
  * Intercept HTTP `401 Unauthorized` to wipe secure storage and trigger route redirect to `/login`.
* Implement Login screen calling `POST /api/v1/foreman/login`.
* **Checkpoint**: Successfully log in with `accountant@example.com`, inspect stored token, and verify auto-redirect.

### Phase 3: Accountant Booking Workflow
* Implement booking creation form with segmented switch: **Cheque** vs **Cash**.
* Dynamic field validation:
  * Cheque requires `cheque_number`, `cheque_date`, `bank_account`, `release_date`.
  * Cash requires `cash_account`, `payment_date`.
* Save Draft (`submit: false`) vs Direct Submit (`submit: true`).
* Resubmission view for rejected bookings with pre-filled inputs.
* **Checkpoint**: Create a cheque booking from mobile; confirm record in MySQL with status `1 (Pending Verification)`.

### Phase 4: Verification & Approval Queues
* Implement Reviewer tab visible only to users with `Payment Booking Verifier` or `Payment Booking Approver` roles.
* Display cards with pending bookings awaiting decision.
* Implement `DecisionModal`:
  * Approve sends `decision: 1`.
  * Reject sends `decision: 2` with required text note.
  * Hold sends `decision: 3` with required text note.
* Enforce Two-Person rule UI feedback if a user attempts to review their own prior decision.
* **Checkpoint**: Walk a booking through Level 1 and Level 2 on two different test accounts; verify status updates to `3 (Approved)`.

### Phase 5: Liquidity Dashboard & Monthly Breakdown
* Fetch `GET /api/v1/payment-bookings/summary`.
* Render hero card with released funds this month vs pending releases.
* Render schedule accordion showing upcoming monthly commitments.
* **Checkpoint**: Verify totals match the web portal dashboard at `/payment-bookings/dashboard`.

### Phase 6: Release Compilation & APK Packaging
* Configure `android/app/build.gradle`:
  * Set `minSdkVersion 23`, `targetSdkVersion 34`.
  * Set versioning: `versionCode 1`, `versionName "1.0.0"`.
* Configure release signing keystore in `android/key.properties`.
* Run production compilation:
  ```bash
  flutter build apk --release
  ```
* **Final Deliverable**: `build/app/outputs/flutter-apk/app-release.apk`.
