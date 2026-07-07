# Tutorial Running Project Agape153

Panduan ini untuk menjalankan project Laravel Agape153 di lokal.

## 1. Prasyarat

Pastikan sudah tersedia:

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- MySQL 8+ atau MySQL dari XAMPP
- Database dengan nama `agape153`

## 2. Masuk ke Folder Project

Buka terminal PowerShell, lalu jalankan:

```powershell
cd D:\AGAPE153
```

## 3. Buat Database

Jika memakai phpMyAdmin/XAMPP:

1. Jalankan Apache dan MySQL dari XAMPP Control Panel.
2. Buka `http://localhost/phpmyadmin`.
3. Buat database baru dengan nama:

```text
agape153
```

## 4. Cek Konfigurasi `.env`

Pastikan file `.env` memiliki konfigurasi database berikut:

```env
APP_NAME=Agape153
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agape153
DB_USERNAME=root
DB_PASSWORD=
```

Jika password MySQL lokal berbeda, isi bagian `DB_PASSWORD`.

## 5. Install Dependency Backend

```powershell
composer install
```

## 6. Install Dependency Frontend

```powershell
npm install
```

## 7. Generate Application Key

```powershell
php artisan key:generate
```

## 8. Jalankan Migration dan Seeder

Perintah ini akan membuat ulang tabel dan mengisi data awal:

```powershell
php artisan migrate:fresh --seed
```

Data awal yang dibuat mencakup kategori, produk, FAQ, testimonial, gallery, user admin, dan user member.

## 9. Build Asset Frontend

Untuk mode production/local static asset:

```powershell
npm run build
```

Untuk mode development dengan hot reload:

```powershell
npm run dev
```

Jika menjalankan `npm run dev`, biarkan terminal tersebut tetap terbuka.

## 10. Jalankan Server Laravel

Buka terminal PowerShell baru, lalu jalankan:

```powershell
cd D:\AGAPE153
php artisan serve --host=127.0.0.1 --port=8000
```

Buka website di browser:

```text
http://127.0.0.1:8000
```

## 11. Akun Demo

Admin:

```text
Email: admin@agape153.com
Password: password
```

Member:

```text
Email: member@agape153.com
Password: password
```

URL penting:

```text
Homepage: http://127.0.0.1:8000
Katalog:  http://127.0.0.1:8000/products
Login:    http://127.0.0.1:8000/login
Admin:    http://127.0.0.1:8000/admin
Tracking: http://127.0.0.1:8000/order-tracking
Sitemap:  http://127.0.0.1:8000/sitemap.xml
```

## 12. Menjalankan Test

```powershell
php artisan test
```

Jika berhasil, hasilnya akan menampilkan test passed.

## 13. Troubleshooting

Jika muncul error koneksi database:

- Pastikan MySQL sudah berjalan.
- Pastikan database `agape153` sudah dibuat.
- Pastikan `DB_USERNAME` dan `DB_PASSWORD` di `.env` sesuai dengan MySQL lokal.
- Setelah mengubah `.env`, jalankan:

```powershell
php artisan config:clear
```

Jika port `8000` sudah dipakai:

```powershell
php artisan serve --host=127.0.0.1 --port=8001
```

Lalu buka:

```text
http://127.0.0.1:8001
```

Jika tampilan CSS belum muncul:

```powershell
npm run build
php artisan view:clear
```

Jika ingin reset database ke data awal:

```powershell
php artisan migrate:fresh --seed
```
