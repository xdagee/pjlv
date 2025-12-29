# PJLV - Employee Leave Management System

A Laravel 10 based employee leave management system for organizations to streamline leave requests, approvals, and tracking.

## Features

### Core Features
- **Staff Management** - Create, view, update, and deactivate employee records
- **Leave Requests** - Apply for leave with automatic day calculation and balance checking
- **Approval Workflow** - Multi-level approval (Supervisor → HR → Director) with email notifications
- **Role-Based Access** - Admin, HR, DG, Director, Normal employee roles with hierarchy

### Dashboard
- Pending leave requests count
- Staff on leave today
- Personal leave balance
- Upcoming holidays (next 30 days)
- Recent leave history

### Calendar
- Interactive FullCalendar.js integration
- Approved leaves displayed as events
- Public holidays highlighted
- Color-coded by leave type

### Reports
- Leave statistics by type
- Monthly trend charts (Chart.js)
- Top leave takers list
- CSV export functionality

### Notifications
- Email notifications for leave submissions
- Approval/rejection emails to employees
- In-app notification system (bell icon)

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

Open http://localhost:8000 in your browser.

## Default Users

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@admin.com | adminpass |
| HR | hr@company.com | hrpass123 |
| Employee | john.doe@company.com | userpass123 |

## User Roles & Permissions

| Role | Permissions |
|------|-------------|
| **Admin** | Full access - manage staff, jobs, leaves, settings, leave types |
| **HR** | Staff management, leave approval, reports, exports |
| **DG** | Approve/reject leaves, view reports |
| **Director** | Approve/reject leaves |
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
│   ├── Http/
│   │   ├── Controllers/     # Request handlers
│   │   ├── Middleware/      # CheckRole, CheckPermission
│   │   └── Requests/        # Form validation classes
│   ├── Mail/                # Email notification classes
│   ├── Services/            # LeaveBalanceService
│   └── [Models]             # Eloquent models
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/             # Sample data (Laravel 10 format)
├── resources/views/
│   ├── emails/              # Email templates
│   ├── leaves/              # Leave CRUD views
│   └── [dashboard, calendar, reports]
└── routes/
    └── web.php              # Route definitions
```

## Key Routes

### Authenticated Routes (All Users)
| Method | URI | Description |
|--------|-----|-------------|
| GET | /dashboard | Dashboard with statistics |
| GET | /calendar | Interactive leave calendar |
| GET | /calendar/events | Calendar events API |
| GET | /leaves | List leave requests |
| GET | /leaves/apply | Apply for leave form |
| POST | /leaves | Submit leave request |
| POST | /leaves/{id}/cancel | Cancel own request |

### HR/Admin Routes
| Method | URI | Description |
|--------|-----|-------------|
| GET | /staff | List all staff |
| GET | /reports | Leave reports & analytics |
| GET | /reports/export | Export CSV report |
| PUT | /leaves/{id} | Approve/reject leave |

### Admin Only Routes
| Method | URI | Description |
|--------|-----|-------------|
| GET | /jobs | Manage job titles |
| GET | /leavetypes | Manage leave types |

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
php artisan test tests/Unit/StaffTest.php
```

## Tech Stack

- **Backend:** Laravel 10.x
- **Database:** MySQL / SQLite
- **Frontend:** Blade Templates, Material Dashboard
- **Calendar:** FullCalendar.js 5.x
- **Charts:** Chart.js
- **Auth:** Laravel UI

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
