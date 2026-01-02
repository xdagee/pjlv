# 📍 QA_SPEC.md

**Quality Assurance Specification — PJLV Leave Management System**

---

## 1. Purpose

Define verifiable test cases and acceptance criteria for all functional, UI, API, and security features of the PJLV Leave Management System. This serves as a source of truth for agents and human testers.

---

## 2. Test Categories

| Category          | Description                                     |
| ----------------- | ----------------------------------------------- |
| Unit Tests        | Isolated logic verification                     |
| Integration Tests | Feature end‑to‑end behavior                     |
| API Tests         | Route and permission validations                |
| UI Tests          | Browser interactions and visual checks          |
| Performance Tests | Load and response behavior                      |
| Security Tests    | Authentication, authorization, input validation |

---

## 3. Authentication & Roles

### 3.1 Login

**Test Case:** Valid login
**Steps**

1. Navigate to `/login`
2. Enter valid credentials ([admin@admin.com](mailto:admin@admin.com)/adminpass)
3. Submit
   **Expected Result:** Redirect to dashboard; token/session created

**Test Case:** Invalid login
**Expected Result:** Show error; no session

**Test Case:** Role‑restricted pages
**Roles:** Normal user cannot access `/admin/*`
**Expected:** HTTP 403 or redirect

---

## 4. Staff Management

### 4.1 Add Staff

**Test Case:** Create staff with valid data
**Expected:** Staff appears in list; fields match input

**Test Case:** Missing required fields
**Expected:** Validation error

### 4.2 Deactivate Staff

**Test Case:** Deactivate account
**Expected:** Staff cannot apply for leave; status = “inactive”

---

## 5. Leave Requests

### 5.1 Apply

**Test Case:** Valid leave request
**Conditions**

* Leave type: Annual
* Duration: 5 working days
  **Expected:** Status = “Unattended”; balance deducted

**Test Case:** Exceed balance
**Expected:** Validation error

**Test Case:** Includes weekends or public holidays
**Expected:** System excludes non‑working days

---

## 6. Approval Workflow

### 6.1 Supervisor Review

**Test Case:** Supervisor approves
**Expected:** Status → “Recommended”

### 6.2 HR Approval

**Test Case:** HR approves
**Expected:** Status → “Approved”

### 6.3 Rejections

**Test Case:** Supervisor rejects
**Expected:** Status → “Rejected”

**Test Case:** HR disapproves
**Expected:** Status → “Disapproved”

---

## 7. Notifications

### 7.1 Email Alerts

**Test Case:** Leave submitted email
**Expected:** Email sent to approver (if enabled)

**Test Case:** Approval/Rejection email
**Expected:** Correct template, correct placeholders

**Test Case:** Toggled off
**Expected:** No email sent

---

## 8. Reports & Analytics

### 8.1 CSV & PDF Exports

**Test Case:** Export CSV
**Expected:** File downloads; content matches filtered report

**Test Case:** Export PDF
**Expected:** PDF renders correct layout

### 8.2 Analytics Metrics

**Test Case:** Monthly trends
**Expected:** Data corresponds to DB values

---

## 9. API & Permission Tests

### 9.1 API Endpoints

**Test Case:** GET /calendar
**Expected:** 200 OK; valid JSON

**Test Case:** POST /leaves without auth
**Expected:** 401 Unauthorized

**Test Case:** Role enforcement
**Expected:** Normal user cannot hit HR admin routes

---

## 10. UI & UX Tests

### 10.1 Dashboard Rendering

**Test Case:** Load dashboard
**Expected:** Correct counts display; no errors

### 10.2 Calendar

**Test Case:** Interact calendar
**Expected:** Leave events visible; color codes match types

---

## 11. Security Tests

### 11.1 Input Validation

**Test Case:** SQL injection attempt
**Expected:** Validations prevent injection

### 11.2 Session Timeout

**Test Case:** After inactivity
**Expected:** Logout after configured duration

---

## 12. Performance Tests

**Test Case:** Dashboard load under load
**Expected:** < 2 seconds response

**Test Case:** 100 concurrent leave submissions
**Expected:** No errors; proper queue handling

---

## 13. Acceptance Criteria Mapping

| Feature       | QA Spec Reference |
| ------------- | ----------------- |
| Login         | Section 3         |
| Leave request | Section 5         |
| Approval      | Section 6         |
| Reports       | Section 8         |
| UI Flows      | Section 10        |

---

## 14. Test Artifacts

* `test_report.json`
* `screenshots/`
* Browser session recordings (if applicable)
* CSV/PDF export files
