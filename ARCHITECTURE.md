# 📐 ARCHITECTURE.md

**PJLV Leave Management System — Architecture Overview**

---

## 1. System Overview

**PJLV** is a web‑based leave management application built with **Laravel 12**.
It supports staff leave workflows, reporting, role‑based access, and integrations like email and calendar visualization.

**Key Goals**

* Clear separation of concerns
* Maintainable, modular codebase
* Secure, role‑based access control
* Observable and testable system behavior

---

## 2. High‑Level Architecture

```
┌──────────────────────────────┐
|        Web Browser UI        |
|   (Blade + FullCalendar)     |
└─────────────▲────────────────┘
              |
              |
       HTTP/HTTPS Requests
              |
┌─────────────▼────────────────┐
|       Laravel HTTP Kernel    |
| (Middleware, Auth, Routing)  |
└─────────────▲────────────────┘
              |
              |
       Controllers & Services
              |
    ┌─────────┴─────────┐
    |                   |
 Business Logic     Notification/Email
  (Services)           (Mail/Queue)
    |                   |
┌───▼────────┐     ┌────▼─────────┐
| Models     |     | Queue Worker  |
| (Eloquent) |     | (Supervisor)  |
└───▲────────┘     └────▲─────────┘
    |                   |
 Database (MySQL)   Notification Sink
 (migrations,        (emails, tasks)
  seeders)
```

---

## 3. Core Modules & Responsibilities

### 3.1 HTTP Layer

**Components**

* **Routes** (`routes/web.php`)
* **Middleware**

  * Authentication & authorization
  * Role enforcement
  * CSRF protection

**Responsibilities**

* Securely handling incoming requests
* Enforcing access control
* Routing to appropriate controllers

---

### 3.2 Controllers

Controllers orchestrate interactions between requests, services, and responses.

**Examples**

* `LeaveController`
* `StaffController`
* `ReportController`

**Pattern**
Each controller should:

1. Validate input (Form Request classes)
2. Delegate to *Services*
3. Return JSON or Blade view

---

### 3.3 Services Layer

Purpose: encapsulate core business logic.

**Key Services**

| Service                  | Responsibility                                |
| ------------------------ | --------------------------------------------- |
| `LeaveCalculatorService` | Compute leave days, exclude weekends/holidays |
| `LeaveBalanceService`    | Maintain and update leave balances            |
| `AnalyticsService`       | Reporting metrics                             |
| `NotificationService`    | Dispatch notifications                        |
| `AuditService`           | Log key application events                    |

**Benefits**

* Testable logic decoupled from controllers
* Reusable across UI and API

---

### 3.4 Models and Persistence

**Core Models**

| Model          | Description                    |
| -------------- | ------------------------------ |
| `User`         | Staff and Admin accounts       |
| `LeaveRequest` | Leave submissions & status     |
| `Department`   | Organization units             |
| `Holiday`      | Public holidays                |
| `Role`         | Role definitions & permissions |

**Relationships**

* `User` has many `LeaveRequest`
* `Department` has many `User`
* `Role` assigned to `User`

Eloquent ORM abstracts SQL interaction and facilitates query building.

---

### 3.5 Views & UI

**Technologies**

* Laravel Blade templates
* JavaScript (FullCalendar, Chartist.js)
* Material Dashboard Pro CSS

**Key UI Sections**

* Dashboard
* Leave Calendar
* Staff Management
* Reports & Analytics

**Principle**
UI = thin layer; heavy logic should reside in services/backend.

---

## 4. Integration Points

### 4.1 Email & Notification

**Mechanisms**

* Configurable email via `.env`
* In‑app notification bell
* Queue for async mail delivery

**Queue Worker**

* Managed via Supervisor
* Laravel queue: `database` driver
* Handles notifications without blocking HTTP responses

---

### 4.2 Calendar Component

**FullCalendar.js**

* Displays approved leaves
* Color coded by leave type
* Configurable week start

**Data Source**

* Server provides JSON events
* Public holidays integrated

---

## 5. API Contracts (Internal)

Routes intended for machine or JS client consumption:

| Endpoint          | Method | Purpose            |
| ----------------- | ------ | ------------------ |
| `/calendar`       | GET    | Fetch leave events |
| `/leaves`         | POST   | Submit leave       |
| `/leaves/{id}`    | PUT    | Approve/Rej        |
| `/reports/export` | GET    | CSV/PDF            |

Requests must be authenticated; RBAC enforced per route.

---

## 6. Data Flow Examples

### 6.1 Leave Request Flow

1. User submits leave via form
2. Controller validates input
3. `LeaveCalculatorService` computes effective days
4. `LeaveRequest` saved, initial status “Unattended”
5. Notification sent to supervisor via `NotificationService`
6. Queue processes mails (Supervisor Inbox)

---

### 6.2 Approval Flow

1. Approver hits `/admin/leaves/{id}`
2. Authorization checks role & department
3. Status updated via `LeaveBalanceService`
4. Leave balance recalculated if approved
5. Notification dispatched to requester

---

## 7. Security Model

**Authentication**

* Laravel built‑in auth (session + guards)
* CSRF protection

**Authorization**

* Role gates (`CheckRole`, `SuperAdmin`)
* Department scoping (HOD only for own dept)

**Input Validation**

* Form Request classes ensure valid and safe data

**Best Practices**

* Password policies enforced
* Rate limiting for login attempts
* Sensitive config in `.env` only

---

## 8. Non‑Functional Requirements

| Requirement     | Target              |
| --------------- | ------------------- |
| Performance     | < 2s dashboard load |
| Scalability     | Support 10k+ users  |
| Reliability     | 99.9% uptime        |
| Maintainability | Modular services    |

---

## 9. Deployment Architecture

**Environment**

* PHP 8.2+
* Nginx/Apache
* MySQL 8+
* Redis optional (cache/sessions)
* Supervisor for queue

**Layers**

```
[Client Browser]
       |
  [Web Server]
       |
  [App Server (Laravel)]
       |
  [Database]
       |
 [Cache/Queue (Redis)]
```

---

## 10. Observability & Metrics

**Logging**

* Laravel `log` channels
* Separate logs for queue workers

**Health Checks**

* `/health` endpoint returns status JSON

**Monitoring**

* External tools recommended (e.g., UptimeRobot)

---

## 11. Deployment & CI/CD Integration

**CI/CD Objectives**

* Run tests
* Build assets
* Validate migrations
* Deploy branches
* Generate reports

Agents and pipelines should ensure environment parity and verification artifacts.

---

## 12. Dependency Summary

| Dependency         | Purpose                    |
| ------------------ | -------------------------- |
| Laravel 12         | Framework                  |
| FullCalendar.js    | Interactive calendar UI    |
| Chartist.js        | Analytics charts           |
| Material Dashboard | UI theme                   |
| Supervisor         | Queue worker orchestration |

---

## 13. Architectural Principles

1. **Separation of Concerns** — Controllers thin, Services encapsulate logic
2. **Testability** — Services and APIs easy to test
3. **Traceability** — Every feature anchored to specs and QA tests
4. **Safety** — Auth, RBAC, validation enforced at all boundaries

---

## 14. Glossary

| Term | Meaning                       |
| ---- | ----------------------------- |
| RBAC | Role‑Based Access Control     |
| HOD  | Head of Department            |
| UI   | User Interface                |
| API  | Application Program Interface |
