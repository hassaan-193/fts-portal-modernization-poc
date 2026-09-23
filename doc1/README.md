# FTS Portal — Documentation Suite (`doc1`)

Welcome to the **FTS Portal (`ft_portal_base`)** technical documentation suite. This folder provides an organized, humanized, and battle-tested reference for every aspect of the application.

---

## 📚 Documentation Index

### 1. Main Project Reference
* 📄 **[PROJECT_DOCUMENTATION.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/PROJECT_DOCUMENTATION.md)**: The central engineering manual covering system architecture, technology stack, all 4 user dimensions, operational lifecycles, and tested credentials.

---

### 2. Portal-by-Portal Guides (`doc1/portals/`)
Detailed operational and technical walkthroughs for each distinct user portal:
* 🏛️ **[portals/ADMIN_PORTAL.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/portals/ADMIN_PORTAL.md)**: Executive dashboard, Spatie RBAC, user governance, double-entry ledgers, and multi-tier purchase order approvals.
* 👷 **[portals/STAFF_PORTAL.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/portals/STAFF_PORTAL.md)**: Sales inquiry pipeline, commercial quotation estimation, active project management, and vendor procurement.
* 📱 **[portals/USER_PORTAL.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/portals/USER_PORTAL.md)**: Employee self-service ("My Requests"), AMC technician field visit reports with offline draft support, and geofenced attendance.
* 🏢 **[portals/CLIENT_PORTAL.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/portals/CLIENT_PORTAL.md)**: External client extranet, strict database tenant scoping (`where company_id = ?`), quotations review, and tax invoices.

---

### 3. API & Mobile Integration (`doc1/api/` & `doc1/mobile/`)
* 🌐 **[api/README.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/api/README.md)**: REST API overview, Bearer token authentication, and endpoints catalog.
* 💳 **[api/PAYMENT_BOOKINGS_MODULE.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/api/PAYMENT_BOOKINGS_MODULE.md)**: High-security cheque/cash payment booking engine with two-level sequential review.
* 📍 **[api/ATTENDANCE_QR_MODULE.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/api/ATTENDANCE_QR_MODULE.md)**: Daily site labor attendance, foreman submission workflows, and GPS geofenced QR check-in.
* 📱 **[mobile/README.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/README.md)**: Overview of the FTS Android companion app suite.
* 📋 **[mobile/AGENT_BRIEF.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/AGENT_BRIEF.md)**: Implementation brief for building the Android client.
* 🎨 **[mobile/UI_SPEC.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/UI_SPEC.md)**: Design tokens, color palette, typography, and atomic widgets.
* 🛠️ **[mobile/BUILD_PLAN.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/mobile/BUILD_PLAN.md)**: Step-by-step phased build sequence, prerequisites, and verification checkpoints.

---

### 4. Architecture & Engineering Standards (`doc1/ai-enablement/`)
* 🗺️ **[ai-enablement/PROJECT_OVERVIEW.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/ai-enablement/PROJECT_OVERVIEW.md)**: High-level architectural narrative for developers and AI assistants.
* 📏 **[ai-enablement/CODING_STANDARDS.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/ai-enablement/CODING_STANDARDS.md)**: Defensive coding patterns, Yajra null-safety rules, global delete protection, and repository patterns.
* 🧭 **[ai-enablement/REPO_MAP.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/ai-enablement/REPO_MAP.md)**: Complete directory and file inventory mapping models, controllers, DataTables, and views.
* 🔒 **[ai-enablement/SECURITY_RULES.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/ai-enablement/SECURITY_RULES.md)**: Multi-guard session isolation, tenant scoping, and IDOR prevention rules.
* 🤖 **[ai-enablement/AI_CONTEXT.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/ai-enablement/AI_CONTEXT.md)**: Guidelines on working within the Laravel monolith without introducing regressions.
* 🤝 **[ai-enablement/WORKING_AGREEMENTS.md](file:///d:/FTSITS/ft_portal_base%282%29/ft_portal_base/doc1/ai-enablement/WORKING_AGREEMENTS.md)**: Engineering team protocols, risk management, and pre-merge checklist.

---

## 🚀 Quick Launch
```powershell
# 1. Start local development server
php artisan serve --port=8001

# 2. Local URL
http://127.0.0.1:8001
```

### Tested Credentials
* **Admin Portal**: `admin@example.com` / `password` (Super-User)
* **Staff Portal**: `staff@example.com` / `password` (Staff)
* **User Portal**: `user@user.com` / `password` (Staff)
* **Client Portal**: `client@example.com` / `password` (Al Futtaim Engineering)
