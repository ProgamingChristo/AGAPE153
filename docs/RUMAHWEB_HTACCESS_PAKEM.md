# Rumahweb cPanel .htaccess Pakem

Gunakan file ini sebagai acuan utama untuk `/home/agaa3627/public_html/.htaccess`.
Masalah 500 yang berulang terjadi ketika cPanel menambahkan PHP handler EA seperti
`AddHandler application/x-httpd-ea-php84`. Pada hosting ini, handler tersebut pernah
menjalankan PHP tanpa extension wajib Laravel seperti `mbstring`, `iconv`, `pdo_mysql`,
`curl`, `fileinfo`, `zip`, dan `intl`.

## File Final `public_html/.htaccess`

Isi `public_html/.htaccess` harus seperti ini saja:

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

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

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

Jangan ada block ini di bawahnya:

```apache
# php -- BEGIN cPanel-generated handler, do not edit
<IfModule mime_module>
    AddHandler application/x-httpd-ea-php84 .php .php8 .phtml
</IfModule>
# php -- END cPanel-generated handler, do not edit
```

## Restore Cepat Kalau Website 500 Lagi

Jalankan dari Terminal cPanel:

```bash
cd ~/agape153.com
cp public/.htaccess ~/public_html/.htaccess
php artisan optimize:clear
```

Kalau cPanel menambahkan block `php -- BEGIN cPanel-generated handler`, hapus otomatis:

```bash
sed -i '/# php -- BEGIN cPanel-generated handler/,/# php -- END cPanel-generated handler/d' ~/public_html/.htaccess
php artisan optimize:clear
```

## Cek PHP Web Yang Dipakai

Buat file sementara `~/public_html/phpcheck.php`:

```php
<?php
$exts = ['iconv','mbstring','pdo_mysql','mysqli','mysqlnd','openssl','curl','fileinfo','zip','intl','dom','xml','tokenizer','ctype','session'];
echo 'PHP_VERSION='.PHP_VERSION.PHP_EOL;
foreach ($exts as $ext) {
    echo $ext.': '.(extension_loaded($ext) ? 'yes' : 'no').PHP_EOL;
}
echo 'iconv_function: '.(function_exists('iconv') ? 'yes' : 'no').PHP_EOL;
```

Buka:

```text
https://agape153.com/phpcheck.php
```

Laravel aman kalau minimal ini `yes`:

```text
iconv
mbstring
pdo_mysql
mysqli atau mysqlnd
openssl
curl
fileinfo
zip
intl
dom
xml
tokenizer
ctype
session
```

Setelah selesai cek, hapus file:

```bash
rm -f ~/public_html/phpcheck.php
```

## Aturan Agar Tidak Down Lagi

- Jangan klik `Apply` di MultiPHP Manager kalau hasilnya menambahkan `ea-php84` ke `.htaccess`.
- Atur extension dari cPanel `Select PHP Version`, bukan dengan menulis block EA-PHP manual.
- Setelah `git pull`, copy `.htaccess` pakem dari repo ke `public_html`:

```bash
cd ~/agape153.com
git pull origin main
cp public/.htaccess ~/public_html/.htaccess
php artisan optimize:clear
php artisan migrate --force
php artisan optimize:clear
```

- Jangan copy seluruh folder Laravel ke `public_html`; `public_html` hanya untuk isi folder `public`.
- Jangan taruh `.env`, `app`, `bootstrap`, `config`, `database`, `resources`, `routes`, `storage`, dan `vendor` di `public_html`.
