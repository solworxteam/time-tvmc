# TVMC Prayer Times (PHP)

Lightweight PHP application for mosque prayer times, mosque listings, and nearest-mosque lookup.

## Current Structure

```text
nodeapps/
    app/
        models/
        views/
        helpers.php
        Logger.php
    config/
        config.php
        database.php
    public/
        index.php
        mosques.php
        mosque.php
        nearest.php
        api/mosques-with-distance.php
        css/
        js/
        .htaccess
    storage/logs/
    bootstrap.php
    .cpanel.yml
    .env.example
```

## Features

- Public home page with daily prayer overview
- Mosques list and individual mosque details
- Nearest mosque lookup (distance API)
- Environment-based configuration (`.env`)
- cPanel Git deployment via `.cpanel.yml`

Admin endpoints are removed by design.

## Local Setup

1. Create environment file:

```bash
copy .env.example .env
```

2. Update `.env` values:

```env
DB_HOST=127.0.0.1
DB_NAME=timetvmcorg_mosquesuk
DB_USER=root
DB_PASSWORD=
APP_ENV=production
APP_DEBUG=false
```

3. Run locally:

```bash
php -S localhost:8000 -t public
```

4. Open:

- `http://localhost:8000/`
- `http://localhost:8000/mosques.php`
- `http://localhost:8000/nearest.php`

## Shared Hosting (cPanel Git)

- Deployment target is `public_html` root.
- `.cpanel.yml` copies `app`, `config`, `storage`, `bootstrap.php`, and `public` contents into `public_html`.

Deployment flow:

1. Push to `main`
2. In cPanel Git Version Control, pull latest
3. Deploy HEAD commit

## Database

Import the provided SQL dump if needed:

```bash
mysql -h 127.0.0.1 -u <user> -p timetvmcorg_mosquesuk < timetvmcorg_mosquesuk.sql
```

## Notes

- `.env` is git-ignored and must not be committed.
- If deploy queue hangs in cPanel, run a minimal `.cpanel.yml` smoke test first, then restore full tasks.
