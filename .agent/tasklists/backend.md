# Backend Task Subset — PJLV

## Leave Balance Logic

- id: svc‑balance‑calculator
  description: Implement leave balance logic.
  inputFiles:
  - app/Services/LeaveBalanceService.php
  expectedOutput: Correct balance calculations.
  verification: Unit test coverage.

## Leave Submission Logic

- id: logic‑submit‑leave
  description: Controller logic for leave requests.
  inputFiles:
  - app/Http/Controllers/LeaveController.php
  expectedOutput: Leave records with proper status.
  verification: Test + DB record check.

## Supervisor Decision Logic

- id: logic‑supervisor‑decision
  description: Update leave status by supervisors.
  inputFiles:
  - app/Http/Controllers/LeaveController.php
  expectedOutput: Status “Recommended”/“Rejected”.
  verification: Unit test + database state.

## HR Decision Logic

- id: logic‑hr‑decision
  description: HR approval/disapproval handler.
  inputFiles:
  - app/Http/Controllers/LeaveController.php
  expectedOutput: “Approved”/“Disapproved” status.
  verification: Unit tests pass.

## API Event Feed

- id: api‑calendar‑events
  description: JSON endpoint for calendar.
  inputFiles:
  - app/Http/Controllers/CalendarController.php
  expectedOutput: Valid events JSON.
  verification: API test.
