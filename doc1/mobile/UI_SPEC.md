# FTS Mobile — UI & Design Specification

Visual contract, color palette, design tokens, and atomic components for the FTS mobile application.

---

## 1. Design Tokens & Palette

### Brand & Accents
```
primary            #2563EB   Standard buttons, active tab indicators, hyper-links
primaryDark        #1D4ED8   Button pressed / active state
navy700            #1E3A8A   Hero total card gradient start
navy600            #1E40AF   Hero total card gradient end
```

### Semantic Status Colors
```
success            #16A34A   Approved, released, cleared
successSurface     #DCFCE7   Light green badge background
warning            #D97706   Pending review, on hold
warningSurface     #FEF3C7   Light amber badge background
danger             #DC2626   Rejected, urgent, destructive action
dangerSurface      #FEE2E2   Light red badge background
info               #2563EB   Draft, neutral informational chip
infoSurface        #DBEAFE   Light blue badge background
```

### Neutrals & Backgrounds
```
bg                 #F8FAFC   App canvas background (cool gray 50)
surface            #FFFFFF   Cards, modals, sheet backgrounds
surfaceSunken      #F1F5F9   Input fields, search bar track
border             #E2E8F0   Card outlines, list dividers
borderStrong       #CBD5E1   Focused input borders
ink                #0F172A   Headings, high-contrast totals
inkSecondary       #475569   Body text, descriptions
inkTertiary        #64748B   Form labels, secondary timestamps
inkMuted           #94A3B8   Placeholders, disabled text
```

### Typography
* **Font Family**: Inter (bundled statically in assets).
* **Headings**: Semi-bold (Weight: 600), dark ink `#0F172A`.
* **Amounts & Totals**: Monospace numerals / Tabular figures with currency code `AED`.

---

## 2. Core Reusable Components

1. **`StatusBadge`**:
   * Renders color-coded chips for statuses: `Draft` (Gray), `Pending Verification` (Yellow), `Pending Approval` (Orange), `Approved` (Green), `Rejected` (Red), `On Hold` (Purple/Amber).
2. **`BookingCard`**:
   * Displays reference number (`CHQ-2026-XXXX` / `CSH-2026-XXXX`), payee name, amount in AED, effective date, and status chip.
3. **`HeroAmountCard`**:
   * Gradient card displayed at the top of accountant and manager dashboards summarizing released vs pending amounts for the month.
4. **`ReviewTimeline`**:
   * Visual 2-step stepper component displaying Level 1 (Verification) and Level 2 (Approval) audit trail with deciding officer name, timestamp, and optional rejection/hold notes.
5. **`DecisionModal`**:
   * Bottom sheet sliding up for Verifiers and Approvers with **Approve**, **Reject**, and **Hold** actions. Forces user input in a `notes` text area when selecting Reject or Hold.
