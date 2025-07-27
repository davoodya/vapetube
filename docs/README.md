# Vape Tube - فروشگاه ویپ و سیگار الکترونیکی

## نمای کلی پروژه

Vape Tube یک فروشگاه آنلاین کامل برای محصولات ویپ و سیگار الکترونیکی است که با استفاده از تکنولوژی‌های مدرن وب طراحی و توسعه یافته است.

## ویژگی‌های اصلی

### 🔐 سیستم احراز هویت
- ثبت نام و ورود کاربران
- مدیریت جلسات امن
- سطوح دسترسی مختلف (کاربر عادی، ادمین)

### 🛒 سیستم سبد خرید
- افزودن محصولات به سبد خرید
- مدیریت تعداد محصولات
- محاسبه خودکار قیمت کل
- ذخیره سبد خرید برای کاربران مهمان

### 📱 طراحی ریسپانسیو
- سازگار با موبایل، تبلت و دسکتاپ
- رابط کاربری مدرن و جذاب
- استفاده از رنگ‌های مناسب برای تشویق خرید

### 🔍 سیستم جستجو پیشرفته
- جستجو در نام محصولات
- فیلتر بر اساس دسته‌بندی
- مرتب‌سازی بر اساس قیمت و تاریخ

### 📦 مدیریت محصولات
- 8 دسته‌بندی اصلی با زیرمجموعه‌ها
- نمایش تصاویر محصولات
- قیمت‌گذاری با تخفیف
- مدیریت موجودی

## ساختار پروژه

```
vape-tube/
├── index.html              # صفحه اصلی
├── config.php              # تنظیمات پایگاه داده
├── database_schema.sql     # ساختار پایگاه داده
├── setup.php              # اسکریپت راه‌اندازی
├── api/                   # API endpoints
│   ├── auth.php           # احراز هویت
│   ├── products.php       # مدیریت محصولات
│   └── cart.php           # سبد خرید
├── includes/              # کلاس‌های PHP
│   ├── Database.php       # اتصال پایگاه داده
│   ├── User.php           # مدیریت کاربران
│   ├── Product.php        # مدیریت محصولات
│   ├── Cart.php           # مدیریت سبد خرید
│   └── functions.php      # توابع کمکی
└── assets/                # فایل‌های استاتیک
    ├── css/
    │   ├── style.css      # استایل اصلی
    │   └── responsive.css # استایل ریسپانسیو
    ├── js/
    │   └── app.js         # جاوااسکریپت اصلی
    └── images/            # تصاویر محصولات
```

## دسته‌بندی محصولات

1. **مود ویپ (Vape Mod)**
   - آیتم 1، آیتم 2، آیتم 3

2. **سیستم‌های پاد (Pod Systems)**
   - آیتم 1، آیتم 2، آیتم 3

3. **نیکوتین نمک (Salt Nicotine)**
   - آیتم 1، آیتم 2، آیتم 3

4. **مایع ویپ (E-Juice)**
   - آیتم 1، آیتم 2، آیتم 3

5. **یکبار مصرف (Disposable)**
   - آیتم 1، آیتم 2، آیتم 3

6. **کویل و کارتریج (Coil & Cartridge)**
   - آیتم 1، آیتم 2، آیتم 3

7. **لوازم جانبی (Accessories)**
   - آیتم 1، آیتم 2، آیتم 3

8. **سیگار الکترونیکی (E-Cigarettes)**
   - آیتم 1، آیتم 2، آیتم 3

## نصب و راه‌اندازی

### پیش‌نیازها
- PHP 8.1 یا بالاتر
- MySQL 8.0 یا بالاتر
- وب سرور (Apache/Nginx) یا PHP Development Server

### مراحل نصب

1. **آپلود فایل‌ها**
   ```bash
   # آپلود تمام فایل‌ها به روت وب سرور
   ```

2. **تنظیم پایگاه داده**
   ```bash
   # ایجاد پایگاه داده
   mysql -u root -p
   CREATE DATABASE vape_tube CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'vape_user'@'localhost' IDENTIFIED BY 'vape_pass';
   GRANT ALL PRIVILEGES ON vape_tube.* TO 'vape_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **تنظیم config.php**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'vape_tube');
   define('DB_USER', 'vape_user');
   define('DB_PASS', 'vape_pass');
   ```

4. **اجرای اسکریپت راه‌اندازی**
   ```bash
   php setup.php
   ```

5. **تست وب‌سایت**
   ```bash
   # برای تست محلی
   php -S localhost:8000
   ```

## ویژگی‌های فنی

### Frontend
- **HTML5** - ساختار معنایی
- **CSS3** - استایل‌دهی مدرن با Flexbox و Grid
- **JavaScript ES6+** - تعاملات کاربری
- **Responsive Design** - سازگار با تمام دستگاه‌ها
- **RTL Support** - پشتیبانی کامل از زبان فارسی

### Backend
- **PHP 8.1** - منطق سرور
- **MySQL** - پایگاه داده
- **RESTful API** - ارتباط Frontend-Backend
- **Session Management** - مدیریت جلسات
- **Security Features** - امنیت و اعتبارسنجی

### امنیت
- **SQL Injection Prevention** - استفاده از Prepared Statements
- **XSS Protection** - فیلتر کردن ورودی‌ها
- **CSRF Protection** - محافظت در برابر حملات CSRF
- **Password Hashing** - رمزگذاری امن رمزهای عبور
- **Rate Limiting** - محدودیت تعداد درخواست‌ها

## API Documentation

### Authentication Endpoints

#### POST /api/auth.php?action=login
ورود کاربر
```json
{
  "username": "admin",
  "password": "admin123"
}
```

#### POST /api/auth.php?action=register
ثبت نام کاربر جدید
```json
{
  "name": "نام کاربر",
  "email": "user@example.com",
  "password": "password123"
}
```

#### GET /api/auth.php?action=check
بررسی وضعیت ورود

#### POST /api/auth.php?action=logout
خروج کاربر

### Products Endpoints

#### GET /api/products.php?action=categories
دریافت لیست دسته‌بندی‌ها

#### GET /api/products.php?action=featured
دریافت محصولات ویژه

#### GET /api/products.php?action=all
دریافت تمام محصولات با پشتیبانی از:
- `page`: شماره صفحه
- `category_id`: فیلتر دسته‌بندی
- `search`: جستجو
- `sort`: مرتب‌سازی

### Cart Endpoints

#### GET /api/cart.php?action=get
دریافت محتویات سبد خرید

#### POST /api/cart.php?action=add
افزودن محصول به سبد خرید
```json
{
  "product_id": 1,
  "quantity": 2
}
```

#### POST /api/cart.php?action=update
به‌روزرسانی تعداد محصول
```json
{
  "product_id": 1,
  "quantity": 3
}
```

#### POST /api/cart.php?action=remove
حذف محصول از سبد خرید
```json
{
  "product_id": 1
}
```

## محصولات نمونه

وب‌سایت شامل 8 محصول نمونه است:

1. **اسموک نورد ۴** - پاد سیستم قدرتمند
2. **ووپو درگ ایکس** - مود ویپ حرفه‌ای
3. **نستی جوس - انبه** - مایع ویپ طبیعی
4. **جول پاد - نعنا** - پاد نیکوتین نمک
5. **پاف بار یکبار مصرف** - ویپ یکبار مصرف
6. **کویل مش ۰.۴ اهم** - کویل با کیفیت
7. **کیف حمل ویپ** - لوازم جانبی
8. **آیکوس ایلوما** - سیگار الکترونیکی

## تست‌های انجام شده

✅ **عملکرد سبد خرید** - افزودن، حذف، به‌روزرسانی محصولات
✅ **سیستم احراز هویت** - ورود، ثبت نام، خروج
✅ **جستجو و فیلتر** - جستجو در محصولات
✅ **طراحی ریسپانسیو** - تست در اندازه‌های مختلف صفحه
✅ **API Endpoints** - تست تمام نقاط پایانی
✅ **امنیت** - اعتبارسنجی ورودی‌ها و محافظت در برابر حملات

## پشتیبانی و نگهداری

### بک‌آپ پایگاه داده
```bash
mysqldump -u vape_user -p vape_tube > backup.sql
```

### بازیابی پایگاه داده
```bash
mysql -u vape_user -p vape_tube < backup.sql
```

### مانیتورینگ
- بررسی لاگ‌های وب سرور
- مانیتورینگ عملکرد پایگاه داده
- بررسی امنیت و به‌روزرسانی‌ها

## مجوز

این پروژه تحت مجوز MIT منتشر شده است.

## تماس

برای پشتیبانی و سوالات:
- ایمیل: info@vapetube.com
- تلفن: ۰۲۱-۱۲۳۴۵۶۷۸

---

**نکته مهم**: این وب‌سایت برای اهداف آموزشی و نمایشی طراحی شده است. برای استفاده تجاری، لطفاً تنظیمات امنیتی و عملکردی اضافی را اعمال کنید.

