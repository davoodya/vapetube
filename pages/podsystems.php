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

  <!-- Pod System Products -->
    <!-- Get All Pod systems from Database -->
    <section class="featured-products">
        <div class="container">
            <div class="section-header">
                <h2 style="display: flex; ; justify-content: center; align-items: center; text-align: center; color: orange; margin-top: 25px;">انواع پاد سیستم ها</h2>
                <p style="display: flex; ; justify-content: center; align-items: center; text-align: center; font-size: 1.2rem">پاد سیستم های موجود در وبسایت ما</p>
            </div>
            <div class="products-grid" id="featuredProducts">
                <?php
                // اتصال به دیتابیس
                $conn = new mysqli('localhost', 'root', '12945', 'vape_tube');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // کوئری برای واکشی محصولات پاد
                $sql = "SELECT * FROM products WHERE category_id LIKE '%2%'";
                $result = $conn->query($sql);

                // بررسی اینکه آیا نتیجه‌ای برگشت داده شد یا نه
                if ($result->num_rows > 0) {
                    // نمایش محصولات
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="product-card">';
                        echo '<img src="' . $row["image_url"] . '" alt="' . $row["name"] . '" style="width: 300px; height: 300px; object-fit: scale-down; object-position: center; display: block; margin: 0 auto; padding: 3px 3px 3px 3px;" >';

                        echo '<h3 style="text-align: center; margin: 0 auto;">' . $row["name"] . '</h3>';

                        echo '<p>' . $row["description_fa"] . '</p>';
                        echo '<p style="display: block; text-align: center; margin: 0 auto;" class="product-price">' . number_format($row["price"], 2) . ' تومان</p>';
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
        </div>
    </section>

    <section class="categories">
        <div class="section-header">
          <h2>دسته بندی محصولات</h2>
          <p>با خیالت راحت محصول را انتخاب و از ویپ کلاب خرید کنید</p>
        </div>
    <div class="category_container">

      <!-- Pod Systems Category -->
      <a href="pages/podsystems.php">
        <div class="category-card">
          <img src="assets/images/categories/podsystem.jpeg" alt="پاد سیستم" />
          <h3>Uwell</h3>

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

  <!-- Filter Brands Section -->
  <section class="filter-brands">
    <div class="container">
      <div class="section-header">
        <h2>برندهای پاد سیستم</h2>
        <select id="brandFilter" class="filter-select">
          <option value="">همه برندها</option>
          <option value="brand1">برند 1</option>
          <option value="brand2">برند 2</option>
          <option value="brand3">برند 3</option>
          <!-- Add more brands here -->
        </select>
      </div>
    </div>
  </section>

  <!-- Products Display Section -->
  <section class="products-display">
    <div class="container">
      <div class="products-container">
        <!-- Sidebar Filter -->
        <aside class="sidebar-filter">
          <h3>فیلتر پیشرفته</h3>
          <form id="filterForm">
            <div class="filter-group">
              <label for="priceRange">محدوده قیمت</label>
              <input type="range" id="priceRange" min="0" max="1000" step="10" />
              <span id="priceValue">۰ تومان</span>
            </div>
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
            <div class="filter-group">
              <label for="availabilityFilter">وضعیت موجودی</label>
              <select id="availabilityFilter">
                <option value="">همه</option>
                <option value="inStock">موجود</option>
                <option value="outOfStock">ناموجود</option>
              </select>
            </div>
          </form>
        </aside>


      </div>
    </div>
  </section>

</main>

<!-- Footer -->
<div id="footer-container"></div>

<!-- JavaScript -->
<script src="assets/js/app.js"></script>
<script src="assets/js/podsystem.js"></script>


</body>
</html>
