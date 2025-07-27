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
    <style>
        body {
            font-family: sans-serif;
            direction: rtl;
            background: #f7f7f7;
            padding: 20px;
        }
        .product-container {
            background: white;
            max-width: 900px;
            margin: auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .product-image {
            max-width: 100%;
            border-radius: 8px;
        }
        .product-title {
            font-size: 26px;
            margin: 15px 0 10px;
        }
        .price {
            font-size: 22px;
            color: green;
        }
        .description {
            margin-top: 15px;
            color: #444;
        }
        .meta {
            margin-top: 25px;
            font-size: 14px;
            color: #888;
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #007BFF;
            text-decoration: none;
        }
    </style>
    <base href="/vape-tube/">
</head>
<body>

<div class="product-container">
    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['name_fa']) ?>" class="product-image">

    <h1 class="product-title"><?= htmlspecialchars($product['name_fa']) ?></h1>

    <div class="price">
        قیمت: <?= number_format($product['price']) ?> تومان
        <?php if (!empty($product['sale_price'])): ?>
            <span style="color:red; text-decoration:line-through; font-size:16px; margin-right:10px;">
                <?= number_format($product['sale_price']) ?> تومان
            </span>
        <?php endif; ?>
    </div>

    <div class="description">
        <strong>توضیحات:</strong><br>
        <?= nl2br(htmlspecialchars($product['description_fa'])) ?>
    </div>

    <div class="meta">
        <div>دسته‌بندی: <?= $product['category_name'] ?></div>
        <?php if ($product['subcategory_name']): ?>
            <div>زیر دسته: <?= $product['subcategory_name'] ?></div>
        <?php endif; ?>
        <div>کد محصول (SKU): <?= htmlspecialchars($product['sku']) ?></div>
        <div>موجودی انبار: <?= $product['stock_quantity'] ?> عدد</div>
    </div>

    <a href="index.html" class="back-link">← بازگشت به صفحه اصلی</a>
</div>

</body>
</html>
