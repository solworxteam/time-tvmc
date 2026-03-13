# TVMC Prayer Times - Project Architecture & Code Quality Improvements

## Overview
This document outlines the refactored architecture and security improvements made to the Prayer Times application.

## Architecture Structure

```
web-app/
├── bootstrap.php           # Application initialization (single entry point)
├── config/
│   ├── config.php         # Environment and application settings
│   └── database.php       # Database connection (singleton pattern)
├── app/
│   ├── Logger.php         # Structured logging system
│   ├── helpers.php        # Utility functions with security features
│   ├── models/
│   │   ├── User.php       # Authentication model
│   │   ├── Mosque.php     # Mosque data model
│   │   ├── PrayerTime.php # Prayer time data model
│   │   └── Parking.php    # Parking information model
│   └── views/             # Template files
├── public/
│   ├── index.php          # Home page entry point
│   ├── mosque.php         # Mosque detail page
│   ├── mosques.php        # All mosques listing
│   ├── nearest.php        # Nearest mosque finder
│   ├── admin/             # Admin panel
│   └── api/              # API endpoints
└── storage/
    └── logs/              # Application logs
```

## Key Improvements

### 1. Security Fixes

#### Password Hashing
- **Before:** Plaintext password comparison
- **After:** bcrypt hashing with cost=12
- **Implementation:** `hashPassword()` and `verifyPassword()` functions

```php
// Secure password hashing
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
```

#### CSRF Protection
- **Before:** No CSRF tokens
- **After:** Session-based CSRF token generation and validation
- **Functions:** `generateCsrfToken()`, `validateCsrfToken()`, `getCsrfField()`

#### Session Security
- **Before:** Basic `session_start()`
- **After:** Secure cookies with HttpOnly, Secure, and SameSite flags
- **Session Timeout:** 1 hour with activity tracking

```php
ini_set('session.cookie_secure', true);
ini_set('session.cookie_httponly', true);
ini_set('session.cookie_samesite', 'Strict');
```

#### Input Validation & Sanitization
- **Before:** Minimal validation
- **After:** Input validation functions for dates, times, emails
- **Functions:** `validateDate()`, `validateTime()`, `validateEmail()`, `sanitize()`

#### Security Headers
- **X-Content-Type-Options:** nosniff
- **X-Frame-Options:** DENY
- **X-XSS-Protection:** 1; mode=block
- **Referrer-Policy:** strict-origin-when-cross-origin

### 2. Database Architecture

#### Singleton Pattern
- **Before:** Multiple database connections created per request
- **After:** Single connection instance (Singleton)
- **Benefit:** Better performance, connection pooling

```php
class Database {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

#### Prepared Statements
- **Before:** Direct query execution
- **After:** All queries use prepared statements
- **Benefit:** SQL injection protection

```php
// PDO prepared statements
Database::getInstance()->execute(
    "SELECT * FROM mosques WHERE id = ?",
    [$id]
);
```

### 3. Error Handling & Logging

#### Structured Logging
- **Levels:** ERROR, WARNING, INFO, DEBUG
- **Output:** Timestamped log files in `storage/logs/`
- **Implementation:** `Logger` class with context support

```php
Logger::error("Database error", ['query' => $sql, 'params' => $params]);
Logger::info("User authenticated", ['username' => $username]);
```

#### Exception Handling
- **Before:** Mixed error handling (die(), error_log())
- **After:** Try-catch blocks with logging
- **User Impact:** Graceful error messages instead of raw PHP errors

### 4. Configuration Management

#### Centralized Config
- **File:** `config/config.php`
- **Constants:** All app settings as constants
- **Environment:** Loaded from `.env` file

```php
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('APP_DEBUG', getenv('APP_DEBUG') ?: false);
define('SESSION_LIFETIME', 3600);
```

#### Single Bootstrap
- **File:** `bootstrap.php`
- **Purpose:** Single initialization point for all requests
- **Includes:** Config, Logger, Database, Helpers, Models

### 5. Model Layer Improvements

#### Data Validation
- All models validate input before database operations
- Date/time format validation
- Coordinate range validation (latitude/longitude)

#### Error Handling
- All model methods wrapped in try-catch
- Proper exception messages logged
- Safe return values (empty array on error, not exception)

#### Consistent Methods
- Standard CRUD operations (getById, getAll, create, update, delete)
- Search methods with query limits
- Input sanitization in models

### 6. Code Quality Standards

#### Documentation
- PHPDoc comments on all classes and methods
- Purpose and parameter descriptions
- Return type documentation

#### Function Organization
- Grouped by functionality
- Clear naming conventions
- Single responsibility principle

#### String Functions
- **mb_substr** for multibyte character handling
- **htmlspecialchars** for output encoding
- **filter_var** for email validation

## File Updates Summary

| File | Change | Benefit |
|------|--------|---------|
| bootstrap.php | NEW - Central init file | Single entry point, consistent initialization |
| config/config.php | NEW - Constants & settings | Centralized configuration |
| config/database.php | REFACTORED - Singleton | Improved performance, cleaner code |
| app/Logger.php | NEW - Logging system | Structured logging with levels |
| app/helpers.php | ENHANCED - Security functions | Password hashing, CSRF, validation |
| app/models/User.php | REFACTORED - Secure auth | bcrypt passwords, error handling |
| app/models/Mosque.php | REFACTORED - Error handling | Try-catch, validation, logging |
| app/models/PrayerTime.php | REFACTORED - Validation | Date/time validation, error handling |
| app/models/Parking.php | REFACTORED - Consistency | Proper error handling |
| public/index.php | UPDATED - New bootstrap | Error handling, try-catch |

## Migration Notes

### For Existing Code
1. Update includes to use `require_once __DIR__ . '/../bootstrap.php'` instead of individual files
2. Replace `getDatabase()` calls with `Database::getInstance()`
3. Use `Logger::` methods instead of `error_log()`
4. Use new validation functions before database operations

### For Admin Panel
1. Add CSRF token to all forms: `<?php echo getCsrfField(); ?>`
2. Validate CSRF token: `validateCsrfToken($_POST['csrf_token'])`
3. Hash passwords on user creation: `hashPassword($password)`
4. Log important actions: `Logger::info("Action description")`

## Security Checklist

- ✅ Password hashing (bcrypt)
- ✅ CSRF token protection
- ✅ Secure session cookies
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS prevention (output encoding)
- ✅ Input validation
- ✅ Error logging (no exposure to users)
- ✅ Security HTTP headers
- ✅ Session timeout
- ⚠️  File upload validation (needs implementation in upload.php)
- ⚠️  Rate limiting (needs implementation)
- ⚠️  API authentication (needs implementation)

## Next Steps

1. **File Upload Security**
   - Validate file types
   - Limit file size
   - Store outside web root
   - Validate CSV content

2. **Rate Limiting**
   - Implement login attempt throttling
   - API request limits

3. **API Authentication**
   - Token-based authentication
   - CORS policy

4. **Database Migration**
   - Encrypt admin_users.password column (hash existing)
   - Add audit logging

5. **Testing**
   - Unit tests for models
   - Integration tests for API
   - Security penetration testing

## Environment Variables (.env)

```env
# Database Configuration
DB_HOST=127.0.0.1
DB_NAME=timetvmcorg_mosquesuk
DB_USER=root
DB_PASSWORD=

# Application Settings
APP_DEBUG=false
APP_ENV=production
```

## Performance Improvements

- Single database connection (singleton pattern)
- Prepared statement caching via PDO
- Query optimization with proper indexing
- Reduced object instantiation
- Efficient session handling

## Compatibility

- PHP 7.4+
- MySQL 5.7+
- All existing functionality preserved
- Backward compatible helper functions
- Legacy plaintext password support (auto-upgrade)

---

**Last Updated:** March 13, 2026
**Architecture Version:** 2.0
**Security Level:** HIGH
