---
trigger: always_on
---

# PJLV Testing & Quality Rules

* For every new feature or fix, generate unit and integration tests.
* Tests must cover all acceptance criteria defined in `QA_SPEC.md`.
* Do not merge changes without passing test results (`test_report.json`).
* Include browser screenshots for UI validation on features affecting frontend flows (e.g., calendar, dashboards).
* Use descriptive test names indicating the feature/bug and expected behavior.
