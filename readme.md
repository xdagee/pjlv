# PJLV - Employee Leave Management System

A Laravel 10 based employee leave management system for organizations to streamline leave requests, approvals, and tracking.

## ✨ Features

### Core Features

- **Staff Management** - Create, view, update, and deactivate employee records
- **Leave Requests** - Apply for leave with automatic day calculation and balance checking
- **Approval Workflow** - Multi-level approval (Supervisor → HR → Director) with email notifications
- **Role-Based Access** - Admin, HR, CEO, OPS, HOD, Normal employee roles with hierarchy
- **Department Management** - Organize staff by departments with HOD assignments

### Dashboard & Analytics

- Pending leave requests count
- Staff on leave today
- Personal leave balance with per-type breakdown
- Upcoming holidays (next 30 days)
- Recent leave history
- **Analytics Dashboard** - Advanced reporting with:
  - Leave statistics by department
  - Leave trends by type
  - Monthly/yearly trend charts
  - Top leave takers
  - CSV/PDF export

### Calendar

- Interactive FullCalendar.js integration
- Approved leaves displayed as events
- Public holidays highlighted with **configurable colors**
- Color-coded by leave type
- Configurable week start day and business hours

### Reports

- Leave statistics by type
- Monthly trend charts (Chartist.js)
- Top leave takers list
- CSV and PDF export functionality
- Department-wise breakdowns

### System Settings (50+ Configurable Options)

- **Leave Management** - Annual days, carry forward limits, notice periods
- **Workflow** - Approval levels, weekend/half-day leave options
- **Display** - Pagination size, date/time formats, theme mode
- **Calendar** - Week start, holiday colors, business hours
- **Email** - Notification toggles, admin email settings
- **Export** - CSV delimiters, PDF page size/orientation
- **Analytics** - Top N takers, cache TTL, auto-refresh
- **Security** - Password requirements, session timeout

### Notifications

- Email notifications for leave submissions
- Approval/rejection emails to employees
- In-app notification system (bell icon)
- Role-based notification delivery

## Requirements

- PHP 8.1 or higher
- Composer 2.x
- MySQL 5.7+ or SQLite
- Node.js (optional, for frontend assets)

## Installation

```bash
# Clone the repository
git clone https://github.com/your-org/pjlv.git
cd pjlv

# Install dependencies
composer install

# Configure environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_DATABASE=pjlv
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Start server
php artisan serve
```

Open <http://localhost:8000> in your browser.

## Default Admin Account

| Role | Email | Password |
|------|-------|----------|
| Super Admin | <admin@admin.com> | adminpass |

> ⚠️ **Security Warning:** Change the default password immediately after first login!

## User Roles & Permissions

| Role | Permissions |
|------|-------------|
| **Admin** | Full access - manage staff, roles, departments, settings, leave types |
| **HR** | Staff management, leave approval, reports, exports |
| **CEO** | View all, approve/reject leaves, access analytics |
| **OPS** | Operational oversight, view all departments |
| **HOD** | Department head - approve team leaves, view department stats |
| **Normal** | View own leaves, apply for leave |

## Leave Workflow

```
Employee Applies → Pending → Supervisor Reviews → Recommended → HR Approves → Approved
                                    ↓                              ↓
                                 Rejected                     Disapproved
                                    
Employee can Cancel pending requests at any time.
```

### Leave Statuses

1. **Unattended** - New request, awaiting action
2. **Recommended** - Supervisor approved, awaiting HR
3. **Approved** - Fully approved, leave granted
4. **Disapproved** - Rejected by HR/Director
5. **Rejected** - Rejected by supervisor
6. **Cancelled** - Cancelled by employee

## Project Structure

```
pjlv/
├── app/
│   ├── Enums/               # Role, LeaveStatus enums
│   ├── Http/
│   │   ├── Controllers/     # Request handlers
│   │   │   ├── Auth/        # Authentication
│   │   │   ├── Admin*/      # Admin-specific controllers
│   │   │   └── ...          # Feature controllers
│   │   ├── Middleware/      # CheckRole, SuperAdmin
│   │   └── Requests/        # Form validation classes
│   ├── Mail/                # Email notification classes
│   ├── Models/              # Eloquent models
│   └── Services/            # Business logic
│       ├── AnalyticsService.php
│       ├── AuditService.php
│       ├── LeaveBalanceService.php
│       ├── LeaveCalculatorService.php
│       ├── NotificationService.php
│       └── SettingsService.php
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/             # Sample data (Laravel 10 format)
├── resources/views/
│   ├── admin/               # Admin panel views
│   │   ├── settings/        # System settings UI
│   │   └── ...
│   ├── emails/              # Email templates
│   ├── layouts/             # Sidebar, admin_sidebar
│   └── [dashboard, calendar, reports, etc.]
└── routes/
    └── web.php              # Route definitions
```

## Key Routes

### Authenticated Routes (All Users)

| Method | URI | Description |
|--------|-----|-------------|
| GET | /dashboard | Dashboard with statistics |
| GET | /calendar | Interactive leave calendar |
| GET | /leaves | List leave requests |
| GET | /leaves/apply | Apply for leave form |
| POST | /leaves | Submit leave request |
| POST | /leaves/{id}/cancel | Cancel own request |
| GET | /leave-balance | Detailed leave balance dashboard |
| GET | /notifications | View all notifications |

### HR/Manager Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | /staff | List all staff |
| GET | /reports | Leave reports & analytics |
| GET | /reports/export | Export CSV report |
| PUT | /leaves/{id} | Approve/reject leave |

### Admin Only Routes (prefix: /admin)

| Method | URI | Description |
|--------|-----|-------------|
| GET | /admin/dashboard | Admin dashboard with analytics |
| GET | /admin/staffs | Manage all staff |
| GET | /admin/roles | Manage user roles |
| GET | /admin/departments | Manage departments |
| GET | /admin/jobs | Manage job titles |
| GET | /admin/leavetypes | Manage leave types |
| GET | /admin/holidays | Manage public holidays |
| GET | /admin/settings | System settings (50+ options) |
| GET | /admin/analytics | Advanced analytics dashboard |
| GET | /admin/analytics/export | Export analytics as CSV |

## Email Configuration

Update `.env` for email notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="Leave Management System"
```

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Unit/LeaveBalanceServiceTest.php

# Run feature tests
php artisan test tests/Feature/
```

## Tech Stack

- **Backend:** Laravel 10.x
- **Database:** MySQL / SQLite
- **Frontend:** Blade Templates, Material Dashboard Pro
- **Calendar:** FullCalendar.js 5.x
- **Charts:** Chartist.js
- **Auth:** Laravel UI
- **Icons:** Material Icons, Font Awesome

## Recent Updates

### v2.0 (January 2026)

- ✅ Added 50+ configurable system settings
- ✅ Integrated settings into controllers (pagination, calendar, analytics)
- ✅ Added Analytics Dashboard with CSV/PDF export
- ✅ Added sidebar active state highlighting
- ✅ Codebase cleanup - removed unused files
- ✅ Added department management
- ✅ Added HOD role for department heads
- ✅ Improved role-based access control

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
