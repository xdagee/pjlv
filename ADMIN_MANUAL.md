# PJLV Leave Management System - Administrator Manual

Welcome to the PJLV Leave Management System. This guide will help you manage staff leaves, configure system settings, and ensure smooth operation of the leave management process.

## Table of Contents
1. [Getting Started](#getting-started)
2. [Dashboard Overview](#dashboard-overview)
3. [Staff Management](#staff-management)
4. [Leave Management](#leave-management)
5. [System Configuration](#system-configuration)
6. [Security Best Practices](#security-best-practices)

---

## Getting Started

### Login
Access the admin portal at `/admin/login`.
*   **Super Admin Default**: `admin@admin.com` / `adminpass`
*   **Security Tip**: Change your password immediately after first login via the Profile page.

### Admin vs. Staff
*   **Admins**: Manage the system, approvals, and settings.
*   **Staff**: Apply for leave, view history, and check balances.
*   **Note**: Admin accounts are separate from Staff accounts for better security.

---

## Dashboard Overview
The Dashboard provides a real-time snapshot of the organization's leave status:
*   **Pending Requests**: Leave applications awaiting approval.
*   **Staff on Leave**: Staff currently away.
*   **Total Staff**: Active workforce count.
*   **Department Overview**: Quick stats by department.

---

## Staff Management

### Adding New Staff
Navigate to **Staff > Add Staff**.
*   **Required**: First/Last Name, Email, Staff ID, Department, Role.
*   **Leave Levels**: Assign the correct level (Management/Senior/Junior) to ensure correct leave day allocation.

### Roles & Permissions
*   **Super Admin**: Full access to all settings and data. Cannot be deleted.
*   **HR Manager**: Can manage staff and leaves but cannot change system settings.
*   **Department Head**: Approves leaves for their department.

---

## Leave Management

### Approval Workflow
1.  **Application**: Staff applies via their portal.
2.  **Notification**: Admin receiving email notification (if enabled).
3.  **Review**: Admin reviews balance, dates, and overlap with other staff.
4.  **Decision**: Approve or Reject.
    *   **Approved**: Days are deducted from balance.
    *   **Rejected**: Status updated, staff notified.

### Leave Policies (Ghana Labour Act Compliance)
*   **Annual Leave**: 15 working days minimum (System defaults: 21-36 days based on level).
*   **Public Holidays**: Automatically excluded from leave calculation.
*   **Weekends**: Excluded from leave calculation.
*   **Maternity Leave**: 12 weeks (84 days) fixed duration.

---

## System Configuration
*New Feature: All settings are managed via **Settings** in the sidebar.*

### Leave Policy Tab
*   **Default Annual Days**: Set the baseline for new staff (e.g., 21).
*   **Medical Certificate**: Days of sick leave before proof is required (Default: 3).

### Email Tab
*   **Notifications**: Toggle email alerts on/off.
*   **From Name**: Customize the sender name (e.g., "PJLV HR").

### System Tab
*   **App Name**: Change the application title.
*   **Maintenance Mode**: Enable during upgrades (prevents staff login).

### Security Tab
*   **Password Policy**: Set minimum length and expiry days.
*   **Login Attempts**: Account lockout threshold (Default: 5).

---

## Security Best Practices
1.  **Rate Limiting**: The system limits admin login attempts to 60 per minute to prevent attacks.
2.  **Session Timeout**: Admins are logged out after 120 minutes of inactivity (configurable).
3.  **Backups**: Automated daily backups are configured. Contact IT support for restoration.
