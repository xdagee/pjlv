---
description: Workflow for planning, implementing, and validating DB schema migrations.
---

# PJLV Database Migration Workflow

## 1. Plan Migration

- Generate migration TASK_LIST.md based on schema changes in SPEC.
- Include rollback plan.

## 2. Implementation

- Create Laravel migration files.
- Document changes.

## 3. Verification

- Run migrations in test environment.
- Run rollback to confirm reversibility.

## 4. Browser Check

- Launch app.
- Verify no UI breakage after migration.

## 5. Artifacts

- migration_plan.md
- migration_files/
- rollback_steps.md
- test_report.json
