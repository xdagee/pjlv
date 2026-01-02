# 📘 PROJECT_SPEC.md

## PJLV Leave Management System — Project Specification

---

## 1. Overview

**Project:** PJLV Leave Management System
**Purpose:** Provide an organization with a robust system to manage employee leave requests, approvals, tracking, and reporting in compliance with relevant labor policies.

**Audience:** Developers, QA, DevOps, Product Managers

---

## 2. Functional Requirements

### 2.1 User Authentication & Roles

| Role   | Permissions                                               |
| ------ | --------------------------------------------------------- |
| Admin  | Full access to settings, staff management, roles, reports |
| HR     | Manage staff, approve leaves, generate reports            |
| CEO    | Approve/reject any leave, view analytics                  |
| OPS    | View organization data across departments                 |
| HOD    | Approve team leaves                                       |
| Normal | Apply for leave, view own history                         |

#### Acceptance Criteria (Authentication)

* Authenticated users see UI based on role.
* Unauthorized access should return HTTP 403.

---

### 2.2 Staff Management

#### Features

* Add/edit/deactivate staff records with name, email, ID, department, role.
* Assign leave levels based on role.

#### Acceptance Criteria (Staff Management)

* Record persists in database after save.
* Deactivated staff cannot apply for leave.

---

### 2.3 Leave Requests

#### Workflow

1. Employee applies via form.
2. Leave calculated (excluding weekends + public holidays).
3. Notifications sent (if enabled).
4. Approval chain: Supervisor → HR → Director.

#### Accepted Leave Types

* Annual Leave
* Sick Leave
* Maternity Leave
* Public Holiday (handled automatically)

#### Acceptance Criteria (Leave Requests)

* Balance calculation is correct per policy.
* System enforces maternity leave duration (84 days).
* Public holidays and weekends excluded in calculations.

---

### 2.4 Approval Workflow

| Status      | Meaning                          |
| ----------- | -------------------------------- |
| Unattended  | Awaiting first action            |
| Recommended | Supervisor approved, awaiting HR |
| Approved    | Final approved                   |
| Rejected    | Supervisor reject                |
| Disapproved | HR/Director reject               |
| Cancelled   | Employee cancelled               |

#### Acceptance Criteria (Approval Workflow)

* Status transitions follow the defined flow.
* Notifications sent for status change.

---

### 2.5 Notifications

#### Mechanisms

* Email alerts for submissions, approvals, rejections.
* In‑app notifications.

#### Acceptance Criteria (Notifications)

* Configurable toggles control notification behavior.
* Email templates render correctly.

---

### 2.6 Reports & Analytics

| Feature          | Description      |
| ---------------- | ---------------- |
| Leave Stats      | Counts by type   |
| Trends           | Monthly & yearly |
| Top Leave Takers | Sorted list      |
| Exports          | CSV and PDF      |

#### Acceptance Criteria (Reports)

* All reports match database results.
* Exports download correctly.

---

## 3. Data Model

### 3.1 Entities & Schema Requirements

#### User

```sql
id (PK)
first_name
last_name
email (unique)
role
department_id (FK)
status
created_at
updated_at
```

#### LeaveRequest

```sql
id (PK)
user_id (FK)
type
start_date
end_date
status
requested_days
approved_days
created_at
updated_at
```

#### Department

```sql
id (PK)
name
head_id (FK to User)
```

#### Holiday

```sql
id (PK)
name
date
```

#### Acceptance Criteria (Data Model)

* Foreign keys enforce relational integrity.
* Indexes applied on frequently queried columns.

---

## 4. APIs & Routes

### 4.1 Public Routes

| Method | Path                | Description          |
| ------ | ------------------- | -------------------- |
| GET    | /calendar           | Leave calendar       |
| GET    | /leaves             | List own leaves      |
| POST   | /leaves             | Submit leave request |
| POST   | /leaves/{id}/cancel | Cancel leave         |

### 4.2 Admin & Manager Routes

| Method | Path         | Description    |
| ------ | ------------ | -------------- |
| GET    | /staff       | List staff     |
| PUT    | /leaves/{id} | Approve/reject |
| GET    | /reports     | Reports        |

#### Acceptance Criteria (APIs)

* Authorization is enforced per role.
* Input validation applied.

---

## 5. UI & UX Flows

### 5.1 Dashboard

* Display pending requests, staff on leave today, personal balance, department summary.

#### Acceptance Criteria (Dashboard)

* Dashboard loads within performance thresholds.
* Data reflects current DB state.

### 5.2 Calendar

* Interactive calendar with color‑coded leave types.
* Public holidays displayed distinctly.

#### Acceptance Criteria (Calendar)

* UI events match leave data.
* Configurable week start day applied.

---

## 6. Quality & Testing Requirements

### 6.1 Unit Tests

* Cover business logic (leave calculations, policy exclusions).

### 6.2 Integration Tests

* End‑to‑end flows (application → approval → reporting).

### 6.3 UI Verification

* Browser screenshot tests for core flows.

#### Acceptance Criteria (Testing)

* Test coverage meets project standard (e.g., ≥ 80%).
* All test suites pass.

---

## 7. Non‑Functional Requirements

| Category    | Requirement                     |
| ----------- | ------------------------------- |
| Performance | Dashboard loads within 2s       |
| Security    | OWASP best practices; no leaks  |
| Scalability | Capable of handling 10k users   |
| Reliability | Failed tasks logged and retried |

---

## 8. Security & Compliance

* Authentication must use secure hashing.
* Rate limiting on login.
* Session timeout configurable.
* Follow Ghana Labour Act for leave entitlements.

### Acceptance Criteria (Security)

* Unauthorized access yields correct HTTP status.
* Password policies enforced per settings.

---

## 9. Deployment Considerations

* Configurable environment variables.
* Migrations must include rollbacks.
* Automated backups retained 30 days.

### Acceptance Criteria (Deployment)

* Deployment scripts execute without errors.
* Backups restore successfully in staging.

---

## 10. Traceability Matrix

| Feature           | Spec Section | Tests               |
| ----------------- | ------------ | ------------------- |
| Leave calculation | 2.3          | Unit + Integration  |
| Notifications     | 2.5          | Unit + Email render |
| Reports export    | 2.6          | Export tests        |
| UI calendar       | 5.2          | Screenshot tests    |

---

## ⛑ Change Log

Maintain revision history with date, author, and summary of changes.

---
