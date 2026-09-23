# FTS Mobile Application Development Hub

Everything required for developers and automated AI agents to build, test, and release the **FTS Mobile Client** (Payment Bookings & Approvals companion).

---

## 1. Documentation Index

1. 📄 **[AGENT_BRIEF.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/AGENT_BRIEF.md)**:
   Architecture brief, target platforms, authentication flows, Flutter stack choices, state management, and APK distribution.
2. 🎨 **[UI_SPEC.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/UI_SPEC.md)**:
   Design tokens, palette, typography, reusable atomic widgets, screen states, and user interaction rules.
3. 🛠️ **[BUILD_PLAN.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/BUILD_PLAN.md)**:
   Phase-by-phase implementation schedule, toolchain prerequisites, build steps, and milestone checkpoints.
4. 🌐 **[Backend Module Reference](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/api/PAYMENT_BOOKINGS_MODULE.md)**:
   The backend single source of truth for payment bookings and sequential reviews.

---

## 2. Executive Summary

* **Target Audience**: Corporate accountants, financial verifiers, and executive approvers on Android mobile devices.
* **App Philosophy**: A thin client over the Laravel API (`/api/v1/payment-bookings/*`). The mobile app performs zero local business logic calculations; all states, review permissions, and action allowances are dictated directly by server responses.
* **Packaging & Distribution**: Side-loaded signed release APK distributed internally (no Google Play Store submission required).
