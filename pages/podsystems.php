<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <base href="/vape-tube/">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>پاد سیستم‌ها | Vape Tube</title>
  <meta name="description" content="نمایش انواع پاد سیستم‌ها با قابلیت فیلترینگ پیشرفته در ویپ تیوب">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/podsystems.css">


</head>
<body>
<!-- Header -->
<div id="header-container"></div>

<!-- Main Content -->
<main class="main-content">
    <div class="main-container">
        <!-- Sidebar for Filters (Right Column) -->
        <aside class="sidebar" style="flex: 0 0 20%; padding: 20px; border-left: 1px solid #ddd; display: flex; flex-direction: column; gap: 20px;">
            <!-- First Row: Search Field -->
            <section class="filter-search">
                <div class="container">
                    <div class="section-header">
                        <h2>جستجو</h2>
                        <div class="search-container">
                            <input type="text" id="searchField" class="filter-input" placeholder="جستجو کنید..." autocomplete="off">
                            <div id="searchResults" class="search-results-container"></div>
                        </div>
                    </div>
                </div>
            </section>

<!-- Second Row: Price Filter -->
<section class="filter-price">
    <div class="container">
        <h3>فیلتر بر اساس قیمت</h3>

        <!-- نوار لغزنده دو طرفه -->
        <div class="filter-group">
            <label for="priceRange">محدوده قیمت</label>
            <input type="range" id="priceMin" min="0" max="50000000" step="1000" value="10" />
            <input type="range" id="priceMax" min="0" max="50000000" step="1000" value="50000000" />
            <div id="priceValues">
                <span id="priceMinValue">1,000,000 تومان</span> -
                <span id="priceMaxValue">10,000,000 تومان</span>
            </div>
        </div>

        <!-- دکمه ثبت فیلتر -->
        <button id="applyFilter" class="btn btn-primary">ثبت فیلتر</button>
    </div>
</section>

<!-- بخش مرتب سازی -->
<section class="sort-options">
    <div class="container">
        <h3>مرتب سازی بر اساس</h3>
        <div class="filter-group">
            <label for="sortBy">گزینه مرتب‌سازی:</label>
            <select id="sortBy" class="filter-select">
                <option value="cheapest">ارزان‌ترین</option>
                <option value="expensive">گران‌ترین</option>
                <option value="default">ترتیب عادی</option> <!-- گزینه جدید برای ترتیب عادی -->
            </select>
        </div>
    </div>
</section>

<!-- Third Row: Brand, Availability, Rating Filters -->
<section class="filter-options">
    <div class="container">
        <h3>فیلتر پیشرفته</h3>
        <form id="filterForm">
            <!-- Brand Filter -->
            <div class="filter-group">
                <label for="brandFilter">برندهای پاد سیستم</label>
                <select id="brandFilter" class="filter-select">
                    <option value="">همه برندها</option>
                    <option value="uwell">uwell</option>
                    <option value="smoke">smoke</option>
                    <option value="voopoo">voopoo</option>
                    <!-- Add more brands here -->
                </select>
            </div>

            <div class="filter-group">
                <label for="availabilityFilter">وضعیت موجودی</label>
                <select id="availabilityFilter">
                    <option value="">همه</option>
                    <option value="inStock">موجود</option>
                    <option value="outOfStock">ناموجود</option>
                </select>
            </div>

            <!-- Rating Filter -->
            <div class="filter-group">
                <label for="ratingFilter">رتبه‌بندی</label>
                <select id="ratingFilter">
                    <option value="">همه</option>
                    <option value="5">۵ ستاره</option>
                    <option value="4">۴ ستاره</option>
                    <option value="3">۳ ستاره</option>
                    <option value="2">۲ ستاره</option>
                    <option value="1">۱ ستاره</option>
                </select>
            </div>
        </form>
    </div>
</section>

<!-- فیلتر بر اساس قدرت وات -->
<section class="filter-power">
    <div class="container">
        <h3>فیلتر بر اساس قدرت وات</h3>
        <div class="filter-group">
            <label><input type="checkbox" id="powerFilter50" class="filter-checkbox" value="50W"> 50 وات</label>
        </div>
        <div class="filter-group">
            <label><input type="checkbox" id="powerFilter100" class="filter-checkbox" value="100W"> 100 وات</label>
        </div>
        <div class="filter-group">
            <label><input type="checkbox" id="powerFilter150" class="filter-checkbox" value="150W"> 150 وات</label>
        </div>
        <div class="filter-group">
            <label><input type="checkbox" id="powerFilter200" class="filter-checkbox" value="200W"> 200 وات</label>
        </div>
    </div>
</section>

</aside>

<!-- Products Section (Left Column) -->
<section class="featured-products" style="flex: 0 0 80%; padding: 20px;">
    <div class="container">
        <div class="section-header">
            <h2 style="display: flex; justify-content: center; align-items: center; text-align: center; color: orange; margin-top: 25px;">انواع پاد سیستم ها</h2>
            <p style="display: flex; justify-content: center; align-items: center; text-align: center; font-size: 1.2rem">پاد سیستم های موجود در وبسایت ما</p>
        </div>

        <!-- Show all Products in Pod System Category -->
        <div class="products-grid" id="featuredProducts">
            <?php
            // اتصال به دیتابیس
            $conn = new mysqli('localhost', 'root', '12945', 'vape_tube');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // دریافت فیلتر قیمت از GET
            $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
            $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 50000000;  // مقدار پیش‌فرض بسیار بالا
            $sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'default';  // مقدار پیش‌فرض مرتب‌سازی بر اساس ترتیب عادی
            $brand = isset($_GET['brand']) ? $_GET['brand'] : '';  // دریافت برند از GET (اگر انتخاب شده باشد)
            $availability = isset($_GET['availability']) ? $_GET['availability'] : '';  // دریافت وضعیت موجودی از GET
            $rating = isset($_GET['rating']) ? $_GET['rating'] : '';  // دریافت رتبه‌بندی از GET

            // ساختن بخش WHERE برای فیلتر قیمت
            $conditions = [];
            $params = []; // این متغیر برای نگه داشتن پارامترهای بایند شده است
            $paramTypes = ''; // برای مشخص کردن نوع داده‌ها در bind_param

            // اضافه کردن فیلتر قیمت
            $conditions[] = "price >= ? AND price <= ?";
            $params[] = $minPrice;
            $params[] = $maxPrice;
            $paramTypes .= 'ii'; // برای minPrice و maxPrice از نوع integer استفاده می‌کنیم

            // اضافه کردن فیلتر برند
            if ($brand) {
                $conditions[] = "brand = ?";
                $params[] = $brand;
                $paramTypes .= 's'; // برای برند از نوع string استفاده می‌کنیم
            }

            // اضافه کردن فیلتر موجودی
            if ($availability === 'inStock') {
                $conditions[] = "is_active = 1";  // موجود
            } elseif ($availability === 'outOfStock') {
                $conditions[] = "is_active = 0";  // ناموجود
            }

            // اضافه کردن فیلتر رتبه‌بندی
            if ($rating) {
                $conditions[] = "rating = ?";
                $params[] = $rating;
                $paramTypes .= 'i'; // برای رتبه از نوع integer استفاده می‌کنیم
            }

            // ساختن کوئری بر اساس شرایط
            $sql = "SELECT * FROM products WHERE category_id LIKE '%2%' AND " . implode(" AND ", $conditions);

            // اضافه کردن بخش مرتب‌سازی به کوئری در صورتی که مرتب‌سازی فعال باشد
            if ($sortBy == 'cheapest') {
                $sql .= " ORDER BY price ASC";
            } elseif ($sortBy == 'expensive') {
                $sql .= " ORDER BY price DESC";
            } elseif ($sortBy == 'sales') {
                $sql .= " ORDER BY sales DESC";
            }

            // آماده‌سازی کوئری
            $stmt = $conn->prepare($sql);

            // بایند کردن پارامترها به کوئری
            $stmt->bind_param($paramTypes, ...$params);

            // اجرای کوئری
            $stmt->execute();
            $result = $stmt->get_result();

            // بررسی اینکه آیا نتیجه‌ای برگشت داده شد یا نه
            if ($result->num_rows > 0) {
                // نمایش محصولات
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img src="' . $row["image_url"] . '" alt="' . $row["name"] . '" style="width: 300px; height: 300px; object-fit: scale-down; object-position: center; display: block; margin: 0 auto; padding: 3px 3px 3px 3px;" >';
                    echo '<h3 style="text-align: center; margin: 0 auto; color: coral;">' . $row["name"] . '</h3>';
                    echo '<br>';
                    echo '<p>' . $row["description_fa"] . '</p>';
                    echo '<p style="display: block; text-align: center; margin: 0 auto; color: green;" class="product-price">' . number_format($row["price"]) . ' تومان</p>';
                    echo '<br>';
                    echo '<button style="display: block; margin: 0 auto;" class="btn btn-primary">افزودن به سبد خرید</button>';
                    echo '</div>';
                }
            } else {
                echo "محصولی یافت نشد.";
            }

            // بستن اتصال به دیتابیس
            $conn->close();
            ?>
        </div>



        <!-- Pod System Categories -->
        <section class="categories">
            <div class="section-header">
                <h2>برندهای پاد سیستم</h2>
                <p>تمام برندهای پاد سیستم در ویپ کلاب</p>
            </div>
            <div class="category_container">

                <!-- Pod Systems Category -->
                <a href="pages/podsystems.php">
                    <div class="category-card">
                        <img src="assets/images/categories/podsystem.jpeg" alt="پاد سیستم" />
                        <h3>پاد سیستم</h3>
                        <p>محصولاتی برای ترک سیگار</p>
                    </div>
                </a>

                <!-- Pod Mod Systems Category -->
                <div class="category-card">
                    <img src="assets/images/categories/podmod.jpeg" alt="پاد ماد" />
                    <h3>پاد ماد</h3>
                    <p>دستگاه هایی دو کاره بین پاد و ماد</p>
                </div>

                <!-- Vape Mod Category -->
                <div class="category-card">
                    <img src="assets/images/categories/vape.jpeg" alt="ویپ" />
                    <h3>ویپ | ماد</h3>
                    <p>دستگاه هایی برای ترک قلیان با وات بالا</p>
                </div>

                <!-- Disposable Category -->
                <div class="category-card">
                    <img src="assets/images/categories/dispossable.jpeg" alt="پاد های یکبار مصرف" />
                    <h3>یکبار مصرف ها</h3>
                    <p>انواع پاد و ماد یکبار مصرف</p>
                </div>

                <!-- E-Cigar Category -->
                <div class="category-card">
                    <img src="assets/images/categories/eciggaret.jpeg" alt="سیگارهای الکترونیکی" />
                    <h3>سیگار های الکترونیکی</h3>
                    <p>انواع سیگار های الکترونیکی آیکاس، جوی و ...</p>
                </div>

                <!-- Nicotine Salt Category -->
                <div class="category-card">
                    <img src="assets/images/categories/salt.jpeg" alt="سالت نیکوتین" />
                    <h3>سالت نیکوتین</h3>
                    <p>مایع مصرفی برای پاد سیستم ها</p>
                </div>

                <!-- E-Juice Category -->
                <div class="category-card">
                    <img src="assets/images/categories/juice.jpeg" alt="جویس ویپ" />
                    <h3>جویس</h3>
                    <p>مایع مصرفی ویپ | ماد</p>
                </div>

                <!-- Coil Cartridge Category -->
                <div class="category-card">
                    <img src="assets/images/categories/coil.jpeg" alt="کویل و کارتریج" />
                    <h3>کویل و کارتریج</h3>
                    <p>انواع کویل و کارتریج دستگاه ها</p>
                </div>

                <!-- Accessory Category -->
                <div class="category-card">
                    <img src="assets/images/categories/accessory.jpeg" alt="اکسسوری" />
                    <h3>لوازم جانبی</h3>
                    <p>لوازم جانبی پاد، پادماد و ماد</p>
                </div>

            </div>
        </section>

    </div>
</section>

</div>
</main>

<!-- Add this CSS for the two-column layout -->
<style>
    /* اطمینان از اینکه فونت Vazir بارگذاری شده باشد */
    @import url('https://fonts.googleapis.com/css2?family=Vazir&display=swap');

    /* استایل برای تقسیم صفحه به دو ستون */
    .main-container {
        display: flex;             /* استفاده از Flexbox برای تقسیم صفحه */
        flex-wrap: wrap;           /* اجازه می‌دهد محتوای صفحه در صورت نیاز به چند خط بشود */
    }

    /* استایل برای سایدبار فیلتر */
    .sidebar {
        width: 20%;                /* عرض سایدبار 20% از صفحه */
        padding: 20px;
        background-color: #f5f5f5;
        box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.1); /* سایه برای سایدبار */
    }

    /* استایل برای بخش نمایش محصولات */
    .featured-products {
        width: 80%;                /* عرض بخش نمایش محصولات 80% از صفحه */
        padding: 20px;
    }

    /* استایل برای گرید محصولات */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* تعداد ستون‌ها همیشه 4 تا باشد */
        gap: 20px;
        justify-items: center; /* این باعث می‌شود محصولات در هر سلول گرید به صورت مرکزی قرار گیرند */
    }

    /* استایل برای کارت‌های محصولات */
    .product-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* سایه برای کارت محصولات */
        padding: 15px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* انیمیشن هنگام هاور */
    }

    /* تغییرات هنگام هاور کارت محصول */
    .product-card:hover {
        transform: translateY(-5px); /* جابجایی کارت به سمت بالا */
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); /* افزایش سایه هنگام هاور */
    }

    .sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px; /* Adds space between each row */
    }

    .filter-group {
        margin-bottom: 15px;
    }

    .filter-input {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }

    .filter-select {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 1rem;
        background-color: #fff;
    }

    .filter-range {
        width: 100%;
        margin-top: 8px;
    }

    .container {
        width: 100%;
    }

    /* استایل برای فیلتر بر اساس برند */
    .filter-brands {
        background-color: #fff;
        padding: 20px 0;
        border-bottom: 2px solid #f39c12; /* خط جداکننده نارنجی */
    }

    .filter-brands .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: right;
    }

    .filter-brands h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #f39c12; /* رنگ نارنجی برای عنوان */
    }

    .filter-brands .filter-select {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
    }

    /* استایل برای فیلتر پیشرفته */
    .filter-options {
        background-color: #fff;
        padding: 20px 0;
        border-bottom: 2px solid #f39c12; /* خط جداکننده نارنجی */
    }

    .filter-options h3 {
        font-size: 1.3rem;
        font-weight: 600;
        color: #f39c12; /* رنگ نارنجی برای عنوان */
        margin-bottom: 10px;
    }

    .filter-options .filter-group {
        margin-bottom: 15px;
    }

    /* استایل برای فیلد‌های ورودی و انتخاب‌ها */
    .sidebar .filter-group label {
        font-size: 1rem;
        color: #555;
    }

    .sidebar .filter-group input[type="range"] {
        width: 100%;
        margin-top: 8px;
        margin-bottom: 10px;
    }

    /* اضافه کردن رنگ نارنجی برای دکمه‌ها */
    .sidebar .filter-group select {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
        background-color: #fff;
    }

    /* استایل برای فیلتر جستجو */
    .filter-search {
        background-color: #fff;
        padding: 20px 0;
        border-bottom: 2px solid #f39c12; /* خط جداکننده نارنجی */
    }

    .filter-search h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #f39c12; /* رنگ نارنجی برای عنوان */
        margin-bottom: 10px;
    }

    .filter-search .filter-input {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
    }

    /* استایل کلی برای هر بخش فیلتر */
    .sidebar-section {
        padding: 15px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-bottom: 2px solid #f39c12; /* خط جداکننده نارنجی */
    }

    /* تغییر رنگ وقتی کاربر روی فیلتر ها هاور می‌کند */
    .sidebar .filter-group select:hover,
    .sidebar .filter-input:hover {
        border-color: #f39c12;
        transition: border-color 0.3s ease;
    }

    /* استایل برای فیلتر بر اساس قدرت وات */
    .filter-power {
        background-color: #fff;
        padding: 20px 0;
        border-bottom: 2px solid #f39c12; /* خط جداکننده نارنجی */
    }

    .filter-power h3 {
        font-size: 1.3rem;
        font-weight: 600;
        color: #f39c12; /* رنگ نارنجی برای عنوان */
        margin-bottom: 10px;
    }

    .filter-power .filter-group {
        margin-bottom: 10px;
    }

    .filter-checkbox {
        margin-right: 10px;
    }

    .filter-power label {
        font-size: 1rem;
        color: #555;
        display: block;
    }

    .filter-checkbox:checked {
        background-color: #f39c12;
        border-color: #f39c12;
    }

    .filter-checkbox:focus {
        outline: none;
        border-color: #f39c12;
    }

    /* استایل برای بخش‌هایی که شامل چک‌باکس‌ها هستند */
    .sidebar .filter-group label {
        font-size: 1rem;
        color: #555;
        margin-bottom: 10px;
    }

    /* استایل برای بخش مرتب سازی */
    .sort-options {
        background-color: #fff;
        padding: 20px 0;
        border-bottom: 2px solid #f39c12; /* خط جداکننده نارنجی */
    }

    .sort-options h3 {
        font-size: 1.3rem;
        font-weight: 600;
        color: #f39c12; /* رنگ نارنجی برای عنوان */
        margin-bottom: 10px;
    }

    .sort-options .filter-group {
        margin-bottom: 10px;
    }

    .sort-options label {
        font-size: 1rem;
        color: #555;
        margin-bottom: 5px;
        display: block;
    }

    .sort-options .filter-select {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
        background-color: #fff;
        cursor: pointer;
    }

    .sort-options .filter-select:focus {
        outline: none;
        border-color: #f39c12; /* تغییر رنگ حاشیه زمانی که فیلد انتخاب می‌شود */
    }

    /* استایل برای دکمه ثبت فیلتر */
    #applyFilter {
        display: block; /* برای قرار دادن دکمه در وسط */
        width: 200px; /* عرض دکمه */
        margin: 20px auto; /* برای قرار گرفتن دکمه در وسط */
        padding: 10px 20px; /* فاصله داخلی دکمه */
        font-family: 'Vazir', sans-serif; /* استفاده از فونت Vazir */
        font-size: 16px; /* اندازه فونت */
        color: #fff; /* رنگ متن سفید */
        background: linear-gradient(to right, #FFA500, #FF0000); /* گرادیانت نارنجی، زرد و قرمز */
        border: none; /* حذف حاشیه */
        border-radius: 5px; /* گوشه‌های دکمه گرد */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2), 0 1px 3px rgba(0, 0, 0, 0.1); /* سایه جعبه */
        cursor: pointer; /* نشانگر موس تغییر می‌کند به یک اشاره‌گر */
        text-align: center; /* متن دکمه در وسط قرار می‌گیرد */
        transition: all 0.3s ease; /* برای انیمیشن نرم */
    }

    /* تغییرات هنگام هاور */
    #applyFilter:hover {
        background: linear-gradient(to right, #FF8C00, #FF6347); /* تغییر گرادیانت در هاور */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3), 0 3px 6px rgba(0, 0, 0, 0.1); /* افزایش سایه */
        transform: translateY(-2px); /* جابجایی دکمه به سمت بالا */
    }

    /*search result styles*/
    .search-result-item {
        padding: 12px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-result-item:hover {
        background-color: #f8f9fa;
    }

    .result-item-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .product-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }

    .product-info h3 {
        margin: 0 0 5px 0;
        font-size: 16px;
        color: #333;
    }

    .product-info .price {
        margin: 0;
        font-size: 14px;
        color: #28a745;
        font-weight: bold;
    }

</style>


<!-- Footer -->
<div id="footer-container"></div>

<!-- JavaScript -->
<script src="assets/js/app.js"></script>
<script src="assets/js/podsystem.js"></script>

<!--Script for limit price-->
<script>
    // گرفتن المان های مورد نظر
    const priceMin = document.getElementById("priceMin");
    const priceMax = document.getElementById("priceMax");
    const priceMinValue = document.getElementById("priceMinValue");
    const priceMaxValue = document.getElementById("priceMaxValue");
    const applyFilterButton = document.getElementById("applyFilter");

    // نمایش مقدار اولیه قیمت با فرمت کاما
    priceMinValue.textContent = formatPrice(priceMin.value) + " تومان";
    priceMaxValue.textContent = formatPrice(priceMax.value) + " تومان";

    // تنظیم رویداد برای تغییر نوار لغزنده حداقل قیمت
    priceMin.addEventListener("input", function () {
        priceMinValue.textContent = formatPrice(priceMin.value) + " تومان";  // نمایش قیمت با فرمت کاما
    });

    // تنظیم رویداد برای تغییر نوار لغزنده حداکثر قیمت
    priceMax.addEventListener("input", function () {
        priceMaxValue.textContent = formatPrice(priceMax.value) + " تومان";  // نمایش قیمت با فرمت کاما
    });

    // تابع برای فرمت کردن قیمت با کاما
    function formatPrice(value) {
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // اعمال فیلتر و رفرش صفحه با دکمه ثبت فیلتر
    applyFilterButton.addEventListener("click", function () {
        const minPrice = priceMin.value;
        const maxPrice = priceMax.value;

        // بروزرسانی URL با پارامترهای جدید فیلتر
        const newUrl = window.location.pathname + "?minPrice=" + minPrice + "&maxPrice=" + maxPrice;
        history.pushState(null, "", newUrl);

        // رفرش صفحه برای نمایش محصولات جدید
        location.reload();  // صفحه به طور خودکار رفرش می‌شود
    });
</script>

<!--Script for Cheapest & Expensive products in order-->
<script>
    // گرفتن المان select مرتب‌سازی
    const sortBy = document.getElementById("sortBy");

    // هنگام تغییر گزینه مرتب‌سازی
    sortBy.addEventListener("change", function () {
        const sortOption = sortBy.value;

        // دریافت فیلتر قیمت از URL
        const minPrice = new URLSearchParams(window.location.search).get('minPrice') || 0;
        const maxPrice = new URLSearchParams(window.location.search).get('maxPrice') || 1000000;

        // بررسی گزینه انتخاب‌شده
        if (sortOption === "default") {
            // اگر گزینه "ترتیب عادی" انتخاب شد، صفحه را بدون فیلتر یا مرتب‌سازی رفرش کنید
            const newUrl = window.location.pathname + "?minPrice=" + minPrice + "&maxPrice=" + maxPrice;
            history.pushState(null, "", newUrl);
            location.reload(); // صفحه به طور خودکار رفرش می‌شود
        } else {
            // بروزرسانی آدرس URL با پارامتر جدید مرتب‌سازی
            const newUrl = window.location.pathname + "?minPrice=" + minPrice + "&maxPrice=" + maxPrice + "&sortBy=" + sortOption;
            history.pushState(null, "", newUrl);
            location.reload(); // صفحه به طور خودکار رفرش می‌شود
        }
    });
</script>

<!-- Script for Brand Filtering -->
<script>
    // گرفتن المان brandFilter
    const brandFilter = document.getElementById("brandFilter");

    // هنگام تغییر گزینه برند
    brandFilter.addEventListener("change", function () {
        const selectedBrand = brandFilter.value;

        // دریافت فیلتر قیمت از URL
        const minPrice = new URLSearchParams(window.location.search).get('minPrice') || 0;
        const maxPrice = new URLSearchParams(window.location.search).get('maxPrice') || 1000000;
        const sortBy = new URLSearchParams(window.location.search).get('sortBy') || 'default';

        // بروزرسانی آدرس URL با پارامترهای جدید فیلتر
        const newUrl = window.location.pathname + "?minPrice=" + minPrice + "&maxPrice=" + maxPrice + "&sortBy=" + sortBy + "&brand=" + selectedBrand;
        history.pushState(null, "", newUrl);

        // رفرش صفحه برای نمایش محصولات جدید
        location.reload();
    });
</script>

<!-- Script for Availability Filtering -->
<script>
    // گرفتن المان availabilityFilter
    const availabilityFilter = document.getElementById("availabilityFilter");

    // هنگام تغییر گزینه وضعیت موجودی
    availabilityFilter.addEventListener("change", function () {
        const selectedAvailability = availabilityFilter.value;

        // دریافت فیلتر قیمت از URL
        const minPrice = new URLSearchParams(window.location.search).get('minPrice') || 0;
        const maxPrice = new URLSearchParams(window.location.search).get('maxPrice') || 1000000;
        const sortBy = new URLSearchParams(window.location.search).get('sortBy') || 'default';
        const brand = new URLSearchParams(window.location.search).get('brand') || '';

        // بروزرسانی آدرس URL با پارامترهای جدید فیلتر
        const newUrl = window.location.pathname + "?minPrice=" + minPrice + "&maxPrice=" + maxPrice + "&sortBy=" + sortBy + "&brand=" + brand + "&availability=" + selectedAvailability;
        history.pushState(null, "", newUrl);

        // رفرش صفحه برای نمایش محصولات جدید
        location.reload();
    });
</script>

<!-- Script for Rating Filtering -->
<script>
    // گرفتن المان ratingFilter
    const ratingFilter = document.getElementById("ratingFilter");

    // هنگام تغییر گزینه رتبه‌بندی
    ratingFilter.addEventListener("change", function () {
        const selectedRating = ratingFilter.value;

        // دریافت سایر فیلترها از URL
        const minPrice = new URLSearchParams(window.location.search).get('minPrice') || 0;
        const maxPrice = new URLSearchParams(window.location.search).get('maxPrice') || 1000000;
        const sortBy = new URLSearchParams(window.location.search).get('sortBy') || 'default';
        const brand = new URLSearchParams(window.location.search).get('brand') || '';
        const availability = new URLSearchParams(window.location.search).get('availability') || '';

        // بروزرسانی آدرس URL با پارامترهای جدید فیلتر
        const newUrl = window.location.pathname + "?minPrice=" + minPrice + "&maxPrice=" + maxPrice + "&sortBy=" + sortBy + "&brand=" + brand + "&availability=" + availability + "&rating=" + selectedRating;
        history.pushState(null, "", newUrl);

        // رفرش صفحه برای نمایش محصولات جدید
        location.reload();
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchField = document.getElementById('searchField');
        const resultsContainer = document.getElementById('searchResults');
        let searchTimeout;

        if (!searchField || !resultsContainer) {
            console.error('عناصر جستجو یافت نشدند!');
            return;
        }

        searchField.addEventListener('input', function(e) {
            const searchTerm = e.target.value.trim();
            clearTimeout(searchTimeout);

            if (searchTerm.length < 2) {
                resultsContainer.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetchSearchResults(searchTerm);
            }, 300);
        });

        async function fetchSearchResults(searchTerm) {
            try {
                const response = await fetch('api/search.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ search_term: searchTerm })
                });

                // بررسی وضعیت پاسخ
                if (!response.ok) {
                    throw new Error(`خطای HTTP: ${response.status}`);
                }

                // بررسی نوع محتوا
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(`پاسخ غیر JSON دریافت شد: ${text.substring(0, 100)}...`);
                }

                return await response.json();

            } catch (error) {
                console.error('جزئیات خطا:', error);
                throw error;
            }
        }

        function updateResultsUI(products) {
            resultsContainer.innerHTML = '';

            if (products.length === 0) {
                resultsContainer.innerHTML = '<div class="no-results">نتیجه‌ای یافت نشد</div>';
                resultsContainer.style.display = 'block';
                return;
            }

            products.forEach(product => {
                const item = document.createElement('div');
                item.className = 'search-result-item';
                item.innerHTML = `
                <div class="result-item-content">
                    ${product.image_url ? `<img src="${product.image_url}" alt="${product.name}" class="product-thumb">` : ''}
                    <div class="product-info">
                        <h3>${product.name_fa || product.name}</h3>
                        <p class="price">${product.price ? new Intl.NumberFormat('fa-IR').format(product.price) + ' تومان' : 'تماس بگیرید'}</p>
                    </div>
                </div>
            `;

                item.addEventListener('click', () => {
                    window.location.href = `product.php?id=${product.id}`;
                });

                resultsContainer.appendChild(item);
            });

            resultsContainer.style.display = 'block';
        }

        document.getElementById('searchField').addEventListener('input', async function(e) {
            const searchTerm = e.target.value.trim();

            if (searchTerm.length < 2) {
                document.getElementById('searchResults').style.display = 'none';
                return;
            }

            try {
                const data = await fetchSearchResults(searchTerm);

                if (!data.success) {
                    throw new Error(data.message || 'خطا در پردازش جستجو');
                }

                updateResultsUI(data.results);

            } catch (error) {
                const resultsContainer = document.getElementById('searchResults');
                resultsContainer.innerHTML = `
            <div class="search-error">
                خطا در دریافت نتایج: ${error.message}
            </div>
        `;
                resultsContainer.style.display = 'block';
            }
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                resultsContainer.style.display = 'none';
            }
        });
    });

</script>
</html>
