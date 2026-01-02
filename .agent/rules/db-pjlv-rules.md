---
trigger: always_on
---

# PJLV Database & Migration Rules

* Only create or alter database migrations based on clear spec changes in `PROJECT_SPEC.md`.
* Each migration must be reversible with implemented rollback logic.
* Run migrations in a test environment first and produce a rollback validation report.
* Do not modify production DB schema without explicit approval steps documented.
