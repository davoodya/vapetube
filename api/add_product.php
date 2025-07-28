<?php
// اتصال به دیتابیس
$conn = new mysqli('localhost', 'root', '12945', 'vape_tube');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// پردازش داده‌های فرم پس از ارسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // دریافت داده‌ها از فرم
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $name = $_POST['name'];
    $name_fa = $_POST['name_fa'];
    $slug = $_POST['slug'];
    $description = $_POST['description'];
    $description_fa = $_POST['description_fa'];
    $short_description = $_POST['short_description'];
    $short_description_fa = $_POST['short_description_fa'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'];
    $sku = $_POST['sku'];
    $stock_quantity = $_POST['stock_quantity'];
    $low_stock_threshold = $_POST['low_stock_threshold'];
    $weight = $_POST['weight'];
    $dimensions = $_POST['dimensions'];
    $image_url = $_FILES['image_url']['name'];
    $gallery_images = json_encode($_FILES['gallery_images']['name']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];

    // بارگذاری تصویر محصول
    $image_tmp = $_FILES['image_url']['tmp_name'];
    $image_path = 'uploads/' . basename($image_url);
    move_uploaded_file($image_tmp, $image_path);

    // بارگذاری گالری تصاویر
    $gallery_image_paths = [];
    foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
        $image_name = $_FILES['gallery_images']['name'][$key];
        $image_path = 'uploads/gallery/' . basename($image_name);
        move_uploaded_file($tmp_name, $image_path);
        $gallery_image_paths[] = $image_path;
    }
    $gallery_images_json = json_encode($gallery_image_paths);

    // واکشی دسته‌بندی‌ها
    $category_sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name";
    $category_result = $conn->query($category_sql);

    // واکشی زیر دسته‌بندی‌ها برای هر دسته
    $subcategory_sql = "SELECT * FROM subcategories WHERE is_active = 1 ORDER BY name";
    $subcategory_result = $conn->query($subcategory_sql);

    $subcategories = [];
    while ($subcategory = $subcategory_result->fetch_assoc()) {
        $subcategories[$subcategory['category_id']][] = $subcategory; // ذخیره زیر دسته‌بندی‌ها بر اساس شناسه دسته‌بندی

        // کوئری برای درج محصول جدید
        $sql = "INSERT INTO products (category_id, subcategory_id, name, name_fa, slug, description, description_fa, short_description, short_description_fa, price, sale_price, sku, stock_quantity, low_stock_threshold, weight, dimensions, image_url, gallery_images, is_featured, is_active, meta_title, meta_description)
    VALUES ('$category_id', '$subcategory_id', '$name', '$name_fa', '$slug', '$description', '$description_fa', '$short_description', '$short_description_fa', '$price', '$sale_price', '$sku', '$stock_quantity', '$low_stock_threshold', '$weight', '$dimensions', '$image_path', '$gallery_images_json', '$is_featured', '$is_active', '$meta_title', '$meta_description')";

        if ($conn->query($sql) === TRUE) {
            echo "محصول با موفقیت افزوده شد.";
        } else {
            echo "خطا در اضافه کردن محصول: " . $conn->error;
        }
    }
}
// بستن اتصال به دیتابیس
$conn->close();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>افزودن محصول جدید</title>
</head>
<body>
<h2>فرم افزودن محصول جدید</h2>
<form action="add_product.php" method="POST" enctype="multipart/form-data">
    <label for="category_id">دسته‌بندی:</label>
    <select name="category_id" id="category_id" required onchange="loadSubcategories()">
        <option value="">انتخاب دسته‌بندی</option>
        <?php
        // نمایش دسته‌بندی‌ها
        if ($category_result->num_rows > 0) {
            while ($category = $category_result->fetch_assoc()) {
                echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
            }
        }
        ?>
    </select><br><br>

    <label for="subcategory_id">زیر دسته‌بندی:</label>
    <label for="subcategory_id">زیر دسته‌بندی:</label>
    <select name="subcategory_id" id="subcategory_id">
        <option value="">انتخاب زیر دسته‌بندی</option>
        <!-- زیر دسته‌بندی‌ها با جاوا اسکریپت بارگذاری می‌شوند -->
    </select><br><br>

    <label for="name">نام محصول:</label>
    <input type="text" name="name" id="name" required><br><br>

    <label for="name_fa">نام محصول به فارسی:</label>
    <input type="text" name="name_fa" id="name_fa" required><br><br>

    <label for="slug">اسلاگ محصول:</label>
    <input type="text" name="slug" id="slug" required><br><br>

    <label for="description">توضیحات:</label>
    <textarea name="description" id="description"></textarea><br><br>

    <label for="description_fa">توضیحات به فارسی:</label>
    <textarea name="description_fa" id="description_fa"></textarea><br><br>

    <label for="short_description">توضیحات کوتاه:</label>
    <textarea name="short_description" id="short_description"></textarea><br><br>

    <label for="short_description_fa">توضیحات کوتاه به فارسی:</label>
    <textarea name="short_description_fa" id="short_description_fa"></textarea><br><br>

    <label for="price">قیمت:</label>
    <input type="number" name="price" id="price" required><br><br>

    <label for="sale_price">قیمت فروش:</label>
    <input type="number" name="sale_price" id="sale_price"><br><br>

    <label for="sku">کد SKU:</label>
    <input type="text" name="sku" id="sku" required><br><br>

    <label for="stock_quantity">تعداد موجودی:</label>
    <input type="number" name="stock_quantity" id="stock_quantity" required><br><br>

    <label for="low_stock_threshold">حد آستانه کمبود موجودی:</label>
    <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="5"><br><br>

    <label for="weight">وزن:</label>
    <input type="number" name="weight" id="weight"><br><br>

    <label for="dimensions">ابعاد:</label>
    <input type="text" name="dimensions" id="dimensions"><br><br>

    <label for="image_url">تصویر محصول:</label>
    <input type="file" name="image_url" id="image_url"><br><br>

    <label for="gallery_images">گالری تصاویر:</label>
    <input type="file" name="gallery_images[]" multiple><br><br>

    <label for="is_featured">آیا محصول ویژه است؟</label>
    <input type="checkbox" name="is_featured" id="is_featured" value="1"><br><br>

    <label for="is_active">آیا محصول فعال است؟</label>
    <input type="checkbox" name="is_active" id="is_active" value="1" checked><br><br>

    <label for="meta_title">عنوان متا:</label>
    <input type="text" name="meta_title" id="meta_title"><br><br>

    <label for="meta_description">توضیحات متا:</label>
    <textarea name="meta_description" id="meta_description"></textarea><br><br>

    <button type="submit">افزودن محصول</button>
</form>
<script>
    // بارگذاری زیر دسته‌بندی‌ها بر اساس دسته‌بندی انتخاب شده
    function loadSubcategories() {
        const categoryId = document.getElementById('category_id').value;
        const subcategorySelect = document.getElementById('subcategory_id');
        subcategorySelect.innerHTML = '<option value="">انتخاب زیر دسته‌بندی</option>'; // پاک کردن گزینه‌های قبلی

        <?php
        // انتقال داده‌های زیر دسته‌بندی‌ها به جاوا اسکریپت
        echo 'const subcategories = ' . json_encode($subcategories) . ';';
        ?>

        // بررسی اینکه دسته‌بندی انتخاب شده زیر دسته‌بندی دارد یا خیر
        if (categoryId && subcategories[categoryId]) {
            subcategories[categoryId].forEach(function(subcategory) {
                const option = document.createElement('option');
                option.value = subcategory.id;
                option.textContent = subcategory.name;
                subcategorySelect.appendChild(option);
            });
        }
    }
</script>
</body>
</html>
