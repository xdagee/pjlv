---
description: Workflow for preparing a stable release of PJLV, running full CI/CD checks, and deploying to the target environment.
---

# PJLV Release & Deployment Workflow

## 1. Code Freeze

- Generate a release branch.
- Ensure all features in scope are complete with passing tests.

## 2. Full Test Suite

- Run entire test suite.
- Output `test_report.json`.

## 3. Static Analysis

- Run configured linting and security scans.
- Document results.

## 4. Deployment Preparation

- Run build tasks (e.g., asset compilation).
- Generate optimized caches.

## 5. Staging Verification

- Deploy to staging environment.
- Browser agent verifies critical flows (login, leave application, approvals).
- Screenshots required.

## 6. Deployment

- Hold human approval checkpoint.
- Deploy only after approval.

## 7. Post-Deployment

- Validate deployment with smoke tests.
- Document final release notes.

## 8. Artifacts

- release_notes.md
- test_report.json
- security_scan_report.md
- screenshots/staging/
