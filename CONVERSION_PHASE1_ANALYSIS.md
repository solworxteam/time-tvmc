# PHASE 1: COMPLETE PROJECT ANALYSIS REPORT

## 1. API ROUTES IDENTIFIED

### Mosques Module
- `GET /api/mosques` - Get all mosques
- `GET /api/mosques/:id` - Get mosque by ID with prayer times
- `PUT /api/mosques/:id` - Update mosque (name, address, location_url)

### Prayer Times Module
- `GET /api/prayertimes/:date` - Get prayer times for specific date
- `POST /api/prayertimes/upload` - Bulk upload prayer times (admin)
- `GET /api/prayertimes/by-mosque/:mosqueId?month=M&year=Y` - Get by mosque and month/year
- `PUT /api/prayertimes/:id` - Update individual prayer time

### Parking Module
- `GET /api/parking/:mosque_id` - Get parking info for mosque
- `PUT /api/parking/:mosque_id` - Update or insert parking info

### Authentication Module
- `POST /api/login` - Admin login

## 2. DATABASE TABLES USED

### Table: admin_users
- Stores admin login credentials
- Used by: /api/login
- Auth method: Plaintext username/password comparison (current active)

### Table: mosques
- id (UUID)
- name
- address
- location_url
- postcode
- Contains 11 mosques

### Table: prayertimes
- id (auto-increment)
- mosque_id (UUID)
- date
- fajar_start, fajar_jamaat
- zuhr_start, zuhr_jamaat
- asr_start, asr_jamaat
- maghrib
- isha_start, isha_jamaat
- Contains prayer times for multiple mosques

### Table: parking
- id (UUID)
- mosque_id
- onsite_parking (number or 'yes'/'no')
- disable_bays
- off_street_parking
- road_name
- address
- distance_to_mosque

### Table: admins
- Exists but appears unused (legacy table)

## 3. DUPLICATE FILES DETECTED

### CRITICAL: Root vs Mosque-Backend Duplication
Both directories contain IDENTICAL code:

**Root Level:**
```
/routes/auth.js
/routes/login.js
/routes/mosques.js
/routes/prayerTimes.js
/routes/prayerTimesAdmin.js
/routes/parking.js
/server.js
/db.js
```

**Mosque-Backend Copy:**
```
/mosque-backend/routes/auth.js
/mosque-backend/routes/login.js
/mosque-backend/routes/mosques.js
/mosque-backend/routes/prayerTimes.js
/mosque-backend/routes/prayerTimesAdmin.js
/mosque-backend/routes/parking.js
/mosque-backend/server.js
/mosque-backend/db.js
```

### DECISION: Use mosque-backend version (ACTIVE PRODUCTION)
The mosque-backend/db.js is the currently active version with local MySQL config.
Root level appears to be legacy backup.

## 4. AUTHENTICATION ANALYSIS

### Current Implementation (ACTIVE): auth.js
```javascript
- Plaintext password comparison
- No JWT tokens
- Simple JSON response
- Accesses admin_users table directly
- NO password hashing
```

### Alternative Implementation: login.js
```javascript
- Uses bcrypt for password hashing
- JWT token generation
- More secure but not currently used
```

### Decision for PHP Conversion
Implement session-based authentication:
- PHP $_SESSION for admin dashboard
- Simple, suits shared hosting
- Plaintext password comparison (matching current behavior)
- Option to add JWT for API if needed later

## 5. QUERY PATTERNS

All queries use prepared statements:
```sql
- SELECT * FROM table WHERE column = ?
- UPDATE table SET col = ? WHERE id = ?
- INSERT INTO table (cols) VALUES (?, ?, ?)
- ON DUPLICATE KEY UPDATE (for prayer times)
```

No SQL injection vulnerabilities detected in current code.

## 6. DATABASE QUERIES BY ROUTE

### Mosques
- `SELECT * FROM mosques` (get all)
- `SELECT * FROM mosques WHERE id = ?` (get by ID)
- `SELECT * FROM prayertimes WHERE mosque_id = ?` (prayer times join)
- `UPDATE mosques SET name = ?, address = ?, location_url = ? WHERE id = ?`

### Prayer Times
- `SELECT * FROM prayertimes WHERE date = ?`
- `INSERT INTO prayertimes (...) VALUES (...) ON DUPLICATE KEY UPDATE`
- `SELECT * FROM prayertimes WHERE mosque_id = ? AND MONTH(date) = ? AND YEAR(date) = ?`
- `UPDATE prayertimes SET fajar_start = ?, ... WHERE id = ?`

### Parking
- `SELECT * FROM parking WHERE mosque_id = ?`
- `UPDATE parking SET onsite_parking = ?, ... WHERE mosque_id = ?`
- `INSERT INTO parking (id, mosque_id, ...) VALUES (UUID(), ?, ...)`

### Authentication
- `SELECT * FROM admin_users WHERE username = ? AND password = ?`

## 7. FRONTEND INTEGRATION

React components consume API endpoints:
- All requests go to http://localhost:3001/api/*
- All responses are JSON
- PHP conversion must maintain same API structure for frontend compatibility

## 8. CONVERSION STRATEGY

### Phase 2 Action Items
1. Use mosque-backend/ only (delete root-level duplicates after conversion)
2. Create PHP MVC structure
3. Replace Node.js server with Apache + PHP
4. Convert PDO database layer
5. Maintain same API routes and response formats
6. Use session-based auth
7. Support .env configuration

### Recommended PHP Structure
```
/public
    /index.php
    /.htaccess
/app
    /controllers (MosqueController, PrayerTimesController, etc.)
    /models (Mosque, PrayerTime, Parking, User)
    /services (DatabaseService, AuthService)
    /middleware (checkAuth.php)
/config
    /database.php
/routes
    /api.php
/.env
/.env.example
/README.md
```

## 9. KEY ENDPOINTS TO MAINTAIN

For React frontend compatibility:

```
POST   /api/login                              (JSON: username, password)
GET    /api/mosques                             (JSON array of mosques)
GET    /api/mosques/:id                         (Single mosque with prayer times)
PUT    /api/mosques/:id                         (Update mosque)
GET    /api/prayertimes/:date                   (Prayer times for date)
GET    /api/prayertimes/by-mosque/:id?m&y      (Prayer times by mosque)
POST   /api/prayertimes/upload                  (Bulk upload)
PUT    /api/prayertimes/:id                     (Update individual time)
GET    /api/parking/:mosque_id                  (Get parking info)
PUT    /api/parking/:mosque_id                  (Update parking info)
```

## 10. CRITICAL NOTES FOR CONVERSION

1. **Environment Configuration**
   - Must use .env file (not committed to Git)
   - Include .env.example template

2. **Database Connection**
   - Use PDO for all queries
   - Prepared statements mandatory
   - Support both local and production configs

3. **Backward Compatibility**
   - React frontend must continue working
   - Same API endpoints and response formats required
   - Same parameter names and query structure

4. **Security**
   - Sanitize all inputs
   - Use prepared statements
   - Add CORS headers if needed
   - Error handling without stack traces

5. **No Node Runtime**
   - Completely remove Node.js/npm dependencies
   - Pure PHP solution
   - Apache .htaccess routing
