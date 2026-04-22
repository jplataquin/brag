# Brag - Deployment Guide for Ubuntu Server

This guide outlines the exact CLI commands required to install and deploy the "Brag" Laravel application on a fresh Ubuntu server.

## Prerequisites
Ensure your server has the following installed:
- PHP 8.2+
- Composer
- Node.js & npm
- MySQL 8.0+
- Git
- Nginx or Apache (Nginx recommended)

---

## 1. Clone the Repository
Navigate to your web directory and clone the project.

```bash
cd /var/www
git clone <your-repository-url> brag
cd brag
```

## 2. Install PHP Dependencies
Install the required PHP packages using Composer. The `--no-dev` flag ensures development packages are excluded for a production environment.

```bash
composer install --optimize-autoloader --no-dev
```

## 3. Set Up Environment Variables
Copy the example environment file and configure it for your production server.

```bash
cp .env.example .env
```

**Important:** Open `.env` (`nano .env`) and update the following critical variables:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://yourdomain.com`
- Database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- Broadcast/Reverb settings (`BROADCAST_CONNECTION=reverb`, `REVERB_HOST="yourdomain.com"`)

## 4. Generate Application Key
Generate the unique application encryption key.

```bash
php artisan key:generate
```

## 5. Directory Permissions
Ensure the web server (usually `www-data`) has the correct permissions to write to the storage and bootstrap cache directories.

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 6. Run Database Migrations
Migrate the database to create all necessary tables.

```bash
php artisan migrate --force
```

*(Optional)* If you need the initial Game Titles seeded into the database:
```bash
php artisan db:seed --class=GameTitleSeeder --force
```

## 7. Install Node Dependencies & Build Assets
Install JavaScript dependencies and compile the Vite frontend assets for production.

```bash
npm install
npm run build
```

## 8. Create Storage Link
Create the symbolic link to ensure public storage files (like avatars and card images) are accessible.

```bash
php artisan storage:link
```

## 9. Optimize Application
Cache the configuration, routes, and views to significantly boost performance.

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 10. Background Services (Crucial for Real-Time Features)

Brag heavily relies on real-time WebSockets and queued notifications. You **must** run these background processes. In a production environment, you should use **Supervisor** or **Systemd** to keep these processes running permanently.

### A. Start the Reverb WebSocket Server
This powers the real-time battle rooms and notifications.
```bash
php artisan reverb:start
```

### B. Start the Queue Worker
This handles processing background jobs.
```bash
php artisan queue:work --queue=default --timeout=60
```

*(Note: Since you are deploying, ensure your `QUEUE_CONNECTION` in `.env` is set to `database` or `redis`, not `sync`).*

## 11. Final Web Server Configuration
Point your Nginx or Apache server block's `DocumentRoot` to the `/var/www/brag/public` directory. Restart your web server, and the application will be live.

---

## 12. Task Scheduling (CRON Job)
Brag uses Laravel's task scheduler to handle automated tasks (like auto-canceling stale battles). To keep these tasks running, add the following entry to your server's crontab:

1. Open the crontab for the `www-data` user (or your current user):
   ```bash
   crontab -e
   ```

2. Add this line at the bottom of the file (ensure the path matches your deployment directory):
   ```bash
   * * * * * cd /var/www/brag && php artisan schedule:run >> /dev/null 2>&1
   ```
