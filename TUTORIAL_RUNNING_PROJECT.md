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

SESSION_LIFETIME=60
ADMIN_SESSION_LIFETIME=30

MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
MIDTRANS_IS_PRODUCTION=false

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

WHATSAPP_WEBHOOK_URL=
WHATSAPP_WEBHOOK_TOKEN=
```

Jika password MySQL lokal berbeda, isi bagian `DB_PASSWORD`.

Untuk reset password via email, pastikan konfigurasi `MAIL_*` sudah mengarah ke SMTP aktif.
Untuk login/register Google, buat OAuth Client di Google Cloud Console dan isi `GOOGLE_CLIENT_ID`,
`GOOGLE_CLIENT_SECRET`, serta redirect URI `http://127.0.0.1:8000/auth/google/callback`.
Google OAuth juga bisa diatur dari admin panel melalui `Appearance > Integrations`.
Untuk register WhatsApp dengan OTP sungguhan, isi `WHATSAPP_WEBHOOK_URL` dan `WHATSAPP_WEBHOOK_TOKEN`
dari provider WhatsApp gateway. Endpoint akan menerima payload `to`, `message`, `event`, dan `payload`.

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

Aktifkan juga public storage supaya gambar hasil upload admin bisa tampil:

```powershell
php artisan storage:link
```

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
Admin Login: http://127.0.0.1:8000/admin/login
Admin:    http://127.0.0.1:8000/admin
About:    http://127.0.0.1:8000/about
Tracking: http://127.0.0.1:8000/order-tracking
Sitemap:  http://127.0.0.1:8000/sitemap.xml
```

Menu admin penting:

```text
Orders:     http://127.0.0.1:8000/admin/orders
Reports:    http://127.0.0.1:8000/admin/reports
Analytics:  http://127.0.0.1:8000/admin/analytics
Products:   http://127.0.0.1:8000/admin/products
Customers:  http://127.0.0.1:8000/admin/customers
Messages:   http://127.0.0.1:8000/admin/contact-messages
CMS:        http://127.0.0.1:8000/admin/cms
Appearance: http://127.0.0.1:8000/admin/appearance
Staff:      http://127.0.0.1:8000/admin/staff
```

Kontak resmi Agape153:

```text
Phone / WhatsApp: +62816795153
Email: info.agape153@gmail.com
YouTube: https://www.youtube.com/@AGAPE153CHANNEL
Instagram: @agape153.official
Facebook: https://www.facebook.com/profile.php?id=61590494259264
LinkedIn: https://www.linkedin.com/in/agape153
TikTok: https://www.tiktok.com/@agape153.official
Threads: @agape153.official
```

Catatan admin:

- Admin memakai halaman login khusus di `/admin/login`.
- Session admin otomatis logout setelah idle sesuai `ADMIN_SESSION_LIFETIME`.
- Default timeout admin adalah 30 menit.
- Contact form di homepage masuk ke menu admin `Messages`.
- Product dan category image diupload dari file melalui halaman CRUD admin.
- Product video dapat diisi lewat URL atau upload file MP4/WebM/Ogg dari halaman CRUD admin `Products`.
- Member harus login sebelum menambahkan produk ke cart atau checkout.
- Member dapat register/login dengan email, Google OAuth, atau nomor WhatsApp yang diverifikasi OTP.
- Forgot password mengirim link reset ke email member memakai konfigurasi `MAIL_*`.
- Checkout menyediakan dua tombol payment: `Bayar Online Midtrans` untuk membuka Snap popup dan `Konfirmasi via WhatsApp` untuk redirect ke WhatsApp.
- Setelah Midtrans berhasil, user diarahkan ke halaman payment success dengan invoice paid dan tombol download PDF.
- Stock produk bisa dilihat dan diubah cepat dari menu `Products`.
- Produk dapat diaktifkan/dinonaktifkan dari menu `Products`.
- Order pending bisa di-ACC dari menu `Orders`.
- Laporan penjualan/keuangan dapat dilihat dan diunduh PDF dari menu `Reports`.
- Laporan juga dapat diunduh CSV dari menu `Reports`.
- Dashboard analytics tersedia di menu `Analytics`.
- Tampilan homepage dapat dicustom dari menu `Appearance`.
- Konten homepage, FAQ, gallery, testimonial, news, footer, dan threshold order besar dapat diedit dari menu `CMS`.
- Staff dan permission dapat diatur dari menu `Staff`.
- Translation ID/EN dapat diatur dari menu `Translations`.
- Product CSV import/export tersedia dari menu `Products`.
- Produk dan kategori yang terhapus dapat direstore dari menu `Trash`.
- Payment Midtrans memakai variabel `MIDTRANS_*` di `.env`; callback webhook diarahkan ke `/payments/midtrans/notification`.
- WhatsApp OTP/notifikasi otomatis membutuhkan provider webhook; jika belum diisi sistem tetap membuat notification log dan link WhatsApp untuk development.
- Member dapat download PDF invoice dari halaman purchase detail.
- List fitur tersedia di `docs/FEATURE_LIST.md`.

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
