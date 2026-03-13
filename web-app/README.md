# Web Application Setup

## Directory Structure

```
web-app/
├── public/                    → Public web root
│   ├── index.php             → Homepage
│   ├── mosques.php           → List all mosques
│   ├── mosque.php            → Single mosque detail
│   ├── nearest.php           → Find nearest mosque
│   ├── api/
│   │   └── mosques-with-distance.php  → API endpoint for geolocation
│   ├── admin/
│   │   ├── login.php         → Admin login page
│   │   ├── dashboard.php     → Admin dashboard
│   │   ├── prayer-times.php  → Manage prayer times
│   │   ├── mosques.php       → Manage mosques
│   │   ├── upload.php        → Bulk upload prayer times
│   │   └── logout.php        → Logout
│   ├── css/
│   │   └── style.css         → Custom styling
│   ├── js/
│   │   └── app.js            → JavaScript utilities
│   └── .htaccess             → Apache routing
├── app/
│   ├── models/
│   │   ├── Mosque.php        → Mosque model
│   │   ├── PrayerTime.php    → Prayer time model
│   │   ├── Parking.php       → Parking model
│   │   └── User.php          → User/admin model
│   ├── views/
│   │   ├── layout.php        → Main layout template
│   │   ├── home.php          → Home page view
│   │   ├── mosques.php       → Mosques list view
│   │   ├── mosque.php        → Single mosque view
│   │   └── nearest.php       → Nearest mosque view
│   └── helpers.php           → Utility functions
└── config/
    └── database.php          → Database connection

```

## Features

### Public Pages
- **Home** (`/`) - Display today's prayer times for all mosques
- **Mosques** (`/mosques.php`) - List all mosques with search/filter
- **Mosque Detail** (`/mosque.php?id=X`) - Show prayer times and parking for a specific mosque
- **Find Nearest** (`/nearest.php`) - Use geolocation to find nearest mosque

### Admin Panel
- **Login** (`/admin/login.php`) - Admin authentication
- **Dashboard** (`/admin/dashboard.php`) - Admin overview
- **Prayer Times** (`/admin/prayer-times.php`) - Manage prayer times by month
- **Mosques** (`/admin/mosques.php`) - Edit mosque information and parking details
- **Upload** (`/admin/upload.php`) - Bulk upload prayer times as CSV

### API Endpoints
- **GET `/api/mosques-with-distance.php?lat=X&lon=Y`** - Get all mosques sorted by distance

## Setup Instructions

### 1. Database

Ensure MySQL is running with the following database:
```sql
CREATE DATABASE timetvmcorg_mosquesuk;
-- Tables should already exist with mosque, prayertimes, parking, and admin_users data
```

### 2. Configuration

Copy `.env.example` to `.env` and set your database credentials:
```bash
cp .env.example .env
```

Then update `.env` values:
```env
DB_HOST=127.0.0.1
DB_NAME=timetvmcorg_mosquesuk
DB_USER=root
DB_PASSWORD=
APP_ENV=production
APP_DEBUG=false
```

Notes:
- `.env` is git-ignored and should not be committed.
- This means push/pull does not require changing tracked config files.

### 3. Apache Setup

Make sure `.htaccess` is enabled and `mod_rewrite` is active.

### 4. File Permissions

Ensure the web server can read all PHP files.

### 5. Admin

The admin panel has been removed. Timetable updates are expected via import/cron workflow.

## Running Locally

If using PHP's built-in server:
```bash
cd web-app/public
php -S localhost:8000
```

Then visit: `http://localhost:8000/`

## Deployment

1. Upload the entire `web-app` folder to your shared hosting
2. Point your domain to the `web-app/public` directory
3. Create `.env` on the server from `.env.example` and set production credentials
4. Ensure PHP 7.4+ is installed
5. Enable `.htaccess` and `mod_rewrite`

## CSV Upload Format

For bulk prayer time uploads, use this CSV format:

```csv
mosque_id,date,fajr,zuhr,asr,maghrib,isha
1,2025-05-01,05:30,12:45,15:30,19:00,20:15
1,2025-05-02,05:29,12:45,15:31,19:01,20:16
2,2025-05-01,05:32,12:47,15:35,19:05,20:20
```

Times must be in 24-hour format (HH:MM).

## Troubleshooting

**404 Errors:**
- Make sure `.htaccess` is in place
- Check that `mod_rewrite` is enabled in Apache

**Database Connection Errors:**
- Verify MySQL is running
- Check credentials in `.env`
- Ensure the database and tables exist

**Admin Access:**
- Admin endpoints were removed; this is expected.

## Notes

- Prayer times are displayed for the current date
- Geolocation uses the Haversine formula to calculate distances
- All input is sanitized to prevent SQL injection and XSS
- Configuration is environment-based via `.env`
