# Panduan Hosting Agape153 di Rumahweb cPanel

Panduan ini dibuat untuk kondisi saat domain masih menampilkan `Index of /`. Itu terjadi karena domain membaca folder `public_html`, sementara Laravel harus dijalankan dari `public/index.php`.

## 1. Struktur Folder Yang Disarankan

Gunakan struktur ini di File Manager cPanel:

```text
/home/agaa3627/
├── agape153_app/          <- root Laravel: app, bootstrap, config, database, resources, routes, storage, vendor, .env
└── public_html/           <- hanya file publik: index.php, .htaccess, build, images, storage, favicon, robots.txt
```

Jangan biarkan folder `app`, `bootstrap`, `config`, `database`, `resources`, `routes`, `storage`, `vendor`, dan file `.env` terbuka langsung di `public_html`.

## 2. Rapikan File Di cPanel

1. Masuk ke cPanel > File Manager.
2. Buat folder baru di luar `public_html`:

```text
/home/agaa3627/agape153_app
```

3. Pindahkan folder/file Laravel dari `public_html` ke `agape153_app`, kecuali isi folder `public`.

Yang dipindahkan ke `agape153_app`:

```text
app
bootstrap
config
database
docs
resources
routes
storage
vendor
artisan
composer.json
composer.lock
package.json
package-lock.json
vite.config.js
.env
```

4. Salin isi folder:

```text
/home/agaa3627/agape153_app/public/*
```

ke:

```text
/home/agaa3627/public_html/
```

Isi `public_html` minimal harus seperti ini:

```text
public_html/
├── index.php
├── .htaccess
├── build/
├── images/
├── videos/
├── favicon.ico
└── robots.txt
```

## 3. Edit `public_html/index.php`

Buka `public_html/index.php`, lalu sesuaikan path Laravel-nya menjadi:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../agape153_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../agape153_app/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../agape153_app/bootstrap/app.php';

$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
```

Jika nama folder app berbeda, misalnya `AGAPE153`, ubah `../agape153_app/` sesuai nama folder tersebut.

## 4. Pastikan `.htaccess` Ada Di `public_html`

Versi pakem ada di `docs/RUMAHWEB_HTACCESS_PAKEM.md` dan `public/.htaccess`.
Setelah `git pull`, salin ulang file ini ke public webroot:

```bash
cd ~/agape153.com
cp public/.htaccess ~/public_html/.htaccess
php artisan optimize:clear
```

File `public_html/.htaccess` harus berisi rewrite Laravel:

```apache
# AGAPE153 Laravel public entrypoint.
# Keep this file as the canonical cPanel public_html/.htaccess.
# Do not append cPanel EA-PHP handlers here, because ea-php84 on this hosting
# has previously booted without required extensions such as mbstring/pdo_mysql.
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

Jangan tambahkan block `# php -- BEGIN cPanel-generated handler` / `AddHandler application/x-httpd-ea-php84` ke file ini. Kalau block tersebut muncul lagi, hapus karena itu bisa memaksa web memakai runtime PHP tanpa extension Laravel yang dibutuhkan.

Kalau domain masih menampilkan `Index of /`, biasanya penyebabnya salah satu ini:

- `index.php` belum ada di `public_html`.
- `.htaccess` belum ada di `public_html`.
- `index.php` masih menunjuk path lama.
- Document root domain belum benar.

## 5. Set PHP Version

Di cPanel > MultiPHP Manager / Select PHP Version:

```text
PHP 8.3 atau PHP 8.4
```

Project ini memakai Laravel dengan requirement:

```text
php: ^8.3
```

Aktifkan extension umum berikut jika tersedia:

```text
bcmath
ctype
curl
dom
fileinfo
gd
intl
mbstring
openssl
pdo_mysql
tokenizer
xml
zip
```

## 6. Buat `.env` Production

File `.env` harus berada di:

```text
/home/agaa3627/agape153_app/.env
```

Contoh isi awal:

```env
APP_NAME=Agape153
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://agape153.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=agaa3627_agape153
DB_USERNAME=agaa3627_NAMAUSERDATABASE
DB_PASSWORD=PASSWORD_DATABASE

SESSION_DRIVER=database
SESSION_LIFETIME=60
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.agape153.com
ADMIN_SESSION_LIFETIME=30

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=info.agape153@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
MIDTRANS_IS_PRODUCTION=false

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

WHATSAPP_WEBHOOK_URL=
WHATSAPP_WEBHOOK_TOKEN=

VITE_APP_NAME="${APP_NAME}"
```

Penting: `DB_USERNAME` tidak selalu sama dengan `DB_DATABASE`. Cek di cPanel > MySQL Databases, lihat user database yang sudah diberi akses ke database `agaa3627_agape153`.

## 7. Jalankan Command Di Terminal cPanel

Masuk ke cPanel > Terminal, lalu:

```bash
cd /home/agaa3627/agape153_app
```

Install dependency jika diperlukan:

```bash
composer install --no-dev --optimize-autoloader
```

Generate app key:

```bash
php artisan key:generate --force
```

Jalankan migration:

```bash
php artisan migrate --force
```

Jika database masih kosong, jalankan seeder:

```bash
php artisan db:seed --class=DatabaseSeeder --force
```

Optimize production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 8. Setup Storage Upload

Karena `public_html` dipakai sebagai public path, buat symlink manual dari `public_html/storage` ke storage Laravel:

```bash
cd /home/agaa3627
rm -rf public_html/storage
ln -s ../agape153_app/storage/app/public public_html/storage
```

Jika `ln -s` tidak diizinkan hosting, buat folder manual:

```text
public_html/storage
```

lalu copy isi:

```text
agape153_app/storage/app/public/*
```

ke:

```text
public_html/storage/
```

Untuk fitur upload admin, symlink lebih disarankan.

## 9. Permission Folder

Set permission aman:

```bash
cd /home/agaa3627/agape153_app
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

Di File Manager, folder jangan dibiarkan `0777` untuk production kecuali terpaksa. Gunakan `0755` atau `0775`.

## 10. Setup Midtrans Production

Di dashboard Midtrans, set URL callback/notification:

```text
https://agape153.com/payments/midtrans/notification
```

Untuk sandbox:

```env
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_MERCHANT_ID=ISI_DARI_SANDBOX
MIDTRANS_CLIENT_KEY=SB-Mid-client-...
MIDTRANS_SERVER_KEY=SB-Mid-server-...
```

Untuk production:

```env
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_MERCHANT_ID=ISI_DARI_PRODUCTION
MIDTRANS_CLIENT_KEY=Mid-client-...
MIDTRANS_SERVER_KEY=Mid-server-...
```

Jangan campur key sandbox dan production.

## 11. Setup Google Login Production

Di Google Cloud Console OAuth Client, tambahkan Authorized redirect URI:

```text
https://agape153.com/auth/google/callback
```

Lalu isi `.env`:

```env
GOOGLE_CLIENT_ID=ISI_CLIENT_ID
GOOGLE_CLIENT_SECRET=ISI_CLIENT_SECRET
GOOGLE_REDIRECT_URI=https://agape153.com/auth/google/callback
```

Atau isi dari Admin Panel:

```text
Admin > Appearance > Integrations
```

## 12. Setup Email SMTP

Jika memakai email cPanel/Rumahweb, buat akun email dulu, contoh:

```text
info@agape153.com
```

Contoh `.env` SMTP TLS:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=mail.agape153.com
MAIL_PORT=587
MAIL_USERNAME=info@agape153.com
MAIL_PASSWORD=PASSWORD_EMAIL
MAIL_FROM_ADDRESS=info@agape153.com
MAIL_FROM_NAME="${APP_NAME}"
```

Jika memakai SSL:

```env
MAIL_SCHEME=ssl
MAIL_PORT=465
```

Setelah mengubah `.env`, jalankan:

```bash
php artisan optimize:clear
php artisan config:cache
```

## 13. Kalau Tidak Bisa Pakai Terminal

Minimal lakukan ini:

1. Pastikan `vendor/` sudah ikut terupload.
2. Pastikan `public_html/index.php` sudah diarahkan ke `agape153_app`.
3. Buat `.env` manual dan isi `APP_KEY`.
4. Untuk `APP_KEY`, generate dari lokal:

```bash
php artisan key:generate --show
```

Lalu paste hasilnya ke `.env` hosting:

```env
APP_KEY=base64:HASIL_KEY
```

5. Import database lokal lewat phpMyAdmin jika tidak bisa menjalankan migration.

## 14. Checklist Setelah Setup

Cek URL berikut:

```text
https://agape153.com
https://agape153.com/products
https://agape153.com/login
https://agape153.com/admin/login
https://agape153.com/up
```

Jika `/up` tampil OK, Laravel sudah booting.

## 15. Error Umum

### Masih `Index of /`

Artinya domain masih membaca folder biasa, bukan Laravel entrypoint.

Solusi:

- Pastikan `public_html/index.php` ada.
- Pastikan `public_html/.htaccess` ada.
- Pastikan isi `index.php` sudah menunjuk ke `../agape153_app/...`.

### 500 Server Error

Solusi cepat:

```bash
cd /home/agaa3627/agape153_app
php artisan optimize:clear
chmod -R 775 storage bootstrap/cache
```

Lalu cek:

```text
storage/logs/laravel.log
```

### Database Error

Cek `.env`:

```env
DB_DATABASE=agaa3627_agape153
DB_USERNAME=agaa3627_NAMAUSERDATABASE
DB_PASSWORD=PASSWORD_DATABASE
DB_HOST=localhost
```

Pastikan user database sudah diberi privilege ke database di cPanel > MySQL Databases.

### CSS/Gambar Tidak Muncul

Pastikan folder ini ada di `public_html`:

```text
build/
images/
videos/
storage/
```

Lalu clear cache browser atau buka incognito.

## 16. Catatan Keamanan

Untuk production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://agape153.com
```

Jangan taruh `.env` di `public_html`.
Jangan biarkan `app`, `vendor`, `database`, `storage`, dan `config` terbuka di `public_html`.
