# Divisional Secretariat Service Management System – Minipe

A web-based system for the Divisional Secretariat Office - Minipe that allows citizens to request government services online, schedule appointments, track requests, and receive notifications.

**Technologies:** HTML5, CSS3, JavaScript, Bootstrap 5, PHP, MySQL

## Features

### Citizen
- Register, login, logout
- Update profile and change password
- Book and manage appointments
- Submit service requests with document upload
- Track request status
- Receive notifications
- Bilingual interface (English / Sinhala)

### Administrative Officer
- Admin dashboard with statistics and charts
- Manage citizen accounts
- Approve/reject/complete appointments
- Process service requests with remarks
- Send notifications
- Generate and export reports (PDF)

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache (XAMPP, WAMP, Laragon, etc.)
- PDO MySQL extension enabled

## Installation

1. Clone or copy this project to your web server directory (e.g. `htdocs/appointment-scheduling`).

2. Update database credentials in `config/database.php` if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'minipe_dssms');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. Start Apache and MySQL.

4. Open in browser: `http://localhost/appointment-scheduling/setup.php`

5. Click **Install Database** to create tables and default accounts.

6. Delete or protect `setup.php` after installation.

## Default Login Credentials

| Role    | Email                 | Password    |
|---------|-----------------------|-------------|
| Admin   | admin@minipe.gov.lk   | admin123    |
| Citizen | kamal@example.com     | citizen123  |

## Project Structure

```
├── admin/              Admin dashboard and management
├── auth/               Login, register, logout
├── citizen/            Citizen portal
├── config/             App and database configuration
├── database/           SQL schema
├── includes/           Shared PHP includes
├── lang/               Language files (en.php, si.php)
├── lib/                PDF export helper
├── uploads/            Uploaded documents
├── assets/             CSS and JavaScript
├── index.php           Home page
└── setup.php           Database installer
```

## Database Tables

- `users` – Citizen and admin accounts
- `appointments` – Appointment scheduling
- `service_requests` – Service request submissions
- `notifications` – User notifications

## Service Types

- Birth / Death / Marriage Certificate Assistance
- Residence Certificate
- Character Certificate Request
- Land, Samurdhi, Pension Related Requests
- Grama Niladhari Services
- Other Public Services

## Language Support

Switch language from the navigation bar. Language files:
- `lang/en.php` – English
- `lang/si.php` – Sinhala

## University Project Notes

This system demonstrates:
- Role-based authentication (Citizen / Admin)
- CRUD operations with MySQL
- File upload handling
- Notification system
- Admin dashboard with Chart.js
- PDF report export
- Bilingual UI with PHP language files

## License

Educational project – Divisional Secretariat Service Management System, Minipe.
