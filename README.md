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
## 10. Background Services (Supervisor Setup)

Brag heavily relies on real-time WebSockets and queued notifications. In a production environment, you must use **Supervisor** to keep these processes running permanently in the background.

### A. Install Supervisor
If not already installed, install supervisor:
```bash
sudo apt install supervisor -y
```

### B. Configure Reverb WebSocket Server
Create a new configuration file for Reverb:
```bash
sudo nano /etc/supervisor/conf.d/reverb.conf
```
Paste the following configuration:
```ini
[program:reverb]
process_name=%(program_name)s
command=php /var/www/brag/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/brag/storage/logs/reverb.log
```

### C. Configure Queue Worker
Create a new configuration file for the Queue worker:
```bash
sudo nano /etc/supervisor/conf.d/queue.conf
```
Paste the following configuration:
```ini
[program:brag-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/brag/artisan queue:work --queue=default --timeout=60 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/brag/storage/logs/queue.log
```

*(Note: Since you are deploying, ensure your `QUEUE_CONNECTION` in `.env` is set to `database` or `redis`, not `sync`).*

### D. Start the Services
Once the configuration files are saved, tell Supervisor to read them and start the processes:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

---
## 11. Final Web Server Configuration
To serve your application on a custom domain, create a new Nginx server block.

1. **Create the config file:**
   ```bash
   sudo nano /etc/nginx/sites-available/yourdomain.com
   ```

2. **Paste the following configuration** (adjusting paths and domains):
   ```nginx
   server {
       listen 80;
       listen [::]:80;
       server_name yourdomain.com www.yourdomain.com;
       root /var/www/brag/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;
       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

3. **Enable the site and restart Nginx:**
   ```bash
   sudo ln -s /etc/nginx/sites-available/yourdomain.com /etc/nginx/sites-enabled/
   sudo rm /etc/nginx/sites-enabled/default
   sudo nginx -t && sudo systemctl restart nginx
   ```

---

## 12. SSL Configuration (HTTPS)
Secure your domain with free SSL certificates from Let's Encrypt using Certbot.

```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```
Follow the prompts to finalize the SSL setup. Certbot will automatically update your Nginx configuration.

---

## 13. Task Scheduling (CRON Job)
Brag uses Laravel's task scheduler to handle automated tasks (like auto-canceling stale battles). To keep these tasks running, add the following entry to your server's crontab:

1. Open the crontab for the `www-data` user (or your current user):
   ```bash
   crontab -e
   ```

2. Add this line at the bottom of the file (ensure the path matches your deployment directory):
   ```bash
   * * * * * cd /var/www/brag && php artisan schedule:run >> /dev/null 2>&1
   ```

   ---

   ## 13. Custom Artisan Commands
   Brag includes several custom Artisan commands to help with system maintenance and administration:

- **Create Admin User:** Interactively creates a new admin user account.
  ```bash
  php artisan make:admin
  ```

- **Convert Badges to WebP:** Optimizes badge icon PNGs by converting them to WebP format.
  ```bash
  php artisan badge:convert-webp
  ```

- **Auto-Cancel Stale Battles:** Automatically cancels battles where a cancellation request has been ignored for 5+ minutes.
  ```bash
  php artisan app:auto-cancel-battles
  ```

- **Convert Images to WebP:** Optimizes existing user avatars and template photos by converting them to WebP format.
  ```bash
  php artisan app:convert-images-to-webp
  ```

- **Fix Storage Permissions:** Automatically fixes directory and file permissions in the public storage folder.
  ```bash
  php artisan storage:fix-permissions
  ```

- **Grant Shards:** Grant a specific amount of shards to a user ID or to everyone (using `*`).
  ```bash
  php artisan app:grant-shards {amount} {user_id|*}
  ```

- **Reset Forge Cooldown:** Manually resets the 3-day forging cooldown for a specific template ID.
  ```bash
  php artisan forge:reset {template_id}
  ```

- **Revert Battle Room:** Resets a battle room back to its initial pending state (removing opponents and marshalls).
  ```bash
  php artisan battle:revert {room_id}
  ```

- **Reset Application State:** Truncates all database records (except migrations), deletes all uploaded images/templates, re-runs default seeders, and clears all application caches. Perfect for resetting to a fresh "back to zero" state during development or testing.
  ```bash
  php artisan app:reset
  ```

