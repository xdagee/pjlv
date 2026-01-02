---
description: Structured workflow to reproduce, diagnose, fix, and validate a bug in PJLV.
---

# PJLV Bug Fix Workflow

## 1. Reproduce

- Agents reproduce the issue locally.
- Document reproduction steps with screenshots and error logs.

## 2. Root Cause Analysis

- Search relevant code related to bug.
- Produce a root cause report.

## 3. Fix

- Write code fix according to investigation result.
- Create regression tests verifying the fix.

## 4. Verification

- Run full test suites.
- Produce `test_report.json` to validate no regressions.

## 5. Browser Check

- Use browser agent to validate fix visually.
- Capture screenshots showing issue resolution.

## 6. Artifacts

- reproduction_steps.md
- root_cause.md
- code_diff.patch
- test_report.json
- screenshots/*
