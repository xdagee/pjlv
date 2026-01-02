---
description: Standard workflow for implementing a new feature in the PJLV Leave Management System.
---

# PJLV Feature Development Workflow

## 1. Planning

- Generate TASK_LIST.md with atomic tasks derived from spec for the feature.
- Include dependencies, expected outputs, and verification method per task.

## 2. Research & Context

- Summarize relevant existing code and specs.
- Extract required domain knowledge from ARCHITECTURE.md and PROJECT_SPEC.md.

## 3. Implementation

- Create/modify code files according to task list.
- Add documentation and inline comments.
- Implement tests for each acceptance criterion in QA_SPEC.md.

## 4. Validation

- Run unit and integration tests.
- Create `test_report.json` with results.

## 5. Browser Verification

- Start local server.
- Use browser agent to verify relevant UI screens (e.g., calendars, dashboards).
- Capture screenshots.

## 6. Artifacts

- TASK_LIST.md
- IMPLEMENTATION_PLAN.md
- code_diff.patch
- test_report.json
- screenshots/*

## 7. Review

- Pause for human review and feedback on all artifacts.
