# Deployment Guide - Production HTTPS Configuration

## Problem: Mixed Content Errors on Share Hosting

When deploying to HTTPS shared hosting, you may encounter Mixed Content errors like:

```
Mixed Content: The page at '<URL>' was loaded over HTTPS, but requested an insecure element '<URL>'. 
This request was not upgraded to HTTPS because it is a local network request.
```

And failed resource loads:
```
Failed to load resource: net::ERR_CONNECTION_REFUSED
Failed to load resource: net::ERR_NAME_NOT_RESOLVED
```

## Root Cause

The issue occurs when:
1. The website is served over HTTPS
2. Resources (images, CSS, JS) are loaded using `http://localhost:8000` or other HTTP URLs
3. Browser blocks insecure content on secure pages

## Solution Implemented

A centralized configuration system (`resources/js/config.js`) has been created that:
- Automatically detects the current domain using `window.location.origin`
- Generates correct storage URLs dynamically
- Works seamlessly in both development (localhost) and production (HTTPS)

## Pre-Deployment Checklist

### 1. Configure Environment Variables

In your production `.env` file, set:

```env
APP_NAME="Warta Daerah Muna Barat"
APP_URL=https://yourdomain.com
APP_DEBUG=false
APP_ENV=production
```

**Important:** 
- `APP_URL` MUST use `https://` protocol
- Set `APP_DEBUG=false` for security
- Set `APP_ENV=production`

### 2. Configure Filesystem

In `config/filesystems.php`, ensure the public disk uses APP_URL:

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

This is already configured correctly in the project.

### 3. Create Storage Symlink

On your production server, run:

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

### 4. Build Frontend Assets

Before deploying, build the Vue.js frontend:

```bash
npm install
npm run build
```

This creates optimized production bundles in the `public/build` directory.

### 5. Set Correct Permissions

Ensure proper file permissions on your production server:

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

(Adjust user/group based on your hosting - may be `nobody:nobody` or similar)

## Deployment Steps for Share Hosting

### Step 1: Upload Files

Upload all project files to your hosting via FTP or cPanel File Manager.

### Step 2: Configure Database

1. Create a MySQL database in cPanel
2. Update `.env` with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_secure_password
   ```

### Step 3: Run Migrations

If you have SSH access:
```bash
php artisan migrate --force
php artisan db:seed --force
```

If no SSH access, you may need to:
1. Export your local database
2. Import via phpMyAdmin
3. Or use a migration tool provided by your host

### Step 4: Set APP_URL

Edit `.env` and set:
```env
APP_URL=https://yourdomain.com
```

Replace `yourdomain.com` with your actual domain.

### Step 5: Clear and Cache Config

With SSH access:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Build Frontend (Optional - can build locally)

If building locally before upload:
```bash
npm run build
```

Then upload the built assets.

## Troubleshooting

### Mixed Content Errors Still Appearing

1. **Clear browser cache**: Press Ctrl+Shift+Delete and clear cached images
2. **Check database URLs**: Some image paths might be hardcoded in database
3. **Verify APP_URL**: Ensure it starts with `https://`
4. **Check CDN/Proxy**: If using Cloudflare or similar, purge cache

### Images Not Loading

1. **Verify storage link**: 
   ```bash
   php artisan storage:link
   ```

2. **Check file permissions**:
   ```bash
   ls -la storage/app/public
   ```

3. **Verify .htaccess** (Apache):
   Ensure `public/.htaccess` exists and contains:
   ```apache
   <IfModule mod_rewrite.c>
       <IfModule mod_negotiation.c>
           Options -MultiViews -Indexes
       </IfModule>

       RewriteEngine On

       # Handle Authorization Header
       RewriteCond %{HTTP:Authorization} .
       RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

       # Redirect Trailing Slashes If Not A Folder...
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_URI} (.+)/$
       RewriteRule ^ %1 [L,R=301]

       # Send Requests To Front Controller...
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [L]
   </IfModule>
   ```

### ERR_CONNECTION_REFUSED

This means the browser is trying to connect to localhost which doesn't exist on the production server. 

**Solution**: The config.js file should handle this automatically. If still occurring:
1. Clear browser cache
2. Clear Laravel cache: `php artisan cache:clear`
3. Rebuild frontend assets: `npm run build`

### ERR_NAME_NOT_RESOLVED

This means DNS cannot resolve the hostname. Check:
1. Domain is properly configured
2. DNS has propagated (can take 24-48 hours)
3. No typos in domain name

## Testing After Deployment

1. **Open Developer Tools** (F12)
2. **Go to Console tab**
3. **Look for errors** - should be no Mixed Content or failed resource errors
4. **Check Network tab** - all resources should load with status 200
5. **Verify HTTPS lock icon** appears in address bar

## Configuration File Reference

### `resources/js/config.js`

This file provides:
- `config.storageUrl` - Dynamic storage URL based on current domain
- `config.getStorageUrl(path)` - Converts image path to full URL
- `config.resolveStorageUrl(path)` - Handles both `/storage/` and raw paths

All Vue components now use this config instead of hardcoded `localhost:8000`.

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug temporarily: `APP_DEBUG=true` (disable after fixing!)
3. Check browser console for JavaScript errors
4. Verify all environment variables are set correctly
