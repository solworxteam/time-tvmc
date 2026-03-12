# TVMC Mosques API

A Node.js/Express REST API for managing mosque information, prayer times, and parking availability across UK Islamic centers.

## Features

- **Mosque Management** - Store and retrieve mosque details
- **Prayer Times** - Display and manage daily prayer times for each mosque
- **Parking Information** - Track available parking (on-site bays, disabled spaces, street parking)
- **Admin Authentication** - Secure login for admins to manage data
- **Database** - MySQL with connection pooling for reliability

## Tech Stack

- **Runtime:** Node.js 18+
- **Framework:** Express.js 5.x
- **Database:** MySQL (MariaDB compatible)
- **Authentication:** JWT (JSON Web Tokens)
- **Middleware:** CORS, Body Parser

## Installation

### Prerequisites
- Node.js 18+ installed
- MySQL database (remote or local)
- npm or yarn

### Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/time-tvmc.git
   cd time-tvmc
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Configure environment variables:**
   ```bash
   cp .env.example .env
   ```
   Then edit `.env` with your database credentials:
   ```
   DB_HOST=your_db_host
   DB_USER=your_db_user
   DB_PASSWORD=your_db_password
   DB_NAME=timetvmcorg_mosquesuk
   PORT=3001
   ```

4. **Import database schema:**
   ```bash
   mysql -h DB_HOST -u DB_USER -p DB_NAME < timetvmcorg_mosquesuk.sql
   ```

5. **Start the server:**
   ```bash
   npm start
   ```

Server runs on `http://localhost:3001`

## API Routes

### Mosques
- `GET /api/mosques` - Get all mosques
- `PUT /api/mosques/:id` - Update mosque info

### Prayer Times
- `GET /api/prayertimes/:date` - Get prayer times for a specific date
- `POST /api/prayertimes/upload` - Upload prayer times (admin)
- `GET /api/prayertimes/mosque/:mosque_id` - Get mosque prayer times

### Parking
- `GET /api/parking/:mosque_id` - Get parking info for a mosque
- `PUT /api/parking/:mosque_id` - Update parking info

### Authentication
- `POST /api/login` - Admin login

## Admin Credentials

Default admin users (from database):
- Username: `Time.TVMC` / Password: `Admin.TVMC.uk`
- Username: `admin` / Password: `hammad`

**Note:** Change these in production!

## Project Structure

```
├── mosque-backend/          # Primary backend code
│   ├── db.js               # MySQL connection pool
│   ├── server.js           # Express server setup
│   ├── package.json        # Dependencies
│   └── routes/
│       ├── mosques.js      # Mosque endpoints
│       ├── prayerTimes.js  # Prayer times endpoints
│       ├── parking.js      # Parking endpoints
│       ├── auth.js         # Authentication endpoints
│       └── prayerTimesAdmin.js
├── timetvmcorg_mosquesuk.sql  # Database schema
├── .env                    # Environment variables (not in git)
├── .env.example           # Example environment file
└── README.md              # This file
```

## Deployment

### Using PM2 (Recommended)

1. **Install PM2 globally:**
   ```bash
   npm install -g pm2
   ```

2. **Start the application:**
   ```bash
   pm2 start mosque-backend/server.js --name "tvmc-api"
   ```

3. **Save PM2 process list (auto-restart on reboot):**
   ```bash
   pm2 save
   pm2 startup
   ```

4. **Monitor:**
   ```bash
   pm2 logs tvmc-api
   pm2 status
   ```

### Using Systemd Service

Create `/etc/systemd/system/tvmc-api.service`:
```ini
[Unit]
Description=TVMC Mosques API
After=network.target

[Service]
Type=simple
User=nodeuser
WorkingDirectory=/path/to/time-tvmc
ExecStart=/usr/bin/node mosque-backend/server.js
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Then enable and start:
```bash
sudo systemctl enable tvmc-api
sudo systemctl start tvmc-api
```

## Environment Variables

Create a `.env` file with:
```
DB_HOST=your_database_host
DB_USER=your_database_user
DB_PASSWORD=your_database_password
DB_NAME=timetvmcorg_mosquesuk
PORT=3001
NODE_ENV=production
```

## Database

The SQL schema includes tables for:
- `mosques` - Mosque information and locations
- `prayertimes` - Prayer times by date and mosque
- `parking` - Parking details per mosque
- `admins` / `admin_users` - Admin credentials

## Troubleshooting

**Connection timeout errors:**
- Verify database host is reachable: `ping your_db_host`
- Check database credentials in `.env`
- Ensure firewall allows MySQL connections (port 3306)

**"Can't add new command when connection is in closed state":**
- This was a bug in the root `db.js` - use `mosque-backend/db.js` instead
- It uses connection pooling which is more reliable

**Port already in use:**
```bash
# Change PORT in .env, or kill the process:
lsof -i :3001
kill -9 <PID>
```

## Contributing

1. Create a feature branch: `git checkout -b feature/your-feature`
2. Commit changes: `git commit -am 'Add new feature'`
3. Push to branch: `git push origin feature/your-feature`
4. Create a Pull Request

## License

ISC

## Support

For issues or questions, contact the development team.
