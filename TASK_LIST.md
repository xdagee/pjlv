# 📋 TASK_LIST.md

**Project:** PJLV Leave Management System
**Purpose:** Atomic tasks derived from the project specification (`PROJECT_SPEC.md`) and QA requirements (`QA_SPEC.md`).

---

## 1. Feature: Leave Balance Display

### Task: UI Component for Leave Balance

```yaml
id: ui‑balance‑component
description: |
  Create the leave balance display section on the user dashboard.
inputFiles:
  - resources/views/dashboard.blade.php
expectedOutput: |
  Dashboard shows leave types and remaining days correctly.
verification: |
  - Dashboard UI renders without error.
  - Screenshot produced.
  - Values match expected results from database.
```

### Task: Service Logic for Balance Calculation

```yaml
id: svc‑balance‑calculator
description: |
  Implement and test leave balance calculation logic in LeaveBalanceService.
inputFiles:
  - app/Services/LeaveBalanceService.php
expectedOutput: |
  Service returns correct leave balances for a user.
verification: |
  Unit tests covering edge cases (weekends, holidays) pass.
```

### Task: API Endpoint for Balance

```yaml
id: api‑balance‑endpoint
description: |
  Create API route/controller for fetching leave balance data.
inputFiles:
  - routes/web.php
  - app/Http/Controllers/BalanceController.php
expectedOutput: |
  Authenticated route returns JSON with balance breakdown.
verification: |
  - API returns 200 with correct JSON schema.
  - Permission test for unauthorized user returns 403.
```

---

## 2. Feature: Leave Request Workflow

### Task: Leave Request Form

```yaml
id: ui‑leave‑form
description: |
  Build the leave request form in Blade (start/end dates, type dropdown).
inputFiles:
  - resources/views/leaves/apply.blade.php
expectedOutput: |
  Functional form validated in UI.
verification: |
  - Form displays correctly.
  - Validation errors show on invalid input.
```

### Task: Leave Submission Logic

```yaml
id: logic‑submit‑leave
description: |
  Implement controller logic to handle leave submissions.
inputFiles:
  - app/Http/Controllers/LeaveController.php
expectedOutput: |
  New leave entries create with "Unattended" status.
verification: |
  - Unit tests for business logic pass.
  - Database records created as expected.
```

### Task: Notification Trigger on Submit

```yaml
id: notif‑submit‑trigger
description: |
  Implement notification dispatch on leave submission.
inputFiles:
  - app/Services/NotificationService.php
expectedOutput: |
  Supervisor receives an in‑app and email notification if enabled.
verification: |
  - Test email mock sends expected template.
  - In‑app notification logged.
```

---

## 3. Feature: Approval Workflow

### Task: Supervisor Action UI

```yaml
id: ui‑supervisor‑approve
description: |
  Add approve/reject buttons for supervisor in leave list view.
inputFiles:
  - resources/views/leaves/index.blade.php
expectedOutput: |
  Buttons only visible to supervisors/HODs.
verification: |
  - Role test confirms visibility rules.
  - UI screenshots show correct state.
```

### Task: Supervisor Logic

```yaml
id: logic‑supervisor‑decision
description: |
  Update LeaveController to handle supervisor decisions.
inputFiles:
  - app/Http/Controllers/LeaveController.php
expectedOutput: |
  Status updates to “Recommended” or “Rejected”.
verification: |
  Database status changes reflects expected logic.
  Unit tests pass.
```

### Task: HR Final Decision Logic

```yaml
id: logic‑hr‑decision
description: |
  Implement HR approval/disapproval logic.
inputFiles:
  - app/Http/Controllers/LeaveController.php
expectedOutput: |
  Status changes to “Approved” or “Disapproved”.
verification: |
  Tests for both HR flows pass.
```

---

## 4. Feature: Reports & Exports

### Task: Reports UI

```yaml
id: ui‑reports
description: |
  Build reports dashboard section with charts and tables.
inputFiles:
  - resources/views/reports/index.blade.php
expectedOutput: |
  Interactive trend charts + filters.
verification: |
  - Browser screenshots.
  - Data consistency checks.
```

### Task: CSV Export Logic

```yaml
id: logic‑csv‑export
description: |
  Implement CSV export route for reports.
inputFiles:
  - app/Http/Controllers/ReportController.php
expectedOutput: |
  Valid CSV downloads with correct encoded data.
verification: |
  Verified structure/content matches filtered report data.
```

### Task: PDF Export Logic

```yaml
id: logic‑pdf‑export
description: |
  Implement PDF export for reports.
inputFiles:
  - app/Http/Controllers/ReportController.php
expectedOutput: |
  Valid PDF file downloads matching print layout.
verification: |
  PDF readability + data correctness.
```

---

## 5. Feature: Calendar Integration

### Task: Calendar Backend Endpoint

```yaml
id: api‑calendar‑events
description: |
  Create backend endpoint that supplies calendar events (approved leaves + holidays).
inputFiles:
  - routes/web.php
  - app/Http/Controllers/CalendarController.php
expectedOutput: |
  JSON feed of events for FullCalendar.
verification: |
  Valid JSON, correct event fields returned.
```

### Task: FullCalendar UI

```yaml
id: ui‑fullcalendar
description: |
  Integrate FullCalendar on the calendar view with color coding.
inputFiles:
  - resources/views/calendar.blade.php
expectedOutput: |
  Calendar shows events with expected colors/types.
verification: |
  Browser screenshot + event correctness.
```

---

## 6. Security Tasks

### Task: Auth & Role Enforcement

```yaml
id: auth‑role‑enforcement
description: |
  Add middleware to protect sensitive routes per role.
inputFiles:
  - app/Http/Middleware/CheckRole.php
expectedOutput: |
  Access correctly restricted.
verification: |
  Role access matrix tests pass.
```

### Task: Input Validation

```yaml
id: input‑validation
description: |
  Validate all form inputs using Form Requests.
inputFiles:
  - app/Http/Requests/
expectedOutput: |
  Cleaner controller logic + valid edge case coverage.
verification: |
  Validation tests ensure error messages and enforcement.
```

---

## 7. Deployment & Infrastructure

### Task: Env Config Validation

```yaml
id: infra‑env‑validation
description: |
  Validate production `.env` settings (mail, db, cache).
inputFiles:
  - .env.example
expectedOutput: |
  Config sync + documentation updated.
verification: |
  Check config caching outputs + docs accuracy.
```

### Task: Supervisor Setup Script

```yaml
id: infra‑supervisor‑script
description: |
  Provide scripts for queue workers via Supervisor.
inputFiles:
  - .agent/workflows/release‑pjlv.md
expectedOutput: |
  Ready‑to‑apply Supervisor conf.
verification: |
  Manual approval before production apply.
```

---

## 8. Testing & QA

### Task: PHPUnit Tests

```yaml
id: test‑phpunit‑suite
description: |
  Write PHPUnit tests based on QA_SPEC.md (unit + integration).
inputFiles:
  - tests/
expectedOutput: |
  All tests for required features.
verification: |
  test_report.json with pass statuses.
```

### Task: Browser Automated Tests

```yaml
id: test‑browser‑suite
description: |
  E2E tests using Laravel Dusk or similar.
inputFiles:
  - tests/Browser/
expectedOutput: |
  Automated verification of UI flows.
verification: |
  Screenshot comparisons + success logs.
```

---

## 9. Documentation

### Task: Update README

```yaml
id: docs‑readme
description: |
  Update project README with updated commands, workflow steps, and contribution guide.
inputFiles:
  - README.md
expectedOutput: |
  Accurate, up‑to‑date docs.
verification: |
  Link & content checks.
```

### Task: Generate CHANGELOG

```yaml
id: docs‑changelog
description: |
  Create a changelog for recent updates v2.0+.
inputFiles:
  - CHANGELOG.md
expectedOutput: |
  Chronological summary of changes.
verification: |
  Human review.
```

---

## 10. Miscellaneous

### Task: Health Endpoint

```yaml
id: infra‑health‑endpoint
description: |
  Implement `/health` route returning JSON status (DB + cache).
inputFiles:
  - routes/web.php
expectedOutput: |
  `{"status":"healthy","database":"ok","cache":"ok"}`
verification: |
  Test with server running; correct JSON.
```
