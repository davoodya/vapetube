<?php

// اتصال به دیتابیس
$host = 'localhost';
$dbname = 'vape_tube';
$username = 'root';
$password = '12945';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
} catch (PDOException $e) {
    die("خطا در اتصال به پایگاه داده: " . $e->getMessage());
}

// دریافت ID از URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId <= 0) {
    die("شناسه محصول نامعتبر است.");
}

// واکشی محصول
$stmt = $pdo->prepare("SELECT p.*, c.name_fa AS category_name, sc.name_fa AS subcategory_name
                       FROM products p
                       LEFT JOIN categories c ON p.category_id = c.id
                       LEFT JOIN subcategories sc ON p.subcategory_id = sc.id
                       WHERE p.id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("محصول یافت نشد.");
}

// مسیر کامل عکس
$imagePath = "assets/images/products/" . htmlspecialchars($product['image_url']);
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name_fa']) ?> | ویپ تیوب</title>
    <!-- وارد کردن فایل CSS -->

    <base href="/vape-tube/">

    <link rel="stylesheet" href="assets/css/product.css">

</head>
<body>

<!-- Header Section -->
<div class="header-section">
    <!-- کارت اول: تصاویر اسلاید شو -->
    <div class="product-card product-images">
        <div class="slider">
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['name_fa']) ?>" class="product-image" id="mainImage">
            <div class="thumbnails">
                <!-- تصاویر گالری -->
                <?php if (!empty($product['gallery_images'])): ?>
                    <?php
                    // تجزیه JSON
                    $galleryImages = json_decode($product['gallery_images'], true);
                    foreach ($galleryImages as $image): ?>
                        <img src="assets/images/products/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($product['name_fa']) ?>" onclick="changeImage('assets/images/products/<?= htmlspecialchars($image) ?>')" class="thumbnail-img">
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- کارت دوم: نام و توضیحات مختصر محصول -->
    <div class="product-card product-info">
        <h2 class="product-title"><?= htmlspecialchars($product['name_fa']) ?></h2>
        <p class="product-subtitle"><?= htmlspecialchars($product['short_description_fa']) ?></p>
    </div>

    <!-- کارت سوم: انتخاب تعداد و دکمه افزودن به سبد خرید -->
    <div class="product-card add-to-cart">
        <div class="quantity-selector">
            <button onclick="changeQuantity(-1)">-</button>
            <input type="number" id="quantity" value="1" min="1">
            <button onclick="changeQuantity(1)">+</button>
        </div>
        <button onclick="addToCart()">افزودن به سبد خرید</button>
    </div>
</div>

<hr>

<!-- Body Section -->
<div class="product-body">
    <h2>توضیحات کامل</h2>
    <p><?= nl2br(htmlspecialchars($product['description_fa'])) ?></p>

    <!-- YouTube Video -->
    <div class="youtube-video">
        <h2>ویدئو محصول</h2>
        <iframe src="https://www.youtube.com/embed/<?= $product['video_url'] ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

<!-- Comments Section -->
<div class="comments">
    <h3>نظرات کاربران</h3>
    <!-- Placeholder for comments section -->
    <p>نظرات کاربران در اینجا نمایش داده خواهد شد.</p>
</div>

<!-- Footer Section -->
<footer>
    <p>تمامی حقوق متعلق به ویپ تیوب است.</p>
</footer>

<script>
    function changeImage(image) {
        document.getElementById('mainImage').src = image;
    }

    function changeQuantity(amount) {
        const quantityInput = document.getElementById('quantity');
        let currentQuantity = parseInt(quantityInput.value);
        currentQuantity += amount;
        if (currentQuantity < 1) currentQuantity = 1;
        quantityInput.value = currentQuantity;
    }

    function addToCart() {
        const productId = <?= $productId ?>;
        const quantity = document.getElementById('quantity').value;
        // Here you can add the logic to add the product to the cart
        alert('محصول به سبد خرید اضافه شد');
    }
</script>

</body>
</html>
