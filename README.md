# AcademiCore - Instrument Allocation System

## Project Overview

AcademiCore is a web-based Laboratory Instrument Allocation and Booking System built with Laravel 12. It digitizes the process of allocating laboratory and workshop instruments to students in ICT, IAT, ET, and AT departments, replacing manual booking methods with a centralized, role-based system.

## Key Features

- 🔐 **Role-Based Access Control** - Student, Staff, and Admin roles
- 📅 **Instrument Booking System** - With conflict detection and approval workflow
- 🏢 **Department Management** - ICT, IAT, ET, AT departments
- 📊 **Usage Reports** - Comprehensive analytics and CSV export
- 🔍 **Advanced Filtering** - Search by department, category, status
- 📝 **Audit Trail** - Complete logging of all booking changes
- ✅ **Approval Workflow** - Staff can approve/reject bookings
- 🔄 **Status Tracking** - Real-time instrument availability

## Technology Stack

- **Framework:** Laravel 12
- **PHP Version:** 8.2+
- **Database:** SQLite (configured)
- **Frontend:** Blade Templates, Bootstrap 5, Tailwind CSS 4
- **Build Tool:** Vite 7.0

## Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM

### Installation

1. **Clone the repository** (if not already done)
   ```powershell
   cd e:\academicore\academicore
   ```

2. **Install Dependencies**
   ```powershell
   composer install
   npm install
   ```

3. **Run Migrations and Seed Database**
   ```powershell
   php artisan migrate:fresh --seed
   ```

4. **Start Development Server**
   ```powershell
   php artisan serve
   ```

5. **Access Application**
   Open browser: `http://localhost:8000`

## Test Credentials

### Admin
- **Email:** admin@university.edu
- **Password:** password123

### Staff
- **ICT Staff:** staff.ict@university.edu
- **IAT Staff:** staff.iat@university.edu
- **Password:** password123

### Students
- **Student 1:** student1@university.edu (ICT)
- **Student 2:** student2@university.edu (IAT)
- **Student 3:** student3@university.edu (ICT)
- **Password:** password123

## Project Structure

```
academicore/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── BookingController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── InstrumentController.php
│   │   │   └── ReportController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Role.php
│       ├── Department.php
│       ├── Instruments.php
│       ├── Booking.php
│       ├── BookingLog.php
│       └── InstrumentStatusHistory.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_roles_table.php
│   │   ├── 2024_01_01_000002_create_departments_table.php
│   │   ├── 2024_01_01_000003_create_custom_users_table.php
│   │   ├── 2024_01_01_000004_create_instruments_table.php
│   │   ├── 2024_01_01_000005_create_bookings_table.php
│   │   ├── 2024_01_01_000006_create_booking_logs_table.php
│   │   └── 2024_01_01_000007_create_instrument_status_history_table.php
│   └── seeders/
│       ├── RoleSeeder.php
│       ├── DepartmentSeeder.php
│       ├── UserSeeder.php
│       └── InstrumentSeeder.php
├── resources/
│   └── views/
│       ├── auth/
│       ├── dashboards/
│       ├── instrument/
│       └── layouts/
└── routes/
    └── web.php
```

## Database Schema

### Core Tables
1. **roles** - User roles (student, staff, admin)
2. **departments** - Academic departments
3. **users** - System users with role and department
4. **instruments** - Laboratory equipment catalog
5. **bookings** - Booking records with approval workflow
6. **booking_logs** - Audit trail for booking changes
7. **instrument_status_history** - Instrument status tracking

## Features by Role

### Student
- Browse available instruments
- Create bookings with time slot selection
- View and manage own bookings
- Cancel future bookings
- View booking history

### Staff (Lab Technical Officer)
- View department-specific dashboard
- Approve/reject booking requests
- View all department bookings
- Update instrument status
- Generate usage reports
- Export reports to CSV

### Administrator
- System-wide dashboard and analytics
- Manage instrument inventory (CRUD)
- View all bookings across departments
- Approve/reject any booking
- Generate system-wide reports
- Manage users and roles

## Key Functionalities

### Booking Conflict Detection
The system automatically checks for overlapping bookings:
- Same instrument
- Same date
- Overlapping time slots
- Prevents double-booking

### Approval Workflow
- Instruments can require approval (configurable)
- Staff can approve/reject bookings
- Automatic approval for non-restricted instruments
- Email notifications (ready to implement)

### Department-Based Access Control
- Staff can only manage their department's instruments
- Students can book instruments from any department
- Admin has full system access

### Audit Trail
- All booking changes are logged
- Track who made changes and when
- Reason for rejection recorded
- Instrument status history maintained

## API Endpoints

### Authentication
- `POST /login` - User login
- `POST /register` - User registration
- `POST /logout` - User logout

### Student Routes
- `GET /student/dashboard` - Dashboard
- `GET /student/instruments` - Browse instruments
- `GET /student/my-bookings` - My bookings
- `POST /student/bookings` - Create booking
- `POST /student/bookings/{id}/cancel` - Cancel booking

### Staff Routes
- `GET /staff/dashboard` - Staff dashboard
- `GET /staff/bookings/pending` - Pending approvals
- `POST /staff/bookings/{id}/approve` - Approve booking
- `POST /staff/bookings/{id}/reject` - Reject booking
- `GET /staff/reports/usage` - Usage reports

### Admin Routes
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/instruments` - Instrument management
- `POST /admin/instruments` - Create instrument
- `PUT /admin/instruments/{id}` - Update instrument
- `DELETE /admin/instruments/{id}` - Delete instrument
- `GET /admin/reports/usage` - System reports

## MVC Architecture

This project strictly follows MVC (Model-View-Controller) architecture:

- **Models:** Handle data and relationships only
- **Views:** Handle presentation and UI
- **Controllers:** Handle business logic and coordination
- **No business logic in views**
- **No database queries in views**
- **Proper separation of concerns**

## Security Features

- ✅ Password hashing with bcrypt
- ✅ CSRF protection
- ✅ Role-based authorization
- ✅ Input validation
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade templates)

## Development Status

**Backend:** 85% Complete
- ✅ Database schema
- ✅ All models with relationships
- ✅ Authentication system
- ✅ Booking system with conflict detection
- ✅ Approval workflow
- ✅ Reports and analytics
- ⚠️ Admin instrument CRUD (needs views)

**Frontend:** 30% Complete
- ✅ Login/Register pages
- ✅ Student dashboard
- ✅ Instrument browsing
- ⚠️ Booking management pages needed
- ⚠️ Staff/Admin dashboards needed
- ⚠️ Admin CRUD pages needed

## Documentation

- `QUICK_START_GUIDE.md` - Complete setup and testing guide
- `IMPLEMENTATION_STATUS.md` - Detailed progress report
- `AcadmiCore_Instrument_Allocation_Laravel_Implementation_Plan(1).md` - Requirements document

## Future Enhancements

- Email notification system
- File uploads for instrument images
- Advanced search and filtering
- Calendar view for bookings
- Real-time notifications
- User management module
- Booking reminders
- QR code generation for instruments

## License

This project is developed for academic purposes as part of university coursework.

## Contributors

- Development Team: AcademiCore Project

## Support

For issues or questions:
1. Check `QUICK_START_GUIDE.md`
2. Review `IMPLEMENTATION_STATUS.md`
3. Inspect Laravel logs: `storage/logs/laravel.log`

## Commands Reference

```powershell
# Fresh start
php artisan migrate:fresh --seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# View routes
php artisan route:list

# Start server
php artisan serve

# Run tests
php artisan test
```

---

**Project Status:** Ready for demonstration and further development
**Laravel Version:** 12.0
**Last Updated:** November 19, 2025
