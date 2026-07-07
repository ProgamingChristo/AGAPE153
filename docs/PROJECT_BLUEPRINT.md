# Agape153 Project Blueprint

## Business Requirement

Agape153.com dibangun sebagai landing page, company profile, katalog produk, dan e-commerce inquiry untuk rempah-rempah serta kopi Indonesia. Target utama adalah pembeli Indonesia, Asia, Timur Tengah, Eropa, dan Amerika, dengan struktur yang siap dikembangkan ke multilingual.

## System Requirement

- Laravel 13, Blade, Tailwind CSS, MySQL 8+.
- Authentication berbasis session Laravel.
- Checkout awal menggunakan WhatsApp agar proses sales cepat berjalan.
- Struktur database disiapkan untuk Midtrans, invoice, analytics, role-permission, audit log, product view, order tracking, dan pengembangan admin lanjutan.

## Database Summary

Entitas utama:
- `users`, `roles`, `permissions`, `role_user`, `permission_role`
- `categories`, `products`, `product_images`
- `orders`, `order_items`, `addresses`, `wishlists`
- `banners`, `galleries`, `news_posts`, `faqs`, `testimonials`, `website_settings`
- `product_views`, `login_histories`, `activity_logs`

Relasi utama:
- Category memiliki banyak Product.
- Product memiliki banyak ProductImage dan OrderItem.
- User memiliki banyak Order, Address, Wishlist.
- Order memiliki banyak OrderItem.

## UX Direction

Tampilan dibuat profesional, ringan, dan product-first: hero dengan visual produk, katalog yang bisa difilter, detail produk dengan CTA cart, checkout manual, member area, serta admin dashboard sederhana.

## Security Strategy

- CSRF aktif di semua form.
- Query menggunakan Eloquent dan validation request.
- Login dibatasi throttle `5,1`.
- Admin route diproteksi middleware `auth` dan `admin`.
- Login history dan activity log table sudah tersedia untuk audit lanjutan.

## Delivery Status

Implemented:
- Landing page lengkap dengan SEO meta, JSON-LD, `robots.txt`, dan `sitemap.xml`.
- Katalog produk, search, filter kategori, detail produk.
- Cart, checkout WhatsApp, order success, order tracking.
- Login, register, logout, profile, password update.
- Member dashboard dengan order history dan wishlist.
- Admin dashboard, CRUD product, CRUD category.
- Migration dan seeder awal untuk database `agape153`.

Next recommended sprint:
- CRUD admin untuk order, FAQ, gallery, testimonial, news, banner, settings.
- Midtrans integration.
- Invoice PDF, export sales report, dan image upload optimization.
- Email verification dan forgot/reset password UI penuh.
- Multilingual content model.
