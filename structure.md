vape-tube/
├── index.html              # صفحه اصلی
├── config.php              # تنظیمات پایگاه داده
├── database_schema.sql     # ساختار پایگاه داده
├── setup.php              # اسکریپت راه‌اندازی
├── api/
│   ├── api_add_product.php                     # API endpoints
│   ├── api_auth.php           # احراز هویت
│   ├── api_products.php       # مدیریت محصولات
│   └── api_cart.php           # سبد خرید
├── includes/              # کلاس‌های PHP
│   ├── Database.php       # اتصال پایگاه داده
│   ├── User.php           # مدیریت کاربران
│   ├── Product.php        # مدیریت محصولات
│   ├── Cart.php           # مدیریت سبد خرید
│   └── functions.php      # توابع کمکی
└── assets/                # فایل‌های استاتیک
|   ├── css/
|   │   ├── style.css          # استایل اصلی
|   │   ├── responsive.css     # استایل ریسپانسیو
|   │   ├── podsystem.css
|   │   ├── style_new.css
|   │   └── product.css
|   ├── js/
|   │   ├── app.js             # جاوااسکریپت اصلی
|   │   ├── additional.js
|   │   ├── podsystem.js
|   ├── images/                # تصاویر محصولات
└── pages/                 # فایل‌های استاتیک
|   ├── components/
|   │   ├── header.html        # استایل اصلی
|   │   └── footer.html        # استایل ریسپانسیو
|   ├── products/
|   │   └── app.js             # جاوااسکریپت اصلی
|   │   └── product_view.php   # نمایش محصولات یافت شده در فیلد جستجو
|   └── podsystem.php/         # نمایش محصولات پاد سیستم
