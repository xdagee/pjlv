# UI Task Subset — PJLV

## Leave Balance UI

- id: ui‑balance‑component
  description: Create leave balance section on dashboard.
  inputFiles:
  - resources/views/dashboard.blade.php
  expectedOutput: UI shows leave types and remaining days.
  verification: Screenshot + data correctness.

## Leave Request Form

- id: ui‑leave‑form
  description: Build leave request form.
  inputFiles:
  - resources/views/leaves/apply.blade.php
  expectedOutput: Functional UI with validation.
  verification: Screenshot + form errors.

## Supervisor Approve/Reject UI

- id: ui‑supervisor‑approve
  description: Approve/reject buttons visible for supervisors.
  inputFiles:
  - resources/views/leaves/index.blade.php
  expectedOutput: Buttons correctly visible.
  verification: Role access test + screenshot.

## Reports Dashboard UI

- id: ui‑reports
  description: Build reports page with charts/tables.
  inputFiles:
  - resources/views/reports/index.blade.php
  expectedOutput: Interactive trend charts with filters.
  verification: Screenshot + data consistency.

## FullCalendar Integration

- id: ui‑fullcalendar
  description: Embed FullCalendar with color coding.
  inputFiles:
  - resources/views/calendar.blade.php
  expectedOutput: Calendar shows events.
  verification: Screenshot + correct event classification.
