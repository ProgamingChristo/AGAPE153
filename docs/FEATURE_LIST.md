# Feature List Agape153

Dokumen ini merangkum fitur yang sudah tersedia dan fitur yang masih bisa dikembangkan berikutnya.

## Fitur yang Sudah Ada

- Homepage responsive dengan hero, kategori, katalog unggulan, gallery, testimonial, FAQ, dan contact section.
- Logo Agape153 resmi dipakai di header, footer, login admin, dan PDF invoice.
- Contact form publik yang menyimpan inquiry ke admin menu `Messages`.
- Admin dapat membaca, mengubah status, dan menghapus contact messages.
- About page berbahasa Inggris berdasarkan company profile Agape153.
- Katalog produk publik dengan kategori, pencarian/filter, detail produk, wishlist, dan cart.
- Add to cart dan checkout wajib login agar order/invoice selalu terhubung ke akun member.
- Checkout manual berbasis WhatsApp dengan order number dan tracking code.
- Checkout memiliki dua tombol pembayaran langsung: online Midtrans dan konfirmasi WhatsApp.
- Pembayaran Midtrans membuka Snap popup, mencatat client success, dan menampilkan halaman payment success dengan invoice paid.
- Member dashboard dengan purchase history, purchase detail, gambar produk/fallback, dan download PDF invoice.
- Admin login khusus di `/admin/login` dengan session token dan auto logout idle.
- Admin dashboard untuk ringkasan produk, order, member, message, dan product views.
- Admin CRUD category dengan upload image file.
- Admin CRUD product dengan upload image file.
- Admin dapat melihat stock produk, mengubah stock langsung dari list, dan mengaktifkan/menonaktifkan produk.
- Admin dapat melihat order, ACC order pending menjadi confirmed, update status order, payment status, tracking code, shipping cost, dan notes.
- Sales/finance report admin dengan metrik revenue, paid/unpaid amount, average order, grafik revenue bulanan, top products, dan download PDF report.
- Sales/finance report dapat diexport ke CSV.
- Admin appearance settings untuk warna utama, warna aksen, soft background, layout homepage, hero copy, hero image URL, dan toggle gallery/testimonial.
- Upload hero image langsung dari admin Appearance.
- Midtrans Snap sandbox integration dengan Snap token, payment page, dan webhook callback signature verification.
- Email notification dan WhatsApp notification log untuk inquiry, order, approval, payment update, dan low stock. WhatsApp dapat dikirim otomatis jika `WHATSAPP_WEBHOOK_URL` dikonfigurasi.
- Stock movement history untuk adjustment dan order acceptance.
- Staff, role, dan permission UI granular.
- CMS admin untuk homepage sections, FAQ, gallery, testimonial, news, footer, dan threshold order besar.
- Language switcher ID/EN dan translation manager per key/page.
- Analytics dashboard untuk conversion funnel, abandoned cart estimate, product demand, device traffic, dan cart source.
- Shipping/logistic tracking fields dengan auto tracking URL untuk provider umum dan override manual.
- Inventory low stock alert via notification log/email/WhatsApp provider.
- Bulk import/export product via CSV spreadsheet.
- Soft delete restore UI untuk produk dan kategori.
- Customer admin module untuk buyer profile, repeat purchase, order lifecycle, dan total spend.
- Approval workflow untuk high-value order, discount, quotation status, dan quotation notes.
- SEO basic: meta description, canonical URL, sitemap XML, robots.txt, dan JSON-LD organization.
- Tutorial running project tersedia di `TUTORIAL_RUNNING_PROJECT.md`.
- Automated feature tests untuk homepage, catalog, cart, admin auth, purchase history, contact message, invoice PDF, image upload, order approval, stock update, report PDF, dan appearance settings.

## Fitur yang Belum Ada / Rekomendasi Berikutnya

- Payment gateway production switch perlu aktivasi akun Midtrans production dan mengganti `MIDTRANS_IS_PRODUCTION=true`.
- WhatsApp sending real memerlukan provider resmi/API gateway dan mengisi `WHATSAPP_WEBHOOK_URL`.
- Shipping tracking real-time API per kurir masih perlu credential/API masing-masing kurir; saat ini sistem membuat tracking URL dan menyimpan status manual.
- Export Excel `.xlsx` native belum ada; CSV sudah tersedia dan bisa dibuka di Excel/Google Sheets.
- Translation coverage semua teks lama masih bertahap; fondasi language switcher dan translation manager sudah tersedia.
