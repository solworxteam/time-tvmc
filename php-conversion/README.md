# TVMC Mosques API - PHP Version

A PHP/MySQL REST API for managing mosque information, prayer times, and parking availability across UK Islamic centers.

**Status:** Converted from Node.js/Express to PHP for shared hosting deployment.

## Features

- **Mosque Management** - Store and retrieve mosque details
- **Prayer Times** - Display and manage daily prayer times for each mosque
- **Parking Information** - Track available parking (on-site bays, disabled spaces, street parking)
- **Admin Authentication** - Session-based login for admins
- **Database** - MySQL with prepared statements and PDO
- **Environment Configuration** - .env file for local and production settings
- **Modern MVC Structure** - Clean separation of concerns

## Tech Stack

- **Language:** PHP 7.4+
- **Database:** MySQL (MariaDB compatible)
- **Web Server:** Apache with mod_rewrite
- **Configuration:** Environment variables via .env
- **Architecture:** MVC with Controllers, Models, Services

## Project Structure

```
php-conversion/
├── public/
│   ├── index.php              # Main entry point
│   └── .htaccess              # Apache routing
├── app/
│   ├── controllers/           # API controllers
│   │   ├── MosqueController.php
│   │   ├── PrayerTimeController.php
│   │   ├── ParkingController.php
│   │   └── AuthController.php
│   ├── models/                # Data models
│   │   ├── Mosque.php
│   │   ├── PrayerTime.php
│   │   ├── Parking.php
│   │   └── User.php
│   ├── services/              # Business logic
│   │   └── DatabaseService.php
│   ├── middleware/            # Request middleware (placeholder)
│   ├── Router.php             # URL routing
│   └── helpers.php            # Utility functions
├── config/
│   └── database.php           # Database configuration
├── storage/
│   ├── logs/                  # Application logs
│   └── .gitkeep
├── .env.example               # Environment template
├── .htaccess                  # Apache configuration
├── .gitignore                 # Git ignore rules
└── README.md                  # This file
```

## Local Development Setup (Laragon + PHP + Apache)

### Prerequisites
- Laragon installed (includes Apache, PHP, MySQL)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation Steps

1. **Create project directory:**
   ```bash
   cd C:\laragon\www
   mkdir time-tvmc
   cd time-tvmc
   ```

2. **Clone repository:**
   ```bash
   git clone https://github.com/yourusername/time-tvmc.git .
   cd php-conversion
   ```

3. **Copy environment file:**
   ```bash
   copy .env.example .env
   ```

4. **Edit .env with your local database credentials:**
   ```
   DB_HOST=127.0.0.1
   DB_USER=root
   DB_PASSWORD=
   DB_NAME=timetvmcorg_mosquesuk
   ```

5. **Import database schema:**
   ```bash
   mysql -u root timetvmcorg_mosquesuk < ../timetvmcorg_mosquesuk.sql
   ```

6. **Start Laragon:**
   - Open Laragon
   - Click "Start All"
   - Project will be available at http://time-tvmc.test

7. **Test API:**
   ```bash
   curl http://time-tvmc.test/api
   # Expected: {"message":"API is working ✅"}
   ```

## API Endpoints

### Health Check
- `GET /` or `GET /api` - API health check

### Mosques
- `GET /api/mosques` - Get all mosques
- `GET /api/mosques/:id` - Get mosque details with prayer times
- `PUT /api/mosques/:id` - Update mosque (admin required)

### Prayer Times
- `GET /api/prayertimes/:date` - Get prayer times for a specific date (format: YYYY-MM-DD)
- `GET /api/prayertimes/by-mosque/:mosque_id?month=M&year=Y` - Get prayer times by mosque and month
- `POST /api/prayertimes/upload` - Bulk upload prayer times (admin required)
- `PUT /api/prayertimes/:id` - Update individual prayer time (admin required)

### Parking
- `GET /api/parking/:mosque_id` - Get parking information for a mosque
- `PUT /api/parking/:mosque_id` - Update parking information (admin required)

### Authentication
- `POST /api/login` - Admin login (JSON: username, password)
- `GET /api/auth/me` - Get current logged-in user
- `POST /api/logout` - Logout

## Example Requests

### Get all mosques
```bash
curl http://time-tvmc.test/api/mosques
```

### Get prayer times for a date
```bash
curl http://time-tvmc.test/api/prayertimes/2025-05-12
```

### Login
```bash
curl -X POST http://time-tvmc.test/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"hammad"}'
```

### Upload prayer times
```bash
curl -X POST http://time-tvmc.test/api/prayertimes/upload \
  -H "Content-Type: application/json" \
  -d '{
    "prayerTimes": [
      {
        "mosque_id": "42f7385d-60ac-4c73-adc8-1ca39654072d",
        "date": "2025-06-01T00:00:00",
        "fajar_start": "03:50",
        "fajar_jamaat": "04:45",
        "zuhr_start": "13:05",
        "zuhr_jamaat": "13:30",
        "asr_start": "17:02",
        "asr_jamaat": "18:00",
        "maghrib": "20:29",
        "isha_start": "21:34",
        "isha_jamaat": "21:59"
      }
    ]
  }'
```

## Environment Variables

### Local Development (.env)
```
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=
DB_NAME=timetvmcorg_mosquesuk
```

### Production (.env on cPanel)
```
DB_HOST=your-production-host.com
DB_USER=your_production_user
DB_PASSWORD=your_production_password
DB_NAME=timetvmcorg_mosquesuk
```

**Note:** Never commit `.env` file to Git. Use `.env.example` as a template.

## Production Deployment (cPanel)

### Prerequisites
- cPanel hosting account
- SSH access (or File Manager)
- MySQL database created via cPanel

### Deployment Steps

1. **Create directories in public_html:**
   ```bash
   mkdir -p public_html/time-tvmc/{app,config,storage/logs}
   mkdir -p public_html/time-tvmc/public
   ```

2. **Upload files via Git or FTP:**
   ```bash
   # Via Git (from Laragon)
   cd php-conversion
   git push origin main
   
   # Then on server
   ssh user@your-host.com
   cd public_html/time-tvmc
   git clone https://github.com/yourusername/time-tvmc.git .
   ```

3. **Copy environment file and configure:**
   ```bash
   cp .env.example .env
   # Edit .env with production database credentials
   nano .env
   ```

4. **Set file permissions:**
   ```bash
   chmod 755 public
   chmod 755 storage
   chmod 755 storage/logs
   chmod 644 .htaccess
   chmod 644 public/.htaccess
   ```

5. **Import database:**
   ```bash
   mysql -u your_user -p your_database < timetvmcorg_mosquesuk.sql
   ```

6. **Verify installation:**
   ```bash
   curl https://yourdomaincom/time-tvmc/api
   ```

### cPanel File Manager Setup

1. Upload contents of `php-conversion/` to `public_html/time-tvmc/`
2. Create `.env` file with your production credentials
3. Upload database schema and import via phpMyAdmin
4. Set correct permissions on directories

### Access API

After deployment, access API at:
```
https://yourdomaine.com/time-tvmc/api
```

## Admin Credentials

Default admin users (from imported database):
- Username: `Time.TVMC` / Password: `Admin.TVMC.uk`
- Username: `admin` / Password: `hammad`

⚠️ **IMPORTANT:** Change these credentials in production!

## Database

The MySQL database schema includes tables for:
- **mosques** - Mosque information and locations
- **prayertimes** - Prayer times by date and mosque
- **parking** - Parking details per mosque
- **admin_users** - Admin login credentials

All tables are defined in `timetvmcorg_mosquesuk.sql`.

## Security

- ✅ All database queries use prepared statements (PDO)
- ✅ Input sanitization on sensitive data
- ✅ CORS headers configured
- ✅ Error handling without exposing stack traces
- ✅ Session-based authentication
- ⚠️ Plaintext passwords in database (matches legacy Node.js implementation)

### Security Recommendations for Production

1. **Hash passwords:** Update admin_users table to use hashed passwords
   ```php
   // Use password_hash() when creating new users
   password_hash($password, PASSWORD_BCRYPT)
   ```

2. **HTTPS only:** Ensure `.htaccess` redirects to HTTPS
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

3. **Rate limiting:** Implement rate limits for login attempts

4. **Logging:** Enable error logging to track issues

## Frontend Integration

The React frontend should continue working without changes:

**Old Node.js endpoint:** `http://localhost:3001/api/*`
**New PHP endpoint:** `http://yourdomain.com/time-tvmc/api/*`

Update React API base URL to point to the new PHP endpoint.

## Troubleshooting

### "Database connection failed"
- Check `.env` file exists and has correct credentials
- Verify MySQL server is running
- Confirm database exists: `mysql -u user -p -e "SHOW DATABASES"`

### "Route not found" (404)
- Verify `.htaccess` files exist and mod_rewrite is enabled
- Check Apache error log: `tail -f /var/log/apache2/error.log`
- Test with: `curl -i http://localhost/api`

### "No prayer times found"
- Verify prayer times were uploaded correctly
- Check date format (must be YYYY-MM-DD)
- Verify mosque_id exists in database

### Cannot write to logs directory
- Check permissions: `chmod 755 storage/logs`
- Verify web server user can write: `ls -la storage/logs`

## Performance

- Database queries use indices on common filters (mosque_id, date)
- Connection pooling handled by PDO
- Prepared statements prevent SQL injection and improve performance
- No N+1 query problems

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit changes: `git commit -am 'Add new feature'`
4. Push to branch: `git push origin feature/your-feature`
5. Create Pull Request

## Maintenance

### Regular Tasks
- Monitor error logs: `storage/logs/error.log`
- Backup database regularly
- Update admin credentials periodically
- Review access logs

### Update Procedures
1. Pull latest changes: `git pull origin main`
2. Test in development environment
3. Verify all API endpoints work
4. Deploy to production

## License

ISC

## Support

For issues or questions about the PHP conversion, contact: support@tvmc.org.uk

## Conversion Notes

This PHP version was converted from Node.js/Express to work on shared hosting with cPanel + Apache + PHP.

**Key Changes:**
- Node.js Express routes replaced with PHP MVC controllers
- Connection pooling (Node.js) replaced with PDO (PHP)
- Removed npm/Node.js dependencies entirely
- Session-based authentication instead of JWT
- Apache .htaccess for URL rewriting instead of Node.js server
- Environment variables loaded from .env file
- All database queries converted to PDO prepared statements

**API Response Format:** Unchanged (same JSON responses as Node.js version)

**Database Schema:** Unchanged (same MySQL tables and structure)
